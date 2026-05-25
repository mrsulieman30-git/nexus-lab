## 2026-05-25 - Optimize whereHas queries
**Learning:** Eloquent whereHas queries can be extremely slow due to EXISTS subqueries. Using whereIn with subqueries or querying denormalized columns directly is much faster.
**Action:** Always consider using whereIn or denormalized columns instead of whereHas when filtering relationships for performance.
