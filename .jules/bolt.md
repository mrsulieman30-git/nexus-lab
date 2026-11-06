## 2026-02-13 - [Performance] Eloquent whereHas vs whereIn
**Learning:** Using `whereHas` introduces an expensive `EXISTS` subquery in MySQL/SQLite, which can be significantly slower than a `whereIn` with a direct subquery, especially on large tables.
**Action:** Replace `whereHas` with `whereIn` using an Eloquent Builder subquery for checking relations when no complex joins are required, to speed up query execution (e.g., ~3x improvement observed in PurchaseOrderController).
