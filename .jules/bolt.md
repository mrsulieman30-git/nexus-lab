
## 2026-02-13 - [Avoid unnecessary whereHas if foreign key exists]
**Learning:** The `ItemBatch` model already had a `laboratory_id` column denormalized onto it for easy filtering, but queries were still using `whereHas('item', ...)` to filter by `laboratory_id`, leading to unnecessary complex SQL subqueries when generating the dashboard and running the check inventory command.
**Action:** Always check if denormalized foreign keys exist directly on the table before using `whereHas` to filter through a relationship in Eloquent queries.
