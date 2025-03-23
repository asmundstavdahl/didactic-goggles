<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/ConversationHandler.php';

require_once __DIR__ . '/../src/OpenAIClient.php';

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');

$config = require __DIR__ . '/../config.php';
$apiKey = $config['api_key'] ?? '';

if (empty($apiKey)) {
    die('OpenAI API key is not configured. Please check your config.php file.');
}

$openAIClient = new OpenAIClient($apiKey);
$conversationHandler = new ConversationHandler($db, $openAIClient);

$conversationId = $_GET['id'] ?? null;
if (!$conversationId) {
    echo "Samtale-ID mangler.";
    exit;
}

$conversationId = (int)$conversationId;
$conversation = $conversationHandler->getConversation($conversationId);

if (!$conversation) {
    die("Samtale med ID $conversationId ble ikke funnet.");
}

$messages = $conversationHandler->getMessages($conversationId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message_id']) && isset($_POST['content'])) {
        // Handle message update
        $messageId = (int)$_POST['message_id'];
        $content = $_POST['content'];
        $conversationHandler->updateMessage($messageId, $content);
        header("Location: view_conversation.php?id=$conversationId");
        exit;
    } elseif (isset($_POST['send_message'])) {
        // Handle user message
        $userMessage = filter_input(INPUT_POST, 'user_message', FILTER_SANITIZE_STRING);
        if (!empty($userMessage)) {
            $conversationHandler->createMessage((int)$conversationId, count($messages) + 1, 'user', $userMessage);
            header("Location: view_conversation.php?id=$conversationId");
            exit;
        }
    } elseif (isset($_POST['generate_response'])) {
        // Handle response generation
        $model = $conversation['model_config'];
        $response = $conversationHandler->generateResponse((int)$conversationId, $model);
        $conversationHandler->createMessage((int)$conversationId, count($messages) + 1, 'assistant', $response);
        header("Location: view_conversation.php?id=$conversationId");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vis Samtale</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .message { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($conversation['title'] ?? 'Untitled Conversation'); ?></h1>
    <p><a href="edit_conversation.php?id=<?php echo $conversationId; ?>">Rediger samtaleinnstillinger</a></p>
    <h2>Meldinger</h2>
    <?php foreach ($messages as $message): ?>
        <div class="message">
            <p><strong>Type:</strong> <?php echo htmlspecialchars($message['type']); ?></p>
            <p><strong>Innhold:</strong> <?php echo $message['content']; ?></p>
            <p>
                <a href="edit_message.php?id=<?php echo $message['id']; ?>">Rediger</a>
                <form method="post" action="handle_form.php?action=delete_message" style="display: inline;">
                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                    <input type="hidden" name="conversation_id" value="<?php echo $conversationId; ?>">
                    <button type="submit" onclick="return confirm('Er du sikker på at du vil slette denne meldingen?')">Slett</button>
                </form>
            </p>
        </div>
    <?php endforeach; ?>
    <h2>Ny melding</h2>
    <form method="post" action="view_conversation.php?id=<?php echo $conversationId; ?>">
        <textarea name="user_message" rows="4" required></textarea>
        <button type="submit" name="send_message">Send melding</button>
    </form>
    
    <h2>KI-handlinger</h2>
    <form method="post" action="view_conversation.php?id=<?php echo $conversationId; ?>">
        <button type="submit" name="generate_response" value="1">Generer KI-respons</button>
    </form>
    
    <p><a href="index.php">Tilbake til samtaler</a></p>
</body>
</html>
