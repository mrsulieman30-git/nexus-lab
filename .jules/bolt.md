## 2025-02-20 - Use Denormalized laboratory_id
**Learning:** The database schema uses denormalized columns (e.g., laboratory_id on item_batches) to optimize query performance and avoid expensive subqueries via Eloquent relationships like whereHas.
**Action:** When querying child tables, always check if laboratory_id or other parent foreign keys are denormalized before joining parent tables.
