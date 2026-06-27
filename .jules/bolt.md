
## 2026-06-27 - Eloquent whereHas vs whereIn / Denormalization
**Learning:** In Laravel Eloquent, using `whereHas` can be extremely slow due to the generated `EXISTS` subquery. Using `whereIn` with an Eloquent Builder subquery, or leveraging denormalized columns (like `laboratory_id` on related tables), provides significant performance wins without losing ORM features.
**Action:** Prefer `whereIn` subqueries or denormalized columns over `whereHas` for filtering by relationships, especially on frequently queried paths.
