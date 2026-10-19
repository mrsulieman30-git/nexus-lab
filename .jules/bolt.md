## 2026-10-19 - Eloquent whereHas Performance Bottleneck
**Learning:** Eloquent's `whereHas` executes expensive `EXISTS` subqueries, which scales poorly on large tables. When running on large datasets, `whereIn` subqueries using an Eloquent Builder can be significantly faster (measured a drop from ~15s to ~0.13s on a large dataset).
**Action:** Use `whereIn` with Eloquent Builder closures for simple relationship existence checks to achieve better performance, while ensuring that the closure retains scopes (e.g. `Model::select(...)->where(...)`).
