<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
$redirect = optional_param('redirect', '', PARAM_LOCALURL);

require_login();

global $DB, $USER;

if ($prompt = $DB->get_record('local_linkedinshare', ['id' => $id, 'userid' => $USER->id])) {
    $prompt->optout = 1;
    $DB->update_record('local_linkedinshare', $prompt);
    \core\notification::success(get_string('dismiss:done', 'local_linkedinshare'));
}

redirect($redirect ? new moodle_url($redirect) : new moodle_url('/'));
