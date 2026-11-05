## 2024-05-20 - Eloquent Subquery Optimization
**Learning:** Using whereHas() can result in expensive EXISTS SQL queries. In this codebase, replacing it with a whereIn() subquery returning specific IDs is significantly faster.
**Action:** Use whereIn() with a model builder subquery instead of whereHas() for checking related models when performance is a concern.
