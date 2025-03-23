<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/ConversationHandler.php';

use OpenAI\OpenAI;

session_start();

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');

$apiKey = 'your-api-key-here';
$openAIClient = \OpenAI::factory()->withApiKey($apiKey)->make();
$conversationHandler = new ConversationHandler($db, $openAIClient);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_GET['action'] ?? '';
        
        if ($action === 'create') {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $systemPrompt = filter_input(INPUT_POST, 'system_prompt', FILTER_SANITIZE_STRING);
            $modelConfig = filter_input(INPUT_POST, 'model_config', FILTER_SANITIZE_STRING);

            if (empty($title) || empty($modelConfig)) {
                throw new InvalidArgumentException('Tittel og modelkonfigurasjon er påkrevd');
            }

            $conversationId = $conversationHandler->createConversation(
                $title,
                $modelConfig,
                !empty($systemPrompt) ? $systemPrompt : null
            );

            header("Location: view_conversation.php?id=$conversationId");
            exit();
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: create_conversation.php');
    exit();
}
