<?php

namespace LaravelAiMemory\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Models\MemoryEmbedding;
use LaravelAiMemory\Support\ContentHasher;

class GenerateEmbeddingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $embeddableType,
        public readonly string|int $embeddableId,
        public readonly string $content,
    ) {
        $this->onConnection(config('ai-memory.queues.connection'));
        $this->onQueue(config('ai-memory.queues.embedding_queue', 'default'));
    }

    public function handle(EmbeddingProvider $provider, ContentHasher $hasher): void
    {
        $hash = $hasher->hash($this->content);

        if (MemoryEmbedding::query()
            ->where('embeddable_type', $this->embeddableType)
            ->where('embeddable_id', (string) $this->embeddableId)
            ->where('content_hash', $hash)
            ->exists()) {
            return;
        }

        $result = $provider->embed($this->content);

        $attributes = [
            'embeddable_type' => $this->embeddableType,
            'embeddable_id' => (string) $this->embeddableId,
            'provider' => $result->provider,
            'model' => $result->model,
            'dimensions' => $result->dimensions,
            'embedding_json' => $result->embedding,
            'content_hash' => $hash,
            'metadata' => [
                'usage' => $result->usage,
            ],
        ];

        if (app('db')->connection(config('ai-memory.database.connection'))->getDriverName() === 'pgsql') {
            $attributes['embedding'] = '['.implode(',', array_map(fn ($value) => (float) $value, $result->embedding)).']';
        }

        MemoryEmbedding::query()->create($attributes);
    }
}
