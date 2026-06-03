<?php

namespace LaravelAiMemory\Search;

use Illuminate\Support\Collection;
use LaravelAiMemory\Contracts\SimilaritySearch;
use LaravelAiMemory\Data\MemoryCandidate;
use LaravelAiMemory\Models\Memory;

class DatabaseFallbackSimilaritySearch implements SimilaritySearch
{
    public function searchMemories(array $embedding, array $filters = [], int $limit = 10): Collection
    {
        $query = Memory::query()->with('thread')->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });

        if (isset($filters['owner_type'], $filters['owner_id'])) {
            $query->where('owner_type', $filters['owner_type'])->where('owner_id', (string) $filters['owner_id']);
        }

        if (! empty($filters['types'])) {
            $query->whereIn('type', $filters['types']);
        }

        if (! empty($filters['thread_id'])) {
            $query->where('thread_id', $filters['thread_id']);
        }

        return $query->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Memory $memory) => new MemoryCandidate($memory, 0.55, ['driver' => 'database_fallback']));
    }
}
