## 2026-11-08 - Eloquent whereHas Performance Pattern
**Learning:** In Laravel, replacing `whereHas` with a `whereIn` subquery using an Eloquent Builder (e.g. `whereIn('item_id', Item::select(...))`) is significantly faster while still preserving global scopes and soft deletes. Avoid raw query builders in subqueries to prevent regressions.
**Action:** Always prefer `whereIn` with an Eloquent Builder over `whereHas` when performing simple relation existence checks.
