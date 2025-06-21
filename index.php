<?php
require('../../config.php');
require_login();
// require_capability('local/groqchat:view', context_system::instance());
$PAGE->set_url('/local/groqchat/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_groqchat'));
$PAGE->set_heading(get_string('chatheading', 'local_groqchat'));
// Add Tailwind CDN (just for dev/demo; ideally compile your own for production)
$PAGE->requires->css(new moodle_url('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'));
echo $OUTPUT->header();
?>

<div class="max-w-4xl mx-auto mt-6 h-screen flex flex-col bg-white shadow-xl rounded-xl overflow-hidden">
    <!-- Chat Header -->
    <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
            <span class="text-lg">🤖</span>
        </div>
        <div>
            <h2 class="text-xl font-semibold">Groq AI Assistant</h2>
            <p class="text-pink-100 text-sm">Powered by Groq</p>
        </div>
        <div class="ml-auto">
            <button id="clear-chat" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded-full text-sm transition-all">
                Clear Chat
            </button>
        </div>
    </div>

    <!-- Chat Messages Container -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        <!-- Welcome Message -->
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-white text-sm">🤖</span>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm max-w-md">
                <p class="text-gray-700">Hello! I'm your Groq AI assistant. Ask me anything and I'll do my best to help you!</p>
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
                placeholder="Type your message here..."
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

<script>
const chatMessages = document.getElementById('chat-messages');
const messageInput = document.getElementById('message-input');
const sendButton = document.getElementById('send-button');
const typingIndicator = document.getElementById('typing-indicator');
const clearButton = document.getElementById('clear-chat');

// Auto-resize textarea
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Handle Enter key (send message) and Shift+Enter (new line)
messageInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chat-form').dispatchEvent(new Event('submit'));
    }
});

// Add message to chat
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

// Show/hide typing indicator
function showTyping() {
    typingIndicator.classList.remove('hidden');
    scrollToBottom();
}

function hideTyping() {
    typingIndicator.classList.add('hidden');
}

// Scroll to bottom of chat
function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Handle form submission
document.getElementById('chat-form').addEventListener('submit', async function(e) {
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
        const res = await fetch('api.php?question=' + encodeURIComponent(question));
        const data = await res.text();
        
        // Hide typing indicator
        hideTyping();
        
        try {
            const parsed = JSON.parse(data);
            const content = parsed.answer.choices[0].message.content;
            
            // Add AI response
            addMessage(content, false);
            
        } catch (parseErr) {
            console.error('Parse error:', parseErr);
            addMessage('Sorry, I encountered an error processing your request. Please try again.', false);
        }
        
    } catch (fetchErr) {
        console.error('Fetch error:', fetchErr);
        hideTyping();
        addMessage('Sorry, I couldn\'t connect to the server. Please check your connection and try again.', false);
    }
    
    // Re-enable send button
    sendButton.disabled = false;
    messageInput.focus();
});

// Clear chat functionality
clearButton.addEventListener('click', function() {
    if (confirm('Are you sure you want to clear the chat history?')) {
        chatMessages.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm">🤖</span>
                </div>
                <div class="bg-white p-3 rounded-lg shadow-sm max-w-md">
                    <p class="text-gray-700">Hello! I'm your Groq AI assistant. Ask me anything and I'll do my best to help you!</p>
                </div>
            </div>
        `;
    }
});

// Focus on input when page loads
messageInput.focus();
</script>

<?php
echo $OUTPUT->footer();
?>