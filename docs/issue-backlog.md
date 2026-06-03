# Issue Backlog

These issues are ready to create once the GitHub repository is public.

## Good First Issues

### Add more retrieval formatter options

Area: retrieval, documentation

Create alternate prompt context formats such as JSON, Markdown sections, and compact bullet lists.

Acceptance criteria:

- formatter option added to config
- tests for each format
- docs updated

### Improve fallback search scoring

Area: search, database

The database fallback currently returns recent memories with a fixed score. Improve it using simple lexical overlap while keeping it dependency-free.

Acceptance criteria:

- query terms influence ranking
- owner/type filters still work
- tests cover ranking order

### Add docs for owner scoping

Area: docs, Laravel integration

Explain how to scope memory by user, team, workspace, or tenant.

Acceptance criteria:

- examples for model owners
- examples for array owners
- warning about cross-tenant leakage

## MVP Issues

### Add pgvector integration test suite

Area: pgvector, testing

Add tests that run against PostgreSQL with pgvector enabled.

Acceptance criteria:

- GitHub Actions PostgreSQL service
- pgvector extension setup documented
- similarity ranking test passes

### Add HNSW/IVFFlat index migration notes

Area: pgvector, docs

Document recommended vector indexes and when to use each one.

Acceptance criteria:

- docs explain cosine distance
- docs include HNSW example
- docs include IVFFlat example

### Add LLM-assisted memory extraction contract

Area: extraction, contracts

Implement a default memory extraction workflow that can turn conversation text into facts, decisions, todos, and technical context.

Acceptance criteria:

- extraction contract finalized
- fake extractor for tests
- queued extraction job covered

### Add batch embedding pipeline

Area: embeddings, queues

Support efficient batch embedding generation for backfills and imports.

Acceptance criteria:

- batch job added
- provider batch method used
- duplicate content hashes skipped

## Release Issues

### Prepare v0.1.0 release

Area: release

Cut the first tagged MVP release.

Acceptance criteria:

- changelog updated
- README quick start verified
- Packagist package submitted
- GitHub release notes published
