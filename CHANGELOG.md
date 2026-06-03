# Changelog

All notable changes to Laravel AI Memory will be documented in this file.

The project follows Semantic Versioning once the first tagged release is published.

## Unreleased

### Added

- Laravel package skeleton with service provider and facade.
- Publishable config and migrations.
- Eloquent models for threads, messages, memories, embeddings, and thread links.
- Embedding provider contract and OpenAI provider.
- pgvector similarity search driver.
- Database fallback search driver for development and tests.
- Thread detection service with confidence scoring.
- Context retrieval service with prompt formatting.
- Suggested merge service.
- Queue job skeletons for embeddings, extraction, and summarization.
- Initial README, docs, and test suite.

## 0.1.0 - Planned

Initial MVP release.
