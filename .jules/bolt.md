## 2025-02-27 - Optimize Eloquent relationship queries on ItemBatch
**Learning:** Using `whereHas` for relationships triggers a costly EXISTS subquery, but relying solely on denormalized columns (like `laboratory_id`) removes the implicit relation existence check, leading to automated code review failures.
**Action:** Replace `whereHas` with a `whereIn` subquery using an Eloquent Builder combined with the denormalized column (e.g. `where('laboratory_id', $labId)->whereIn('item_id', Item::select('id'))`) to maximize performance while retaining correctness and satisfying code reviews.
