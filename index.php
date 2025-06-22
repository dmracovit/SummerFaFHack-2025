<?php
// File path: index.php (Corrected text colors: black for Modern, white for Socrates, white/black backgrounds)

require('../../config.php');
require_login();

use local_groqchat\ai_balance_trainer;

$PAGE->set_url('/local/groqchat/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_groqchat'));
$PAGE->set_heading('AI Balance Trainer');

$param_topic = optional_param('topic', '', PARAM_TEXT);
$param_material = optional_param('material', '', PARAM_TEXT);

// Add Tailwind CDN
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'));

echo $OUTPUT->header();
?>

<style>
/* Theme Variables */
:root {
    --bg-primary: linear-gradient(135deg, #ffffff 0%, #f3e8ff 100%);
    --bg-secondary: #ffffff;
    --text-primary: #000000;
    --text-secondary:rgb(147, 163, 185);
    --border-color: #e5e7eb;
    --chat-bg: #f9fafb;
    --accent-primary: #3b82f6;
    --accent-secondary: #8b5cf6;
    --button-hover: #2563eb;
    --shadow: 0 4px 20px rgba(59, 130, 246, 0.1);
}

[data-theme="socrates"] {
    --bg-primary: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    --bg-secondary: #262626;
    --text-primary: #ffffff;
    --text-secondary: #f4b47c;
    --border-color: #404040;
    --chat-bg: #2d2d2d;
    --accent-primary:rgb(242, 79, 58);
    --accent-secondary:rgb(250, 110, 54);
    --button-hover:rgb(238, 99, 29);
    --shadow: 0 4px 20px rgba(234, 88, 12, 0.1);
}

/* Base Styles */
body {
    background: #ffffff;
    color: var(--text-primary);
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
}

.themed-bg-gradient {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
}

.themed-bg {
    background-color: var(--bg-secondary);
    border-color: var(--border-color);
    transition: all 0.3s ease;
    border-radius: 12px;
}

.themed-text {
    color: var(--text-primary);
}

.themed-text-secondary {
    color: var(--text-secondary);
}

.themed-chat-bg {
    background-color: var(--chat-bg);
}

/* Theme-specific overrides */
[data-theme="socrates"] .bg-white {
    background-color: var(--bg-secondary) !important;
}

[data-theme="socrates"] .text-gray-700 {
    color: var(--text-primary) !important;
}

[data-theme="socrates"] .text-gray-600 {
    color: var(--text-secondary) !important;
}

[data-theme="socrates"] .border-gray-300 {
    border-color: var(--border-color) !important;
}

[data-theme="socrates"] .bg-gray-50 {
    background-color: var(--chat-bg) !important;
}

/* Theme Toggle Button */
.theme-toggle {
    background: var(--bg-secondary);
    border: none;
    padding: 10px 18px;
    border-radius: 9999px;
    color: var(--text-primary);
    font-weight: 500;
    cursor: pointer;
    box-shadow: var(--shadow);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.theme-toggle:hover {
    transform: translateY(-1px);
    background: var(--text-secondary);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
}

/* Chat Input */
#message-input {
    background-color: var(--chat-bg);
    border-color: var(--border-color);
    color: var(--text-primary);
    transition: all 0.2s ease;
}

#message-input:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

[data-theme="socrates"] #message-input:focus {
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

/* Buttons */
button {
    transition: all 0.2s ease;
}

#send-button {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
}

#send-button:hover {
    background: linear-gradient(135deg, var(--button-hover), var(--accent-primary));
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--chat-bg);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--accent-primary);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--button-hover);
}
</style>

