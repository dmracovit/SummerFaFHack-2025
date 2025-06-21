<?php
// File path: index.php (REPLACE existing index.php)

require('../../config.php');
require_login();

use local_groqchat\ai_balance_trainer;

$PAGE->set_url('/local/groqchat/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_groqchat'));
$PAGE->set_heading('AI Balance Trainer');

// Add Tailwind CDN
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'));

// Get user's current progress
$trainer = new ai_balance_trainer($USER->id);
$programming_progress = $trainer->get_user_progress('programming');
$writing_progress = $trainer->get_user_progress('writing');

echo $OUTPUT->header();
?>

<div class="max-w-6xl mx-auto mt-6 space-y-6">
    <!-- Header Section with Progress -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">🎯 AI Balance Trainer</h1>
                <p class="text-indigo-100">Learn to collaborate with AI effectively while maintaining your independence</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-indigo-200">Layer 1: AI Collaboration Training</div>
                <div class="text-xs text-indigo-300">Version 1.1</div>
            </div>
        </div>
    </div>
    
    <!-- Progress Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Programming Progress -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-800">💻 Programming</h3>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    <?php echo ai_balance_trainer::get_level_name($programming_progress['ai_level']); ?>
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span>AI-Assisted Score</span>
                    <span class="font-semibold"><?php echo $programming_progress['ai_assisted_score']; ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Independent Score</span>
                    <span class="font-semibold"><?php echo $programming_progress['independent_score']; ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Total Challenges</span>
                    <span class="font-semibold"><?php echo $programming_progress['total_challenges']; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <?php 
                    $total_score = $programming_progress['ai_assisted_score'] + $programming_progress['independent_score'];
                    $independence_percentage = $total_score > 0 ? ($programming_progress['independent_score'] / $total_score) * 100 : 0;
                    ?>
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $independence_percentage; ?>%"></div>
                </div>
                <div class="text-xs text-gray-600">Independence: <?php echo round($independence_percentage); ?>%</div>
            </div>
        </div>
        
        <!-- Writing Progress -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-800">✍️ Writing</h3>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <?php echo ai_balance_trainer::get_level_name($writing_progress['ai_level']); ?>
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span>AI-Assisted Score</span>
                    <span class="font-semibold"><?php echo $writing_progress['ai_assisted_score']; ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Independent Score</span>
                    <span class="font-semibold"><?php echo $writing_progress['independent_score']; ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Total Challenges</span>
                    <span class="font-semibold"><?php echo $writing_progress['total_challenges']; ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <?php 
                    $total_score = $writing_progress['ai_assisted_score'] + $writing_progress['independent_score'];
                    $independence_percentage = $total_score > 0 ? ($writing_progress['independent_score'] / $total_score) * 100 : 0;
                    ?>
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $independence_percentage; ?>%"></div>
                </div>
                <div class="text-xs text-gray-600">Independence: <?php echo round($independence_percentage); ?>%</div>
            </div>
        </div>
    </div>

    <!-- Main Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chat Interface (2/3 width) -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-xl rounded-xl overflow-hidden h-[600px] flex flex-col">
                <!-- Chat Header -->
                <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-lg">🤖</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">AI Assistant</h2>
                            <p id="current-level-display" class="text-pink-100 text-sm">Tutor Mode</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <select id="subject-selector" class="bg-white bg-opacity-20 text-white rounded px-3 py-1 text-sm">
                            <option value="programming">💻 Programming</option>
                            <option value="writing">✍️ Writing</option>
                            <option value="math">🔢 Math</option>
                            <option value="general">🎯 General</option>
                        </select>
                        <button id="clear-chat" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded text-sm transition-all">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm">🤖</span>
                        </div>
                        <div class="bg-white p-3 rounded-lg shadow-sm max-w-md">
                            <p class="text-gray-700">Welcome to AI Balance Trainer! I'm here to help you learn to collaborate with AI effectively. Choose a subject and start asking questions!</p>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div id="typing-indicator" class="px-4 py-2 hidden">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm">🤖</span>
                        </div>
                        <div class="bg-white p-3 rounded-lg shadow-sm">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="border-t bg-white p-4">
                    <form id="chat-form" class="flex space-x-3">
                        <textarea
                            id="message-input"
                            name="question"
                            placeholder="Ask your question here..."
                            rows="1"
                            class="flex-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent resize-none"
                            style="min-height: 44px; max-height: 120px;"
                        ></textarea>
                        <button
                            type="submit"
                            id="send-button"
                            class="bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                        >
                            <span>Send</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar (1/3 width) -->
        <div class="space-y-6">
            <!-- Current Level Info -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">🎯 Current AI Level</h3>
                <div id="level-info">
                    <div class="text-center">
                        <div class="text-3xl mb-2">🎓</div>
                        <div class="font-semibold text-lg" id="current-level-name">Tutor Mode</div>
                        <div class="text-sm text-gray-600 mt-2" id="current-level-description">
                            AI provides detailed step-by-step guidance
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Level Progression -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">📈 Level Progression</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                        <div class="flex-1">
                            <div class="font-medium text-sm">Tutor Mode</div>
                            <div class="text-xs text-gray-500">Full guidance</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">2</div>
                        <div class="flex-1">
                            <div class="font-medium text-sm">Partner Mode</div>
                            <div class="text-xs text-gray-500">Collaborative</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-sm font-bold">3</div>
                        <div class="flex-1">
                            <div class="font-medium text-sm">Assistant Mode</div>
                            <div class="text-xs text-gray-500">Hints & suggestions</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold">4</div>
                        <div class="flex-1">
                            <div class="font-medium text-sm">Validator Mode</div>
                            <div class="text-xs text-gray-500">Feedback only</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">5</div>
                        <div class="flex-1">
                            <div class="font-medium text-sm">Independent Mode</div>
                            <div class="text-xs text-gray-500">Minimal assistance</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">⚡ Quick Actions</h3>
                <div class="space-y-3">
                    <button id="start-coding-challenge" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors">
                        Start Coding Challenge
                    </button>
                    <button id="start-writing-challenge" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition-colors">
                        Start Writing Challenge
                    </button>
                    <button id="view-progress" class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg transition-colors">
                        View Detailed Progress
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Challenge Modal -->
<div id="challenge-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl max-w-2xl w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold" id="challenge-title">Challenge</h3>
            <button id="close-challenge" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="challenge-content" class="mb-6">
            <!-- Challenge content will be loaded here -->
        </div>
        <div class="flex justify-end space-x-3">
            <button id="cancel-challenge" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            <button id="complete-challenge" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Mark Complete</button>
        </div>
    </div>
