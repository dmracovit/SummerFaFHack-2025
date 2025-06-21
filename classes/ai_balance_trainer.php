<?php
// File path: classes/ai_balance_trainer.php (REPLACE existing - FIXED VERSION)

namespace local_groqchat;

class ai_balance_trainer {
    
    // AI Assistance Levels
    const LEVEL_TUTOR = 1;        // Full guidance and explanations
    const LEVEL_PARTNER = 2;      // Collaborative problem solving
    const LEVEL_ASSISTANT = 3;    // Helpful hints and suggestions
    const LEVEL_VALIDATOR = 4;    // Feedback and validation only
    const LEVEL_INDEPENDENT = 5;  // Minimal to no assistance
    
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
        $base_context = !empty($context) ? "Context: {$context}. " : "";
        $base_context .= "Subject: {$subject}. Student question: {$question}";
        
        switch ($level) {
            case self::LEVEL_TUTOR:
                return "You are a helpful tutor. Provide clear, step-by-step guidance in 2-3 short paragraphs maximum. Use simple language and include a brief example if helpful. Keep responses concise but educational. " . $base_context;
                
            case self::LEVEL_PARTNER:
                return "You are a study partner. Ask 1-2 guiding questions to help the student think through the problem. Give hints but don't solve it directly. Keep response to 1-2 paragraphs maximum. " . $base_context;
                
            case self::LEVEL_ASSISTANT:
                return "You are a helpful assistant. Provide brief hints or point to relevant concepts in 1 paragraph maximum. Don't give complete solutions. Be concise. " . $base_context;
                
            case self::LEVEL_VALIDATOR:
                return "You are a validator. Only provide brief feedback on the student's approach. Point out errors or confirm correctness in 2-3 sentences maximum. Don't provide new information unless asked. " . $base_context;
                
            case self::LEVEL_INDEPENDENT:
                return "You provide minimal help. Only respond if the student is completely stuck with basic concepts. Keep to 1-2 sentences maximum. Encourage independent thinking. " . $base_context;
                
            default:
                return $base_context;
        }
    }
    
    /**
     * Call AI API with level-specific constraints
     */
    private function call_ai_api($prompt, $level) {
        $apikey = get_config('local_groqchat', 'apikey') ?: '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';
        
        // Adjust parameters based on level
        $temperature = $this->get_temperature_for_level($level);
        $max_tokens = $this->get_max_tokens_for_level($level);
        
        $curl = curl_init('https://api.groq.com/openai/v1/chat/completions');
        $postfields = json_encode([
            'model' => 'llama-3.1-8b-instant',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => $temperature,
            'max_tokens' => $max_tokens,
            'stop' => ['\n\n\n', '---'] // Additional stop sequences to limit length
        ]);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apikey,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($http_code !== 200) {
            return 'Error: Could not get AI response (HTTP ' . $http_code . ')';
        }
        
        $json = json_decode($response, true);
        
        if (!isset($json['choices'][0]['message']['content'])) {
            return 'Error: Invalid API response format';
        }
        
        $content = $json['choices'][0]['message']['content'];
        
        // Additional post-processing to ensure conciseness
        return $this->post_process_response($content, $level);
    }
    
    /**
     * Post-process response to ensure it meets level requirements
     */
    private function post_process_response($content, $level) {
        // Remove excessive whitespace and normalize line breaks
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        $content = trim($content);
        
        // Character limits for additional safety
        $char_limits = [
            self::LEVEL_TUTOR => 500,
            self::LEVEL_PARTNER => 350, 
            self::LEVEL_ASSISTANT => 250,
            self::LEVEL_VALIDATOR => 150,
            self::LEVEL_INDEPENDENT => 100
        ];
        
        $limit = $char_limits[$level] ?? 350;
        
        if (strlen($content) > $limit) {
            // Find the best place to cut off
            $content = substr($content, 0, $limit);
            $last_sentence = max(
                strrpos($content, '.'),
                strrpos($content, '!'),
                strrpos($content, '?')
            );
            
            if ($last_sentence !== false && $last_sentence > $limit * 0.6) {
                $content = substr($content, 0, $last_sentence + 1);
            } else {
                $content = substr($content, 0, $limit - 3) . '...';
            }
        }
        
        return $content;
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
            default: return 0.5;
        }
    }
    
    /**
     * Get max tokens based on assistance level
     */
    private function get_max_tokens_for_level($level) {
        switch ($level) {
            case self::LEVEL_TUTOR: return 150; // Detailed but limited
            case self::LEVEL_PARTNER: return 100; // Moderate responses
            case self::LEVEL_ASSISTANT: return 75; // Brief hints
            case self::LEVEL_VALIDATOR: return 50; // Short feedback
            case self::LEVEL_INDEPENDENT: return 30; // Minimal responses
            default: return 100;
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
        $new_level = $this->calculate_level_progression($new_ai_score, $new_independent_score, $new_total, $progress['ai_level']);
        
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
    private function calculate_level_progression($ai_score, $independent_score, $total_challenges, $current_level) {
        if ($total_challenges < 2) {
            return self::LEVEL_TUTOR; // Need minimum challenges
        }
        
        $total_score = $ai_score + $independent_score;
        $independence_ratio = $total_score > 0 ? $independent_score / $total_score : 0;
        
        // Level progression thresholds (configurable via settings)
        $thresholds = [
            self::LEVEL_PARTNER => ['ratio' => 0.2, 'challenges' => 3],
            self::LEVEL_ASSISTANT => ['ratio' => 0.4, 'challenges' => 5],
            self::LEVEL_VALIDATOR => ['ratio' => 0.6, 'challenges' => 7],
            self::LEVEL_INDEPENDENT => ['ratio' => 0.8, 'challenges' => 10]
        ];
        
        // Start from highest level and work down
        foreach (array_reverse($thresholds, true) as $level => $requirements) {
            if ($independence_ratio >= $requirements['ratio'] && $total_challenges >= $requirements['challenges']) {
                return max($level, $current_level); // Don't allow level regression
            }
        }
        
        return max(self::LEVEL_TUTOR, $current_level); // Don't allow level regression
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
                return 'AI provides detailed step-by-step guidance';
            case self::LEVEL_PARTNER: 
                return 'AI collaborates with guiding questions';
            case self::LEVEL_ASSISTANT: 
                return 'AI provides helpful hints and suggestions';
            case self::LEVEL_VALIDATOR: 
                return 'AI only provides feedback and validation';
            case self::LEVEL_INDEPENDENT: 
                return 'Minimal AI assistance - work independently';
            default: 
                return '';
        }
    }
}