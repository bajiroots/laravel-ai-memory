# Security Policy

Laravel Smart Thread Memory is early-stage software. Please do not use it for highly sensitive production memory storage without reviewing the schema, retention behavior, and provider configuration for your own application.

## Reporting a Vulnerability

If you find a vulnerability, please do not open a public issue with exploit details.

Until a dedicated security contact is published, email the maintainer privately or open a minimal GitHub issue asking for a secure contact channel.

Please include:

- affected version or commit
- database driver
- Laravel version
- steps to reproduce
- impact
- suggested fix, if known

## Data Handling Notes

This package can store conversation content and extracted memory in your application database. If you send content to an embedding provider, that provider may receive user or application data. Review your provider's data policy before enabling embeddings in production.
