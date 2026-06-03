<?php

namespace LaravelAiMemory\Contracts;

use LaravelAiMemory\Data\EmbeddingResult;

interface EmbeddingProvider
{
    public function embed(string $text, array $options = []): EmbeddingResult;

    public function embedBatch(array $texts, array $options = []): array;

    public function dimensions(): int;

    public function model(): string;

    public function providerName(): string;
}
