<?php

namespace LaravelAiMemory\Tests\Feature;

use LaravelAiMemory\Facades\AiMemory;
use LaravelAiMemory\Tests\TestCase;

class MemoryRepositoryTest extends TestCase
{
    public function test_it_records_threads_messages_and_memories(): void
    {
        $thread = AiMemory::createThread([
            'owner_type' => 'user',
            'owner_id' => '1',
            'title' => 'Stripe invoice sync',
        ]);

        $message = AiMemory::recordMessage($thread, [
            'role' => 'user',
            'content' => 'Use Stripe webhooks.',
        ]);

        $memory = AiMemory::remember([
            'thread_id' => $thread->id,
            'message_id' => $message->id,
            'owner_type' => 'user',
            'owner_id' => '1',
            'type' => 'decision',
            'content' => 'Invoice sync uses Stripe webhooks.',
            'importance' => 5,
        ]);

        $this->assertDatabaseHas('ai_memory_threads', ['id' => $thread->id]);
        $this->assertDatabaseHas('ai_memory_messages', ['id' => $message->id]);
        $this->assertDatabaseHas('ai_memories', ['id' => $memory->id]);
    }
}
