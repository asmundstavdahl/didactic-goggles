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
    $title = $_POST['title'] ?? '';
    $modelConfig = $_POST['model_config'] ?? '';
    $systemPrompt = $_POST['system_prompt'] ?? '';
    
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
    <link rel="stylesheet" href="css/styles.css">
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
        
        <button type="submit" class="button" title="Lagre">
            <i class="fas fa-save"></i>
        </button>
        <a href="view_conversation.php?id=<?php echo $conversationId; ?>" class="button__secondary" title="Avbryt">
            <i class="fas fa-times"></i>
        </a>
    </form>
</body>
</html>
