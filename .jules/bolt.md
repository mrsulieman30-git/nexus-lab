## 2026-10-13 - Optimize Relationship Existence Checks
**Learning:** Using `whereHas` to check relationship existence generates expensive `EXISTS` subqueries. Using `whereIn` with an Eloquent subquery (e.g. `whereIn('item_id', Item::where(...)->select('id'))`) achieves the same implicit relation existence check while being significantly faster (around ~2x in tested scenarios).
**Action:** Use `whereIn` with an Eloquent Builder subquery instead of `whereHas` for checking relationship constraints when querying large datasets.
