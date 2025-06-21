<?php

require('../../config.php');
require_login();

use local_groqchat\ai_balance_trainer;
use local_groqchat\logger;

// Get parameters
$question = required_param('question', PARAM_TEXT);
$subject = optional_param('subject', 'general', PARAM_ALPHA);
$action = optional_param('action', 'chat', PARAM_ALPHA);
$session_id = optional_param('session_id', uniqid(), PARAM_ALPHANUMEXT);

// Initialize AI Balance Trainer
$trainer = new ai_balance_trainer($USER->id);

$response = [];

try {
    switch ($action) {
        case 'chat':
            // Get AI assistance based on user's current level
            $answer = $trainer->get_ai_assistance($question, $subject);
            $current_level = $trainer->get_user_level($subject);
            
            // Log the interaction
            logger::log_usage($USER->id, $question, $answer, $current_level, $subject, $session_id);
            
            $response = [
                'answer' => $answer,
                'ai_level' => $current_level,
                'level_name' => ai_balance_trainer::get_level_name($current_level),
                'level_description' => ai_balance_trainer::get_level_description($current_level)
            ];
            break;
            
        case 'get_progress':
            $progress = $trainer->get_user_progress($subject);
            $response = [
                'progress' => $progress,
                'level_name' => ai_balance_trainer::get_level_name($progress['ai_level']),
                'level_description' => ai_balance_trainer::get_level_description($progress['ai_level'])
            ];
            break;
            
        case 'complete_challenge':
            $ai_score = optional_param('ai_score', 0, PARAM_INT);
            $independent_score = optional_param('independent_score', 0, PARAM_INT);
            
            $new_level = $trainer->update_progress($subject, $ai_score, $independent_score, true);
            $progress = $trainer->get_user_progress($subject);
            
            $response = [
                'success' => true,
                'new_level' => $new_level,
                'progress' => $progress,
                'level_name' => ai_balance_trainer::get_level_name($new_level),
                'level_description' => ai_balance_trainer::get_level_description($new_level)
            ];
            break;
            
        case 'start_challenge':
            $challenge_type = optional_param('challenge_type', 'coding', PARAM_ALPHA);
            $challenge = generate_challenge($challenge_type, $subject, $trainer->get_user_level($subject));
            
            $response = [
                'challenge' => $challenge,
                'current_level' => $trainer->get_user_level($subject),
                'level_name' => ai_balance_trainer::get_level_name($trainer->get_user_level($subject))
            ];
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response = [
        'error' => true,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

/**
 * Generate challenges based on subject and level
 */
function generate_challenge($type, $subject, $level) {
    $challenges = [
        'programming' => [
            'beginner' => [
                'title' => 'FizzBuzz Challenge',
                'description' => 'Write a program that prints numbers 1-100, but prints "Fizz" for multiples of 3, "Buzz" for multiples of 5, and "FizzBuzz" for multiples of both.',
                'difficulty' => 'beginner',
                'estimated_time' => '15 minutes'
            ],
            'intermediate' => [
                'title' => 'Binary Search Implementation',
                'description' => 'Implement a binary search algorithm to find an element in a sorted array.',
                'difficulty' => 'intermediate', 
                'estimated_time' => '25 minutes'
            ],
            'advanced' => [
                'title' => 'Design Pattern Implementation',
                'description' => 'Implement a Observer pattern for a notification system.',
                'difficulty' => 'advanced',
                'estimated_time' => '45 minutes'
            ]
        ],
        'writing' => [
            'beginner' => [
                'title' => 'Persuasive Essay',
                'description' => 'Write a 300-word persuasive essay on the importance of renewable energy.',
                'difficulty' => 'beginner',
                'estimated_time' => '20 minutes'
            ],
            'intermediate' => [
                'title' => 'Technical Documentation',
                'description' => 'Write technical documentation for a REST API with at least 5 endpoints.',
                'difficulty' => 'intermediate',
                'estimated_time' => '30 minutes'
            ],
            'advanced' => [
                'title' => 'Research Analysis',
                'description' => 'Analyze and critique a research paper, identifying methodological strengths and weaknesses.',
                'difficulty' => 'advanced',
                'estimated_time' => '45 minutes'
            ]
        ]
    ];
    
    // Select difficulty based on AI assistance level
    $difficulty_map = [
        1 => 'beginner',   // Tutor
        2 => 'beginner',   // Partner  
        3 => 'intermediate', // Assistant
        4 => 'intermediate', // Validator
        5 => 'advanced'    // Independent
    ];
    
    $difficulty = $difficulty_map[$level] ?? 'beginner';
    $subject_challenges = $challenges[$subject] ?? $challenges['programming'];
    
    return $subject_challenges[$difficulty] ?? $subject_challenges['beginner'];
}