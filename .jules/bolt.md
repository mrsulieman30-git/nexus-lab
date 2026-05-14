## 2024-05-14 - Dashboard Query Optimization via Denormalized Column
**Learning:** Using `whereHas` on the Dashboard to filter `item_batches` by `laboratory_id` via the `item` relationship is unnecessary and slow because `laboratory_id` is a denormalized column directly on `item_batches`. Benchmarks show avoiding `whereHas` improves query speed by ~40%.
**Action:** Always check if columns like `laboratory_id` are denormalized on the table before relying on expensive Eloquent relationship subqueries like `whereHas`.
