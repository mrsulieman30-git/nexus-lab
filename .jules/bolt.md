## 2026-05-15 - [Database Query Optimization]
**Learning:** Using denormalized columns (e.g., `laboratory_id` on `ItemBatch`) is significantly faster than using Eloquent relationships with `whereHas`.
**Action:** Always prefer querying denormalized columns directly on the model rather than relying on subqueries via `whereHas` when filtering by foreign keys like `laboratory_id`.
