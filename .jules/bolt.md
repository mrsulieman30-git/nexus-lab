## 2026-10-10 - Optimize Dashboard Expiry Query
**Learning:** Using a denormalized column combined with `whereIn` for subqueries is significantly faster than using `whereHas`.
**Action:** Prefer using denormalized columns and `whereIn` over `whereHas` for related model filtering when possible.
