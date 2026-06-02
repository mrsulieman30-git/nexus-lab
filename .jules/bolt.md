## 2026-06-02 - Replacing whereHas with whereIn Subqueries
**Learning:** The whereHas method in Eloquent often generates expensive EXISTS subqueries, leading to performance bottlenecks when querying large datasets.
**Action:** Use whereIn combined with an Eloquent Builder subquery for measurable performance improvements.
