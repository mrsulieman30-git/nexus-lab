## 2026-05-18 - [Replace whereHas with denormalized column]
**Learning:** The database schema uses denormalized columns (e.g., laboratory_id on item_batches) to optimize query performance and avoid expensive subqueries via Eloquent relationships like whereHas. Using whereHas is ~3x slower in our performance tests.
**Action:** Use direct `where('laboratory_id', $id)` queries on models like ItemBatch instead of relationship existence checks.
