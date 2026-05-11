## 2025-02-14 - Optimize item_batches queries with denormalized column
**Learning:** Using denormalized `laboratory_id` on the `item_batches` table instead of querying via Eloquent `whereHas` on the `item` relationship provides a ~66% performance improvement for simple filtration.
**Action:** Always prefer filtering directly using the denormalized columns available on the table rather than executing subqueries through model relationships when the denormalized data is reliably maintained.
