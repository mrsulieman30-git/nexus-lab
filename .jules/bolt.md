## 2026-05-26 - [Optimize whereHas Eloquent relationships]
**Learning:** Using whereHas in Eloquent generates an EXISTS query which can be slow in this architecture. Using whereIn with a subquery (Eloquent Builder) retains ORM features while speeding up execution by ~1.5-2x.
**Action:** Replace whereHas with whereIn passing an Eloquent Builder subquery where applicable.
