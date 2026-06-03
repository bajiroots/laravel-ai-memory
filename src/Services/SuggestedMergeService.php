<?php

namespace LaravelAiMemory\Services;

use LaravelAiMemory\Data\SuggestedMerge;
use LaravelAiMemory\Models\MemoryThread;
use LaravelAiMemory\Models\MemoryThreadLink;

class SuggestedMergeService
{
    public function __construct(
        private readonly SemanticThreadDetector $detector,
    ) {}

    public function suggest(MemoryThread $thread, array $options = []): ?SuggestedMerge
    {
        $content = trim(($thread->title ?? '')."\n".($thread->summary ?? ''));

        if ($content === '') {
            return null;
        }

        $related = $this->detector->detect($content, [
            'owner_type' => $thread->owner_type,
            'owner_id' => $thread->owner_id,
        ], $options)->reject(fn ($candidate) => $candidate->thread->is($thread))->values();

        $top = $related->first();

        if (! $top) {
            return null;
        }

        $mergeThreshold = (float) ($options['merge_threshold'] ?? config('ai-memory.thread_detection.merge_threshold', 0.85));
        $tieMargin = (float) ($options['tie_margin'] ?? config('ai-memory.thread_detection.tie_margin', 0.05));
        $second = $related->get(1);

        if ($top->confidence < $mergeThreshold || ($second && ($top->confidence - $second->confidence) < $tieMargin)) {
            return null;
        }

        $suggestion = new SuggestedMerge(
            sourceThreadId: $thread->getKey(),
            targetThreadId: $top->thread->getKey(),
            confidence: $top->confidence,
            reason: $top->reason,
            matchedMemoryIds: $top->matchedMemories->pluck('id')->all(),
        );

        if (config('ai-memory.memory.store_suggestions', true)) {
            MemoryThreadLink::query()->create([
                'source_thread_id' => $suggestion->sourceThreadId,
                'target_thread_id' => $suggestion->targetThreadId,
                'relationship' => 'continuation',
                'score' => $suggestion->confidence,
                'status' => 'suggested',
                'metadata' => [
                    'reason' => $suggestion->reason,
                    'matched_memory_ids' => $suggestion->matchedMemoryIds,
                ],
            ]);
        }

        return $suggestion;
    }
}
