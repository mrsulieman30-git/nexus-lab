
## 2026-10-31 - [Optimize PurchaseOrder Queries]
**Learning:** Replacing `whereHas` relation existence queries with a `whereIn` subquery utilizing the Eloquent Builder significantly outperforms the former on large datasets because it avoids expensive `EXISTS` checks.
**Action:** Use `whereIn` subqueries passing the Eloquent Builder directly to maintain ORM features while optimizing performance when checking existence in related tables.
