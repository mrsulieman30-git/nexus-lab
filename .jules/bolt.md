## 2024-04-19 - Avoid expensive `whereHas` by using denormalized columns
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** When filtering related models by `laboratory_id`, check if the denormalized column exists on the table to perform a direct query instead of using relationship subqueries.
