<?php
// File path: api.php (REPLACE existing api.php - FIXED VERSION)

require('../../config.php');
require_login();

use local_groqchat\ai_balance_trainer;
use local_groqchat\logger;

// Set content type
header('Content-Type: application/json');

// Get parameters
$question = optional_param('question', '', PARAM_TEXT);
$subject = optional_param('subject', 'general', PARAM_ALPHA);
$action = optional_param('action', 'chat', PARAM_ALPHA);
$session_id = optional_param('session_id', uniqid(), PARAM_ALPHANUMEXT);

// Initialize AI Balance Trainer
$trainer = new ai_balance_trainer($USER->id);

$response = [];

try {
    switch ($action) {
        case 'chat':
            if (empty($question)) {
                throw new Exception('Question parameter is required');
            }
            
            // Get AI assistance based on user's current level
            $answer = $trainer->get_ai_assistance($question, $subject);
            $current_level = $trainer->get_user_level($subject);
            
            // Apply additional length limiting based on level
            $answer = limit_response_by_level($answer, $current_level);
            
            // Log the interaction
            logger::log_usage($USER->id, $question, $answer, $current_level, $subject, $session_id);
            
            $response = [
                'answer' => $answer,
                'ai_level' => $current_level,
                'level_name' => ai_balance_trainer::get_level_name($current_level),
                'level_description' => ai_balance_trainer::get_level_description($current_level),
                'subject' => $subject
            ];
            break;
            
        case 'get_progress':
            $progress = $trainer->get_user_progress($subject);
            $response = [
                'progress' => $progress,
                'level_name' => ai_balance_trainer::get_level_name($progress['ai_level']),
                'level_description' => ai_balance_trainer::get_level_description($progress['ai_level']),
                'subject' => $subject
            ];
            break;
            
        case 'complete_challenge':
            $ai_score = optional_param('ai_score', 0, PARAM_INT);
            $independent_score = optional_param('independent_score', 0, PARAM_INT);
            
            // Validate scores
            if ($ai_score < 0 || $ai_score > 10 || $independent_score < 0 || $independent_score > 10) {
                throw new Exception('Scores must be between 0 and 10');
            }
            
            $new_level = $trainer->update_progress($subject, $ai_score, $independent_score, true);
            $progress = $trainer->get_user_progress($subject);
            
            $response = [
                'success' => true,
                'new_level' => $new_level,
                'progress' => $progress,
                'level_name' => ai_balance_trainer::get_level_name($new_level),
                'level_description' => ai_balance_trainer::get_level_description($new_level),
                'subject' => $subject
            ];
            break;
            
        case 'start_challenge':
            $challenge_type = optional_param('challenge_type', 'coding', PARAM_ALPHA);
            $challenge = generate_challenge($challenge_type, $subject, $trainer->get_user_level($subject));
            
            $response = [
                'challenge' => $challenge,
                'current_level' => $trainer->get_user_level($subject),
                'level_name' => ai_balance_trainer::get_level_name($trainer->get_user_level($subject)),
                'subject' => $subject
            ];
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
    
} catch (Exception $e) {
    $response = [
        'error' => true,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);

/**
 * Limit response length based on AI level
 */
function limit_response_by_level($text, $level) {
    // Character limits based on level (much stricter than token limits)
    $char_limits = [
        1 => 600,  // Tutor - detailed but not overwhelming
        2 => 400,  // Partner - moderate responses
        3 => 300,  // Assistant - brief hints
        4 => 200,  // Validator - short feedback
        5 => 150   // Independent - minimal responses
    ];
    
    $limit = $char_limits[$level] ?? 400;
    
    if (strlen($text) <= $limit) {
        return $text;
    }
    
    // Truncate at the last complete sentence before the limit
    $truncated = substr($text, 0, $limit);
    $last_period = strrpos($truncated, '.');
    $last_question = strrpos($truncated, '?');
    $last_exclamation = strrpos($truncated, '!');
    
    $last_sentence_end = max($last_period, $last_question, $last_exclamation);
    
    if ($last_sentence_end !== false && $last_sentence_end > $limit * 0.7) {
        return substr($text, 0, $last_sentence_end + 1);
    }
    
    // If no good sentence break, truncate and add ellipsis
    return substr($text, 0, $limit - 3) . '...';
}

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
                'description' => 'Implement a binary search algorithm to find an element in a sorted array. Your function should return the index of the element or -1 if not found.',
                'difficulty' => 'intermediate', 
                'estimated_time' => '25 minutes'
            ],
            'advanced' => [
                'title' => 'Design Pattern Implementation',
                'description' => 'Implement an Observer pattern for a notification system. Include at least one publisher and two different types of subscribers.',
                'difficulty' => 'advanced',
                'estimated_time' => '45 minutes'
            ]
        ],
        'writing' => [
            'beginner' => [
                'title' => 'Persuasive Essay',
                'description' => 'Write a 300-word persuasive essay on the importance of renewable energy. Include an introduction, main arguments, and conclusion.',
                'difficulty' => 'beginner',
                'estimated_time' => '20 minutes'
            ],
            'intermediate' => [
                'title' => 'Technical Documentation',
                'description' => 'Write technical documentation for a REST API with at least 5 endpoints. Include endpoint descriptions, parameters, and example responses.',
                'difficulty' => 'intermediate',
                'estimated_time' => '30 minutes'
            ],
            'advanced' => [
                'title' => 'Research Analysis',
                'description' => 'Analyze and critique a research paper in your field, identifying methodological strengths and weaknesses. Provide constructive feedback.',
                'difficulty' => 'advanced',
                'estimated_time' => '45 minutes'
            ]
        ],
        'math' => [
            'beginner' => [
                'title' => 'Linear Equations',
                'description' => 'Solve a system of 3 linear equations with 3 variables using elimination or substitution method. Show all work.',
                'difficulty' => 'beginner',
                'estimated_time' => '15 minutes'
            ],
            'intermediate' => [
                'title' => 'Calculus Problem',
                'description' => 'Find the derivative of f(x) = x³ + 2x² - 5x + 3 and determine critical points. Analyze the function behavior.',
                'difficulty' => 'intermediate',
                'estimated_time' => '25 minutes'
            ],
            'advanced' => [
                'title' => 'Statistics Analysis',
                'description' => 'Perform a statistical analysis on a given dataset. Calculate mean, median, mode, and standard deviation. Identify any outliers.',
                'difficulty' => 'advanced',
                'estimated_time' => '40 minutes'
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
    
    // Map challenge type to subject if needed
    $subject_mapping = [
        'coding' => 'programming',
        'writing' => 'writing',
        'math' => 'math'
    ];
    
    $mapped_subject = $subject_mapping[$type] ?? $subject;
    $subject_challenges = $challenges[$mapped_subject] ?? $challenges['programming'];
    
    $challenge = $subject_challenges[$difficulty] ?? $subject_challenges['beginner'];
    
    // Add some metadata
    $challenge['id'] = uniqid();
    $challenge['subject'] = $mapped_subject;
    $challenge['type'] = $type;
    $challenge['level'] = $level;
    $challenge['created'] = time();
    
    return $challenge;
}