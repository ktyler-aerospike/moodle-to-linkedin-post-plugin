<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);

require_login();
require_sesskey();

use local_linkedinshare\local\repository;

repository::dismiss($id);

\core\notification::add(get_string('dismiss:done', 'local_linkedinshare'), \core\output\notification::NOTIFY_SUCCESS);

$redirect = optional_param('redirect', '', PARAM_LOCALURL);
if ($redirect) {
    redirect(new moodle_url($redirect));
} else {
    redirect(new moodle_url('/'));
}
