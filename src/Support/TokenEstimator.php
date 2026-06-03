<?php

namespace LaravelAiMemory\Support;

class TokenEstimator
{
    public function estimate(string $content): int
    {
        return max(1, (int) ceil(strlen($content) / 4));
    }
}
