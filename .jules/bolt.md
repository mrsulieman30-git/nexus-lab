## 2026-04-22 - [Database Performance Improvement] Replace `whereHas` subqueries with direct queries on denormalized columns.
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`. Using `whereHas` inside frequently run code (like Dashboard rendering or Inventory checks) results in slow performance.
**Action:** Use the denormalized `laboratory_id` column directly on the `ItemBatch` model to avoid `whereHas('item', ...)` subqueries.
