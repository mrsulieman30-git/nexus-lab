## 2025-02-18 - Avoid whereHas when denormalized columns exist
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`. Using `whereHas` on the `item` relationship in `ItemBatch` queries adds a heavy `EXISTS (SELECT ...)` subquery.
**Action:** Always check if a relation's attribute (like `laboratory_id`) is directly available on the table being queried before using `whereHas`.
