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

$conversation = $conversationHandler->getConversation((int)$conversationId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $modelConfig = filter_input(INPUT_POST, 'model_config', FILTER_SANITIZE_STRING);
    $systemPrompt = filter_input(INPUT_POST, 'system_prompt', FILTER_SANITIZE_STRING);
    
    if ($title && $modelConfig) {
        $conversationHandler->updateConversation(
            (int)$conversationId,
            $title,
            $modelConfig,
            $systemPrompt
        );
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
    <title>Rediger Samtale</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        textarea { height: 150px; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Rediger Samtale</h1>
    
    <form method="post">
        <div class="form-group">
            <label for="title">Tittel:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($conversation['title'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="model_config">Modellkonfigurasjon:</label>
            <input type="text" id="model_config" name="model_config" value="<?php echo htmlspecialchars($conversation['model_config'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="system_prompt">Systemprompt:</label>
            <textarea id="system_prompt" name="system_prompt"><?php echo htmlspecialchars($conversation['system_prompt'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" class="button">Lagre endringer</button>
        <a href="view_conversation.php?id=<?php echo $conversationId; ?>" class="button">Avbryt</a>
    </form>
</body>
</html>
