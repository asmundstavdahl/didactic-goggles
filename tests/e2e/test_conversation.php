<?php
declare(strict_types=1);

class ConversationTest
{
    private const BASE_URL = 'http://localhost:8000';

    public function testFullConversationFlow()
    {
        $conversationTitle = 'Test Conversation';
        $messageContent = 'Hello, world!';

        // Create conversation
        $createResponse = $this->makePostRequest('/create_conversation.php', [
            'title' => $conversationTitle,
            'model_config' => 'gpt-3.5-turbo'
        ]);
        
        $this->assertResponseOk($createResponse);
        
        // Get conversation ID
        $conversationId = $this->extractConversationId($createResponse['content']);
        
        // Create message
        $messageResponse = $this->makePostRequest('/edit_message.php', [
            'conversation_id' => $conversationId,
            'content' => $messageContent
        ]);
        
        $this->assertResponseOk($messageResponse);
        
        // Verify message appears in conversation
        $conversationHtml = $this->makeGetRequest('/view_conversation.php?id=' . $conversationId);
        $this->assertStringContains($messageContent, $conversationHtml['content']);
        
        // Clean up
        $this->deleteConversation($conversationId);
    }

    private function makePostRequest(string $endpoint, array $postData): array
    {
        $url = self::BASE_URL . $endpoint;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        
        return $this->makeRequest($curl);
    }

    private function makeGetRequest(string $endpoint): array
    {
        $url = self::BASE_URL . $endpoint;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        return $this->makeRequest($curl);
    }

    private function makeRequest($curl): array
    {
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($error) {
            throw new RuntimeException("Curl error: $error");
        }
        
        return [
            'http_code' => $httpCode,
            'content' => $response
        ];
    }

    private function extractConversationId(string $html): int
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $links = $doc->getElementsByTagName('a');
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, 'view_conversation.php?id=') !== false) {
                $parts = explode('=', $href);
                return (int)$parts[1];
            }
        }
        throw new RuntimeException('Could not find conversation ID');
    }

    private function deleteConversation(int $conversationId)
    {
        $deleteResponse = $this->makePostRequest('/edit_conversation.php', [
            'id' => $conversationId,
            'action' => 'delete'
        ]);
        $this->assertResponseOk($deleteResponse);
    }

    private function assertResponseOk(array $response)
    {
        if ($response['http_code'] !== 200) {
            throw new RuntimeException("Request failed with code {$response['http_code']}");
        }
    }

    private function assertStringContains(string $needle, string $haystack)
    {
        if (strpos($haystack, $needle) === false) {
            throw new RuntimeException("Expected string contains '$needle' but it was not found");
        }
    }
}
