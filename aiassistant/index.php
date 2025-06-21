<?php
require('../../config.php');
require_login();

$courseid = required_param('id', PARAM_INT);
$course = get_course($courseid);

$PAGE->set_url('/mod/aiassistant/index.php', ['id' => $courseid]);
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('modulenameplural', 'mod_aiassistant'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_aiassistant'));

 $url = new moodle_url('/local/groqchat');

$button = html_writer::link(
    $url,
    get_string('launchbutton', 'block_aiassistant'),
    [
        'class' => 'btn btn-primary',
        'style' => 'display: inline-block; padding: 10px 20px; background-color: #0073e6; color: #fff; border-radius: 5px; text-decoration: none;'
    ]
);

echo html_writer::div($button, 'text-center');

// echo html_writer::tag('p', 'This is a list of AI Assistant instances (feature not implemented).');

echo $OUTPUT->footer();
