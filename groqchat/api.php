<?php
require('../../config.php');
require_login();

// use local_groqchat\logger;

$question = required_param('question', PARAM_TEXT);
$apikey = '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'; // You can later move this to settings

$curl = curl_init('https://api.groq.com/openai/v1/chat/completions');
$postfields = json_encode([
    'model' => 'llama-3.1-8b-instant', // Example model
    'messages' => [['role' => 'user', 'content' => $question]],
    'temperature' => 0.7
]);

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json'
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postfields
]);

$response = curl_exec($curl);
curl_close($curl);
$json = json_decode($response, true);
// $answer = $json['choices'][0]['message']['content'];

// logger::log_usage($USER->id, $question, $answer);

echo json_encode(['answer' => $json]);
