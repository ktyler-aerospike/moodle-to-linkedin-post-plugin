<?php
namespace local_linkedinshare;

defined('MOODLE_INTERNAL') || die();

use core\event\base as core_event;
use local_linkedinshare\local\repository;

class observer {

    /**
     * Catch all events and filter by my conditions.
     * We match the standard log equivalents:
     *   component = 'tool_certificate'
     *   action    = 'issued'
     *   target    = 'certificate'
     *   courseid  <> 0
     *
     * We then extract:
     *   - badgeid   from course.idnumber
     *   - verifcode from $event->other['code']
     */
    public static function catch_all(core_event $event): void {
        // Fast pre-filter: must belong to a course and the minimal fields exist.
        if (empty($event->courseid)) {
            return;
        }

        // Match the same criteria as the SQL.
        if ($event->component !== 'tool_certificate') {
            return;
        }
        if ($event->action !== 'issued' || $event->target !== 'certificate') {
            return;
        }

        global $DB;
        $course = $DB->get_record('course', ['id' => $event->courseid], 'id, idnumber', IGNORE_MISSING);
        if (!$course || empty($course->idnumber)) {
            // If no idnumber, we can’t form the badgeid; skip gracefully.
            return;
        }

        $other = (array)$event->other;
        $verifcode = $other['code'] ?? null;
        if (empty($verifcode)) {
            // The log usually stores JSON other->code; if absent, skip.
            return;
        }

        $userid  = (int)$event->userid;
        $badgeid = (string)$course->idnumber;

        repository::store_prompt($userid, $event->courseid, $badgeid, $verifcode);
    }
}
