<?php

declare(strict_types=1);

$apiKey = getenv("OPENAI_API_KEY") ?: 'your-api-key-here';

if (empty($apiKey)) {
    throw new RuntimeException('OpenAI API key is not configured. Please set OPENAI_API_KEY environment variable or edit config.php');
}

return [
    'api_key' => $apiKey,
];
