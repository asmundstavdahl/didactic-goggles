<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/ConversationHandler.php';

require_once __DIR__ . '/../src/OpenAIClient.php';

$db = new PDO('sqlite:' . __DIR__ . '/../chatphp.db');

$config = require __DIR__ . '/../config.php';
$apiKey = $config['api_key'];
$openAIClient = new OpenAIClient($apiKey);
$conversationHandler = new ConversationHandler($db, $openAIClient);

?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ny samtale - ChatPHP</title>
    <style>
        .conversation-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .conversation-form__group {
            margin-bottom: 15px;
        }

        .conversation-form__label {
            display: block;
            margin-bottom: 5px;
        }

        .conversation-form__input,
        .conversation-form__textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .conversation-form__textarea {
            resize: vertical;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .button--secondary {
            background-color: #6c757d;
        }

        .button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <h1>Ny samtale</h1>
    <div class="conversation-form">
        <form class="conversation-form__form" action="handle_form.php?action=create" method="POST">
            <div class="conversation-form__group">
                <label class="conversation-form__label" for="title">Tittel:</label>
                <input class="conversation-form__input" type="text" id="title" name="title" required>
            </div>
            
            <div class="conversation-form__group">
                <label class="conversation-form__label" for="system_prompt">Systeminstruksjon:</label>
                <textarea class="conversation-form__textarea" id="system_prompt" name="system_prompt" rows="4"></textarea>
            </div>
            
            <div class="conversation-form__group">
                <label class="conversation-form__label" for="model_config">Modelkonfigurasjon:</label>
                <input class="conversation-form__input" type="text" id="model_config" name="model_config" value="gpt-3.5-turbo" required>
            </div>
            
            <div class="conversation-form__actions">
                <button type="submit" class="button">Opprett samtale</button>
                <a href="index.php" class="button button--secondary">Avbryt</a>
            </div>
        </form>
    </div>
</body>
</html>
