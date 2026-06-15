## 2026-06-15 - Eloquent whereHas vs whereIn Performance
**Learning:** In Laravel Eloquent, using `whereHas` generates an `EXISTS` subquery which can be slow for large datasets. Replacing it with a `whereIn` subquery is significantly faster.
**Action:** Use `whereIn` subqueries instead of `whereHas` when optimizing Eloquent relationship queries.
