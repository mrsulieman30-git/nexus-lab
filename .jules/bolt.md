## 2026-06-04 - [Optimize Eloquent queries using denormalized columns and whereIn]
**Learning:** When using denormalized columns (like laboratory_id) to optimize relation queries, replacing whereHas removes the implicit relation existence check. Using has() reintroduces EXISTS and negates the benefit.
**Action:** Use a combination of the denormalized column filter and a whereIn subquery using an Eloquent Builder to maintain the existence check while maximizing performance.
