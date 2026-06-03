<?php

namespace LaravelAiMemory\Tests;

use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Data\EmbeddingResult;

class FakeEmbeddingProvider implements EmbeddingProvider
{
    public function embed(string $text, array $options = []): EmbeddingResult
    {
        return new EmbeddingResult(
            embedding: [0.1, 0.2, 0.3],
            model: $this->model(),
            provider: $this->providerName(),
            dimensions: $this->dimensions(),
        );
    }

    public function embedBatch(array $texts, array $options = []): array
    {
        return array_map(fn (string $text) => $this->embed($text, $options), $texts);
    }

    public function dimensions(): int
    {
        return 3;
    }

    public function model(): string
    {
        return 'fake-embedding';
    }

    public function providerName(): string
    {
        return 'fake';
    }
}
