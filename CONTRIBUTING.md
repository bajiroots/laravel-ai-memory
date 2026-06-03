# Contributing

Thanks for taking a look at Laravel AI Memory.

This project is early, so the best contributions are small, specific, and tested. The goal is to build a dependable memory layer for Laravel AI applications without turning the package into a full chat framework.

## Local Setup

```bash
composer install
composer test
```

## Development Commands

```bash
composer test
composer format
composer validate --strict
```

## Contribution Style

- Keep public APIs boring and predictable.
- Prefer Laravel conventions over custom framework ideas.
- Add tests for behavior changes.
- Keep provider-specific code behind contracts.
- Document anything an application developer must configure.

## Areas That Need Help

- pgvector integration testing
- better fallback search
- memory extraction
- batch embeddings
- prompt context formatting
- example Laravel apps
- provider implementations

## Pull Requests

Open a focused PR with a clear summary, tests, and docs when behavior changes.
