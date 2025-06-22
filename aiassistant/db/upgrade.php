<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_aiassistant_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025062203) {
        $table = new xmldb_table('aiassistant');

        // Add topic field if it does not exist
        if (!$dbman->field_exists($table, 'topic')) {
            $field = new xmldb_field('topic', XMLDB_TYPE_TEXT, null, XMLDB_NOTNULL, null, '', '');
            $dbman->add_field($table, $field);
        }
        // Add material field if it does not exist
        if (!$dbman->field_exists($table, 'material')) {
            $field = new xmldb_field('material', XMLDB_TYPE_TEXT, null, null, null, '', '');
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2025062203, 'aiassistant');
    }

    return true;
}
