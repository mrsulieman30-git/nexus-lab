## 2024-05-18 - [Eloquent whereHas Performance]
**Learning:** Eloquent `whereHas` generates an EXISTS subquery that can be significantly slower (~5x slower in our benchmarks) compared to a `whereIn` using a direct query builder subquery.
**Action:** Replace `whereHas` with `whereIn` combined with `select` for simple relation existence checks.
