## 2025-02-14 - Replace whereHas with whereIn in Eloquent subqueries
**Learning:** Eloquent's `whereHas` can be slow due to `EXISTS` subqueries. Using `whereIn` with an Eloquent Builder subquery reduces execution time significantly (e.g. from 0.06s to 0.03s) and maintains features like global scopes.
**Action:** Always prefer `whereIn` with an Eloquent Builder instance when replacing simple `whereHas` relationship existence checks.
