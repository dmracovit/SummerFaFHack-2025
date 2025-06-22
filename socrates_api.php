<?php
// File: socrates_api.php
// Socratic mode API endpoint for AI Balance Trainer (chat only)

require('../../config.php');
require_login();

use local_groqchat\ai_balance_trainer;

header('Content-Type: application/json');

$question = optional_param('question', '', PARAM_RAW);
$subject = optional_param('subject', 'general', PARAM_TEXT);

$user = $USER;
$userid = $user->id;

// Validate question input
if (empty($question)) {
    echo json_encode(["error" => true, "message" => "No question provided."]);
    exit;
}


// Build Socratic prompt
$trainer = new ai_balance_trainer($userid);
$ai_level = ai_balance_trainer::LEVEL_PARTNER;
$prompt = "You are a Socratic AI tutor. Instead of giving direct answers, respond to the student's question by asking guiding questions, prompting critical thinking, and encouraging the student to reflect and reason. Do not provide the solution directly.\n\nStudent question: {$question}";

// Get AI-generated guidance
$answer = $trainer->get_ai_assistance($prompt, $subject, 'socratic');

// Return response
$response = [
    "error" => false,
    "answer" => $answer,
    "ai_level" => $ai_level,
    "level_name" => "Socratic Mode",
    "level_description" => "AI guides you with questions and prompts to help you think independently."
];

echo json_encode($response);
