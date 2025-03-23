<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/ConversationHandler.php';

use OpenAI\OpenAI;

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');

$apiKey = 'your-api-key-here'; // Sett inn din faktiske API-nøkkel
$openAIClient = OpenAI::factory()->withApiKey($apiKey)->make();
$conversationHandler = new ConversationHandler($db, $openAIClient);

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
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
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
        </div>
    <?php endforeach; ?>
</body>
</html>
