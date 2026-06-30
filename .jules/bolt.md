## 2026-06-30 - Eloquent whereHas Performance Drop
**Learning:** In scenarios with thousands of records, Eloquent's `whereHas` (which uses `EXISTS`) can be significantly slower than a `whereIn` subquery using an Eloquent Builder. I observed a 15x speedup changing it in PurchaseOrderController.
**Action:** Use `whereIn('id', RelatedModel::select('foreign_id')->where(...))` instead of `whereHas` for heavy relationship existence checks, but always pass an Eloquent Builder to maintain Global Scopes and Soft Deletes.
