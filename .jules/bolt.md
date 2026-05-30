## 2026-05-30 - Replace whereHas with whereIn for Eloquent relations
**Learning:** Eloquent's `whereHas` generates an expensive `EXISTS` subquery. For straightforward relation existence checks, using `whereIn` with an Eloquent Builder subquery (e.g. `whereIn('id', Model::select('id')->where(...))`) is significantly faster in this architecture while maintaining features like global scopes.
**Action:** Replace `whereHas` with `whereIn` using an Eloquent Builder subquery where applicable to avoid expensive `EXISTS` operations, but be careful to retain constraints.
