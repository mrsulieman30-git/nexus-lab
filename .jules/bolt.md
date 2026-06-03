## 2026-06-03 - [Optimize whereHas to whereIn subqueries]
**Learning:** In Laravel Eloquent, using `whereHas` can result in slow `EXISTS` subqueries. Replacing it with a `whereIn` subquery on an Eloquent Builder can significantly improve performance (e.g. 3.5x faster measured on PurchaseOrders).
**Action:** Use `whereIn` with an Eloquent Builder subquery instead of `whereHas` where appropriate, explicitly applying constraints.
