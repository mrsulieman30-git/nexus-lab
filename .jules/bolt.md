## 2025-02-23 - Replaced whereHas with whereIn for Eloquent Subqueries
**Learning:** Using `whereHas` for relationships generates a slow `EXISTS` subquery. For 10,000 Purchase Orders and 50,000 Items, `whereHas` took ~330 seconds vs `whereIn` taking ~0.04 seconds.
**Action:** When filtering a model by a simple related condition and performance is critical, use `whereIn('id', RelatedModel::where(...)->select('foreign_key'))` instead of `whereHas`, provided explicit where clauses are defined.
