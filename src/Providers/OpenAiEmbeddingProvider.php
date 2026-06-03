<?php

namespace LaravelAiMemory\Providers;

use Illuminate\Support\Facades\Http;
use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Data\EmbeddingResult;

class OpenAiEmbeddingProvider implements EmbeddingProvider
{
    public function embed(string $text, array $options = []): EmbeddingResult
    {
        $results = $this->embedBatch([$text], $options);

        return $results[0];
    }

    public function embedBatch(array $texts, array $options = []): array
    {
        $response = Http::withToken((string) config('ai-memory.providers.openai.api_key'))
            ->timeout((int) config('ai-memory.providers.openai.timeout', 30))
            ->post(rtrim((string) config('ai-memory.providers.openai.base_url'), '/').'/embeddings', [
                'model' => $options['model'] ?? $this->model(),
                'input' => array_values($texts),
            ])
            ->throw()
            ->json();

        return collect($response['data'] ?? [])
            ->sortBy('index')
            ->map(fn (array $item) => new EmbeddingResult(
                embedding: $item['embedding'],
                model: $response['model'] ?? $this->model(),
                provider: $this->providerName(),
                dimensions: count($item['embedding']),
                usage: $response['usage'] ?? null,
            ))
            ->values()
            ->all();
    }

    public function dimensions(): int
    {
        return (int) config('ai-memory.embeddings.dimensions', 1536);
    }

    public function model(): string
    {
        return (string) config('ai-memory.providers.openai.model', 'text-embedding-3-small');
    }

    public function providerName(): string
    {
        return 'openai';
    }
}
