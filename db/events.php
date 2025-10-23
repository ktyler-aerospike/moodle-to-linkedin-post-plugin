<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    // tool_certificate: when a certificate PDF is issued.
    [
        'eventname'   => '\tool_certificate\event\certificate_issued',
        'callback'    => '\local_linkedinshare\observer::catch_all',
        'includefile' => '/local/linkedinshare/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999
    ],

    // (Optional) If mod_coursecertificate also emits its own event in your setup.
    [
        'eventname'   => '\mod_coursecertificate\event\certificate_issued',
        'callback'    => '\local_linkedinshare\observer::catch_all',
        'includefile' => '/local/linkedinshare/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999
    ],
];
