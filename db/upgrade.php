<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Plugin upgrade script for local_linkedinshare.
 *
 * This upgrade step DROPS and RE-CREATES the table local_linkedinshare.
 * ⚠️ ALL EXISTING DATA IN THIS TABLE WILL BE LOST.
 */
function xmldb_local_linkedinshare_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Adjust this version gate to your next version number.
    // Example: bump $plugin->version to 2025101800 in version.php
    if ($oldversion < 2025102802) {
        $table = new xmldb_table('local_linkedinshare');

        // If the table already exists, drop it (data loss by design).
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Recreate table with the desired schema.
        $table = new xmldb_table('local_linkedinshare');

        // Fields
        $table->addFieldInfo('id',               XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->addFieldInfo('userid',           XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('courseid',         XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('badgeid',          XMLDB_TYPE_CHAR,    '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('verifcode',        XMLDB_TYPE_CHAR,    '255', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('timecreated',      XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->addFieldInfo('dismissuntil',     XMLDB_TYPE_INTEGER, '10', null, null,         null, null);
        $table->addFieldInfo('clickedat',        XMLDB_TYPE_INTEGER, '10', null, null,         null, null);
        $table->addFieldInfo('sharedconfirmedat',XMLDB_TYPE_INTEGER, '10', null, null,         null, null);
        $table->addFieldInfo('optout',           XMLDB_TYPE_INTEGER, '1',  null, XMLDB_NOTNULL, null, '0');

        // Keys
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Indexes
        $table->addIndexInfo('useridx',   XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->addIndexInfo('courseidx', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Create the table.
        $dbman->create_table($table);

        // Mark savepoint.
        upgrade_plugin_savepoint(true, 2025102802, 'local', 'linkedinshare');
    }

    return true;
}
