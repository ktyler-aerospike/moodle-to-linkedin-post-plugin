<?php
// local/linkedinshare/db/hooks.php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_top_of_body_html_generation::class,
        'callback' => [\local_linkedinshare\hooks\output::class, 'before_top_of_body'],
        'priority' => 500, // Optional: lower is later, higher runs earlier.
    ],
];
