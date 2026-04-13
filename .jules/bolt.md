## 2025-02-14 - Use denormalized `laboratory_id` to avoid `whereHas` on `ItemBatch`
**Learning:** The database schema uses denormalized columns (specifically `laboratory_id` on the `item_batches` table) to optimize query performance.
**Action:** When querying `ItemBatch` records, avoid using the expensive `whereHas('item', ...)` subquery for authorization. Instead, query the `laboratory_id` column directly on the `ItemBatch` table.
