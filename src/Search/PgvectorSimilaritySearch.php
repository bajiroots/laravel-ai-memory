<?php

namespace LaravelAiMemory\Search;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelAiMemory\Contracts\SimilaritySearch;
use LaravelAiMemory\Data\MemoryCandidate;
use LaravelAiMemory\Models\Memory;

class PgvectorSimilaritySearch implements SimilaritySearch
{
    public function searchMemories(array $embedding, array $filters = [], int $limit = 10): Collection
    {
        if (DB::connection(config('ai-memory.database.connection'))->getDriverName() !== 'pgsql') {
            return app(DatabaseFallbackSimilaritySearch::class)->searchMemories($embedding, $filters, $limit);
        }

        $vector = '['.implode(',', array_map(fn ($value) => (float) $value, $embedding)).']';

        $query = Memory::query()
            ->select('ai_memories.*')
            ->selectRaw('1 - (ai_memory_embeddings.embedding <=> ?::vector) as similarity', [$vector])
            ->join('ai_memory_embeddings', function ($join) {
                $join->on('ai_memory_embeddings.embeddable_id', '=', 'ai_memories.id')
                    ->where('ai_memory_embeddings.embeddable_type', '=', Memory::class);
            })
            ->with('thread')
            ->where(function ($query) {
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

        return $query->orderByDesc('similarity')
            ->limit($limit)
            ->get()
            ->map(fn (Memory $memory) => new MemoryCandidate(
                memory: $memory,
                score: (float) $memory->getAttribute('similarity'),
                metadata: ['driver' => 'pgvector'],
            ));
    }
}
