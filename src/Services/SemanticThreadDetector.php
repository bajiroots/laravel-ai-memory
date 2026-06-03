<?php

namespace LaravelAiMemory\Services;

use Illuminate\Support\Collection;
use LaravelAiMemory\Contracts\ConfidenceScorer;
use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Contracts\SimilaritySearch;
use LaravelAiMemory\Contracts\ThreadDetector;
use LaravelAiMemory\Data\RelatedThread;
use LaravelAiMemory\Repositories\EloquentMemoryRepository;

class SemanticThreadDetector implements ThreadDetector
{
    public function __construct(
        private readonly EmbeddingProvider $embeddings,
        private readonly SimilaritySearch $search,
        private readonly ConfidenceScorer $scorer,
    ) {}

    public function detect(string $content, mixed $owner = null, array $options = []): Collection
    {
        $content = trim($content);

        if ($content === '') {
            return collect();
        }

        $embedding = $this->embeddings->embed($content, $options)->embedding;
        $filters = array_merge(EloquentMemoryRepository::ownerAttributes($owner), $options['filters'] ?? []);
        $limit = (int) ($options['limit'] ?? config('ai-memory.thread_detection.limit', 10));
        $minimumScore = (float) ($options['minimum_score'] ?? config('ai-memory.thread_detection.minimum_score', 0.55));

        return $this->search->searchMemories($embedding, $filters, $limit * 3)
            ->filter(fn ($candidate) => $candidate->memory->thread !== null)
            ->groupBy(fn ($candidate) => $candidate->memory->thread_id)
            ->map(function (Collection $candidates) use ($minimumScore, $options) {
                $score = (float) $candidates->avg('score');
                $confidence = $this->scorer->score($score, $candidates, $options);

                if ($confidence < $minimumScore) {
                    return null;
                }

                $thread = $candidates->first()->memory->thread;
                $count = $candidates->count();

                return new RelatedThread(
                    thread: $thread,
                    score: round($score, 4),
                    confidence: $confidence,
                    reason: "Matched {$count} related memories.",
                    matchedMemories: $candidates->pluck('memory')->values(),
                );
            })
            ->filter()
            ->sortByDesc('confidence')
            ->values()
            ->take($limit);
    }
}
