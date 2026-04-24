## 2024-04-24 - Avoiding Expensive whereHas with Denormalization
**Learning:** The database schema uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** When filtering records by relationships, always check if a denormalized column like `laboratory_id` exists on the target table before resorting to `whereHas`.
