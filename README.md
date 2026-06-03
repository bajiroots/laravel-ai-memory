# Laravel Smart Thread Memory

Give your Laravel AI app a memory that survives the next chat window.

[Baca dalam Bahasa Indonesia](README.id.md)

Laravel Smart Thread Memory is a package for storing conversation memory, finding related threads, and pulling useful context back into your prompts. It is built for AI products where users jump between chats, projects, tickets, tasks, and decisions, but still expect the assistant to remember what came before.

It does not blindly merge conversations. It suggests related threads, shows confidence, and lets your app decide what happens next.

## Why This Exists

Most AI chat apps treat every new conversation like a fresh start.

That works until a user says:

> "Let's continue the Stripe invoice sync thing from last week."

The app may have the answer somewhere, but the assistant does not know where to look. Laravel Smart Thread Memory gives your app a semantic memory layer so old decisions, facts, todos, and technical notes can be found again when they matter.

## Why Laravel

Laravel teams are already building AI copilots, support agents, internal tools, CRM assistants, coding assistants, and workflow automation. The missing piece is often not the chat box. It is memory that fits normal Laravel applications:

- Eloquent models instead of a separate service.
- migrations your app owns.
- queues for expensive embedding work.
- config that can be published and reviewed.
- provider contracts for teams that need OpenAI today and local embeddings later.

This package aims to be that memory layer: boring enough for Laravel apps, semantic enough for AI products.

## What It Does

- Stores persistent AI memory in your Laravel database.
- Turns conversations into reusable memory records.
- Searches memory semantically with embeddings.
- Detects whether a new message is related to older threads.
- Returns confidence scores for related thread suggestions.
- Retrieves relevant context for prompt injection.
- Supports PostgreSQL + pgvector as the main vector store.
- Keeps embedding providers swappable through a clean interface.

## What It Does Not Do

- It does not ship a chat UI.
- It does not auto-merge threads behind your back.
- It does not pretend keyword search is the same as semantic memory.
- It does not lock you into one embedding provider forever.

## Current Status

Early MVP. The foundation is in place:

- Laravel package skeleton
- publishable config and migrations
- Eloquent models
- OpenAI embedding provider
- embedding provider contract
- pgvector search driver
- database fallback search
- thread detection
- context retrieval
- suggested merge service
- test baseline

The shape is here. The edges are still being sharpened.

## Installation

```bash
composer require smart-memory/laravel-smart-thread-memory

php artisan vendor:publish --tag=ai-memory-config
php artisan vendor:publish --tag=ai-memory-migrations
php artisan migrate
```

## Quick Start

Record a thread, store a message, and save a decision as memory:

```php
use LaravelAiMemory\Facades\AiMemory;

$thread = AiMemory::createThread([
    'owner_type' => 'user',
    'owner_id' => $user->id,
    'title' => 'Stripe invoice sync',
]);

$message = AiMemory::recordMessage($thread, [
    'role' => 'user',
    'content' => 'We decided to use Stripe webhooks for invoice sync.',
]);

AiMemory::remember([
    'thread_id' => $thread->id,
    'message_id' => $message->id,
    'owner_type' => 'user',
    'owner_id' => $user->id,
    'type' => 'decision',
    'content' => 'Invoice sync should be triggered by Stripe webhooks.',
    'importance' => 5,
]);
```

Later, when the user starts a new chat:

```php
$related = AiMemory::detectRelatedThreads(
    content: 'Can we continue invoice sync retries?',
    owner: ['owner_type' => 'user', 'owner_id' => $user->id],
);
```

Before sending a prompt to your AI provider:

```php
$context = AiMemory::retrieveContext(
    query: 'What did we decide about invoice sync?',
    owner: ['owner_type' => 'user', 'owner_id' => $user->id],
);

$prompt = <<<PROMPT
Use this prior context when it is relevant:

{$context->formattedContext}

User message:
{$message}
PROMPT;
```

## Memory Types

The MVP focuses on a small set of memory types that are useful in real products:

- `fact`
- `decision`
- `todo`
- `technical_context`
- `summary`

These are intentionally boring. Boring memory types are easier to query, rank, review, and trust.

## Suggested Threads, Not Forced Merges

The package is designed around suggestions:

```php
$related = AiMemory::detectRelatedThreads(
    content: $request->input('message'),
    owner: $request->user(),
);
```

Your app can then decide whether to:

- show "continue previous thread?"
- attach related context quietly
- start a clean thread
- let the user choose between multiple matches

The package gives you candidates and confidence. Product behavior stays yours.

## PostgreSQL + pgvector

PostgreSQL with pgvector is the primary target for semantic search.

Enable pgvector before running the migrations:

```sql
CREATE EXTENSION IF NOT EXISTS vector;
```

Then configure:

```env
AI_MEMORY_VECTOR_DRIVER=pgvector
AI_MEMORY_EMBEDDING_DIMENSIONS=1536
```

For local development or unsupported databases:

```env
AI_MEMORY_VECTOR_DRIVER=database
```

The fallback driver is useful for development and tests. It is not meant to compete with real vector search.

## Embeddings

OpenAI is the default provider, but the package uses a provider contract so you can swap it later:

```php
use LaravelAiMemory\Contracts\EmbeddingProvider;

$this->app->singleton(EmbeddingProvider::class, LocalEmbeddingProvider::class);
```

Default OpenAI config:

```env
OPENAI_API_KEY=
AI_MEMORY_EMBEDDING_PROVIDER=openai
AI_MEMORY_OPENAI_EMBEDDING_MODEL=text-embedding-3-small
```

## Testing

```bash
composer test
composer format
```

## Roadmap

Near-term:

- stronger pgvector integration tests
- better ranking for retrieval
- batch embedding pipeline
- LLM-assisted memory extraction
- thread summarization
- richer docs and examples

Later:

- more embedding providers
- hybrid search
- review/approval workflow for extracted memory
- demo Laravel app

## Documentation

Start with:

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Usage](docs/usage.md)
- [Examples](docs/examples.md)
- [Embeddings](docs/embeddings.md)
- [Thread Detection](docs/thread-detection.md)
- [Issue Backlog](docs/issue-backlog.md)
- [Release Checklist](docs/release-checklist.md)
- [Contributing](docs/contributing.md)

## Name

The package name is **Laravel Smart Thread Memory**. The idea behind it is simple: memory that helps an AI app notice when a new conversation belongs near an old one.

The name can still change. The problem is real either way.
