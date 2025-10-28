<?php
namespace local_linkedinshare\local;

defined('MOODLE_INTERNAL') || die();

class repository {

    /**
     * Store or refresh a pending prompt.
     * If one exists for the same user+course+badge+code within 1 day, don’t duplicate.
     */
    public static function store_prompt(int $userid, int $courseid, string $badgeid, string $verifcode): void {
        global $DB;
        $now = time();
        $dayago = $now - 86400;

        // Check for a recent identical prompt.
        $params = [
            'userid'     => $userid,
            'courseid'   => $courseid,
            'badgeid'    => $badgeid,
            'verifcode'  => $verifcode,
            'since'      => $dayago
        ];

        $existing = $DB->get_record_select('local_linkedinshare',
            'userid = :userid AND courseid = :courseid AND badgeid = :badgeid AND verifcode = :verifcode AND timecreated >= :since',
            $params);

        if ($existing) {
            return;
        }

        $rec = (object)[
            'userid'      => $userid,
            'courseid'    => $courseid,
            'badgeid'     => $badgeid,
            'verifcode'   => $verifcode,
            'timecreated' => $now,
            'dismissuntil'=> null,
        ];

        $DB->insert_record('local_linkedinshare', $rec);
    }

    /**
     * Get a pending prompt for the current user, honoring dismissals.
     */
    public static function get_current_user_prompt(?int $courseid = null) {
        global $DB, $USER;
        if (empty($USER->id)) {
            return null;
        }

         // If table isn’t there yet, don’t query.
        $dbman = $DB->get_manager();
        $table = new \xmldb_table('local_linkedinshare');
        if (!$dbman->table_exists($table)) {
            return null;
        }

        $now = time();
        $wheres = ['userid = :userid', '(dismissuntil IS NULL OR dismissuntil < :now)'];
        $params = ['userid' => $USER->id];
        
        // Optional: restrict to the current course to only show on course/activity pages.
        if (!empty($courseid)) {
            $wheres[] = 'courseid = :courseid';
            $params['courseid'] = $courseid;
        }

        // Add optional filters only if columns exist.
        if ($dbman->field_exists($table, new \xmldb_field('sharedconfirmedat'))) {
            $wheres[] = '(sharedconfirmedat IS NULL)';
        }
        if ($dbman->field_exists($table, new \xmldb_field('optout'))) {
            $wheres[] = '(optout = 0 OR optout IS NULL)';
        }

        $params['now'] = $now;

        // Most recent first.
        $sql = 'SELECT * FROM {local_linkedinshare} WHERE ' . implode(' AND ', $wheres) . ' ORDER BY timecreated DESC';

        return $DB->get_record_sql($sql, $params, IGNORE_MISSING);
    }

    /**
     * Dismiss this prompt for 7 days.
     */
    public static function dismiss(int $id): void {
        global $DB, $USER;
        if (!$prompt = $DB->get_record('local_linkedinshare', ['id' => $id, 'userid' => $USER->id])) {
            return;
        }
        $prompt->dismissuntil = time() + 7 * 86400;
        $DB->update_record('local_linkedinshare', $prompt);
    }
}
