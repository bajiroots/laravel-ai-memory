<?php

namespace LaravelAiMemory\Tests\Feature;

use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Facades\AiMemory;
use LaravelAiMemory\Tests\FakeEmbeddingProvider;
use LaravelAiMemory\Tests\TestCase;

class SuggestedMergeTest extends TestCase
{
    public function test_it_suggests_a_merge_for_a_strong_related_thread(): void
    {
        $this->app->singleton(EmbeddingProvider::class, FakeEmbeddingProvider::class);

        $existingThread = AiMemory::createThread([
            'owner_type' => 'user',
            'owner_id' => '1',
            'title' => 'Stripe invoice sync',
            'summary' => 'Stripe invoice sync uses webhooks.',
        ]);

        AiMemory::remember([
            'thread_id' => $existingThread->id,
            'owner_type' => 'user',
            'owner_id' => '1',
            'type' => 'decision',
            'content' => 'Invoice sync uses Stripe webhooks.',
            'importance' => 5,
        ]);

        $newThread = AiMemory::createThread([
            'owner_type' => 'user',
            'owner_id' => '1',
            'title' => 'Continue invoice sync retries',
            'summary' => 'Retry handling for Stripe invoice sync.',
        ]);

        $suggestion = AiMemory::suggestMerge($newThread, [
            'merge_threshold' => 0.60,
            'minimum_score' => 0.50,
        ]);

        $this->assertNotNull($suggestion);
        $this->assertSame($newThread->id, $suggestion->sourceThreadId);
        $this->assertSame($existingThread->id, $suggestion->targetThreadId);
        $this->assertDatabaseHas('ai_memory_thread_links', [
            'source_thread_id' => $newThread->id,
            'target_thread_id' => $existingThread->id,
            'status' => 'suggested',
        ]);
    }
}
