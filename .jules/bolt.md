## 2026-06-16 - Replace whereHas with whereIn using Eloquent Builder
**Learning:** In Laravel with SQLite, `whereHas` can be extremely slow due to the generated `EXISTS` subquery. For 1000 records, `whereHas` took ~4.1s while an equivalent `whereIn` subquery using an Eloquent builder took ~0.97s (4x faster).
**Action:** When optimizing Eloquent queries, proactively replace `whereHas` with `whereIn(..., Model::where(...)->select(...))` using an Eloquent Builder directly to maintain ORM features.
