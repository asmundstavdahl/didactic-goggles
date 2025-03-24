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

$conversations = $db->query('SELECT * FROM conversation')->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatPHP</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>ChatPHP</h1>
    <h2>Samtaler</h2>
    <a href="create_conversation.php" class="button">Ny samtale</a>
    <?php foreach ($conversations as $conversation): ?>
        <div class="conversation">
            <h3><?php echo htmlspecialchars($conversation['title']); ?></h3>
            <p>Opprettet: <?php echo htmlspecialchars($conversation['created_at']); ?></p>
            <p>Sist oppdatert: <?php echo htmlspecialchars($conversation['updated_at']); ?></p>
            <a href="view_conversation.php?id=<?php echo $conversation['id']; ?>">Vis samtale</a>
            <form method="post" action="handle_form.php?action=delete_conversation" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $conversation['id']; ?>">
                <button type="submit" onclick="return confirm('Er du sikker på at du vil slette denne samtalen?')">Slett samtale</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
