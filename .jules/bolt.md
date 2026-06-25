## 2026-06-25 - Optimize Eloquent relationship queries
**Learning:** Relying solely on a denormalized column and removing `whereHas` drops the implicit relation existence check. Using `whereIn` with a subquery is faster than `whereHas` while maintaining relationship integrity.
**Action:** Replace `whereHas` with `whereIn` subqueries using Eloquent Builders to optimize performance without losing implicit checks.
