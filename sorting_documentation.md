# Sorting Functionality Documentation

Sorting has been implemented for the Crons List table headers. The sorting is handled via query parameters passed to the page, and the backend performs the sorting on the active cron jobs.

## Supported Sort Fields

1. **ID**
   - **Query Parameter:** `sort=cron_id`
   - **Backend Mapping:** Maps directly to MongoDB `_id` field to sort by creation order/database ID.
2. **Created At**
   - **Query Parameter:** `sort=created_at`
   - **Backend Mapping:** Maps to the `created_at` field in MongoDB.
3. **Updated At**
   - **Query Parameter:** `sort=updated_at`
   - **Backend Mapping:** Maps to the `updated_at` field in MongoDB.
4. **Last Executed at**
   - **Query Parameter:** `sort=last_executed`
   - **Backend Mapping:** Sorted in memory (PHP-level) using the latest matching entry from the `cron_executions` collection.

## How It Works

- Clicking on any of the supported headers toggles between Ascending (`asc`) and Descending (`desc`) sorting.
- The active sort column and direction are represented by up/down arrows (`ti-arrow-up` / `ti-arrow-down`).
