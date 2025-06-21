<?php
// File path: classes/ai_balance_trainer.php

namespace local_groqchat;

class ai_balance_trainer {
    
    // AI Assistance Levels
    const LEVEL_TUTOR = 1;        // Full guidance and explanations
    const LEVEL_PARTNER = 2;      // Collaborative problem solving
    const LEVEL_ASSISTANT = 3;    // Helpful hints and suggestions
    const LEVEL_VALIDATOR = 4;    
    const LEVEL_INDEPENDENT = 5;  
    
    // Subject types
    const SUBJECT_PROGRAMMING = 'programming';
    const SUBJECT_WRITING = 'writing';
    const SUBJECT_MATH = 'math';
    const SUBJECT_GENERAL = 'general';
    
    private $db;
    private $userid;
    
    public function __construct($userid) {
        global $DB;
        $this->db = $DB;
        $this->userid = $userid;
    }
    
    /**
     * Get user's current AI assistance level for a specific subject
     */
    public function get_user_level($subject) {
        $record = $this->db->get_record('local_groqchat_user_progress', [
            'userid' => $this->userid,
            'subject' => $subject
        ]);
        
        return $record ? $record->ai_level : self::LEVEL_TUTOR;
    }
    
    /**
     * Get user's progress scores
     */
    public function get_user_progress($subject) {
        $record = $this->db->get_record('local_groqchat_user_progress', [
            'userid' => $this->userid,
            'subject' => $subject
        ]);
        
        if (!$record) {
            return [
                'ai_assisted_score' => 0,
                'independent_score' => 0,
                'total_challenges' => 0,
                'ai_level' => self::LEVEL_TUTOR
            ];
        }
        
        return [
            'ai_assisted_score' => $record->ai_assisted_score,
            'independent_score' => $record->independent_score,
            'total_challenges' => $record->total_challenges,
            'ai_level' => $record->ai_level
        ];
    }
    
    /**
     * Process AI assistance request based on current level
     */
    public function get_ai_assistance($question, $subject, $challenge_context = '') {
        $level = $this->get_user_level($subject);
        $prompt = $this->build_level_specific_prompt($question, $level, $subject, $challenge_context);
        
        // Call AI API with level-specific prompt
        return $this->call_ai_api($prompt, $level);
    }
    
    /**
     * Build prompts based on assistance level
     */
    private function build_level_specific_prompt($question, $level, $subject, $context) {
        $base_context = "Subject: {$subject}. Context: {$context}. Student question: {$question}";
        
        switch ($level) {
            case self::LEVEL_TUTOR:
                return "Act as a detailed tutor. Provide step-by-step explanations, examples, and guide the student through the complete solution. " . $base_context;
                
            case self::LEVEL_PARTNER:
                return "Act as a collaborative partner. Work together with the student, ask guiding questions, and provide assistance when they get stuck. Don't give direct answers immediately. " . $base_context;
                
            case self::LEVEL_ASSISTANT:
                return "Act as a helpful assistant. Provide hints, point to relevant concepts, and give suggestions without solving the problem directly. " . $base_context;
                
            case self::LEVEL_VALIDATOR:
                return "Act as a validator. Only provide feedback on the student's approach or solution. Point out errors or confirm correctness, but don't provide new information unless specifically asked. " . $base_context;
                
            case self::LEVEL_INDEPENDENT:
                return "Act minimally. Only respond if the student is completely stuck or asks for clarification on basic concepts. Encourage independent thinking. " . $base_context;
                
            default:
                return $base_context;
        }
    }
    
    /**
     * Call AI API with level-specific constraints
     */
    private function call_ai_api($prompt, $level) {
        $apikey = get_config('local_groqchat', 'apikey') ?: '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';
        
        // Adjust temperature based on level
        $temperature = $this->get_temperature_for_level($level);
        
        $curl = curl_init('https://api.groq.com/openai/v1/chat/completions');
        $postfields = json_encode([
            'model' => 'llama-3.1-8b-instant',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => $temperature,
            'max_tokens' => $this->get_max_tokens_for_level($level)
        ]);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apikey,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postfields
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        
        $json = json_decode($response, true);
        return $json['choices'][0]['message']['content'] ?? 'Error: Could not get AI response';
    }
    
