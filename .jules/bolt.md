## 2026-04-23 - [Database Query Optimization]
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** When querying models that have relationships with `laboratory_id`, check if the table itself contains a denormalized `laboratory_id` column to use for direct filtering instead of relying on Eloquent's relationship constraints.
