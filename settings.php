<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_linkedinshare', get_string('pluginname', 'local_linkedinshare'));

    // Gateway endpoint (the API Gateway URL your Moodle calls, e.g. https://<gateway-host>/verify).
    $settings->add(new admin_setting_configtext(
        'local_linkedinshare/endpoint',
        'Function endpoint',
        'Full URL to your API Gateway route that proxies to Cloud Run (e.g.https://mtl-911747996491.us-west1.run.app).',
        'https://mtl-911747996491.us-west1.run.app',
        PARAM_URL
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
