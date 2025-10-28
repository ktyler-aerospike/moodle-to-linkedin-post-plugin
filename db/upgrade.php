<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_linkedinshare_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // Add clickedat + sharedconfirmedat if missing (from earlier step).
    if ($oldversion < 2025101700) {
        $table = new xmldb_table('local_linkedinshare');

        $clicked = new xmldb_field('clickedat', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $clicked)) {
            $dbman->add_field($table, $clicked);
        }

        $confirmed = new xmldb_field('sharedconfirmedat', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $confirmed)) {
            $dbman->add_field($table, $confirmed);
        }

        upgrade_plugin_savepoint(true, 2025101700, 'local', 'linkedinshare');
    }

    // Add optout flag (Never show again).
    if ($oldversion < 2025102601) {
        $table = new xmldb_table('local_linkedinshare');

        $optout = new xmldb_field('optout', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        if (!$dbman->field_exists($table, $optout)) {
            $dbman->add_field($table, $optout);
        }

        upgrade_plugin_savepoint(true, 2025101701, 'local', 'linkedinshare');
    }

    return true;
}
