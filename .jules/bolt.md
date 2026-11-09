## 2026-11-09 - [Optimized PurchaseOrder Pending Query]
**Learning:** Using whereIn with a subquery is significantly faster than whereHas for checking relationship existence in SQLite/Laravel when querying large datasets.
**Action:** Prefer whereIn for simple relationship existence checks when performance is critical.
