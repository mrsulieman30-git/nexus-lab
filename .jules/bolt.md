## 2024-05-23 - [Optimizing Eloquent Relationships]
**Learning:** Using `whereHas` with a closure in Eloquent can lead to significant performance bottlenecks, especially when dealing with large datasets, as it translates to an `EXISTS` subquery.
**Action:** Replace `whereHas` with a `whereIn` subquery using an Eloquent Builder (e.g., `whereIn('id', Model::select('foreign_id')->where(...))`) when possible, which is significantly faster while still maintaining ORM features.
