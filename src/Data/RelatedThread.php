<?php

namespace LaravelAiMemory\Data;

use Illuminate\Support\Collection;
use LaravelAiMemory\Models\MemoryThread;

class RelatedThread
{
    public function __construct(
        public readonly MemoryThread $thread,
        public readonly float $score,
        public readonly float $confidence,
        public readonly string $reason,
        public readonly Collection $matchedMemories,
    ) {}
}
