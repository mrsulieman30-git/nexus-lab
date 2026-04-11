## 2024-05-18 - Avoid assuming schema based on code patterns
**Learning:** Reviewers may incorrectly assume columns do not exist when they do, especially when looking at sub-optimal `whereHas` calls. Always explicitly verify schema structure when reviewing feedback.
**Action:** Use schema checking tools like `Schema::getColumnListing` when encountering feedback about missing columns to verify before accepting the reviewer’s reasoning.
