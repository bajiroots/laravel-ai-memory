<?php

namespace LaravelAiMemory\Data;

use Illuminate\Support\Collection;

class RetrievalResult
{
    public function __construct(
        public readonly string $query,
        public readonly Collection $memories,
        public readonly int $total,
        public readonly int $estimatedTokens,
        public readonly string $formattedContext,
        public readonly array $metadata = [],
    ) {}
}
