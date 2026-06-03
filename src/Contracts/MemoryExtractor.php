<?php

namespace LaravelAiMemory\Contracts;

interface MemoryExtractor
{
    public function extract(string $content, array $options = []): array;
}
