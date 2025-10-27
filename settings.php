<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_linkedinshare',
        get_string('pluginname', 'local_linkedinshare')
    );

    // Gateway endpoint (the API Gateway URL your Moodle calls, e.g. https://<gateway-host>/verify).
    $settings->add(new admin_setting_configtext(
        'local_linkedinshare/endpoint',
        'Function endpoint',
        'Full URL to your API Gateway route that proxies to Cloud Run (e.g. https://share-badge.your-domain.com).',
        'https://share-badge.your-domain.com',       
        PARAM_URL
    )

    $settings->add(
        new admin_setting_configcheckbox(
            'local_linkedinshare/enabled',
            get_string('enabled', 'local_linkedinshare'),
            get_string('enabled_desc', 'local_linkedinshare'),
            1 // Default = enabled
        )
);

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
