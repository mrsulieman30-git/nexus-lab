## 2024-03-24 - Eloquent whereHas Performance
**Learning:** In this architecture, Eloquent's `whereHas` can be extremely slow (measured ~158s in test) compared to `whereIn` with a subquery (measured ~0.05s). This is because `whereHas` generates an `EXISTS` subquery which evaluates poorly on large data.
**Action:** Always prefer `whereIn` with an explicit Eloquent Builder subquery over `whereHas` when filtering by relation conditions on large tables, being careful to explicitly import related models and preserve Eloquent features.
