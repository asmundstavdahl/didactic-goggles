<?php

declare(strict_types=1);

class ConversationHandler
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
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
}
