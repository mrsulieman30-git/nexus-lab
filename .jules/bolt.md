## 2026-05-06 - Avoid `whereHas` on ItemBatch with Denormalization
**Learning:** The `ItemBatch` model has a denormalized `laboratory_id` column added for security scoping. Querying this directly is significantly faster than using a correlated subquery via the `whereHas('item', ...)` relation when finding batches for a specific laboratory.
**Action:** Next time you need to filter batches by a laboratory, use `->where('laboratory_id', $labId)` directly on the `ItemBatch` query instead of traversing the `item` relationship.
