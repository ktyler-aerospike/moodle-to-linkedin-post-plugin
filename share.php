<?php
require('../../config.php');
require_login();
require_once($CFG->libdir . '/filelib.php');

$badgeid   = required_param('badgeid', PARAM_ALPHANUMEXT);
$verifcode = required_param('verifcode', PARAM_ALPHANUMEXT);

// --- Load configuration.
$cfg = get_config('local_linkedinshare');

// Endpoint: allow admin to store the API Gateway base (e.g. https://...gateway.dev)
// and append /auth/linkedin/start here if it isn't already included.
$endpoint = trim($cfg->endpoint ?? '');
if ($endpoint === '') {
    // Fallback default base if not configured.
    $endpoint = 'https://mtl-gateway-1tx4fl3l.wl.gateway.dev';
}
$starturl = $endpoint;
if (!preg_match('#/auth/linkedin/start/?$#', $starturl)) {
    $starturl = rtrim($starturl, '/') . '/auth/linkedin/start';
}

// API key must be present.
$apikey = get_config('local_linkedinshare', 'apikey');
if (empty($apikey)) {
    throw new \moodle_exception('missingconfig', 'local_linkedinshare', '', 'API key');
}

// --- Build upstream URL WITHOUT putting the key in the query.
$query = [
    'badgeid'   => $badgeid,
    'verifcode' => $verifcode,
];
$upstreamUrl = $starturl . '?' . http_build_query($query);

// --- Use Moodle's curl helper.
$curl = new \curl();

// Send GET to the gateway with the key in a header, not the URL.
$options = [
    'CURLOPT_HTTPHEADER' => [
        'x-api-key: ' . $apikey, // Preferred for API Gateway/ESPv2
    ],
    'RETURNTRANSFER'   => true,
    'FOLLOWLOCATION'   => false, // We want to relay 3xx ourselves
    'HEADER'           => true,  // Include headers so we can inspect Location/status
    'SSL_VERIFYPEER'   => true,
    'SSL_VERIFYHOST'   => 2,
];

// --- Execute
$response = $curl->get($upstreamUrl, [], $options);

if ($response === false || $curl->get_errno()) {
    // Transport-level failure.
    $err = method_exists($curl, 'error') ? $curl->error : 'cURL error';
    throw new \moodle_exception('curlerror', 'local_linkedinshare', '', $err);
}

// --- Split headers/body (single header block because FOLLOWLOCATION=false).
list($rawheaders, $body) = explode("\r\n\r\n", $response, 2);
$headers = explode("\r\n", $rawheaders);

// --- Get HTTP status code safely.
$info = $curl->get_info();
$httpcode = (int)($info['http_code'] ?? 0);

if ($httpcode === 0) {
    // Fallback: parse the status line if curl info isn't available.
    $statusline = is_array($headers) ? (string)reset($headers) : (string)$headers;

    // Support HTTP/1.0, HTTP/1.1, and HTTP/2 (with optional .x minor version).
    if (preg_match('#HTTP/\d+(?:\.\d+)?\s+(\d{3})#i', $statusline, $m)) {
        $httpcode = (int)$m[1];
    } else {
        // Throw a proper Moodle exception with the raw status line as {$a}.
        throw new \moodle_exception('invalidupstreamresponse', 'local_linkedinshare', '', $statusline);
    }
}

// --- Relay typical outcomes:

// 1) 3xx → send user to the Location (usually LinkedIn). This Location will NOT contain your API key.
if (in_array($httpcode, [301, 302, 303, 307, 308], true)) {
    $location = null;
    foreach ($headers as $h) {
        if (stripos($h, 'Location:') === 0) {
            $location = trim(substr($h, 9));
            break;
        }
    }
    if ($location) {
        \core\session\manager::write_close();
        redirect(new \moodle_url($location)); // Safe: typically LinkedIn’s URL
    } else {
        throw new \moodle_exception('redirectmissinglocation', 'local_linkedinshare', '', $httpcode);
    }
}

// 2) 2xx → proxy body back (if the start endpoint renders a page)
if ($httpcode >= 200 && $httpcode < 300) {
    echo $body;
    exit;
}

// 3) Other → show a concise error (don’t leak secrets)
http_response_code(502);
echo 'Share service is unavailable (upstream status ' . $httpcode . ').';
exit;
