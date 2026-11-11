## 2024-05-24 - Denormalized columns for relationship elimination
**Learning:** The database schema has a denormalized `laboratory_id` column on `item_batches`. This allows for direct querying instead of relying on an expensive `whereHas('item', ...)` query, which acts like an `EXISTS` subquery.
**Action:** Always check for denormalized columns before using `whereHas` on related models for simple attribute filtering. Replacing `whereHas` with a direct `where` condition on the denormalized column can save query time significantly.
## 2024-05-24 - whereIn subquery optimization
**Learning:** Replacing `whereHas` with a `whereIn` subquery using an Eloquent Builder preserves implicit relation existence checks while significantly reducing query time by avoiding expensive EXISTS subqueries.
**Action:** Replace `whereHas` with `whereIn` using an Eloquent Builder (e.g., `whereIn('item_id', Item::...)`) when filtering on related models, especially for large datasets.
