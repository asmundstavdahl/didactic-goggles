<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/ConversationHandler.php';

require_once __DIR__ . '/../src/OpenAIClient.php';

session_start();

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');

$config = require __DIR__ . '/../config.php';
$apiKey = $config['api_key'];
$openAIClient = new OpenAIClient($apiKey);
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
        } elseif ($action === 'delete_conversation') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new InvalidArgumentException('Ugyldig samtale-ID');
            }
            
            $success = $conversationHandler->deleteConversation($id);
            if (!$success) {
                throw new RuntimeException('Kunne ikke slette samtalen');
            }
            
            header("Location: index.php");
            exit();
        } elseif ($action === 'delete_message') {
            $messageId = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);
            $conversationId = filter_input(INPUT_POST, 'conversation_id', FILTER_VALIDATE_INT);
            if (!$messageId || !$conversationId) {
                throw new InvalidArgumentException('Ugyldig meldings-ID eller samtale-ID');
            }
            
            $success = $conversationHandler->deleteMessage($messageId);
            if (!$success) {
                throw new RuntimeException('Kunne ikke slette meldingen');
            }
            
            header("Location: view_conversation.php?id=$conversationId");
            exit();
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: create_conversation.php');
    exit();
}
