
## 2026-06-22 - Optimizing Eloquent Relationships
**Learning:** Using `whereHas` for checking relations is extremely slow as it creates an `EXISTS` subquery. Replacing it with `whereIn` containing a subquery selecting the foreign key provides > 95% performance improvement.
**Action:** Always prefer `whereIn` subqueries over `whereHas` when checking relationships in Eloquent queries.
