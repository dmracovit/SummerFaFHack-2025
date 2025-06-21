<?php
// File path: lang/en/local_groqchat.php (REPLACE existing file)

$string['pluginname'] = 'AI Balance Trainer';
$string['chatheading'] = 'AI Balance Training - Layer 1';

// AI Level strings
$string['level_tutor'] = 'Tutor Mode';
$string['level_partner'] = 'Partner Mode';
$string['level_assistant'] = 'Assistant Mode';
$string['level_validator'] = 'Validator Mode';
$string['level_independent'] = 'Independent Mode';

$string['level_tutor_desc'] = 'AI provides detailed step-by-step guidance and explanations';
$string['level_partner_desc'] = 'AI collaborates with you, asking guiding questions';
$string['level_assistant_desc'] = 'AI provides helpful hints and suggestions';
$string['level_validator_desc'] = 'AI only provides feedback and validation';
$string['level_independent_desc'] = 'Minimal AI assistance - you work independently';

// Subject strings
$string['subject_programming'] = 'Programming';
$string['subject_writing'] = 'Writing';
$string['subject_math'] = 'Mathematics';
$string['subject_general'] = 'General';

// Progress strings
$string['ai_assisted_score'] = 'AI-Assisted Score';
$string['independent_score'] = 'Independent Score';
$string['total_challenges'] = 'Total Challenges';
$string['independence_percentage'] = 'Independence Percentage';

// Challenge strings
$string['start_challenge'] = 'Start Challenge';
$string['complete_challenge'] = 'Complete Challenge';
$string['challenge_completed'] = 'Challenge Completed';
$string['challenge_title'] = 'Challenge';
$string['challenge_description'] = 'Description';
$string['difficulty'] = 'Difficulty';
$string['estimated_time'] = 'Estimated Time';

// Interface strings
$string['clear_chat'] = 'Clear Chat';
$string['send_message'] = 'Send Message';
$string['type_message'] = 'Type your message here...';
$string['current_level'] = 'Current Level';
$string['level_progression'] = 'Level Progression';
$string['quick_actions'] = 'Quick Actions';
$string['view_progress'] = 'View Progress';

// Privacy and capabilities
$string['privacy:metadata:local_groqchat_logs'] = 'Information about user interactions with the AI assistant';
$string['privacy:metadata:local_groqchat_logs:userid'] = 'The ID of the user';
$string['privacy:metadata:local_groqchat_logs:question'] = 'The question asked by the user';
$string['privacy:metadata:local_groqchat_logs:answer'] = 'The AI response provided';
$string['privacy:metadata:local_groqchat_logs:ai_level'] = 'The AI assistance level used';
$string['privacy:metadata:local_groqchat_logs:subject'] = 'The subject area of the interaction';
$string['privacy:metadata:local_groqchat_logs:timecreated'] = 'The time when the interaction occurred';

$string['privacy:metadata:local_groqchat_user_progress'] = 'Information about user progress in AI balance training';
$string['privacy:metadata:local_groqchat_user_progress:userid'] = 'The ID of the user';
$string['privacy:metadata:local_groqchat_user_progress:subject'] = 'The subject area';
$string['privacy:metadata:local_groqchat_user_progress:ai_level'] = 'Current AI assistance level';
$string['privacy:metadata:local_groqchat_user_progress:ai_assisted_score'] = 'Score achieved with AI assistance';
$string['privacy:metadata:local_groqchat_user_progress:independent_score'] = 'Score achieved independently';

// Error messages
$string['error_api_connection'] = 'Could not connect to AI service';
$string['error_invalid_subject'] = 'Invalid subject selected';
$string['error_challenge_not_found'] = 'Challenge not found';
$string['error_permission_denied'] = 'Permission denied';

// Success messages
$string['success_level_up'] = 'Congratulations! You\'ve advanced to {$a}';
$string['success_challenge_complete'] = 'Challenge completed successfully';
$string['success_progress_updated'] = 'Progress updated';

// Settings strings
$string['apiheading'] = 'API Configuration';
$string['apiheading_desc'] = 'Configure the AI service connection settings';
$string['apikey'] = 'API Key';
$string['apikey_desc'] = 'Enter your Groq API key for AI service access';
$string['apimodel'] = 'AI Model';
$string['apimodel_desc'] = 'Specify the AI model to use (e.g., llama-3.1-8b-instant)';

$string['trainingheading'] = 'AI Balance Training Configuration';
$string['trainingheading_desc'] = 'Configure the AI balance training system parameters';
$string['enable_level_progression'] = 'Enable Level Progression';
$string['enable_level_progression_desc'] = 'Allow users to progress through AI assistance levels based on performance';
$string['min_challenges_for_progression'] = 'Minimum Challenges for Progression';
$string['min_challenges_for_progression_desc'] = 'Minimum number of challenges required before level progression';
$string['independence_threshold_level2'] = 'Level 2 Independence Threshold';
$string['independence_threshold_level2_desc'] = 'Independence ratio required to advance to Partner Mode (0.0-1.0)';
$string['independence_threshold_level3'] = 'Level 3 Independence Threshold';
$string['independence_threshold_level3_desc'] = 'Independence ratio required to advance to Assistant Mode (0.0-1.0)';
$string['independence_threshold_level4'] = 'Level 4 Independence Threshold';
$string['independence_threshold_level4_desc'] = 'Independence ratio required to advance to Validator Mode (0.0-1.0)';
$string['independence_threshold_level5'] = 'Level 5 Independence Threshold';
$string['independence_threshold_level5_desc'] = 'Independence ratio required to advance to Independent Mode (0.0-1.0)';

$string['loggingheading'] = 'Logging Configuration';
$string['loggingheading_desc'] = 'Configure logging and data retention settings';
$string['enable_detailed_logging'] = 'Enable Detailed Logging';
$string['enable_detailed_logging_desc'] = 'Log detailed interaction data for analytics and progress tracking';
$string['log_retention_days'] = 'Log Retention (Days)';
$string['log_retention_days_desc'] = 'Number of days to retain log data before automatic cleanup';