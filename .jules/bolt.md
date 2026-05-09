## 2025-05-09 - Avoid Expensive `whereHas` for Filtering on Denormalized Columns
**Learning:** Using Eloquent's `whereHas` to filter by a parent model's attributes (e.g., `laboratory_id`) causes significant overhead and executes expensive `EXISTS` subqueries.
**Action:** When a denormalized column exists on the child table (e.g., `laboratory_id` on `item_batches`), query that column directly using `where('laboratory_id', $id)` instead of relying on the relationship.
