<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
$redirect = optional_param('redirect', '', PARAM_LOCALURL);

require_login();

global $DB, $USER;

if ($prompt = $DB->get_record('local_linkedinshare', ['id' => $id, 'userid' => $USER->id])) {
    // Mark as confirmed; keep the row for audit.
    if (empty($prompt->sharedconfirmedat)) {
        $prompt->sharedconfirmedat = time();
        $DB->update_record('local_linkedinshare', $prompt);
    }
    \core\notification::success(get_string('confirm:success', 'local_linkedinshare'));
}

if ($redirect) {
    redirect(new moodle_url($redirect));
} else {
    redirect(new moodle_url('/'));
}
