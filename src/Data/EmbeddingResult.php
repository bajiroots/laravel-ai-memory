<?php

namespace LaravelAiMemory\Data;

class EmbeddingResult
{
    public function __construct(
        public readonly array $embedding,
        public readonly string $model,
        public readonly string $provider,
        public readonly int $dimensions,
        public readonly ?array $usage = null,
        public readonly array $metadata = [],
    ) {}
}
