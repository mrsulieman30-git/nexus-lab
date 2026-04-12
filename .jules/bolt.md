## 2026-04-12 - Avoid N+1 Subquery using `whereHas` on existing foreign keys
**Learning:** Checking for foreign keys through relationship bindings with `whereHas('item', function($q){...})` performs an unnecessary SQL `EXISTS` subquery when the foreign key (`laboratory_id`) is actually present directly on the model (`ItemBatch`) being queried.
**Action:** When filtering models that have directly assigned foreign keys matching parent/related scope, use `where('foreign_key', $id)` rather than `whereHas` nested relationship scoping to save significant query overhead on large datasets.
