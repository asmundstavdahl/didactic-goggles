<?php

declare(strict_types=1);

class OpenAIClient
{
    private \OpenAI\Client $client;

    public function __construct(string $apiKey)
    {
        $this->client = \OpenAI::client($apiKey);
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
