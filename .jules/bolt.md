## 2024-06-11 - Use Denormalized laboratory_id on item_batches
**Learning:** The database schema deliberately uses denormalized columns (e.g., `laboratory_id` on `item_batches`) to optimize query performance and avoid expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** When querying `item_batches` to filter by laboratory, always filter directly using `where('laboratory_id', $labId)` rather than executing an expensive `whereHas('item', ...)` subquery.
