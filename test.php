<?php
$OPENAI_API_KEY = "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";  // same key you used in assessment_c.php

$payload = [
    "model" => "gpt-4o-mini",
    "messages" => [
        ["role" => "user", "content" => "Say hello"]
    ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $OPENAI_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$result = curl_exec($ch);
var_dump($result);
