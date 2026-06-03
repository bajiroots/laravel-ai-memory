<?php

namespace LaravelAiMemory\Tests\Feature;

use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Facades\AiMemory;
use LaravelAiMemory\Tests\FakeEmbeddingProvider;
use LaravelAiMemory\Tests\TestCase;

class ThreadDetectionTest extends TestCase
{
    public function test_it_returns_related_threads_for_the_same_owner(): void
    {
        $this->app->singleton(EmbeddingProvider::class, FakeEmbeddingProvider::class);

        $thread = AiMemory::createThread([
            'owner_type' => 'user',
            'owner_id' => '1',
            'title' => 'Stripe invoice sync',
        ]);

        AiMemory::remember([
            'thread_id' => $thread->id,
            'owner_type' => 'user',
            'owner_id' => '1',
            'type' => 'decision',
            'content' => 'Invoice sync uses Stripe webhooks.',
            'importance' => 5,
        ]);

        AiMemory::remember([
            'owner_type' => 'user',
            'owner_id' => '2',
            'type' => 'decision',
            'content' => 'Another user has unrelated memory.',
            'importance' => 5,
        ]);

        $related = AiMemory::detectRelatedThreads(
            content: 'Can we continue invoice sync?',
            owner: ['owner_type' => 'user', 'owner_id' => '1'],
        );

        $this->assertCount(1, $related);
        $this->assertTrue($thread->is($related->first()->thread));
        $this->assertGreaterThanOrEqual(0.55, $related->first()->confidence);
    }

    public function test_it_returns_no_threads_when_content_is_empty(): void
    {
        $related = AiMemory::detectRelatedThreads('   ');

        $this->assertCount(0, $related);
    }
}
