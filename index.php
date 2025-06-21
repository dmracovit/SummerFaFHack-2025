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

<div class="max-w-3xl mx-auto mt-10 p-6 bg-white shadow-xl rounded-xl">
    <h2 class="text-2xl font-semibold text-pink-600 mb-4">💬 Chat with Groq</h2>

    <form id="chat-form" class="space-y-4">
        <textarea 
            name="question"
            placeholder="Ask Groq anything..."
            rows="4"
            class="w-full p-4 border border-pink-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none"
        ></textarea>

        <button 
            type="submit"
            class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-full transition-all shadow-md"
        >
            ✨ Send
        </button>
    </form>

    <div 
        id="groq-answer" 
        class="mt-6 p-4 bg-pink-50 border border-pink-200 rounded-lg text-gray-700 whitespace-pre-wrap hidden"
    ></div>
</div>

<script>
document.getElementById('chat-form').onsubmit = async function(e) {
    e.preventDefault();
    const question = this.question.value.trim();
    if (!question) return;

    const res = await fetch('api.php?question=' + encodeURIComponent(question));
    const data = await res.text();
    try {
        const parsed = JSON.parse(data);
        const content = parsed.answer.choices[0].message.content;
        const answerBox = document.getElementById('groq-answer');
        answerBox.innerText = content;
        answerBox.classList.remove('hidden');
    } catch (err) {
        alert("Error parsing response: " + err.message);
    }
}
</script>

<?php
echo $OUTPUT->footer();
