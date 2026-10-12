## 2025-02-14 - Initialize Journal
**Learning:** Started performance optimization tracking.
**Action:** Will log codebase-specific performance patterns here.

## 2025-02-14 - Optimize Eloquent whereHas queries
**Learning:** Eloquent's `whereHas` generates an expensive `EXISTS` subquery which can be a performance bottleneck.
**Action:** Replaced `whereHas` with `whereIn` subqueries passing an Eloquent Builder. This maintains global scopes (unlike raw query closures) while significantly improving performance (measured ~10-15% faster).
