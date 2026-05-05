## 2025-05-05 - Avoid whereHas when denormalized columns exist
**Learning:** The application uses denormalized columns (e.g., `laboratory_id` on `item_batches`) specifically to optimize query performance. Using Eloquent's `whereHas` on the `item` relationship just to filter by `laboratory_id` bypasses this optimization and creates expensive EXISTS subqueries.
**Action:** Always check if a foreign key (like `laboratory_id`) is denormalized onto the child table before using `whereHas` to filter by the parent's relationship. Querying the denormalized column directly is exponentially faster.
