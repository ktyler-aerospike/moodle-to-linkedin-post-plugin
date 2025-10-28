<?php
// local/linkedinshare/classes/hooks/output.php
namespace local_linkedinshare\hooks;

defined('MOODLE_INTERNAL') || die();

use core\hook\output\before_standard_top_of_body_html_generation;

final class output {
    /**
     * Injects the LinkedIn share banner at the very top of <body>.
     */
    public static function before_top_of_body(before_standard_top_of_body_html_generation $hook): void {
        global $PAGE, $COURSE, $USER;

        // Skip during installation/upgrade.
        if (\during_initial_install()) {
            return;
        }

        // Optional feature toggle (only if you actually have this setting).
        $cfg = \get_config('local_linkedinshare');
        if (isset($cfg->enabled) && empty($cfg->enabled)) {
            return;
        }

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

        // Get the most recent prompt for this user/course, honoring dismissals, optout, and confirmed share.
        $prompt = \local_linkedinshare\local\repository::get_current_user_prompt($courseid);
        if (!$prompt) {
            return;
        }

        // Build internal action URLs (no sesskey by design, per requirements).
        $shareurl   = new \moodle_url('/local/linkedinshare/share.php',   ['id' => $prompt->id]); // sets clickedat and redirects to endpoint
        $confirmurl = new \moodle_url('/local/linkedinshare/confirm.php', ['id' => $prompt->id, 'redirect' => \qualified_me()]);
        $dismissurl = new \moodle_url('/local/linkedinshare/dismiss.php', ['id' => $prompt->id, 'redirect' => \qualified_me()]);
        $neverurl   = new \moodle_url('/local/linkedinshare/never.php',   ['id' => $prompt->id, 'redirect' => \qualified_me()]);

        // Strings.
        $prompttext = \get_string('prompt_text', 'local_linkedinshare');  // "Are you ready to share your certificate on LinkedIn?"
        $sharelabel = \get_string('share', 'local_linkedinshare');        // "Share"
        $notnow     = \get_string('notnow', 'local_linkedinshare');       // "Not now"
        $neverlabel = \get_string('never', 'local_linkedinshare');        // "Never show again"
        $postedq    = \get_string('postedq', 'local_linkedinshare');      // "Did it post successfully?"
        $yesposted  = \get_string('yesposted', 'local_linkedinshare');    // "Yes, it posted"
        $notyet     = \get_string('notyet', 'local_linkedinshare');       // "Not yet"

        // CSS shim (sticky just below fixed header).
        $shim = \html_writer::tag('style', '
#local-linkedinshare-banner { position: sticky; top: 56px; z-index: 1030; margin: 0; }
#local-linkedinshare-followup { margin-top: .5rem; }
body.path-admin #local-linkedinshare-banner { top: 0; }
');

        // Buttons in required order: Never | Not Now | Share.
        $neverbutton  = \html_writer::link($neverurl,  \s($neverlabel), ['class' => 'btn btn-secondary ml-2']);
        $notnowbutton = \html_writer::link($dismissurl, \s($notnow),     ['class' => 'btn btn-secondary ml-2']);
        $sharebutton  = \html_writer::link($shareurl,  \s($sharelabel),  ['class' => 'btn btn-primary  ml-2']);

        // Follow-up row (visible if already clicked and not confirmed).
        $showfollowup = !empty($prompt->clickedat) && empty($prompt->sharedconfirmedat);
        $followupstyle = $showfollowup ? '' : 'display:none;';
        $followup = \html_writer::div(
            \html_writer::span(\s($postedq)) . ' ' .
            \html_writer::link($confirmurl, \s($yesposted), ['class' => 'btn btn-success ml-2']) . ' ' .
            \html_writer::tag('button', \s($notyet), [
                'type' => 'button',
                'class' => 'btn btn-light ml-2',
                'id' => 'local-linkedinshare-notyet'
            ]),
            '',
            ['id' => 'local-linkedinshare-followup', 'style' => $followupstyle]
        );

        // Main banner content.
        $bannercontent = \html_writer::div(
            \html_writer::tag('strong', \s($prompttext)) . ' ' .
            $neverbutton . ' ' . $notnowbutton . ' ' . $sharebutton,
            'd-flex flex-wrap align-items-center'
        );

        $banner = \html_writer::div(
            $bannercontent . $followup,
            'alert alert-info shadow-sm',
            ['id' => 'local-linkedinshare-banner', 'role' => 'region', 'aria-label' => 'LinkedIn Share Prompt']
        );

        // Tiny JS: allow hiding the follow-up if the user clicks "Not yet".
        $js = \html_writer::tag('script', "
(function(){
  var follow = document.getElementById('local-linkedinshare-followup');
  var ny = document.getElementById('local-linkedinshare-notyet');
  if (ny && follow) ny.addEventListener('click', function(){ follow.style.display = 'none'; });
})();
");

        // Output to the hook.
        $hook->add_html($shim . $banner . $js);
    }
}
