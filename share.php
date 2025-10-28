<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);

require_login();

global $DB, $USER;

$prompt = $DB->get_record('local_linkedinshare', ['id' => $id, 'userid' => $USER->id], '*', IGNORE_MISSING);

// Endpoint from settings (or fallback).
$cfg = get_config('local_linkedinshare');
$endpoint = trim($cfg->endpoint ?? '');
if ($endpoint === '') {
    $endpoint = 'https://post-as-function-135218520943.us-west1.run.app/auth/linkedin/start';
}

if ($prompt) {
    // Mark that the user clicked Share (first time only).
    if (empty($prompt->clickedat)) {
        $prompt->clickedat = time();
        $DB->update_record('local_linkedinshare', $prompt);
    }

    // Build external URL and redirect there.
    $shareurl = new moodle_url($endpoint, [
        'badgeid'   => $prompt->badgeid,
        'verifcode' => $prompt->verifcode,
    ]);

    redirect($shareurl);
}

// If nothing to do, go home.
redirect(new moodle_url('/'));
