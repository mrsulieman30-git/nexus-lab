# Bolt's Journal

## 2024-03-20 - Eloquent whereHas vs whereIn
**Learning:** Using `whereHas` in Eloquent can introduce performance overhead due to the `EXISTS` subqueries it generates. However, replacing it with `whereIn` using a closure drops global scopes and soft deletes!
**Action:** When replacing `whereHas` for performance, if using `whereIn`, pass an Eloquent Builder instance (`Model::where(...)`) instead of a closure (`function($q) {...}`) to ensure global scopes and ORM features are retained, OR use `DB::table` directly if ORM features aren't strictly needed for the subquery logic.
