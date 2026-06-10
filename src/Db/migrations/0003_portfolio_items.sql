CREATE TABLE IF NOT EXISTS portfolio_items (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    title       TEXT NOT NULL,
    period      TEXT,
    role        TEXT,
    body        TEXT,
    tags        TEXT,
    url         TEXT,
    position    INTEGER NOT NULL DEFAULT 0,
    created_at  INTEGER NOT NULL,
    updated_at  INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_portfolio_position ON portfolio_items(position);

INSERT INTO portfolio_items (title, period, role, body, tags, url, position, created_at, updated_at)
SELECT * FROM (
    SELECT 'streaming-chat-agent' AS title, '2026' AS period, 'Builder' AS role,
           'Token-streaming chat agent with server-sent events, tool calls, and short-term conversation memory. A playground for real-time LLM UX — backpressure, retries, and clean cancellation.' AS body,
           '["TypeScript","LLM","SSE"]' AS tags,
           'https://github.com/soubihabdenour/streaming-chat-agent' AS url,
           10 AS position, strftime('%s','now') AS created_at, strftime('%s','now') AS updated_at
    UNION ALL SELECT 'ai-entity-extractor','2026','Builder',
           'Structured field extraction from unstructured text. Prompts an LLM to pull named entities, normalizes them against a schema, and emits clean JSON for downstream pipelines.',
           '["Python","LLM","NLP"]','https://github.com/soubihabdenour/ai-entity-extractor',20,strftime('%s','now'),strftime('%s','now')
    UNION ALL SELECT 'rag-mini-demo','2026','Builder',
           'Compact retrieval-augmented generation demo: chunking, embeddings, vector search, and grounded LLM responses. Built as a learning surface for retrieval patterns and prompt design.',
           '["JavaScript","RAG","Embeddings"]','https://github.com/soubihabdenour/rag-mini-demo',30,strftime('%s','now'),strftime('%s','now')
    UNION ALL SELECT 'HARFED — Federated Learning research','2024–2026','Lead researcher · SKKU InfoLab',
           'Master''s research on heterogeneity, attacks, and robustness in federated learning. PyTorch + Flower framework, evaluating privacy attacks and defenses on cross-device FL.',
           '["Python","PyTorch","Flower","Privacy"]','https://github.com/soubihabdenour/harfed',40,strftime('%s','now'),strftime('%s','now')
    UNION ALL SELECT 'sympto-php','2026','Builder',
           'PHP backend for an AI symptom-triage interface. Conversational endpoint, prompt structuring, and a clean REST surface for any frontend to plug into.',
           '["PHP","LLM","REST"]','https://github.com/soubihabdenour/sympto-php',50,strftime('%s','now'),strftime('%s','now')
    UNION ALL SELECT 'strava-coach','2026','Builder',
           'Strava integration that turns ride telemetry into structured coaching feedback. OAuth flow, activity ingestion, and rule-based training cues.',
           '["PHP","Strava API","OAuth"]','https://github.com/soubihabdenour/strava-coach',60,strftime('%s','now'),strftime('%s','now')
    UNION ALL SELECT 'arabic-for-korean','2026','Solo builder',
           'Web app teaching Modern Standard Arabic to Korean speakers — alphabet, vocabulary, daily phrases, and beginner grammar, with a quiz layer. PHP-only, no database; lessons authored as plain arrays.',
           '["PHP","Arabic","Korean","Education"]','https://github.com/soubihabdenour/arabic-for-korean',70,strftime('%s','now'),strftime('%s','now')
    UNION ALL SELECT 'Greenhouse','2026','Contributor',
           'Greenhouse monitoring system in C++ — environmental sensing, control loops, and configuration handling for an automated cultivation setup.',
           '["C++","Embedded","Sensors"]','https://github.com/soubihabdenour/Greenhouse',80,strftime('%s','now'),strftime('%s','now')
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items);
