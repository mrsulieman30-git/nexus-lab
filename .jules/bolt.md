## 2024-05-19 - Replace whereHas with denormalized columns or whereIn
**Learning:** Eloquent's `whereHas` generates an expensive `EXISTS` subquery with joins which scales poorly and affects performance significantly.
**Action:** When possible, leverage denormalized columns (e.g. `laboratory_id`) to directly filter related models. Alternatively, using a `whereIn` clause with a subquery provides a massive performance boost over `whereHas` for complex relations.
