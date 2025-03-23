<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenAI\Client;

class OpenAIClient
{
    private Client $client;

    public function __construct(string $apiKey)
    {
        $this->client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->make();
    }

    public function getCompletion(string $prompt, string $model): string
    {
        $response = $this->client->completions()->create([
            'model' => $model,
            'prompt' => $prompt,
            'max_tokens' => 150,
        ]);

        return $response['choices'][0]['text'] ?? '';
    }
}
