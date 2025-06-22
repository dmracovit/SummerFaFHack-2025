<?php
// File: socrates_api.php
// Socratic mode API endpoint for AI Balance Trainer (chat only)

require('../../config.php');
require_login();
require_once($CFG->libdir . '/moodlelib.php');

use local_groqchat\ai_balance_trainer;

header('Content-Type: application/json');

// Use Moodle's PARAM_RAW constant and optional_param function
if (!defined('PARAM_RAW')) {
    define('PARAM_RAW', 0);
}

$user_reply = isset($_REQUEST['user_reply']) ? $_REQUEST['user_reply'] : '';
$conversation_json = isset($_REQUEST['conversation']) ? $_REQUEST['conversation'] : '';
$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : 'general';

$user = $USER;
$userid = $user->id;

if (empty($conversation_json)) {
    // First turn: Socrates starts the dialogue
    $initial_prompt = "You are a Socratic AI tutor. Start a dialogue by asking the student a thought-provoking question about the topic. Do not provide answers, only ask a question that encourages critical thinking and reflection. Topic: {$subject}.";
    $trainer = new ai_balance_trainer($userid);
    $ai_level = ai_balance_trainer::LEVEL_PARTNER;
    $answer = $trainer->get_ai_assistance($initial_prompt, $subject, 'socratic');
    $response = [
        "error" => false,
        "answer" => $answer,
        "ai_level" => $ai_level,
        "level_name" => "Socratic Mode",
        "level_description" => "AI guides you with questions and prompts to help you think independently."
    ];
    echo json_encode($response);
    exit;
}

// If conversation exists, continue the Socratic dialogue
$conversation = json_decode($conversation_json, true);
if (!is_array($conversation)) $conversation = [];

$prompt = "You are a Socratic AI tutor. Continue the dialogue below by asking the student a new, thought-provoking question based on their last reply. Do not provide answers, only ask a question that encourages critical thinking and reflection.\n\n";
$prompt .= "Dialogue so far:\n";
foreach ($conversation as $turn) {
    $prompt .= $turn . "\n";
}
$prompt .= "\nStudent's last reply: {$user_reply}\n\nYour next question:";

$trainer = new ai_balance_trainer($userid);
$ai_level = ai_balance_trainer::LEVEL_PARTNER;
$answer = $trainer->get_ai_assistance($prompt, $subject, 'socratic');

$response = [
    "error" => false,
    "answer" => $answer,
    "ai_level" => $ai_level,
    "level_name" => "Socratic Mode",
    "level_description" => "AI guides you with questions and prompts to help you think independently."
];

echo json_encode($response);
