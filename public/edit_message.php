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

$messageId = $_GET['id'] ?? null;
if (!$messageId) {
    die("Meldings-ID mangler.");
}

$message = $conversationHandler->getMessage((int)$messageId);
if (!$message) {
    die("Melding med ID $messageId ble ikke funnet.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    if (!empty($content)) {
        $conversationHandler->updateMessage((int)$messageId, $content);
        header("Location: view_conversation.php?id={$message['conversation_id']}");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rediger Melding</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Rediger Melding</h1>
    
    <form method="post">
        <div class="form-group">
            <textarea name="content"><?php echo $message['content']; ?></textarea>
        </div>
        
        <button type="submit" class="button">Lagre endringer</button>
        <a href="view_conversation.php?id=<?php echo $message['conversation_id']; ?>" class="button__secondary">Avbryt</a>
    </form>
</body>
</html>
