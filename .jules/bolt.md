## 2025-02-12 - Replace whereHas with denormalized column lookup
**Learning:** `whereHas` generates an `EXISTS` subquery which can be much slower than direct column filters or `whereIn` using another model query in Laravel/SQLite, even ignoring indexes. Using denormalized data or a simpler `whereIn` gives a fast path.
**Action:** Replace `whereHas('item', ...)` with `where('laboratory_id', $labId)` on `ItemBatch` (relying on its denormalization).
