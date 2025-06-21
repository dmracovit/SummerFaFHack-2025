<?php
defined('MOODLE_INTERNAL') || die();

function aiassistant_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE: return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_NO_VIEW_LINK: return false;
        default: return null;
    }
}

// function aiassistant_add_instance($data, $mform) {
//     global $DB;
//     $data->timecreated = time();
//     return $DB->insert_record('aiassistant', $data);
// }

    function aiassistant_add_instance($data, $mform) {
        global $DB;

        $record = new stdClass();
        $record->course = $data->course;
        $record->name = $data->name;
        $record->intro = ''; // unused
        $record->introformat = FORMAT_HTML;
        $record->timecreated = time();

        $id = $DB->insert_record('aiassistant', $record);

        // Append topic/material to module instance URL as URL params (not standard!)
        $_SESSION['ai_temp_topic'][$id] = $data->custom_topic;
        $_SESSION['ai_temp_material'][$id] = $data->custom_material['text'];

        return $id;
    }



function aiassistant_update_instance($data, $mform) {
    global $DB;
    $data->id = $data->instance;
    return $DB->update_record('aiassistant', $data);
}

function aiassistant_delete_instance($id) {
    global $DB;
    return $DB->delete_records('aiassistant', ['id' => $id]);
}

