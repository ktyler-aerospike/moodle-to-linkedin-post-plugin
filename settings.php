<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_linkedinshare',
        get_string('pluginname', 'local_linkedinshare')
    );

    // Gateway endpoint (the API Gateway URL your Moodle calls, e.g. https://share-badge.your-domain.com).
    $settings->add(new admin_setting_configtext(
        'local_linkedinshare/endpoint',
        get_string('endpoint', 'local_linkedinshare'), // or 'Function endpoint'
        get_string('endpoint_desc', 'local_linkedinshare'), // or a literal string if you prefer
        'https://share-badge.domain.com/auth/linkedin/start',
        PARAM_URL
    )); // <-- CLOSE the add() call properly

    // Enable/disable the plugin.
    $settings->add(new admin_setting_configcheckbox(
        'local_linkedinshare/enabled',
        get_string('enabled', 'local_linkedinshare'),
        get_string('enabled_desc', 'local_linkedinshare'),
        1 // Default = enabled
    ));

    // Optional: HTTP timeout when calling the gateway.
    $settings->add(new admin_setting_configtext(
        'local_linkedinshare/timeout',
        get_string('timeout', 'local_linkedinshare'), // or 'HTTP timeout (seconds)'
        get_string('timeout_desc', 'local_linkedinshare'), // or a literal string
        20,
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}
