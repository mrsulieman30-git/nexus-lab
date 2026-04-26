## 2026-04-26 - Denormalized columns for performance
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) specifically to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** When querying models that belong to a laboratory (like ItemBatch or StockTransaction), always check if `laboratory_id` is denormalized on the table itself and use a direct `where('laboratory_id', )` instead of joining/whereHas through the parent relationship.
