<?php

namespace LaravelAiMemory\Support;

class ContentHasher
{
    public function hash(string $content): string
    {
        return hash('sha256', trim(preg_replace('/\s+/', ' ', $content) ?? $content));
    }
}
