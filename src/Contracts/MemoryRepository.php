<?php

namespace LaravelAiMemory\Contracts;

use LaravelAiMemory\Models\Memory;
use LaravelAiMemory\Models\MemoryMessage;
use LaravelAiMemory\Models\MemoryThread;

interface MemoryRepository
{
    public function createThread(array $attributes): MemoryThread;

    public function recordMessage(MemoryThread|string|int $thread, array $attributes): MemoryMessage;

    public function remember(array $attributes): Memory;
}
