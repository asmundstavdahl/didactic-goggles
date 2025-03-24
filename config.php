<?php

declare(strict_types=1);

$apiKey = getenv("OPENROUTER_API_KEY") ?: 'your-api-key-here';

if (empty($apiKey)) {
    throw new RuntimeException('OpenRouter API key is not configured. Please set OPENROUTER_API_KEY environment variable or edit config.php');
}

return [
    'api_key' => $apiKey,
    'api_url' => 'https://openrouter.ai/api/v1',
];
