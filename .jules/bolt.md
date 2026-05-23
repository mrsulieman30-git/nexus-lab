## 2026-05-23 - Eloquent Performance Optimization
**Learning:** Replaced expensive `whereHas` EXISTS subqueries with faster `whereIn` lookups using Eloquent Builder to maintain scope constraints and improve query performance.
**Action:** Always prefer `whereIn` subqueries with Eloquent Builder over `whereHas` when filtering by relation existence in high-traffic endpoints or commands.
