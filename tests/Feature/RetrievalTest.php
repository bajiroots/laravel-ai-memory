<?php

namespace LaravelAiMemory\Tests\Feature;

use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Facades\AiMemory;
use LaravelAiMemory\Tests\FakeEmbeddingProvider;
use LaravelAiMemory\Tests\TestCase;

class RetrievalTest extends TestCase
{
    public function test_it_retrieves_prompt_context_with_fallback_search(): void
    {
        $this->app->singleton(EmbeddingProvider::class, FakeEmbeddingProvider::class);

        $thread = AiMemory::createThread([
            'owner_type' => 'user',
            'owner_id' => '1',
            'title' => 'Billing',
        ]);

        AiMemory::remember([
            'thread_id' => $thread->id,
            'owner_type' => 'user',
            'owner_id' => '1',
            'type' => 'decision',
            'content' => 'Invoice sync uses Stripe webhooks.',
            'importance' => 5,
        ]);

        $result = AiMemory::retrieveContext('How does invoice sync work?', [
            'owner_type' => 'user',
            'owner_id' => '1',
        ]);

        $this->assertSame(1, $result->total);
        $this->assertStringContainsString('Stripe webhooks', $result->formattedContext);
    }
}
