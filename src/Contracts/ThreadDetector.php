<?php

namespace LaravelAiMemory\Contracts;

use Illuminate\Support\Collection;

interface ThreadDetector
{
    public function detect(string $content, mixed $owner = null, array $options = []): Collection;
}
