## 2024-06-07 - [Eloquent Performance Improvement]
**Learning:** Replacing `whereHas` with `whereIn` using an Eloquent subquery provides a significant performance boost in this application compared to `whereHas`, and we should not rely solely on denormalized columns if it removes the implicit relation existence check.
**Action:** Use `whereIn` subqueries instead of `whereHas` for performance critical queries while maintaining ORM features.
