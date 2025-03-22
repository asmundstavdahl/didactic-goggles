CREATE TABLE conversation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    model_config TEXT NOT NULL,
    system_prompt TEXT
);

CREATE TABLE message (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    conversation_id INTEGER NOT NULL,
    sequence INTEGER NOT NULL,
    type TEXT CHECK(type IN ('user', 'assistant', 'system', 'function')) NOT NULL,
    content TEXT NOT NULL,
    edited_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversation(id)
);
