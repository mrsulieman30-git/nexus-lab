
## 2024-05-01 - Avoid whereHas when denormalized scopes exist
**Learning:** Using `whereHas('relation', fn($q) => $q->where('scope_id', $id))` introduces an expensive EXISTS subquery that can heavily degrade performance, especially on tables like `item_batches`.
**Action:** Always check if the related model's table has a denormalized scope column (e.g., `laboratory_id` on `item_batches`). If so, use a direct `where('laboratory_id', $id)` clause instead. This provided a ~48% performance improvement in local benchmarking.
