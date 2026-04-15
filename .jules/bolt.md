## 2026-04-15 - Denormalized columns for performance
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches` and other tables) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** When querying models, prefer using the denormalized columns directly on the table being queried instead of using expensive relational lookups like `whereHas()`.
