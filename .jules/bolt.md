## 2025-02-26 - whereHas vs whereIn performance
**Learning:** In this application's database schema, using `whereHas` can be extremely slow compared to a `whereIn` subquery using the related model directly. For example, in `PurchaseOrderController`, `whereHas` took ~15.6s compared to ~0.03s for `whereIn`.
**Action:** Favor `whereIn` with an explicit Eloquent Builder subquery over `whereHas` for complex queries on large tables to improve performance.
