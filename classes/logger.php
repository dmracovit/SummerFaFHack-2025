<?php
// File path: classes/logger.php (REPLACE existing logger.php)

namespace local_groqchat;

class logger {
    public static function log_usage($userid, $question, $answer, $ai_level = 1, $subject = 'general', $session_id = null) {
        global $DB;
        
        $DB->insert_record('local_groqchat_logs', [
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
            'ai_level' => $ai_level,
            'subject' => $subject,
            'session_id' => $session_id ?: uniqid(),
            'timecreated' => time(),
        ]);
    }
    
    /**
     * Get user's chat history for a specific session
     */
    public static function get_session_history($userid, $session_id, $limit = 20) {
        global $DB;
        
        return $DB->get_records('local_groqchat_logs', [
            'userid' => $userid,
            'session_id' => $session_id
        ], 'timecreated DESC', '*', 0, $limit);
    }
    
    /**
     * Get user's usage statistics
     */
    public static function get_user_stats($userid, $subject = null) {
        global $DB;
        
        $conditions = ['userid' => $userid];
        if ($subject) {
            $conditions['subject'] = $subject;
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_interactions,
                    AVG(ai_level) as avg_ai_level,
                    MAX(ai_level) as max_ai_level,
                    subject
                FROM {local_groqchat_logs} 
                WHERE userid = :userid";
                
        $params = ['userid' => $userid];
        
        if ($subject) {
            $sql .= " AND subject = :subject";
            $params['subject'] = $subject;
        }
        
        $sql .= " GROUP BY subject";
        
        return $DB->get_records_sql($sql, $params);
    }
}