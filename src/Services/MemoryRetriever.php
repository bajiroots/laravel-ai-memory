<?php

namespace LaravelAiMemory\Services;

use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Contracts\SimilaritySearch;
use LaravelAiMemory\Data\RetrievalResult;
use LaravelAiMemory\Repositories\EloquentMemoryRepository;
use LaravelAiMemory\Support\PromptContextFormatter;
use LaravelAiMemory\Support\TokenEstimator;

class MemoryRetriever
{
    public function __construct(
        private readonly EmbeddingProvider $embeddings,
        private readonly SimilaritySearch $search,
        private readonly PromptContextFormatter $formatter,
        private readonly TokenEstimator $tokenEstimator,
    ) {}

    public function retrieve(string $query, mixed $owner = null, array $options = []): RetrievalResult
    {
        $embedding = $this->embeddings->embed($query, $options)->embedding;
        $filters = array_merge(EloquentMemoryRepository::ownerAttributes($owner), $options['filters'] ?? []);

        if (! empty($options['types'])) {
            $filters['types'] = $options['types'];
        }

        $limit = (int) ($options['limit'] ?? config('ai-memory.retrieval.limit', 12));
        $maxTokens = (int) ($options['max_tokens'] ?? config('ai-memory.retrieval.max_tokens', 1500));
        $minimumScore = (float) ($options['minimum_score'] ?? config('ai-memory.retrieval.minimum_score', 0.55));

        $selected = collect();
        $tokens = 0;

        $this->search->searchMemories($embedding, $filters, $limit * 2)
            ->filter(fn ($candidate) => $candidate->score >= $minimumScore)
            ->each(function ($candidate) use ($selected, &$tokens, $maxTokens) {
                $cost = $this->tokenEstimator->estimate($candidate->memory->content);

                if ($tokens + $cost <= $maxTokens) {
                    $tokens += $cost;
                    $selected->push($candidate->memory);
                }
            });

        return new RetrievalResult(
            query: $query,
            memories: $selected->take($limit)->values(),
            total: $selected->count(),
            estimatedTokens: $tokens,
            formattedContext: $this->formatter->format($selected->take($limit)->values()),
        );
    }
}
