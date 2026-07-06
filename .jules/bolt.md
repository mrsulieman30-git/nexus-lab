## 2025-02-28 - Eloquent `whereHas` Performance Bottleneck
**Learning:** Eloquent's `whereHas` executes an expensive `EXISTS` SQL subquery which scales poorly on tables with many records (like `item_batches`).
**Action:** Replaced `whereHas` with `whereIn` combined with a subquery Eloquent Builder (e.g. `whereIn('item_id', Item::where('laboratory_id', $labId)->select('id'))`). This preserves the model's Global Scopes while delivering a measurable ~28% performance improvement by avoiding `EXISTS`.