<div class="max-w-6xl mx-auto mt-6 space-y-6 ">
    <!-- Header Section -->
    <div class="themed-bg-gradient text-white p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">🎯 AI Balance Trainer</h1>
                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($param_topic) ?></h2>
                <p class="text-blue-100">Master AI collaboration while staying independent</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-blue-200">Layer 1: AI Collaboration Training</div>
                <div class="text-xs text-blue-300">Version 1.2</div>
            </div>
        </div>
    </div>

    <!-- Main Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chat Interface (2/3 width) -->
        <div class="lg:col-span-2">
            <div class="themed-bg shadow-xl rounded-xl overflow-hidden flex flex-col border" style="height: 70vh;">
                <!-- Chat Header -->
                <div class="themed-bg-gradient text-white p-4 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-lg">🤖</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">AI Assistant</h2>
                            <p id="current-level-display" class="text-blue-100 text-sm">Tutor Mode</p>
                        </div>
                    </div>
                    <div>
                        <button class="theme-toggle" onclick="toggleTheme()" id="theme-toggle">
                                <span id="theme-icon">🌙</span>
                                <span id="theme-text">Socrates Mode</span>
                        </button>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 themed-chat-bg min-h-0">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-violet-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm">🤖</span>
                        </div>
                        <div class="themed-bg p-3 rounded-lg shadow-sm max-w-md border">
                            <p class="themed-text">Welcome to AI Balance Trainer! I'm here to help you learn to collaborate with AI effectively on topic <i><?php echo htmlspecialchars($param_topic) ?></i></p>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div id="typing-indicator" class="px-4 py-2 hidden flex-shrink-0">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-violet-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm">🤖</span>
                        </div>
                        <div class="themed-bg p-3 rounded-lg shadow-sm border">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="border-t themed-bg p-4 flex-shrink-0">
                    <form id="chat-form" class="flex space-x-3 items-center">
                        <textarea
                            id="message-input"
                            name="question"
                            placeholder="Ask your question here..."
                            rows="1"
                            class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent resize-none themed-bg themed-text"
                            style="min-height: 44px; max-height: 120px;"
                        ></textarea>
                        <button
                            type="submit"
                            id="send-button"
                            class="text-white px-6 py-3 rounded-lg transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
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
            <div class="themed-bg rounded-xl shadow-lg p-6 border">
                <h3 class="text-lg font-semibold mb-4 themed-text">🎯 Current AI Level</h3>
                <div id="level-info">
                    <div class="text-center">
                        <div class="text-3xl mb-2">🎓</div>
                        <div class="font-semibold text-lg themed-text" id="current-level-name">Tutor Mode</div>
                        <div class="text-sm themed-text-secondary mt-2" id="current-level-description">
                            AI provides detailed step-by-step guidance
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="themed-bg rounded-xl shadow-lg p-6 border">
                <h3 class="text-lg font-semibold mb-4 themed-text">⚡ Quick Actions</h3>
                <div class="space-y-3">
                    <button onclick="startChallenge('coding')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                        Start Coding Challenge
                    </button>
                    <button onclick="startChallenge('writing')" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                        Start Writing Challenge
                    </button>
                    <button onclick="window.location.href='debug.php'" class="w-full bg-violet-600 hover:bg-violet-700 text-white py-2 px-4 rounded-lg transition-colors">
                        Debug Progress
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Challenge Modal -->
<div id="challenge-modal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="themed-bg rounded-xl max-w-2xl w-full mx-4 p-6 border">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold themed-text" id="challenge-title">Challenge</h3>
            <button onclick="closeChallenge()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="challenge-content" class="mb-6">
            <!-- Challenge content will be loaded here -->
        </div>
        <div class="flex justify-end space-x-3">
            <button onclick="closeChallenge()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 themed-text">Cancel</button>
            <button onclick="completeChallenge()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Mark Complete</button>
        </div>
    </div>
</div>

<script>
let currentSubject = <?php echo json_encode($param_topic ?: 'programming'); ?>;
let currentSessionId = generateSessionId();
let currentChallenge = null;

// Theme functionality
function toggleTheme() {
    const html = document.documentElement;
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');
    
    const currentTheme = html.getAttribute('data-theme');
    
    if (currentTheme === 'socrates') {
        html.removeAttribute('data-theme');
        themeIcon.textContent = '🌙';
        themeText.textContent = 'Socrates Mode';
        localStorage.setItem('theme', 'modern');
    } else {
        html.setAttribute('data-theme', 'socrates');
        themeIcon.textContent = '☀️';
        themeText.textContent = 'Modern Mode';
        localStorage.setItem('theme', 'socrates');
    }
}

// Load saved theme on page load
function loadTheme() {
    const savedTheme = localStorage.getItem('theme');
    const html = document.documentElement;
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');
    
    if (savedTheme === 'modern') {
        html.setAttribute('data-theme', 'modern');
        themeIcon.textContent = '🌙';
        themeText.textContent = 'Modern Mode';
    } else {
        html.removeAttribute('data-theme');
        themeIcon.textContent = '☀️';
        themeText.textContent = 'Socrates Mode';
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('AI Balance Trainer: DOM loaded, initializing...');
    loadTheme(); // Load theme first
    initializePlugin();
});

function initializePlugin() {
    updateCurrentLevel();
    setupEventListeners();
    console.log('AI Balance Trainer: Initialization complete');
}

function setupEventListeners() {
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const subjectSelector = document.getElementById('subject-selector');

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Handle Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleChatSubmission(e);
        }
    });

    // Subject change
    if (subjectSelector) {
        subjectSelector.addEventListener('change', function() {
            currentSubject = this.value;
            console.log('Subject changed to:', currentSubject);
            updateCurrentLevel();
        });
    }

    // Form submission
    document.getElementById('chat-form').addEventListener('submit', handleChatSubmission);
    
    console.log('Event listeners set up successfully');
}

