<?php

declare(strict_types=1);

class ConversationHandler
{
    private \PDO $db;
    private $openAIClient;

    public function __construct(\PDO $db, $openAIClient)
    {
        $this->db = $db;
        $this->openAIClient = $openAIClient;
    }

    public function generateResponse(int $conversationId, string $model): string
    {
        $conversation = $this->getConversation($conversationId);
        $messages = $this->getMessages($conversationId);
        $formattedMessages = [];
        
        // Add system prompt if it exists
        if (!empty($conversation['system_prompt'])) {
            $formattedMessages[] = [
                'role' => 'system',
                'content' => $conversation['system_prompt']
            ];
        }
        
        // Add conversation messages
        foreach ($messages as $msg) {
            $formattedMessages[] = [
                'role' => $msg['type'],
                'content' => $msg['content']
            ];
        }
        
        try {
            $response = $this->openAIClient->chat()->create([
                'model' => $model,
                'messages' => $formattedMessages,
                'max_tokens' => 150,
            ]);
            
            return $response->choices[0]->message->content ?? '';
        } catch (\Exception $e) {
            // Fallback to completions if chat fails
            $prompt = implode("\n", array_map(fn($msg) => $msg['content'], $messages));
            $response = $this->openAIClient->completions()->create([
                'model' => $model,
                'prompt' => $prompt,
                'max_tokens' => 150,
            ]);
            
            return $response->choices[0]->text ?? '';
        }
    }

    public function createConversation(string $title, string $modelConfig, ?string $systemPrompt = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO conversation (title, model_config, system_prompt) VALUES (:title, :model_config, :system_prompt)');
        $stmt->execute([
            ':title' => $title,
            ':model_config' => $modelConfig,
            ':system_prompt' => $systemPrompt,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getConversation(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM conversation WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $conversation = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $conversation ?: null;
    }

    public function updateConversation(int $id, string $title, string $modelConfig, ?string $systemPrompt = null): bool
    {
        $stmt = $this->db->prepare('UPDATE conversation SET title = :title, model_config = :model_config, system_prompt = :system_prompt, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':model_config' => $modelConfig,
            ':system_prompt' => $systemPrompt,
        ]);
    }

    public function deleteConversation(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM conversation WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
    public function createMessage(int $conversationId, int $sequence, string $type, string $content): int
    {
        $stmt = $this->db->prepare('INSERT INTO message (conversation_id, sequence, type, content) VALUES (:conversation_id, :sequence, :type, :content)');
        $stmt->execute([
            ':conversation_id' => $conversationId,
            ':sequence' => $sequence,
            ':type' => $type,
            ':content' => $content,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getMessages(int $conversationId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM message WHERE conversation_id = :conversation_id ORDER BY sequence');
        $stmt->execute([':conversation_id' => $conversationId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateMessage(int $id, string $content): bool
    {
        $stmt = $this->db->prepare('UPDATE message SET content = :content, edited_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([
            ':id' => $id,
            ':content' => $content,
        ]);
    }

    public function deleteMessage(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM message WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
