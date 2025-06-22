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
    --text-secondary: rgb(147, 163, 185);
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
    --accent-primary: rgb(242, 79, 58);
    --accent-secondary: rgb(250, 110, 54);
    --button-hover: rgb(238, 99, 29);
    --shadow: 0 4px 20px rgba(234, 88, 12, 0.1);
}

/* Socratic Chat Custom Theme (always dark/orange) */
.socratic-theme {
    --bg-primary: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    --bg-secondary: #262626;
    --text-primary: #ffffff;
    --text-secondary: #f4b47c;
    --border-color: #404040;
    --chat-bg: #2d2d2d;
    --accent-primary: rgb(242, 79, 58);
    --accent-secondary: rgb(250, 110, 54);
    --button-hover: rgb(238, 99, 29);
    --shadow: 0 4px 20px rgba(234, 88, 12, 0.1);
}

/* Visual Novel Socrates UI */
.socratic-vn-bg {
    background: linear-gradient(135deg, #181818 60%, #2d2d2d 100%);
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.45);
    border: 2px solid #f97316;
    position: relative;
    overflow: hidden;
}
.socratic-vn-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 4px solid #f97316;
    background: #181818;
    object-fit: cover;
    box-shadow: 0 4px 16px rgba(250,110,54,0.25);
}
.socratic-vn-bubble {
    background: rgba(34, 34, 34, 0.98);
    color: #fff;
    border-radius: 18px 18px 18px 0;
    padding: 22px 28px;
    font-size: 1.15rem;
    font-family: 'Georgia', serif;
    margin-left: 24px;
    margin-bottom: 8px;
    box-shadow: 0 2px 12px rgba(250,110,54,0.10);
    border: 2px solid #f97316;
    max-width: 80%;
    position: relative;
}
.socratic-vn-bubble:after {
    content: '';
    position: absolute;
    left: -18px;
    top: 32px;
    width: 0;
    height: 0;
    border-top: 18px solid transparent;
    border-bottom: 18px solid transparent;
    border-right: 18px solid #222;
    filter: drop-shadow(-2px 0 0 #f97316);
}
.socratic-vn-log {
    max-height: 320px;
    overflow-y: auto;
    padding: 0 8px;
    margin-bottom: 12px;
}
.socratic-vn-input {
    background: #232323;
    color: #fff;
    border: 2px solid #f97316;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 1rem;
    width: 100%;
    font-family: 'Inter', sans-serif;
    margin-right: 12px;
}
.socratic-vn-send {
    background: linear-gradient(135deg, #f97316, #fa6e36);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 12px 28px;
    font-weight: bold;
    font-size: 1rem;
    box-shadow: 0 2px 8px rgba(250,110,54,0.15);
    transition: background 0.2s;
}
.socratic-vn-send:hover {
    background: linear-gradient(135deg, #fa6e36, #f97316);
}
.socratic-vn-bottom {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 12px;
}
.socratic-vn-bg::-webkit-scrollbar {
    width: 8px;
}
.socratic-vn-bg::-webkit-scrollbar-thumb {
    background: #f97316;
    border-radius: 4px;
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

<div class="max-w-6xl mx-auto mt-6 space-y-6">
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

    <!-- Main Interface: Socrates Chat in center, AI Assistant on right -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Socrates Bubble Interface (center, 2/3 width) -->
        <div class="lg:col-span-2 flex flex-col items-center justify-center" id="socrates-chat" style="height: 70vh;">
            <div class="socratic-vn-bg w-full max-w-2xl flex flex-col items-center justify-end h-full p-0 relative">
                <!-- Dialogue Log -->
                <div id="socrates-vn-log" class="socratic-vn-log w-full flex flex-col justify-end">
                    <div class="flex items-end mb-2">
                        <img src="https://cloak.romanbaths.co.uk/images/characters/haruspex-talking.gif" alt="Socrates Talking" class="socratic-vn-avatar mr-2" />
                        <div class="socratic-vn-bubble" id="socrates-bubble-text">
                            Welcome to Socratic Mode! I'll guide you with questions to help you think independently about <i><?php echo htmlspecialchars($param_topic) ?></i>
                        </div>
                    </div>
                </div>
                <!-- Input at the bottom -->
                <form id="socrates-form" class="socratic-vn-bottom px-4 pb-4">
                    <textarea
                        id="socrates-input"
                        name="question"
                        placeholder="Ask Socrates a question..."
                        rows="1"
                        class="socratic-vn-input"
                        style="min-height: 44px; max-height: 120px;"
                    ></textarea>
                    <button
                        type="submit"
                        id="socrates-send"
                        class="socratic-vn-send ml-2"
                    >
                        Send
                    </button>
                </form>
            </div>
        </div>
        <!-- Sidebar (1/3 width): AI Assistant Chat and Info -->
        <div class="space-y-6 flex flex-col">
            <!-- AI Assistant Chat -->
            <div class="themed-bg shadow-xl rounded-xl overflow-hidden flex flex-col border flex-1" style="min-height: 350px;">
                <!-- Chat Header -->
                <div class="themed-bg-gradient text-white p-4 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <span class="text-lg">🤖</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">AI Assistant (Modern)</h2>
                            <p id="current-level-display" class="text-blue-100 text-sm">Tutor Mode</p>
                        </div>
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
            <!-- Current Level Info -->
            <div class="themed-bg p-6 rounded-xl shadow-lg border mt-6">
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
            <div class="themed-bg p-6 rounded-xl shadow-lg border mt-6">
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
        <div id="challenge-content" class="mb-6"></div>
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

// System prompts for each mode
const SYSTEM_PROMPTS = {
    modern: "You are an AI tutor providing clear, detailed, and step-by-step explanations to help the user understand the topic. Answer directly and concisely, offering practical examples where applicable.",
    socrates: "You are a Socratic AI that responds with thought-provoking questions to guide the user toward deeper understanding and independent thinking. Avoid giving direct answers; instead, ask questions that encourage reflection and exploration of the topic."
};

// Initialize plugin
document.addEventListener('DOMContentLoaded', function() {
    console.log('AI Balance Trainer: DOM loaded, initializing...');
    loadTheme();
    initializePlugin();
});

function initializePlugin() {
    updateCurrentLevel();
    setupEventListeners();
    console.log('AI Balance Trainer: Initialization complete');
}

function setupEventListeners() {
    const messageInput = document.getElementById('message-input');
    const socratesInput = document.getElementById('socrates-input');

    // Auto-resize textareas
    [messageInput, socratesInput].forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });
        }
    });

    // Handle Enter key for Modern mode
    if (messageInput) {
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('chat-form').dispatchEvent(new Event('submit'));
            }
        });
    }

    // Handle Enter key for Socrates mode
    if (socratesInput) {
        socratesInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('socrates-form').dispatchEvent(new Event('submit'));
            }
        });
    }
}

