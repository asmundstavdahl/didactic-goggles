<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

class OpenAIClient
{
    private $client;

    public function __construct(string $apiKey)
    {
        $this->client = new \OpenAI\Client([
            'api_key' => $apiKey,
        ]);
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
    
    public function completions()
    {
        return $this->client->completions();
    }
}
