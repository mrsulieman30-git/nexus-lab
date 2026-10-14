
## 2026-10-14 - Eloquent whereHas Performance Bottlenecks
**Learning:** In Laravel, `whereHas` uses an `EXISTS` subquery which can be surprisingly slow, even for simple relationships. Replacing it with a `whereIn` subquery using an Eloquent Builder combined with applying the constraints on a denormalized column directly can yield a ~15-20% performance improvement while preserving relation existence checks.
**Action:** Use `whereIn` subqueries instead of `whereHas` where appropriate, and always pass an Eloquent Builder (e.g. `whereIn('id', Model::select('id'))`) rather than a raw closure to maintain ORM features like global scopes.
