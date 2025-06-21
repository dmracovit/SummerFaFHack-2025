<?php
require('../../config.php');

$id = required_param('id', PARAM_INT); // course module ID
$cm = get_coursemodule_from_id('aiassistant', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);

$PAGE->set_url('/mod/aiassistant/view.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('modulename', 'mod_aiassistant'));
$PAGE->set_heading($course->fullname);


// $topic = "Understanding Neural Networks";
// $material = "Neural networks are a class of models within the general machine learning literature. They are inspired by the structure of the human brain and are particularly powerful for recognizing patterns, learning from data, and enabling AI systems to make decisions.";

// Get topic/material from session (passed from lib.php)
$topic = $_SESSION['ai_temp_topic'][$cm->instance] ?? 'No topic provided';
$material = $_SESSION['ai_temp_material'][$cm->instance] ?? 'No content available';


$url = new moodle_url('/local/groqchat/');

echo $OUTPUT->header();

echo html_writer::start_div('aiassistant-container', ['style' => 'max-width: 800px; margin: 0 auto; padding: 30px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);']);

echo html_writer::tag('h2', $topic, ['style' => 'font-size: 28px; color: #2c3e50; margin-bottom: 20px;']);

echo html_writer::tag('div', $material, ['style' => 'font-size: 18px; line-height: 1.6; color: #333; margin-bottom: 30px;']);

// echo $OUTPUT->single_button($url, get_string('launchbutton', 'aiassistant'), 'get', [
//     'class' => 'btn btn-primary',
//     'style' => 'padding: 10px 20px; font-size: 16px;'
// ]);

echo html_writer::link(
    $url,
    get_string('launchbutton', 'aiassistant'),
    [
        'class' => 'btn ai-button',
        'style' => '
            display: inline-block;
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            transition: all 0.3s ease;
        ',
        'onmouseover' => "this.style.opacity='0.9';",
        'onmouseout' => "this.style.opacity='1';"
    ]
);

echo html_writer::end_div();

echo $OUTPUT->footer();