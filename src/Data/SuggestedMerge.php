<?php

namespace LaravelAiMemory\Data;

class SuggestedMerge
{
    public function __construct(
        public readonly string|int $sourceThreadId,
        public readonly string|int $targetThreadId,
        public readonly float $confidence,
        public readonly string $reason,
        public readonly array $matchedMemoryIds = [],
        public readonly array $metadata = [],
    ) {}
}
