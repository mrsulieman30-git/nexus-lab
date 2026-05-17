## 2026-05-17 - Denormalized columns for faster querying
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance. Using Eloquent's `whereHas` for filtering by relations is significantly slower (e.g. 0.018s vs 0.0004s).
**Action:** Always check if a denormalized column exists on the table to avoid expensive subqueries via Eloquent relationships like `whereHas`.

## 2026-05-17 - Eloquent whereIn subquery vs whereHas
**Learning:** For Eloquent queries, using `whereIn` with a subquery is significantly faster than `whereHas` (e.g., tested on `PurchaseOrderController` taking 0.0006s vs 0.017s).
**Action:** Prefer `whereIn` subqueries over `whereHas` when joining simple relations for filtering.
