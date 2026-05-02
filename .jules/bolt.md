
## 2024-05-02 - Denormalized column optimization
**Learning:** The database schema correctly uses denormalized columns (e.g. `laboratory_id` on `item_batches`) to avoid expensive subqueries and `whereHas` calls.
**Action:** Always prefer direct querying on denormalized columns when filtering data on large tables, avoiding relationships if possible.
