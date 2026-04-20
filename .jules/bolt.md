## 2024-04-20 - ItemBatch N+1 and schema denormalization
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** Replace `whereHas('item', ...)` with direct `where('laboratory_id', ...)` filtering on `ItemBatch` queries in `DashboardController` and `CheckInventory` to avoid unnecessary JOINs and subqueries.
