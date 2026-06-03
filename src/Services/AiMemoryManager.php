<?php

namespace LaravelAiMemory\Services;

use Illuminate\Support\Collection;
use LaravelAiMemory\Contracts\MemoryRepository;
use LaravelAiMemory\Contracts\ThreadDetector;
use LaravelAiMemory\Data\RetrievalResult;
use LaravelAiMemory\Data\SuggestedMerge;
use LaravelAiMemory\Models\Memory;
use LaravelAiMemory\Models\MemoryMessage;
use LaravelAiMemory\Models\MemoryThread;

class AiMemoryManager
{
    public function __construct(
        private readonly MemoryRepository $repository,
        private readonly ThreadDetector $detector,
        private readonly MemoryRetriever $retriever,
        private readonly SuggestedMergeService $mergeService,
    ) {}

    public function createThread(array $attributes): MemoryThread
    {
        return $this->repository->createThread($attributes);
    }

    public function recordMessage(MemoryThread|string|int $thread, array $message): MemoryMessage
    {
        return $this->repository->recordMessage($thread, $message);
    }

    public function remember(array $attributes): Memory
    {
        return $this->repository->remember($attributes);
    }

    public function detectRelatedThreads(string $content, mixed $owner = null, array $options = []): Collection
    {
        return $this->detector->detect($content, $owner, $options);
    }

    public function retrieveContext(string $query, mixed $owner = null, array $options = []): RetrievalResult
    {
        return $this->retriever->retrieve($query, $owner, $options);
    }

    public function suggestMerge(MemoryThread $thread, array $options = []): ?SuggestedMerge
    {
        return $this->mergeService->suggest($thread, $options);
    }
}
