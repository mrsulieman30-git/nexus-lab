## 2024-05-24 - Query Optimization: whereHas vs whereIn
**Learning:** Replacing `whereHas('relation', closure)` with a `whereIn` subquery using an Eloquent Builder (e.g., `whereIn('item_id', Model::select('id')->where(...))`) provides significant performance improvements by eliminating expensive `EXISTS` subqueries, while still maintaining the necessary relational existence checks and global scopes.
**Action:** Use `whereIn` with an Eloquent Builder subquery instead of `whereHas` for cross-table scoping when performance is critical.
