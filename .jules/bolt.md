
## 2026-06-09 - Optimize Eloquent with whereIn and Denormalized Columns
**Learning:** Relying on `whereHas` can be expensive due to `EXISTS` subqueries. Using denormalized columns directly or `whereIn` subqueries using an Eloquent Builder provides significant performance improvements without losing relationship constraints.
**Action:** Replace `whereHas` with `whereIn` subqueries (and denormalized columns where available) to optimize slow queries, ensuring to explicitly pass constraints inside the subquery.
