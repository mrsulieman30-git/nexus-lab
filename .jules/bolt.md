
## 2024-05-15 - Eloquent Subquery Optimization
**Learning:** Using `whereIn` with an Eloquent subquery (`Item::where(...)->select('id')`) combined with denormalized columns (e.g., `laboratory_id` on `item_batches`) is significantly faster than using `whereHas` because it avoids expensive `EXISTS` subqueries while preserving Eloquent features.
**Action:** Replace `whereHas` with `whereIn` subqueries when filtering by relations, ensuring the related model class is imported and passed as a Builder instance.
