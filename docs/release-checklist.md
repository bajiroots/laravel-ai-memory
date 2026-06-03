# Release Checklist

Use this checklist before tagging a public release.

## Before Tagging

- `composer validate --strict`
- `composer test`
- `composer format`
- README quick start reviewed
- docs links checked
- changelog updated
- version tag chosen
- GitHub Actions passing

## GitHub

- repository is public
- repository description is set
- topics are set: `laravel`, `ai`, `memory`, `embeddings`, `pgvector`, `openai`
- issue templates enabled
- discussions enabled if desired
- security policy visible

## Packagist

- submit the public GitHub repository
- verify package name
- verify auto-update hook
- check install command

## Suggested First Tag

```bash
git tag v0.1.0
git push origin v0.1.0
```
