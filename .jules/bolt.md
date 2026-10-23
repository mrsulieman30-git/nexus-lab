## 2026-02-15 - Replace whereHas with whereIn for Implicit Existence Checks
**Learning:** Using whereHas in Eloquent introduces a slower EXISTS subquery. When optimizing and denormalized columns are available, using whereIn on a selected subquery of IDs preserves the implicit relationship existence check while significantly improving query performance.
**Action:** Use whereIn(fk, Model::select('id')->where(...)) instead of whereHas() when possible to optimize database interactions without losing ORM features.
