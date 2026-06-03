<?php

namespace LaravelAiMemory\Contracts;

use Illuminate\Support\Collection;

interface SimilaritySearch
{
    public function searchMemories(array $embedding, array $filters = [], int $limit = 10): Collection;
}