// Modern chat submission (calls api.php)
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const question = messageInput.value.trim();
    if (!question) return;

    addMessage(question, true);
    messageInput.value = '';
    messageInput.style.height = 'auto';
    sendButton.disabled = true;
    showTyping();
    try {
        const url = `api.php?action=chat&question=${encodeURIComponent(question)}&subject=${encodeURIComponent(currentSubject)}&session_id=${encodeURIComponent(currentSessionId)}&prompt=${encodeURIComponent(SYSTEM_PROMPTS.modern)}`;
        const response = await fetch(url);
        const data = await response.json();
        hideTyping();
        if (data.error) {
            addMessage('Error: ' + (data.message || 'Unknown error'), false);
            console.error('Modern API error:', data.message);
        } else {
            addMessage(data.answer, false);
            if (data.ai_level) {
                updateLevelDisplay(data.ai_level, data.level_name, data.level_description);
            }
        }
    } catch (error) {
        hideTyping();
        addMessage('Error connecting to server: ' + error.message, false);
        console.error('Modern API connection error:', error);
    }
    sendButton.disabled = false;
    messageInput.focus();
});

// Socrates chat submission (calls socrates_api.php)
document.getElementById('socrates-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageInput = document.getElementById('socrates-input');
    const sendButton = document.getElementById('socrates-send');
    const question = messageInput.value.trim();
    if (!question) return;

    addSocratesMessage(question, true);
    messageInput.value = '';
    messageInput.style.height = 'auto';
    sendButton.disabled = true;
    showSocratesTyping();
    try {
        const url = `socrates_api.php?action=socratic_chat&question=${encodeURIComponent(question)}&subject=${encodeURIComponent(currentSubject)}&session_id=${encodeURIComponent(currentSessionId)}&prompt=${encodeURIComponent(SYSTEM_PROMPTS.socrates)}`;
        const response = await fetch(url);
        const data = await response.json();
        hideSocratesTyping();
        if (data.error) {
            addSocratesMessage('Error: ' + (data.message || 'Unknown error'), false);
            console.error('Socrates API error:', data.message);
        } else {
            addSocratesMessage(data.answer, false);
        }
    } catch (error) {
        hideSocratesTyping();
        addSocratesMessage('Error connecting to server: ' + error.message, false);
        console.error('Socrates API connection error:', error);
    }
    sendButton.disabled = false;
    messageInput.focus();
});

function addMessage(content, isUser = false) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex items-start space-x-3 ${isUser ? 'flex-row-reverse space-x-reverse' : ''}`;
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
            <p class="${textClass}">${formattedContent}</p>
        </div>
    `;
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

/* Visual Novel Socrates UI */
function addSocratesMessage(content, isUser = false) {
    const log = document.getElementById('socrates-vn-log');
    const bubble = document.createElement('div');
    bubble.className = 'flex items-end mb-2';
    if (isUser) {
        bubble.innerHTML = `
            <div class="flex-1"></div>
            <div class="socratic-vn-bubble bg-gradient-to-br from-gray-700 to-gray-900 text-orange-200 border-orange-400" style="border-radius:18px 18px 0 18px; margin-right:12px; margin-left:auto;">
                <span class="opacity-80 italic">You: ${content.replace(/\n/g, '<br>')}</span>
            </div>
        `;
    } else {
        bubble.innerHTML = `
            <img src="https://cloak.romanbaths.co.uk/images/characters/haruspex-talking.gif" alt="Socrates Talking" class="socratic-vn-avatar mr-2" />
            <div class="socratic-vn-bubble">
                ${content.replace(/\n/g, '<br>')}
            </div>
        `;
    }
    log.appendChild(bubble);
    log.scrollTop = log.scrollHeight;
}

function showSocratesTyping() {
    addSocratesMessage('<span class="opacity-70">Socrates is thinking...</span>', false);
}

function hideSocratesTyping() {
    // No-op, handled by addSocratesMessage
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
    chatMessages.scrollTo({ top: chatMessages.scrollHeight, behavior: 'smooth' });
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
        const response = await fetch(`api.php?action=get_progress&subject=${encodeURIComponent(currentSubject)}`);
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
    try {
        const response = await fetch(`api.php?action=start_challenge&challenge_type=${encodeURIComponent(type)}&subject=${encodeURIComponent(currentSubject)}`);
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
        const response = await fetch(`api.php?action=complete_challenge&subject=${encodeURIComponent(currentSubject)}&ai_score=${encodeURIComponent(aiScore)}&independent_score=${encodeURIComponent(independentScore)}`);
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

console.log('AI Balance Trainer: Script loaded successfully');
</script>

<?php
echo $OUTPUT->footer();
?>