<?php

namespace LaravelAiMemory\Services;

use Illuminate\Support\Collection;
use LaravelAiMemory\Contracts\ConfidenceScorer;

class DefaultConfidenceScorer implements ConfidenceScorer
{
    public function score(float $similarity, Collection $memories, array $options = []): float
    {
        $score = $similarity;

        $importanceBoost = (float) ($options['importance_boost'] ?? config('ai-memory.thread_detection.importance_boost', 0.05));
        $recencyBoost = (float) ($options['recency_boost'] ?? config('ai-memory.thread_detection.recency_boost', 0.05));

        $maxImportance = (int) $memories->max(fn ($candidate) => $candidate->memory->importance ?? 0);
        if ($maxImportance >= 4) {
            $score += $importanceBoost;
        }

        $latest = $memories->max(fn ($candidate) => optional($candidate->memory->updated_at)->timestamp);
        if ($latest && $latest >= now()->subDays(30)->timestamp) {
            $score += $recencyBoost;
        }

        return round(min(1, max(0, $score)), 4);
    }
}
