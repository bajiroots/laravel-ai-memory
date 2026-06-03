<?php

namespace LaravelAiMemory\Tests\Feature;

use LaravelAiMemory\Jobs\GenerateEmbeddingJob;
use LaravelAiMemory\Models\Memory;
use LaravelAiMemory\Models\MemoryEmbedding;
use LaravelAiMemory\Support\ContentHasher;
use LaravelAiMemory\Tests\FakeEmbeddingProvider;
use LaravelAiMemory\Tests\TestCase;

class GenerateEmbeddingJobTest extends TestCase
{
    public function test_it_generates_an_embedding_once_per_content_hash(): void
    {
        $provider = new FakeEmbeddingProvider;

        $memory = Memory::query()->create([
            'owner_type' => 'user',
            'owner_id' => '1',
            'type' => 'decision',
            'content' => 'Invoice sync uses Stripe webhooks.',
            'importance' => 5,
        ]);

        $job = new GenerateEmbeddingJob(
            embeddableType: Memory::class,
            embeddableId: $memory->id,
            content: $memory->content,
        );

        $job->handle($provider, new ContentHasher);
        $job->handle($provider, new ContentHasher);

        $this->assertDatabaseCount('ai_memory_embeddings', 1);

        $embedding = MemoryEmbedding::query()->firstOrFail();

        $this->assertSame(Memory::class, $embedding->embeddable_type);
        $this->assertSame($memory->id, $embedding->embeddable_id);
        $this->assertSame('fake', $embedding->provider);
        $this->assertSame([0.1, 0.2, 0.3], $embedding->embedding_json);
    }
}
