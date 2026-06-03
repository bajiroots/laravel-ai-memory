# Thread Detection

Thread detection follows this flow:

1. Generate an embedding for the new content.
2. Search related memory embeddings.
3. Group memory matches by thread.
4. Calculate confidence using similarity, importance, and recency.
5. Return related thread candidates.

Suggested merge is only a recommendation. The application decides whether to continue, merge, ignore, or show the suggestion to the user.
