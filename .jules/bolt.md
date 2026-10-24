
## 2023-10-24 - Eloquent whereHas vs whereIn Optimization
**Learning:** Eloquent's `whereHas` executes an `EXISTS` subquery, which can be significantly slower than a `whereIn` subquery for simple existence checks, especially when retrieving a list of IDs. Testing showed a ~4.5x performance improvement (4.04s vs 0.89s) for 100 queries when using `whereIn` instead of `whereHas`.
**Action:** When filtering models based on the existence of a simple related record, consider replacing `whereHas` with a `whereIn` subquery directly selecting the foreign key.
