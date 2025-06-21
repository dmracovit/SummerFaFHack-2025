<?php
namespace local_groqchat;

class logger {
    public static function log_usage($userid, $question, $answer) {
        global $DB;
        $DB->insert_record('local_groqchat_logs', [
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
            'timecreated' => time(),
        ]);
    }
}