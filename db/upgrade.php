<?php

function xmldb_local_groqchat_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // 2025062101 - Create missing tables if they do not exist
    if ($oldversion < 2025062101) {
        // Table: local_groqchat_logs
        if (!$dbman->table_exists('local_groqchat_logs')) {
            $table = new xmldb_table('local_groqchat_logs');
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('question', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $table->add_field('answer', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $table->add_field('ai_level', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
            $table->add_field('subject', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'general');
            $table->add_field('session_id', XMLDB_TYPE_CHAR, '100', null, null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
            $table->add_index('userid_subject', XMLDB_INDEX_NOTUNIQUE, ['userid', 'subject']);
            $table->add_index('session_id', XMLDB_INDEX_NOTUNIQUE, ['session_id']);
            $dbman->create_table($table);
        }

        // Table: local_groqchat_user_progress
        if (!$dbman->table_exists('local_groqchat_user_progress')) {
            $table = new xmldb_table('local_groqchat_user_progress');
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('subject', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
            $table->add_field('ai_level', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
            $table->add_field('ai_assisted_score', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('independent_score', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('total_challenges', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
            $table->add_index('userid_subject', XMLDB_INDEX_UNIQUE, ['userid', 'subject']);
            $dbman->create_table($table);
        }

        // Table: local_groqchat_challenges
        if (!$dbman->table_exists('local_groqchat_challenges')) {
            $table = new xmldb_table('local_groqchat_challenges');
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('subject', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
            $table->add_field('challenge_type', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
            $table->add_field('challenge_data', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('ai_assistance_used', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('ai_score', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('independent_score', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
            $table->add_index('userid_subject', XMLDB_INDEX_NOTUNIQUE, ['userid', 'subject']);
            $table->add_index('completed', XMLDB_INDEX_NOTUNIQUE, ['completed']);
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025062101, 'local', 'groqchat');
    }

    return true;
}