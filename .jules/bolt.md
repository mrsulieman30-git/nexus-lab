## 2026-06-26 - Replace whereHas with whereIn Subqueries
**Learning:** Using Eloquent's `whereHas` compiles into an expensive `EXISTS` subquery, which can degrade database performance on larger tables. We found that `whereIn` combined with an Eloquent Builder subquery (e.g., `whereIn('id', Model::select(...))`) maintains ORM features while executing roughly 50% faster.
**Action:** Default to `whereIn` with an Eloquent subquery over `whereHas` when filtering relationships without sacrificing implicit relationship checks or dropping global scopes.
