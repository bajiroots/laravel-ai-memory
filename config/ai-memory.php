<?php

return [
    'database' => [
        'connection' => env('AI_MEMORY_DB_CONNECTION'),
        'vector_driver' => env('AI_MEMORY_VECTOR_DRIVER', 'pgvector'),
    ],

    'embeddings' => [
        'provider' => env('AI_MEMORY_EMBEDDING_PROVIDER', 'openai'),
        'queue' => env('AI_MEMORY_EMBEDDING_QUEUE', true),
        'dimensions' => env('AI_MEMORY_EMBEDDING_DIMENSIONS', 1536),
    ],

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('AI_MEMORY_OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'timeout' => env('AI_MEMORY_OPENAI_TIMEOUT', 30),
        ],
    ],

    'thread_detection' => [
        'limit' => 10,
        'minimum_score' => 0.55,
        'related_threshold' => 0.70,
        'merge_threshold' => 0.85,
        'tie_margin' => 0.05,
        'recency_boost' => 0.05,
        'importance_boost' => 0.05,
    ],

    'retrieval' => [
        'limit' => 12,
        'max_tokens' => 1500,
        'minimum_score' => 0.55,
        'memory_type_priority' => [
            'decision' => 100,
            'technical_context' => 90,
            'todo' => 80,
            'fact' => 70,
            'summary' => 60,
        ],
    ],

    'memory' => [
        'default_importance' => 3,
        'store_message_embeddings' => true,
        'store_thread_summary_embeddings' => true,
        'store_suggestions' => true,
    ],

    'queues' => [
        'connection' => env('AI_MEMORY_QUEUE_CONNECTION'),
        'embedding_queue' => env('AI_MEMORY_EMBEDDING_QUEUE_NAME', 'default'),
        'summary_queue' => env('AI_MEMORY_SUMMARY_QUEUE_NAME', 'default'),
    ],
];
