## 2026-06-23 - [Optimize whereHas to whereIn for Performance]
**Learning:** Replacing `whereHas` with a `whereIn` subquery utilizing denormalized columns is significantly faster (e.g., ~18.8ms vs ~4.2ms) while maintaining the relationship check.
**Action:** Use `whereIn('item_id', Model::select('id')->where(...))` instead of `whereHas` for related existence checks when possible to avoid expensive EXISTS subqueries.
