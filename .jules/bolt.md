## 2025-02-17 - Optimize ItemBatch Queries

**Learning:** `whereHas` on ItemBatch to check `laboratory_id` is expensive and unnecessary because `laboratory_id` is denormalized onto `ItemBatch`.
**Action:** Replace `ItemBatch::whereHas('item', ...)` with direct `where('laboratory_id', $labId)` or `whereIn('item_id', Item::...)` where applicable to significantly improve query performance without sacrificing safety, as tested in DashboardController.

## 2025-02-17 - Caution with whereIn and whereHas replacement
**Learning:** `whereHas` on relationships can be expensive. However, replacing it blindly with `whereIn(..., Model::...)` requires caution to ensure the referenced `Model` is explicitly imported at the top of the file to prevent fatal 'Class not found' errors. Modern databases optimize EXISTS (used by whereHas) and IN (used by whereIn) similarly, so real-world impact must be profiled carefully.
**Action:** Always verify that newly referenced models in `whereIn` subqueries are properly imported using `use App\Models\ModelName;`.