</div>

<script>
// Global variables
let currentSubject = 'programming';
let currentSessionId = generateSessionId();
let currentChallenge = null;

// DOM elements
const chatMessages = document.getElementById('chat-messages');
const messageInput = document.getElementById('message-input');
const sendButton = document.getElementById('send-button');
const typingIndicator = document.getElementById('typing-indicator');
const clearButton = document.getElementById('clear-chat');
const subjectSelector = document.getElementById('subject-selector');
const challengeModal = document.getElementById('challenge-modal');

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentLevel();
    setupEventListeners();
});

function setupEventListeners() {
    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Handle Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chat-form').dispatchEvent(new Event('submit'));
        }
    });

    // Subject change
    subjectSelector.addEventListener('change', function() {
        currentSubject = this.value;
        updateCurrentLevel();
    });

    // Form submission
    document.getElementById('chat-form').addEventListener('submit', handleChatSubmission);

    // Clear chat
    clearButton.addEventListener('click', clearChat);

    // Challenge buttons
    document.getElementById('start-coding-challenge').addEventListener('click', () => startChallenge('coding'));
    document.getElementById('start-writing-challenge').addEventListener('click', () => startChallenge('writing'));
    document.getElementById('view-progress').addEventListener('click', viewProgress);

    // Modal events
    document.getElementById('close-challenge').addEventListener('click', closeChallenge);
    document.getElementById('cancel-challenge').addEventListener('click', closeChallenge);
    document.getElementById('complete-challenge').addEventListener('click', completeChallenge);
}

async function handleChatSubmission(e) {
    e.preventDefault();
    
    const question = messageInput.value.trim();
    if (!question) return;
    
    // Add user message
    addMessage(question, true);
    
    // Clear input and disable send button
    messageInput.value = '';
    messageInput.style.height = 'auto';
    sendButton.disabled = true;
    
    // Show typing indicator
    showTyping();
    
    try {
        const response = await fetch(`api.php?action=chat&question=${encodeURIComponent(question)}&subject=${currentSubject}&session_id=${currentSessionId}`);
        const data = await response.json();
        
        hideTyping();
        
        if (data.error) {
            addMessage('Sorry, I encountered an error. Please try again.', false);
        } else {
            addMessage(data.answer, false);
            updateLevelDisplay(data.ai_level, data.level_name, data.level_description);
        }
        
    } catch (error) {
        console.error('Fetch error:', error);
        hideTyping();
        addMessage('Sorry, I couldn\'t connect to the server. Please try again.', false);
    }
    
    sendButton.disabled = false;
    messageInput.focus();
}

