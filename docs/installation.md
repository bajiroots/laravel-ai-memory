# Installation

```bash
composer require smart-memory/laravel-smart-thread-memory

php artisan vendor:publish --tag=ai-memory-config
php artisan vendor:publish --tag=ai-memory-migrations
php artisan migrate
```

For PostgreSQL vector search, install and enable pgvector first:

```sql
CREATE EXTENSION IF NOT EXISTS vector;
```
