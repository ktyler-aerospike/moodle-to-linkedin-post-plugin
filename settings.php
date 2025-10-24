<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_linkedinshare', get_string('pluginname', 'local_linkedinshare'));

    // Gateway endpoint (the API Gateway URL your Moodle calls, e.g. https://<gateway-host>/verify).
    $settings->add(new admin_setting_configtext(
        'local_linkedinshare/endpoint',
        'Gateway endpoint',
        'Full URL to your API Gateway route that proxies to Cloud Run (e.g.https://mtl-911747996491.us-west1.run.app/auth/linkedin/start).',
        'https://mtl-911747996491.us-west1.run.app/auth/linkedin/start',
        PARAM_URL
    ));

    // API key for the gateway (sent as x-api-key). Masked in UI.
    $settings->add(new admin_setting_configpasswordunmask(
        'local_linkedinshare/apikey',
        'API key',
        'API key created in Google Cloud Console (APIs & Services → Credentials). This will be sent as the x-api-key header.',
        ''
    ));

    // Optional: HTTP timeout when calling the gateway.
    $settings->add(new admin_setting_configtext(
        'local_linkedinshare/timeout',
        'HTTP timeout (seconds)',
        'Timeout for outbound cURL request to the gateway.',
        20,
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}
