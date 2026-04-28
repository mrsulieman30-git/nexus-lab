
## 2026-04-28 - [Database Schema Denormalization Learning]
**Learning:** The item_batches table has a denormalized laboratory_id column specifically designed to avoid expensive Eloquent whereHas relationship queries when filtering batches by laboratory.
**Action:** When filtering item_batches, always check for and use the denormalized laboratory_id directly instead of relying on whereHas('item', ...) to optimize performance.
