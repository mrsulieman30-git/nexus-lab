## 2026-04-29 - Use denormalized laboratory_id instead of whereHas
**Learning:** The database schema uses denormalized columns (like `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** Whenever possible, use denormalized columns directly on the model being queried instead of using relation querying methods like `whereHas` for significant performance gains.
