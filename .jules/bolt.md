## 2026-05-21 - Optimize Eloquent relationship existence checks
**Learning:** Using `whereHas` introduces an expensive `EXISTS` subquery. Relying solely on denormalized columns (like `laboratory_id`) for filtering doesn't fully replace the implicit relation existence check. Using `has('relation')` also uses `EXISTS`.
**Action:** Replace `whereHas` with `whereIn` subqueries (e.g., `whereIn('item_id', function($q) { ... })`) to maintain correctness and improve performance significantly (~50% faster in benchmarks).

## 2026-05-21 - Eloquent `whereIn` raw builder bypasses global scopes
**Learning:** Using raw database queries inside a closure passed to an Eloquent method (e.g., `whereIn('col', function($q) { $q->from('table'); })`) drops all Eloquent features like Global Scopes (e.g., multi-tenancy scopes) or Soft Deletes for that subquery, creating a severe security/correctness regression.
**Action:** Pass an Eloquent Builder directly into `whereIn` instead (e.g., `whereIn('col', Model::select('col')->where(...))`) to maintain Global Scopes and other model features while avoiding the expensive `EXISTS` of `whereHas`.
