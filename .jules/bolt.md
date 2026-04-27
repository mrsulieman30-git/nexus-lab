## 2024-05-18 - Denormalized columns for relationship performance
**Learning:** The database schema extensively uses denormalized columns (e.g., `laboratory_id` on `item_batches`) specifically to optimize query performance and bypass expensive subqueries via Eloquent relationships like `whereHas`.
**Action:** Before performing expensive relationship queries (like `whereHas`), check if a denormalized column already exists on the target table (like checking the migration files or using `Schema::hasColumn`) to filter directly.