function addMessage(content, isUser = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex items-start space-x-3 ${isUser ? 'flex-row-reverse space-x-reverse' : ''}`;
    
    const avatarClass = isUser 
        ? 'w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0'
        : 'w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0';
    
    const avatarIcon = isUser ? '👤' : '🤖';
    
    const messageClass = isUser 
        ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white p-3 rounded-lg shadow-sm max-w-md'
        : 'bg-white p-3 rounded-lg shadow-sm max-w-md';
    
    messageDiv.innerHTML = `
        <div class="${avatarClass}">
            <span class="text-white text-sm">${avatarIcon}</span>
        </div>
        <div class="${messageClass}">
            <p class="${isUser ? 'text-white' : 'text-gray-700'}">${content}</p>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

function showTyping() {
    typingIndicator.classList.remove('hidden');
    scrollToBottom();
}

function hideTyping() {
    typingIndicator.classList.add('hidden');
}

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function clearChat() {
    if (confirm('Are you sure you want to clear the chat history?')) {
        chatMessages.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm">🤖</span>
                </div>
                <div class="bg-white p-3 rounded-lg shadow-sm max-w-md">
                    <p class="text-gray-700">Chat cleared! Ready for a fresh start. What would you like to learn today?</p>
                </div>
            </div>
        `;
        currentSessionId = generateSessionId();
    }
}

async function updateCurrentLevel() {
    try {
        const response = await fetch(`api.php?action=get_progress&subject=${currentSubject}`);
        const data = await response.json();
        
        if (!data.error) {
            updateLevelDisplay(data.progress.ai_level, data.level_name, data.level_description);
        }
    } catch (error) {
        console.error('Error updating level:', error);
    }
}

function updateLevelDisplay(level, levelName, levelDescription) {
    document.getElementById('current-level-display').textContent = levelName;
    document.getElementById('current-level-name').textContent = levelName;
    document.getElementById('current-level-description').textContent = levelDescription;
    
    // Update progress indicators
    const indicators = document.querySelectorAll('.level-indicator');
    indicators.forEach((indicator, index) => {
        if (index < level) {
            indicator.classList.add('bg-green-500');
            indicator.classList.remove('bg-gray-300');
        } else {
            indicator.classList.add('bg-gray-300');
            indicator.classList.remove('bg-green-500');
        }
    });
}

async function startChallenge(type) {
    try {
        const response = await fetch(`api.php?action=start_challenge&challenge_type=${type}&subject=${currentSubject}`);
        const data = await response.json();
        
        if (!data.error) {
            currentChallenge = data.challenge;
            showChallengeModal(data.challenge);
        }
    } catch (error) {
        console.error('Error starting challenge:', error);
    }
}

function showChallengeModal(challenge) {
    document.getElementById('challenge-title').textContent = challenge.title;
    document.getElementById('challenge-content').innerHTML = `
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">Challenge Description</h4>
                <p class="text-blue-700">${challenge.description}</p>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold">Difficulty:</span> 
                    <span class="capitalize">${challenge.difficulty}</span>
                </div>
                <div>
                    <span class="font-semibold">Estimated Time:</span> 
                    ${challenge.estimated_time}
                </div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-yellow-800 text-sm">
                    💡 <strong>Tip:</strong> Use the chat to get AI assistance based on your current level. 
                    Try to solve independently first to improve your independence score!
                </p>
            </div>
        </div>
    `;
    
    challengeModal.classList.remove('hidden');
    challengeModal.classList.add('flex');
}

function closeChallenge() {
    challengeModal.classList.add('hidden');
    challengeModal.classList.remove('flex');
    currentChallenge = null;
}

async function completeChallenge() {
    if (!currentChallenge) return;
    
    const aiScore = prompt("Rate how much you used AI assistance (0-10):");
    const independentScore = prompt("Rate how much you solved independently (0-10):");
    
    if (aiScore === null || independentScore === null) return;
    
    try {
        const response = await fetch(`api.php?action=complete_challenge&subject=${currentSubject}&ai_score=${aiScore}&independent_score=${independentScore}`);
        const data = await response.json();
        
        if (data.success) {
            alert(`Challenge completed! New level: ${data.level_name}`);
            closeChallenge();
            updateCurrentLevel();
            location.reload(); // Refresh to update progress displays
        }
    } catch (error) {
        console.error('Error completing challenge:', error);
    }
}

function viewProgress() {
    // Redirect to a dedicated progress page or show detailed modal
    alert('Detailed progress view coming soon!');
}

function generateSessionId() {
    return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

// Focus on input when page loads
messageInput.focus();
</script>

<?php
echo $OUTPUT->footer();
?>