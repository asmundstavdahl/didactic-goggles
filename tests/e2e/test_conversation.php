<?php
declare(strict_types=1);

const BASE_URL = 'http://localhost:8000';

function makePostRequest(string $endpoint, array $postData): array
{
    $url = BASE_URL . $endpoint;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
    
    return makeRequest($curl);
}

function makeGetRequest(string $endpoint): array
{
    $url = BASE_URL . $endpoint;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    return makeRequest($curl);
}

function makeRequest($curl): array
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

function extractConversationId(string $html): int
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

function deleteConversation(int $conversationId)
{
    $deleteResponse = makePostRequest('/edit_conversation.php', [
        'id' => $conversationId,
        'action' => 'delete'
    ]);
    assertResponseOk($deleteResponse);
}

function assertResponseOk(array $response)
{
    if ($response['http_code'] !== 200) {
        throw new RuntimeException("Request failed with code {$response['http_code']}");
    }
}

function assertStringContains(string $needle, string $haystack)
{
    if (strpos($haystack, $needle) === false) {
        throw new RuntimeException("Expected string contains '$needle' but it was not found");
    }
}

function runTests()
{
    $conversationTitle = 'Test Conversation';
    $messageContent = 'Hello, world!';

    // Create conversation
    $createResponse = makePostRequest('/create_conversation.php', [
        'title' => $conversationTitle,
        'model_config' => 'gpt-3.5-turbo'
    ]);
    
    assertResponseOk($createResponse);
    
    // Get conversation ID
    $conversationId = extractConversationId($createResponse['content']);
    
    // Create message
    $messageResponse = makePostRequest('/edit_message.php', [
        'conversation_id' => $conversationId,
        'content' => $messageContent
    ]);
    
    assertResponseOk($messageResponse);
    
    // Verify message appears in conversation
    $conversationHtml = makeGetRequest('/view_conversation.php?id=' . $conversationId);
    assertStringContains($messageContent, $conversationHtml['content']);
    
    // Clean up
    deleteConversation($conversationId);
}

// Run tests when script is executed
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    try {
        runTests();
        echo "All tests passed successfully!\n";
    } catch (Throwable $e) {
        echo "Test failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}
