<?php

namespace LaravelAiMemory\Repositories;

use Illuminate\Database\Eloquent\Model;
use LaravelAiMemory\Contracts\MemoryRepository;
use LaravelAiMemory\Models\Memory;
use LaravelAiMemory\Models\MemoryMessage;
use LaravelAiMemory\Models\MemoryThread;

class EloquentMemoryRepository implements MemoryRepository
{
    public function createThread(array $attributes): MemoryThread
    {
        return MemoryThread::query()->create($attributes);
    }

    public function recordMessage(MemoryThread|string|int $thread, array $attributes): MemoryMessage
    {
        $thread = $thread instanceof MemoryThread ? $thread : MemoryThread::query()->findOrFail($thread);

        $message = $thread->messages()->create($attributes);

        $thread->forceFill(['last_message_at' => $message->created_at])->save();

        return $message;
    }

    public function remember(array $attributes): Memory
    {
        return Memory::query()->create($attributes);
    }

    public static function ownerAttributes(mixed $owner): array
    {
        if ($owner instanceof Model) {
            return [
                'owner_type' => $owner->getMorphClass(),
                'owner_id' => (string) $owner->getKey(),
            ];
        }

        if (is_array($owner)) {
            return array_intersect_key($owner, array_flip(['owner_type', 'owner_id']));
        }

        return [];
    }
}
