# Examples

The `examples/` directory contains framework-level snippets that show how Laravel AI Memory can fit into an application.

## Memory-aware Chat Controller

See [`examples/MemoryAwareChatController.php`](../examples/MemoryAwareChatController.php).

The example shows a typical flow:

1. Detect related threads from the new user message.
2. Reuse a strong related thread or create a new one.
3. Record the user message.
4. Retrieve prior context for prompt injection.
5. Send the prompt to your AI provider.
6. Record the assistant response.
7. Store a simple memory summary.

It intentionally leaves the AI provider call as application code. This package owns memory and retrieval, not the full chat stack.
