<?php
// File path: debug.php (NEW FILE - for testing only)

require('../../config.php');
require_login();

use local_groqchat\ai_balance_trainer;

$PAGE->set_url('/local/groqchat/debug.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('AI Balance Trainer - Debug');

echo $OUTPUT->header();

// Test API connectivity
echo "<h2>🔧 AI Balance Trainer Debug Page</h2>";

// Test 1: Check if classes exist
echo "<h3>Class Loading Test:</h3>";
try {
    $trainer = new ai_balance_trainer($USER->id);
    echo "✅ ai_balance_trainer class loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Error loading ai_balance_trainer: " . $e->getMessage() . "<br>";
}

// Test 2: Check database tables
echo "<h3>Database Test:</h3>";
global $DB;
try {
    $tables_to_check = [
        'local_groqchat_logs',
        'local_groqchat_user_progress', 
        'local_groqchat_challenges'
    ];
    
    foreach ($tables_to_check as $table) {
        if ($DB->get_manager()->table_exists($table)) {
            $count = $DB->count_records($table);
            echo "✅ Table {$table} exists (records: {$count})<br>";
        } else {
            echo "❌ Table {$table} does not exist<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Test API endpoints
echo "<h3>API Test:</h3>";
echo "<div id='api-test-results'>Running tests...</div>";

// Test 4: Progress display
echo "<h3>Current Progress:</h3>";
try {
    $programming_progress = $trainer->get_user_progress('programming');
    $writing_progress = $trainer->get_user_progress('writing');
    
    echo "<strong>Programming:</strong> Level " . $programming_progress['ai_level'] . 
         " (" . ai_balance_trainer::get_level_name($programming_progress['ai_level']) . ")<br>";
    echo "<strong>Writing:</strong> Level " . $writing_progress['ai_level'] . 
         " (" . ai_balance_trainer::get_level_name($writing_progress['ai_level']) . ")<br>";
} catch (Exception $e) {
    echo "❌ Progress error: " . $e->getMessage() . "<br>";
}

// Test 5: Quick action buttons
echo "<h3>Quick Tests:</h3>";
echo "<button onclick='testChat()' style='margin: 5px; padding: 10px; background: #007cba; color: white; border: none; border-radius: 5px;'>Test Chat API</button>";
echo "<button onclick='testChallenge()' style='margin: 5px; padding: 10px; background: #28a745; color: white; border: none; border-radius: 5px;'>Test Challenge API</button>";
echo "<button onclick='testProgress()' style='margin: 5px; padding: 10px; background: #6f42c1; color: white; border: none; border-radius: 5px;'>Test Progress API</button>";

echo "<div id='test-output' style='margin-top: 20px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;'></div>";

?>

<script>
async function testChat() {
    const output = document.getElementById('test-output');
    output.innerHTML = 'Testing chat API...';
    
    try {
        const response = await fetch('api.php?action=chat&question=What is a variable in programming?&subject=programming');
        const data = await response.json();
        
        if (data.error) {
            output.innerHTML = '<strong style="color: red;">❌ Chat API Error:</strong> ' + data.message;
        } else {
            output.innerHTML = '<strong style="color: green;">✅ Chat API Success:</strong><br>' +
                              '<strong>Level:</strong> ' + data.level_name + '<br>' +
                              '<strong>Response:</strong> ' + data.answer.substring(0, 200) + '...';
        }
    } catch (error) {
        output.innerHTML = '<strong style="color: red;">❌ Chat API Error:</strong> ' + error.message;
    }
}

async function testChallenge() {
    const output = document.getElementById('test-output');
    output.innerHTML = 'Testing challenge API...';
    
    try {
        const response = await fetch('api.php?action=start_challenge&challenge_type=coding&subject=programming');
        const data = await response.json();
        
        if (data.error) {
            output.innerHTML = '<strong style="color: red;">❌ Challenge API Error:</strong> ' + data.message;
        } else {
            output.innerHTML = '<strong style="color: green;">✅ Challenge API Success:</strong><br>' +
                              '<strong>Title:</strong> ' + data.challenge.title + '<br>' +
                              '<strong>Description:</strong> ' + data.challenge.description.substring(0, 100) + '...';
        }
    } catch (error) {
        output.innerHTML = '<strong style="color: red;">❌ Challenge API Error:</strong> ' + error.message;
    }
}

async function testProgress() {
    const output = document.getElementById('test-output');
    output.innerHTML = 'Testing progress API...';
    
    try {
        const response = await fetch('api.php?action=get_progress&subject=programming');
        const data = await response.json();
        
        if (data.error) {
            output.innerHTML = '<strong style="color: red;">❌ Progress API Error:</strong> ' + data.message;
        } else {
            output.innerHTML = '<strong style="color: green;">✅ Progress API Success:</strong><br>' +
                              '<strong>Level:</strong> ' + data.level_name + '<br>' +
                              '<strong>AI Score:</strong> ' + data.progress.ai_assisted_score + '<br>' +
                              '<strong>Independent Score:</strong> ' + data.progress.independent_score;
        }
    } catch (error) {
        output.innerHTML = '<strong style="color: red;">❌ Progress API Error:</strong> ' + error.message;
    }
}

// Auto-run API tests
document.addEventListener('DOMContentLoaded', function() {
    testProgress();
});
</script>

<?php
echo $OUTPUT->footer();
?>