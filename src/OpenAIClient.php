<?php

declare(strict_types=1);

class OpenAIClient
{
    private string $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getCompletion(string $prompt, string $model): string
    {
        $data = [
            'model' => $model,
            'prompt' => $prompt,
            'max_tokens' => 150,
        ];

        $response = $this->makeRequest('completions', $data);
        return $response['choices'][0]['text'] ?? '';
    }

    public function chat()
    {
        return new ChatCompletionHandler($this);
    }

    public function completions()
    {
        return new CompletionHandler($this);
    }

    public function makeRequest(string $endpoint, array $data): array
    {
        $ch = curl_init("{$this->apiUrl}/{$endpoint}");

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("API request failed with code $httpCode: $response");
        }

        return json_decode($response, true);
    }
}

class ChatCompletionHandler
{
    private OpenAIClient $client;

    public function __construct(OpenAIClient $client)
    {
        $this->client = $client;
    }

    public function create(array $options): object
    {
        $response = $this->client->makeRequest('chat/completions', $options);
        return json_decode(json_encode($response)); // Convert array to object
    }
}

class CompletionHandler
{
    private OpenAIClient $client;

    public function __construct(OpenAIClient $client)
    {
        $this->client = $client;
    }

    public function create(array $options): object
    {
        $response = $this->client->makeRequest('completions', $options);
        return json_decode(json_encode($response)); // Convert array to object
    }
}
