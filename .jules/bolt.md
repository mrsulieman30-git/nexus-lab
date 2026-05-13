## 2026-05-13 - Replace whereHas with denormalized column for ItemBatch
**Learning:** The database schema uses denormalized columns (e.g., laboratory_id on item_batches) to optimize query performance. Using Eloquent relationships like whereHas creates expensive subqueries.
**Action:** Always use the denormalized laboratory_id directly on queries (e.g., ItemBatch::where('laboratory_id', $labId)) instead of querying through the parent relation.
