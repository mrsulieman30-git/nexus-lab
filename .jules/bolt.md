## 2026-05-31 - Eloquent whereHas vs whereIn Subquery Optimization
**Learning:** In Laravel Eloquent queries, `whereHas` executes an `EXISTS` subquery which can be slower. Replacing it with a `whereIn` coupled with an Eloquent builder subquery is typically significantly faster.
**Action:** Use `whereIn` instead of `whereHas` for simple relationship existence checks to improve performance.
