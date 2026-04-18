## 2024-04-18 - Optimize nested whereHas queries in Eloquent
**Learning:** Using `whereHas` on denormalized schema relationships, like filtering `item_batches` by `laboratory_id` through the `item` relationship, causes expensive subqueries which can become a bottleneck.
**Action:** Exploit denormalized columns (e.g. `laboratory_id` on `item_batches`) directly in the base query instead of using `whereHas`, significantly reducing query execution time and optimizing performance.
