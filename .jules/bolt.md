## 2026-05-27 - [Replace whereHas with whereIn Subqueries]
**Learning:** In this codebase, replacing Eloquent's `whereHas` with a `whereIn` subquery utilizing denormalized columns (like `laboratory_id`) provides significant query performance improvements by eliminating expensive `EXISTS` subqueries, while maintaining ORM features by passing an Eloquent Builder to `whereIn`.
**Action:** Always prefer `whereIn` with an Eloquent Builder subquery over `whereHas` for filtering by relationship attributes, especially when denormalized columns are available.
