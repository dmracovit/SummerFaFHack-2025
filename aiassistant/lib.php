<?php
defined('MOODLE_INTERNAL') || die();

function aiassistant_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE: return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_NO_VIEW_LINK: return false;
        default: return null;
    }
}

function aiassistant_add_instance($data, $mform) {
    global $DB;

    // Ensure session is started (usually handled by Moodle, but good to check)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $record = new stdClass();
    $record->course = $data->course;
    $record->name = $data->name;
    $record->topic = $data->custom_topic;
    $record->material = $data->custom_material['text'];
    $record->timecreated = time();

    $id = $DB->insert_record('aiassistant', $record);

    // Store in session with namespaced keys to avoid conflicts
    $_SESSION['aiassistant_temp']['topic'][$id] = $data->custom_topic;
    $_SESSION['aiassistant_temp']['material'][$id] = $data->custom_material['text'];

    return $id;
}

function aiassistant_update_instance($data, $mform) {
    global $DB;

    $data->id = $data->instance;
    $record = new stdClass();
    $record->id = $data->id;
    $record->course = $data->course;
    $record->name = $data->name;
    $record->topic = $data->custom_topic;
    $record->material = $data->custom_material['text'];
    $record->timecreated = time();

    $result = $DB->update_record('aiassistant', $record);

    // Optionally update session data if needed
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['aiassistant_temp']['topic'][$data->id])) {
        $_SESSION['aiassistant_temp']['topic'][$data->id] = $data->custom_topic ?? '';
        $_SESSION['aiassistant_temp']['material'][$data->id] = $data->custom_material['text'] ?? '';
    }

    return $result;
}

function aiassistant_delete_instance($id) {
    global $DB;

    // Clean up session data when deleting instance
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['aiassistant_temp']['topic'][$id])) {
        unset($_SESSION['aiassistant_temp']['topic'][$id]);
        unset($_SESSION['aiassistant_temp']['material'][$id]);
    }

    return $DB->delete_records('aiassistant', ['id' => $id]);
}

/**
 * Example function to retrieve session data (e.g., for rendering or processing)
 * @param int $id AI Assistant instance ID
 * @return array|null
 */
function aiassistant_get_session_data($id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['aiassistant_temp']['topic'][$id])) {
        return [
            'topic' => $_SESSION['aiassistant_temp']['topic'][$id],
            'material' => $_SESSION['aiassistant_temp']['material'][$id]
        ];
    }

    return null;
}

/**
 * Clean up session data after use (call when data is no longer needed)
 * @param int $id AI Assistant instance ID
 */
function aiassistant_cleanup_session($id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['aiassistant_temp']['topic'][$id])) {
        unset($_SESSION['aiassistant_temp']['topic'][$id]);
        unset($_SESSION['aiassistant_temp']['material'][$id]);
    }
}