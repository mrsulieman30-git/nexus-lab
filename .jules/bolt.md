## 2026-05-03 - Avoid whereHas with denormalized columns
**Learning:** Replaced an expensive `whereHas` query with a direct `where` condition utilizing the denormalized `laboratory_id` column on the `item_batches` table, which avoids a subquery and significantly improves performance.
**Action:** Always check if a denormalized column exists on the related table before using `whereHas` for filtering.
