<?php

namespace LaravelAiMemory\Data;

use LaravelAiMemory\Models\Memory;

class MemoryCandidate
{
    public function __construct(
        public readonly Memory $memory,
        public readonly float $score,
        public readonly array $metadata = [],
    ) {}
}
