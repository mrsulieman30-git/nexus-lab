## 2026-11-03 - [Replace whereHas with denormalized direct check or whereIn subquery]
**Learning:** Using `whereHas` on Eloquent relationships with a closure for sub-filtering can generate slow correlated subqueries. For simple filters or ID existence, denormalized columns (`laboratory_id`) or `whereIn` queries using `Builder::select()` provide significant speed boosts.
**Action:** Instead of `whereHas`, use `where('laboratory_id', $id)` when the table is denormalized, or use `whereIn('id', RelatedModel::select('parent_id')->where(...))` to preserve ORM features.
