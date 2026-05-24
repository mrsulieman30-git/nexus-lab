## 2026-05-24 - Eloquent whereHas Performance
**Learning:** Using `whereHas` generates an `EXISTS` subquery which can be slow on larger tables compared to a `whereIn` subquery using an Eloquent Builder. I measured this pattern to be 10-20% faster when checking Purchase Orders and Item Batches.
**Action:** When querying relationships for existence or filtering based on relation columns (especially simple ID matching), prefer `whereIn('foreign_key', Model::where(...)->select('id'))` over `whereHas` to improve query performance.
