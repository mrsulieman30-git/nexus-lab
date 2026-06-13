
## 2026-06-13 - Optimize Dashboard Expiry Query with whereIn Subquery
**Learning:** The `DashboardController` used an expensive `whereHas('item', ...)` query to filter `ItemBatch` by `laboratory_id`. Replacing it with a `whereIn` subquery using an Eloquent Builder provides a significant performance boost by avoiding the `EXISTS` subquery, while still maintaining implicit relationship checks.
**Action:** Use `whereIn` subqueries with Eloquent Builders instead of `whereHas` for relationship existence checks when optimizing queries.
