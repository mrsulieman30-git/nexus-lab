## 2024-05-30 - Optimize Eloquent relationship queries by utilizing denormalized columns alongside whereIn
**Learning:** Using whereIn combined with denormalized columns is significantly faster than using whereHas subqueries when querying against large datasets, avoiding the EXISTS subqueries.
**Action:** Apply this pattern across the codebase where applicable.
