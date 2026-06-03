# Embeddings

The package uses `LaravelAiMemory\Contracts\EmbeddingProvider`.

The default provider is OpenAI. Custom providers can be bound in the Laravel container:

```php
$this->app->singleton(
    \LaravelAiMemory\Contracts\EmbeddingProvider::class,
    \App\Ai\LocalEmbeddingProvider::class,
);
```