    /**
     * Get temperature setting based on assistance level
     */
    private function get_temperature_for_level($level) {
        switch ($level) {
            case self::LEVEL_TUTOR: return 0.3; // More consistent, educational responses
            case self::LEVEL_PARTNER: return 0.5; // Balanced creativity
            case self::LEVEL_ASSISTANT: return 0.7; // More varied suggestions
            case self::LEVEL_VALIDATOR: return 0.2; // Very consistent feedback
            case self::LEVEL_INDEPENDENT: return 0.1; // Minimal, consistent responses
            default: return 0.7;
        }
    }
    
    /**
     * Get max tokens based on assistance level
     */
    private function get_max_tokens_for_level($level) {
        switch ($level) {
            case self::LEVEL_TUTOR: return 800; // Detailed explanations
            case self::LEVEL_PARTNER: return 400; // Moderate responses
            case self::LEVEL_ASSISTANT: return 300; // Brief hints
            case self::LEVEL_VALIDATOR: return 200; // Short feedback
            case self::LEVEL_INDEPENDENT: return 100; // Minimal responses
            default: return 400;
        }
    }
    
    /**
     * Update user progress after completing a challenge
     */
    public function update_progress($subject, $ai_assisted_score, $independent_score, $challenge_completed = true) {
        $progress = $this->get_user_progress($subject);
        
        $new_ai_score = $progress['ai_assisted_score'] + $ai_assisted_score;
        $new_independent_score = $progress['independent_score'] + $independent_score;
        $new_total = $progress['total_challenges'] + ($challenge_completed ? 1 : 0);
        
        // Calculate if user should level up
        $new_level = $this->calculate_level_progression($new_ai_score, $new_independent_score, $new_total);
        
        $record = [
            'userid' => $this->userid,
            'subject' => $subject,
            'ai_assisted_score' => $new_ai_score,
            'independent_score' => $new_independent_score,
            'total_challenges' => $new_total,
            'ai_level' => $new_level,
            'timemodified' => time()
        ];
        
        $existing = $this->db->get_record('local_groqchat_user_progress', [
            'userid' => $this->userid,
            'subject' => $subject
        ]);
        
        if ($existing) {
            $record['id'] = $existing->id;
            $this->db->update_record('local_groqchat_user_progress', $record);
        } else {
            $record['timecreated'] = time();
            $this->db->insert_record('local_groqchat_user_progress', $record);
        }
        
        return $new_level;
    }
    
    /**
     * Calculate AI level progression based on performance
     */
    private function calculate_level_progression($ai_score, $independent_score, $total_challenges) {
        if ($total_challenges < 3) {
            return self::LEVEL_TUTOR; // Need minimum challenges
        }
        
        $independence_ratio = $total_challenges > 0 ? $independent_score / ($ai_score + $independent_score + 1) : 0;
        
        // Level progression thresholds
        if ($independence_ratio >= 0.8 && $total_challenges >= 10) {
            return self::LEVEL_INDEPENDENT;
        } elseif ($independence_ratio >= 0.6 && $total_challenges >= 8) {
            return self::LEVEL_VALIDATOR;
        } elseif ($independence_ratio >= 0.4 && $total_challenges >= 6) {
            return self::LEVEL_ASSISTANT;
        } elseif ($independence_ratio >= 0.2 && $total_challenges >= 4) {
            return self::LEVEL_PARTNER;
        } else {
            return self::LEVEL_TUTOR;
        }
    }
    
    /**
     * Get level name for display
     */
    public static function get_level_name($level) {
        switch ($level) {
            case self::LEVEL_TUTOR: return 'Tutor Mode';
            case self::LEVEL_PARTNER: return 'Partner Mode';
            case self::LEVEL_ASSISTANT: return 'Assistant Mode';
            case self::LEVEL_VALIDATOR: return 'Validator Mode';
            case self::LEVEL_INDEPENDENT: return 'Independent Mode';
            default: return 'Unknown Level';
        }
    }
    
    /**
     * Get level description
     */
    public static function get_level_description($level) {
        switch ($level) {
            case self::LEVEL_TUTOR: 
                return 'AI provides detailed step-by-step guidance and explanations';
            case self::LEVEL_PARTNER: 
                return 'AI collaborates with you, asking guiding questions';
            case self::LEVEL_ASSISTANT: 
                return 'AI provides helpful hints and suggestions';
            case self::LEVEL_VALIDATOR: 
                return 'AI only provides feedback and validation';
            case self::LEVEL_INDEPENDENT: 
                return 'Minimal AI assistance - you work independently';
            default: 
                return '';
        }
    }
}