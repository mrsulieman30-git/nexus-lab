## 2026-11-04 - Eloquent whereHas Performance Bottleneck
**Learning:** The `whereHas()` method generates an `EXISTS` subquery which can be extremely slow on large tables. However, replacing it with a `whereIn` query targeting a subquery builder is significantly faster while still implicitly enforcing the relation check.
**Action:** Always use `whereIn` with an Eloquent Builder subquery instead of `whereHas` to ensure optimal execution times without losing Eloquent features.
