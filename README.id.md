# Laravel Smart Thread Memory

Berikan aplikasi AI Laravel kamu memory yang tetap hidup setelah user membuka chat baru.

Laravel Smart Thread Memory adalah package untuk menyimpan memory percakapan, menemukan thread yang berhubungan, dan mengambil konteks lama yang relevan untuk dimasukkan ke prompt AI. Package ini dibuat untuk produk AI ketika user berpindah-pindah antara chat, proyek, tiket, task, dan keputusan teknis, tetapi tetap berharap assistant memahami konteks sebelumnya.

Package ini tidak menggabungkan percakapan secara membabi buta. Ia memberikan saran thread terkait, confidence score, dan membiarkan aplikasi kamu menentukan langkah berikutnya.

## Kenapa Ini Dibuat

Banyak aplikasi chat AI memperlakukan setiap percakapan baru seperti mulai dari nol.

Itu terasa baik-baik saja sampai user berkata:

> "Lanjutkan yang invoice sync Stripe minggu lalu."

Konteksnya mungkin ada di database, tetapi assistant tidak tahu harus mencari ke mana. Laravel Smart Thread Memory memberi aplikasi kamu lapisan semantic memory agar keputusan lama, fakta, todo, dan catatan teknis bisa ditemukan lagi saat dibutuhkan.

## Kenapa Laravel

Tim Laravel sudah banyak membangun AI copilot, support agent, internal tool, CRM assistant, coding assistant, dan workflow automation. Sering kali bagian yang kurang bukan chat box-nya, tetapi memory yang cocok dengan cara aplikasi Laravel bekerja:

- Eloquent model, bukan service terpisah yang asing.
- migration yang dimiliki aplikasi.
- queue untuk pekerjaan mahal seperti embedding.
- config yang bisa dipublish dan direview.
- provider contract untuk tim yang memakai OpenAI hari ini dan mungkin local embedding nanti.

Target package ini sederhana: cukup membosankan untuk nyaman dipakai di aplikasi Laravel, cukup semantik untuk berguna di produk AI.

## Fitur Utama

- Menyimpan persistent AI memory di database Laravel.
- Mengubah percakapan menjadi memory yang bisa digunakan ulang.
- Mencari memory secara semantik dengan embeddings.
- Mendeteksi apakah pesan baru berkaitan dengan thread lama.
- Mengembalikan confidence score untuk suggested thread.
- Mengambil relevant context untuk prompt AI.
- Mendukung PostgreSQL + pgvector sebagai vector store utama.
- Menyediakan embedding provider contract agar provider bisa diganti.

## Yang Bukan Tujuan Package Ini

- Tidak menyediakan UI chat bawaan.
- Tidak melakukan auto-merge thread tanpa keputusan aplikasi.
- Tidak menganggap keyword search sama dengan semantic memory.
- Tidak mengunci aplikasi ke satu embedding provider selamanya.

## Status Saat Ini

Early MVP. Fondasi awal sudah tersedia:

- Laravel package skeleton
- publishable config dan migrations
- Eloquent models
- OpenAI embedding provider
- embedding provider contract
- pgvector search driver
- database fallback search
- thread detection
- context retrieval
- suggested merge service
- test baseline

Bentuk besarnya sudah ada. Detailnya masih perlu diasah bersama.

## Instalasi

```bash
composer require smart-memory/laravel-smart-thread-memory

php artisan vendor:publish --tag=ai-memory-config
php artisan vendor:publish --tag=ai-memory-migrations
php artisan migrate
```

## Quick Start

Simpan thread, catat message, lalu simpan sebuah keputusan sebagai memory:

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

Saat user membuka chat baru:

```php
$related = AiMemory::detectRelatedThreads(
    content: 'Can we continue invoice sync retries?',
    owner: ['owner_type' => 'user', 'owner_id' => $user->id],
);
```

Sebelum mengirim prompt ke AI provider:

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

## Tipe Memory

MVP memakai tipe memory kecil yang sering muncul di produk nyata:

- `fact`
- `decision`
- `todo`
- `technical_context`
- `summary`

Tipe-tipe ini sengaja dibuat sederhana. Memory yang sederhana lebih mudah dicari, diranking, direview, dan dipercaya.

## Suggested Thread, Bukan Forced Merge

Package ini dirancang dengan prinsip suggestion:

```php
$related = AiMemory::detectRelatedThreads(
    content: $request->input('message'),
    owner: $request->user(),
);
```

Aplikasi kamu bisa memutuskan apakah akan:

- menampilkan "lanjutkan thread sebelumnya?"
- menyisipkan konteks terkait secara diam-diam
- membuat thread baru
- meminta user memilih jika ada beberapa kandidat kuat

Package memberi kandidat dan confidence. Keputusan produk tetap ada di aplikasi.

## PostgreSQL + pgvector

PostgreSQL dengan pgvector adalah target utama untuk semantic search.

Aktifkan pgvector sebelum menjalankan migration:

```sql
CREATE EXTENSION IF NOT EXISTS vector;
```

Lalu konfigurasi:

```env
AI_MEMORY_VECTOR_DRIVER=pgvector
AI_MEMORY_EMBEDDING_DIMENSIONS=1536
```

Untuk local development atau database yang belum mendukung vector search:

```env
AI_MEMORY_VECTOR_DRIVER=database
```

Fallback driver berguna untuk development dan test. Ia tidak dimaksudkan untuk menggantikan vector search sungguhan.

## Embeddings

OpenAI adalah provider default, tetapi package memakai provider contract agar bisa diganti:

```php
use LaravelAiMemory\Contracts\EmbeddingProvider;

$this->app->singleton(EmbeddingProvider::class, LocalEmbeddingProvider::class);
```

Config OpenAI default:

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

Dalam waktu dekat:

- integration test pgvector yang lebih kuat
- ranking retrieval yang lebih baik
- batch embedding pipeline
- memory extraction berbasis LLM
- thread summarization
- dokumentasi dan contoh yang lebih lengkap

Setelah itu:

- embedding provider tambahan
- hybrid search
- workflow review/approval untuk extracted memory
- demo Laravel app

## Dokumentasi

Mulai dari:

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Usage](docs/usage.md)
- [Examples](docs/examples.md)
- [Embeddings](docs/embeddings.md)
- [Thread Detection](docs/thread-detection.md)
- [Issue Backlog](docs/issue-backlog.md)
- [Release Checklist](docs/release-checklist.md)
- [Contributing](docs/contributing.md)
- [Publish ke GitHub](docs/publish-github.md)

## Nama

Nama package ini adalah **Laravel Smart Thread Memory**. Ide di baliknya sederhana: memory yang membantu aplikasi AI menyadari ketika percakapan baru sebenarnya masih dekat dengan percakapan lama.

Namanya masih bisa berubah. Masalah yang ingin diselesaikan tetap nyata.
