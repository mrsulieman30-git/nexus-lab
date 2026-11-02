
## 2026-11-02 - Eloquent whereHas to whereIn Subquery
**Learning:** Using whereHas('relation') on large tables introduces a slow EXISTS subquery. Querying a denormalized column directly drops implicit relation checks.
**Action:** Replace whereHas with an Eloquent Builder whereIn subquery (e.g., whereIn('item_id', Item::where('laboratory_id', $id)->select('id'))) to improve performance while maintaining ORM features.
