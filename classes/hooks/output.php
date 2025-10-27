<?php
// local/linkedinshare/classes/hooks/output.php
namespace local_linkedinshare\hooks;

defined('MOODLE_INTERNAL') || die();

use core\hook\output\before_standard_top_of_body_html_generation;

final class output {
    /**
     * Injects my LinkedIn share banner at the very top of <body>.
     */
    public static function before_top_of_body(before_standard_top_of_body_html_generation $hook): void {
        global $PAGE, $COURSE, $USER;

        // Require a logged-in user (avoid guests).
        if (empty($USER->id) || \isguestuser()) {
            return;
        }

        // OPTIONAL: Restrict to mod_coursecertificate pages only.
        /*
        $iscertificatepage = (isset($PAGE->cm) && $PAGE->cm && $PAGE->cm->modname === 'coursecertificate');
        if (!$iscertificatepage) {
            return;
        }
        */

        $courseid = !empty($COURSE->id) ? (int)$COURSE->id : null;

        // Keep the existing repository call exactly as before.
        $prompt = \repository::get_current_user_prompt($courseid);
        if (!$prompt) {
            return;
        }

        // NOTE: Do not s() values for URLs; moodle_url will encode params.
        $badgeid   = $prompt->badgeid;
        $verifcode = $prompt->verifcode;

        // Read endpoint from plugin settings, with a safe fallback.
        $cfg = \get_config('local_linkedinshare');
        $endpoint = \trim($cfg->endpoint ?? '');
        if ($endpoint === '') {
            $endpoint = 'https://mtl-911747996491.us-west1.run.app';
        }

        $endpoint_w_path = \rtrim($endpoint, '/') . '/auth/linkedin/start';

        // Build the target URL with your params.
        $shareurl = new \moodle_url($endpoint_w_path, [
            'badgeid'   => $badgeid,
            'verifcode' => $verifcode,
        ]);

        $dismissurl = new \moodle_url('/local/linkedinshare/dismiss.php', [
            'id'       => $prompt->id,
            'sesskey'  => \sesskey(),
            'redirect' => \qualified_me(), // return to current page
        ]);

        $prompttext = \get_string('prompt_text', 'local_linkedinshare');
        $sharelabel = \get_string('share', 'local_linkedinshare');
        $notnow     = \get_string('notnow', 'local_linkedinshare');

        // Inline CSS shim (safe in this hook).
        $shim = \html_writer::tag('style', '
#local-linkedinshare-banner {
    position: sticky;
    top: 56px;      /* tweak to 64px+ if your theme header is taller */
    z-index: 1030;  /* above content, below modals */
    margin: 0;
}
body.path-admin #local-linkedinshare-banner { top: 0; }
');

        // Build the banner HTML.
        $html = \html_writer::div(
            \html_writer::div(
                \html_writer::tag('strong', \s($prompttext)) . ' ' .
                \html_writer::link($shareurl, \s($sharelabel), ['class' => 'btn btn-primary ml-2']) . ' ' .
                \html_writer::link($dismissurl, \s($notnow), ['class' => 'btn btn-secondary ml-2']),
                'd-flex flex-wrap align-items-center'
            ),
            'alert alert-info shadow-sm',
            ['id' => 'local-linkedinshare-banner', 'role' => 'region', 'aria-label' => 'LinkedIn Share Prompt']
        );

        // Push CSS + banner into the hook output.
        $hook->add_html($shim . $html);
    }
}
