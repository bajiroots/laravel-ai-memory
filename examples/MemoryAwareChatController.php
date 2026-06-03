<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelAiMemory\Facades\AiMemory;
use LaravelAiMemory\Models\MemoryThread;

class MemoryAwareChatController
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $message = $request->string('message')->toString();

        $relatedThreads = AiMemory::detectRelatedThreads(
            content: $message,
            owner: $user,
            options: [
                'limit' => 3,
                'minimum_score' => 0.60,
            ],
        );

        $thread = $this->resolveThread($request, $relatedThreads);

        $storedMessage = AiMemory::recordMessage($thread, [
            'role' => 'user',
            'content' => $message,
            'metadata' => [
                'source' => 'chat',
            ],
        ]);

        $context = AiMemory::retrieveContext(
            query: $message,
            owner: $user,
            options: [
                'types' => ['decision', 'technical_context', 'todo', 'fact'],
                'max_tokens' => 1200,
            ],
        );

        $prompt = <<<PROMPT
Use the prior context when it helps. Do not mention it unless it is relevant.

{$context->formattedContext}

User message:
{$message}
PROMPT;

        // Send $prompt to your AI provider here.
        $assistantReply = 'Example response from your AI provider.';

        AiMemory::recordMessage($thread, [
            'role' => 'assistant',
            'content' => $assistantReply,
        ]);

        AiMemory::remember([
            'thread_id' => $thread->id,
            'message_id' => $storedMessage->id,
            'owner_type' => $user->getMorphClass(),
            'owner_id' => (string) $user->getKey(),
            'type' => 'summary',
            'content' => $this->summarizeForMemory($message),
            'importance' => 3,
            'source' => 'example',
        ]);

        return response()->json([
            'thread_id' => $thread->id,
            'reply' => $assistantReply,
            'related_threads' => $relatedThreads->map(fn ($related) => [
                'thread_id' => $related->thread->id,
                'title' => $related->thread->title,
                'confidence' => $related->confidence,
                'reason' => $related->reason,
            ])->values(),
        ]);
    }

    private function resolveThread(Request $request, $relatedThreads)
    {
        if ($request->filled('thread_id')) {
            return MemoryThread::query()->firstOrCreate([
                'external_id' => $request->string('thread_id')->toString(),
                'owner_type' => $request->user()->getMorphClass(),
                'owner_id' => (string) $request->user()->getKey(),
            ], [
                'title' => 'Imported chat thread',
            ]);
        }

        $strongMatch = $relatedThreads->first(fn ($related) => $related->confidence >= 0.85);

        if ($strongMatch) {
            return $strongMatch->thread;
        }

        return AiMemory::createThread([
            'owner_type' => $request->user()->getMorphClass(),
            'owner_id' => (string) $request->user()->getKey(),
            'title' => str($request->string('message')->toString())->limit(80)->toString(),
        ]);
    }

    private function summarizeForMemory(string $message): string
    {
        return str($message)->limit(240)->toString();
    }
}
