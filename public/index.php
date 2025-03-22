<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/ConversationHandler.php';

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');
$conversationHandler = new ConversationHandler($db);

$conversations = $db->query('SELECT * FROM conversation')->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatPHP</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .conversation { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>ChatPHP</h1>
    <h2>Samtaler</h2>
    <?php foreach ($conversations as $conversation): ?>
        <div class="conversation">
            <h3><?php echo htmlspecialchars($conversation['title']); ?></h3>
            <p>Opprettet: <?php echo htmlspecialchars($conversation['created_at']); ?></p>
            <p>Sist oppdatert: <?php echo htmlspecialchars($conversation['updated_at']); ?></p>
            <a href="view_conversation.php?id=<?php echo $conversation['id']; ?>">Vis samtale</a>
        </div>
    <?php endforeach; ?>
</body>
</html>
