<?php

namespace LaravelAiMemory\Contracts;

use Illuminate\Support\Collection;

interface ConfidenceScorer
{
    public function score(float $similarity, Collection $memories, array $options = []): float;
}
