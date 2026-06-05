## 2026-06-05 - Optimize Eloquent Exists Queries
**Learning:** Eloquent's `whereHas` executes an `EXISTS` subquery which can be extremely slow on large datasets (e.g. 1500ms vs 1.5ms). Subqueries via `whereIn` or utilizing denormalized columns on the related table are significantly faster.
**Action:** Default to `whereIn` subqueries using Eloquent Builders or direct query on denormalized columns instead of `whereHas` for relation filtering.
