## 2024-05-15 - Optimize Eloquent whereHas
**Learning:** Eloquent's whereHas generates an expensive EXISTS subquery which can be significantly slower than a whereIn subquery.
**Action:** Replace whereHas with whereIn directly referencing the related model when possible for up to 80% performance gain.
