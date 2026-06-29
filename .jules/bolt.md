## 2025-02-14 - [Eloquent Subquery Optimization]
**Learning:** In Laravel Eloquent, using `whereIn` with an Eloquent Builder subquery is significantly faster than using `whereHas` (which relies on an `EXISTS` subquery) for simple relationship checks, improving query performance by roughly 3x in testing.
**Action:** Prefer `whereIn` subqueries over `whereHas` when filtering by simple conditions on related models where full relationship instantiation or existence checking is not strictly required.