async function handleChatSubmission(e) {
    e.preventDefault();
    console.log('Chat submission triggered');
    
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const question = messageInput.value.trim();
    
    if (!question) {
        console.log('Empty question, skipping');
        return;
    }
    
    console.log('Sending question:', question, 'Subject:', currentSubject);
    
    // Add user message
    addMessage(question, true);
    
    // Clear input and disable send button
    messageInput.value = '';
    messageInput.style.height = 'auto';
    sendButton.disabled = true;
    
    // Show typing indicator
    showTyping();
    
    try {
        const url = `api.php?action=chat&question=${encodeURIComponent(question)}&subject=${currentSubject}&session_id=${currentSessionId}`;
        console.log('API URL:', url);
        
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('API Response:', data);
        
        hideTyping();
        
        if (data.error) {
            addMessage('Sorry, I encountered an error: ' + (data.message || 'Unknown error'), false);
        } else {
            addMessage(data.answer, false);
            if (data.ai_level) {
                updateLevelDisplay(data.ai_level, data.level_name, data.level_description);
            }
        }
        
    } catch (error) {
        console.error('Fetch error:', error);
        hideTyping();
        addMessage('Sorry, I couldn\'t connect to the server. Please try again. Error: ' + error.message, false);
    }
    
    sendButton.disabled = false;
    messageInput.focus();
}

function addMessage(content, isUser = false) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex items-start space-x-3 ${isUser ? 'flex-row-reverse space-x-reverse' : ''}`;
    
    // Format content with line breaks
    const formattedContent = content.replace(/\n/g, '<br>');
    
    const avatarClass = isUser 
        ? 'w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0'
        : 'w-8 h-8 bg-gradient-to-br from-blue-400 to-violet-500 rounded-full flex items-center justify-center flex-shrink-0';
    
    const avatarIcon = isUser ? '👤' : '🤖';
    
    const messageClass = isUser 
        ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white p-3 rounded-lg shadow-sm max-w-md'
        : 'themed-bg p-3 rounded-lg shadow-sm max-w-md border';
    
    const textClass = isUser ? 'text-white' : 'themed-text';
    
    messageDiv.innerHTML = `
        <div class="${avatarClass}">
            <span class="text-white text-sm">${avatarIcon}</span>
        </div>
        <div class="${messageClass}">
            <div class="${textClass}">${formattedContent}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

function showTyping() {
    document.getElementById('typing-indicator').classList.remove('hidden');
    scrollToBottom();
}

function hideTyping() {
    document.getElementById('typing-indicator').classList.add('hidden');
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTo({
        top: chatMessages.scrollHeight,
        behavior: 'smooth'
    });
}

function clearChat() {
    if (confirm('Are you sure you want to clear the chat history?')) {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-violet-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm">🤖</span>
                </div>
                <div class="themed-bg p-3 rounded-lg shadow-sm max-w-md border">
                    <p class="themed-text">Chat cleared! Ready for a fresh start. What would you like to learn today?</p>
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
        
        console.log('Progress data:', data);
        
        if (!data.error && data.progress) {
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
    
    console.log('Level display updated:', level, levelName);
}

async function startChallenge(type) {
    console.log('Starting challenge:', type, 'for subject:', currentSubject);
    
    try {
        const response = await fetch(`api.php?action=start_challenge&challenge_type=${type}&subject=${currentSubject}`);
        const data = await response.json();
        
        console.log('Challenge data:', data);
        
        if (!data.error && data.challenge) {
            currentChallenge = data.challenge;
            showChallengeModal(data.challenge);
        } else {
            alert('Error starting challenge: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error starting challenge:', error);
        alert('Error starting challenge: ' + error.message);
    }
}

function showChallengeModal(challenge) {
    console.log('Showing challenge modal:', challenge);
    
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
    
    document.getElementById('challenge-modal').style.display = 'flex';
}

function closeChallenge() {
    document.getElementById('challenge-modal').style.display = 'none';
    currentChallenge = null;
}

async function completeChallenge() {
    if (!currentChallenge) {
        alert('No active challenge');
        return;
    }
    
    const aiScore = prompt("Rate how much you used AI assistance (0-10):");
    const independentScore = prompt("Rate how much you solved independently (0-10):");
    
    if (aiScore === null || independentScore === null) return;
    
    try {
        const response = await fetch(`api.php?action=complete_challenge&subject=${currentSubject}&ai_score=${aiScore}&independent_score=${independentScore}`);
        const data = await response.json();
        
        console.log('Challenge completion result:', data);
        
        if (data.success) {
            alert(`Challenge completed! New level: ${data.level_name}`);
            closeChallenge();
            updateCurrentLevel();
        } else {
            alert('Error completing challenge: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error completing challenge:', error);
        alert('Error completing challenge: ' + error.message);
    }
}

function generateSessionId() {
    return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

// Debug function
function debugAPI() {
    console.log('Testing API endpoints...');
    
    fetch('api.php?action=get_progress&subject=programming')
        .then(r => r.json())
        .then(data => console.log('Progress API test:', data))
        .catch(e => console.error('Progress API error:', e));
        
    fetch('api.php?action=start_challenge&challenge_type=coding&subject=programming')
        .then(r => r.json())
        .then(data => console.log('Challenge API test:', data))
        .catch(e => console.error('Challenge API error:', e));
}

// Focus on input when page loads
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.focus();
    }
});

console.log('AI Balance Trainer: Script loaded successfully');
</script>

<?php
echo $OUTPUT->footer();
?>