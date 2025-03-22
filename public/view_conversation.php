<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/ConversationHandler.php';

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');
$conversationHandler = new ConversationHandler($db);

$conversationId = $_GET['id'] ?? null;
if (!$conversationId) {
    echo "Samtale-ID mangler.";
    exit;
}

$conversation = $conversationHandler->getConversation((int)$conversationId);
$messages = $conversationHandler->getMessages((int)$conversationId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageId = (int)$_POST['message_id'];
    $content = $_POST['content'];
    $conversationHandler->updateMessage($messageId, $content);
    header("Location: view_conversation.php?id=$conversationId");
    exit;
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
    <h1><?php echo htmlspecialchars($conversation['title']); ?></h1>
    <h2>Meldinger</h2>
    <?php foreach ($messages as $message): ?>
        <div class="message">
            <p><strong>Type:</strong> <?php echo htmlspecialchars($message['type']); ?></p>
            <p><strong>Innhold:</strong> <?php echo htmlspecialchars($message['content']); ?></p>
            <form method="post">
                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                <textarea name="content"><?php echo htmlspecialchars($message['content']); ?></textarea>
                <button type="submit">Oppdater</button>
            </form>
        </div>
    <?php endforeach; ?>
    <a href="index.php">Tilbake til samtaler</a>
</body>
</html>
