
## 2026-10-11 - Optimizing whereHas with whereIn Subqueries
**Learning:** Eloquent's `whereHas` uses an `EXISTS` subquery which can be significantly slower than a `whereIn` subquery utilizing an Eloquent Builder, particularly for tables with many records.
**Action:** When filtering by a related model's attributes, if denormalized columns alone aren't sufficient because we still need to verify the relation exists, use `whereIn('foreign_key', RelatedModel::where(...)->select('id'))` instead of `whereHas` to maintain performance while preserving implicit relation existence checks and ORM features.
