## 2024-05-16 - [Eloquent Optimization: whereHas vs whereIn]
**Learning:** Replacing `whereHas` with a `whereIn` subquery for simple relation checks can yield a massive performance improvement (e.g., from ~6400ms to ~175ms for 100 queries on a medium-sized dataset) by avoiding the `EXISTS` subquery overhead while maintaining Eloquent ORM features by passing the Builder object directly.
**Action:** Use `whereIn` with an Eloquent Builder subquery instead of `whereHas` when filtering parent models based on child attributes, especially when the condition is simple and the dataset is large.
