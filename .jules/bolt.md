## 2025-05-20 - [whereIn over whereHas optimization]
**Learning:** Using `whereHas` generates an expensive `EXISTS` subquery. Replacing `whereHas` with a `whereIn` subquery offers significant performance improvements without sacrificing correctness.
**Action:** Always replace `whereHas` with `whereIn` subqueries for relationship existence checks, and avoid using `has('relation')` as it reintroduces `EXISTS`.
