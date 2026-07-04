## 2025-02-14 - Optimize Eloquent Subqueries
**Learning:** Replacing whereHas with whereIn combined with an Eloquent Builder subquery significantly speeds up queries by avoiding slow EXISTS checks, while preserving ORM features like scopes and preventing N+1 issues when denormalized columns are missing or insufficient.
**Action:** Use whereIn('id', Model::select('id')->...) instead of whereHas('relation', ...) for critical paths where performance matters.
