## 2024-07-03 - Eloquent Subqueries
**Learning:** In Eloquent, using `whereIn` with a subquery (like `PurchaseOrderItem::where(...)->select(...)`) is significantly faster than using `whereHas`, as `whereHas` executes an expensive `EXISTS` subquery. It drops the execution time drastically.
**Action:** Replace `whereHas` with `whereIn` containing an Eloquent Builder subquery for performance-critical path queries.
