# Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=ai-memory-config
```

Important environment variables:

```env
AI_MEMORY_VECTOR_DRIVER=pgvector
AI_MEMORY_EMBEDDING_PROVIDER=openai
AI_MEMORY_OPENAI_EMBEDDING_MODEL=text-embedding-3-small
AI_MEMORY_EMBEDDING_DIMENSIONS=1536
OPENAI_API_KEY=
```

Use `AI_MEMORY_VECTOR_DRIVER=database` for the fallback search driver.
