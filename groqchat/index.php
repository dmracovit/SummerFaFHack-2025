<?php
require('../../config.php');
require_login();
require_capability('local/groqchat:view', context_system::instance());

$PAGE->set_url('/local/groqchat/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_groqchat'));
$PAGE->set_heading(get_string('chatheading', 'local_groqchat'));

echo $OUTPUT->header();
?>
<form id="chat-form">
    <textarea name="question" placeholder="Ask Groq..." rows="4" cols="80"></textarea><br>
    <button type="submit">Send</button>
</form>
<div id="groq-answer" style="margin-top: 20px;"></div>

<script>
document.getElementById('chat-form').onsubmit = async function(e) {
    e.preventDefault();
    const question = this.question.value;
    const res = await fetch('api.php?question=' + encodeURIComponent(question));
    const data = await res.text()
    const parsed = JSON.parse(data);
    const content = parsed.answer.choices[0].message.content;;
    document.getElementById('groq-answer').innerText = content;
}
</script>
<?php
echo $OUTPUT->footer();