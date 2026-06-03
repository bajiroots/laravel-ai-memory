# Usage

Use the `AiMemory` facade or inject `LaravelAiMemory\Services\AiMemoryManager`.

Main flows:

- `createThread()` creates a memory thread.
- `recordMessage()` stores a message.
- `remember()` stores a fact, decision, todo, technical context, or summary.
- `detectRelatedThreads()` returns related thread candidates with confidence scores.
- `retrieveContext()` returns memory that can be inserted into an AI prompt.
