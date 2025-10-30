# 📋 PHASE 3.6 TESTING: Export Functionality

**Date:** October 30, 2025  
**Status:** Testing Plan & Test Cases  
**Total Test Cases:** 40+

---

## Test Execution Overview

### Test Environment
- Framework: Laravel 8/9/10
- Database: MySQL/MariaDB
- Queue: Laravel Queue (testing with sync driver or redis)
- Storage: Local filesystem (exports disk)
- Authentication: Sanctum

### Test Data Setup
```php
// Create test assets (100 items)
$assets = factory(Asset::class)->times(100)->create();

// Create test tickets (150 items)
$tickets = factory(Ticket::class)->times(150)->create();

// Create varied statuses
$statusIds = Status::pluck('id')->toArray();
$priorityIds = TicketPriority::pluck('id')->toArray();
```

---

## 1. Asset Export Tests (12 cases)

### 1.1: Basic CSV Export
```
Test: Export all assets to CSV
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "name", "asset_tag", "serial_number", "status_id"],
    "async": false
  }

Expected: 
  - Status: 200 OK
  - Content-Type: text/csv
  - File downloaded with header row
  - Data matches database
```

### 1.2: Excel Export
```
Test: Export all assets to Excel
Request:
  POST /api/v1/assets/export
  {
    "format": "excel",
    "columns": ["id", "name", "asset_tag", "serial_number", "status_id"],
    "async": false
  }

Expected:
  - Status: 200 OK
  - Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
  - File downloaded (.xlsx format)
  - Columns auto-fitted
  - Data intact
```

### 1.3: JSON Export
```
Test: Export assets to JSON
Request:
  POST /api/v1/assets/export
  {
    "format": "json",
    "columns": ["id", "name", "status_id"]
  }

Expected:
  - Status: 200 OK
  - JSON structure: { export_id, resource_type, exported_at, item_count, data: [...] }
  - All items present
  - Timestamp included
```

### 1.4: CSV with Filters
```
Test: Export only active assets
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "name", "status_id"],
    "filters": {
      "status_id": 2
    }
  }

Expected:
  - Status: 200 OK
  - File contains only status_id=2 assets
  - Record count matches filter
```

### 1.5: Excel with Date Range Filter
```
Test: Export assets created in date range
Request:
  POST /api/v1/assets/export
  {
    "format": "excel",
    "columns": ["id", "name", "created_at"],
    "filters": {
      "date_from": "2025-10-01",
      "date_to": "2025-10-30"
    }
  }

Expected:
  - Status: 200 OK
  - All assets in date range
  - Dates formatted correctly
```

### 1.6: Export with Column Selection
```
Test: Export subset of columns
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["asset_tag", "serial_number"],
    "async": false
  }

Expected:
  - Status: 200 OK
  - CSV header: asset_tag,serial_number
  - Only 2 columns present
  - No other columns included
```

### 1.7: Dry-Run Export Validation
```
Test: Validate without persisting
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "name"],
    "dry_run": true
  }

Expected:
  - Status: 200 OK
  - Response: { status: "DRY_RUN_SUCCESS", preview_count: 10, total_items: 100, estimated_file_size: "25 KB" }
  - No Export record created
  - No file on disk
```

### 1.8: Async Export (Large Dataset)
```
Test: Export 15,000 assets asynchronously
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "name", "status_id"],
    "async": true
  }

Expected:
  - Status: 202 ACCEPTED
  - Response: { export_id: "export-xxx", status: "pending", check_status_url: "..." }
  - Export record created with status="pending"
  - Job queued
  - User receives email after completion
```

### 1.9: Invalid Column Selection
```
Test: Attempt export with invalid columns
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "invalid_column", "name"]
  }

Expected:
  - Status: 422 Unprocessable Entity
  - Error: Validation failed
  - Message indicates invalid_column not allowed
```

### 1.10: Empty Result Set Export
```
Test: Export with filter matching no records
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "name"],
    "filters": {
      "status_id": 999
    }
  }

Expected:
  - Status: 200 OK
  - File contains only header row
  - exported_items: 0
```

### 1.11: Export Authorization
```
Test: User can only export their own data
Scenario:
  - User A creates export
  - User B attempts to access export ID
  
Expected:
  - Status: 404 Not Found
  - Error: "Export not found"
  - User B cannot access User A's exports
```

### 1.12: Special Characters & Unicode
```
Test: Export with special characters
Data: Assets with names containing: é, ñ, 中文, etc.
Request:
  POST /api/v1/assets/export
  {
    "format": "csv",
    "columns": ["id", "name"]
  }

Expected:
  - CSV file with UTF-8 BOM
  - All special characters preserved
  - No encoding errors
```

---

## 2. Ticket Export Tests (12 cases)

### 2.1: Basic Ticket CSV Export
```
Test: Export all tickets to CSV
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "subject", "status_id", "priority_id"],
    "async": false
  }

Expected:
  - Status: 200 OK
  - File contains all tickets
  - Columns in order
```

### 2.2: Ticket Excel Export
```
Test: Export to Excel format
Request:
  POST /api/v1/tickets/export
  {
    "format": "excel",
    "columns": ["ticket_code", "subject", "priority_id", "assigned_to"]
  }

Expected:
  - Status: 200 OK
  - .xlsx file
  - Proper formatting
```

### 2.3: Multi-Status Filter
```
Test: Export tickets with multiple statuses
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "status_id"],
    "filters": {
      "status_id": [1, 2, 3]
    }
  }

Expected:
  - Status: 200 OK
  - Only tickets with status_id in [1, 2, 3]
  - Correct count
```

### 2.4: Open Tickets Only
```
Test: Export only open tickets
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "is_open"],
    "filters": {
      "is_open": true
    }
  }

Expected:
  - All tickets have is_open=true
  - Closed tickets excluded
```

### 2.5: Resolution Status Filter
```
Test: Export resolved tickets
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "is_resolved"],
    "filters": {
      "is_resolved": true
    }
  }

Expected:
  - Only resolved tickets exported
  - is_resolved column shows true for all
```

### 2.6: Priority Filter Export
```
Test: Export high priority tickets
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "priority_id"],
    "filters": {
      "priority_id": 1
    }
  }

Expected:
  - Only priority_id=1 tickets
```

### 2.7: Assigned Tickets Only
```
Test: Export only assigned tickets
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "assigned_to"],
    "filters": {
      "assigned_to": 5
    }
  }

Expected:
  - Only tickets assigned to user_id=5
```

### 2.8: Ticket Date Range Export
```
Test: Export recent tickets
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "created_at"],
    "filters": {
      "date_from": "2025-10-20",
      "date_to": "2025-10-30"
    }
  }

Expected:
  - Only tickets created in date range
  - Dates correctly formatted
```

### 2.9: Async Ticket Export
```
Test: Large ticket export asynchronously
Request:
  POST /api/v1/tickets/export
  {
    "format": "excel",
    "columns": [...20 columns...],
    "async": true,
    "email_notification": true
  }

Expected:
  - Status: 202 ACCEPTED
  - Export queued
  - Email sent on completion
```

### 2.10: Complex Filter Combination
```
Test: Multiple filters combined
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["ticket_code", "subject", "priority_id"],
    "filters": {
      "status_id": [1, 2],
      "priority_id": 1,
      "is_open": true,
      "date_from": "2025-10-01"
    }
  }

Expected:
  - All filters applied
  - Records match ALL conditions (AND logic)
  - Correct count
```

### 2.11: Ticket with Long Description
```
Test: Export handles long text fields
Data: Tickets with description > 5000 characters
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code", "description"]
  }

Expected:
  - CSV properly escapes long text
  - No truncation
  - Quotes handled correctly
```

### 2.12: No Results with Filter
```
Test: Export with no matching tickets
Request:
  POST /api/v1/tickets/export
  {
    "format": "csv",
    "columns": ["id", "ticket_code"],
    "filters": {
      "priority_id": 999
    }
  }

Expected:
  - Status: 200 OK
  - Header row only
  - exported_items: 0
```

---

## 3. Format Validation Tests (8 cases)

### 3.1: CSV UTF-8 Encoding
```
Test: CSV file has proper encoding
Data: Various Unicode characters
Validation:
  - File starts with UTF-8 BOM (\xEF\xBB\xBF)
  - All special characters preserved
  - No encoding errors
```

### 3.2: CSV Escaping
```
Test: CSV handles quotes and commas
Data: Name field contains: 'Smith, "The King"'
Expected in CSV:
  "Smith, ""The King"""
```

### 3.3: Excel Formatting
```
Test: Excel file formatting
Validation:
  - Columns auto-fitted
  - Headers present
  - Data types preserved
  - Numbers formatted as numbers
  - Dates formatted as dates
```

### 3.4: JSON Structure
```
Test: JSON export structure
Expected:
  {
    "export_id": "export-...",
    "resource_type": "assets",
    "exported_at": "2025-10-30T...",
    "item_count": 100,
    "columns": ["id", "name"],
    "data": [...]
  }
```

### 3.5: Large Value Handling
```
Test: Export with large numbers
Data: Asset price = 999999999999
Expected:
  - CSV: 999999999999 (no scientific notation)
  - Excel: Formatted as number
  - JSON: Numeric type
```

### 3.6: Empty Field Handling
```
Test: Export with NULL values
Data: Some records have NULL fields
Expected:
  - CSV: Empty cells (blank)
  - Excel: Empty cells
  - JSON: null values
```

### 3.7: Date Format Consistency
```
Test: Date formatting across formats
Dates: 2025-10-30 14:30:00
CSV Expected: 2025-10-30 14:30:00
Excel Expected: Date/time cell
JSON Expected: ISO8601 string
```

### 3.8: Special Characters in Headers
```
Test: Column names with special chars
Columns: ["asset_tag", "serial_number", "notes"]
Expected:
  - CSV: asset_tag,serial_number,notes
  - Excel: Proper header row
  - JSON: Correct keys
```

---

## 4. Async Export & Notifications (8 cases)

### 4.1: Queue Job Creation
```
Test: Export job queued for large dataset
Action: Export 15,000 assets
Expected:
  - Export record status: "pending"
  - Job visible in queue table
  - ExportDataJob::class created
```

### 4.2: Job Processing
```
Test: Queue processes export job
Action: Run: php artisan queue:work --once
Expected:
  - Job executes
  - Export status changes to "processing"
  - ExportLog records created for progress
```

### 4.3: Job Completion
```
Test: Export job completes successfully
Expected:
  - Export status: "completed"
  - exported_items: matches total_items
  - file_path set
  - file_size calculated
  - completed_at timestamp set
```

### 4.4: Email Notification Sent
```
Test: User receives completion email
Expected Email Contains:
  - Subject: "Your CSV Export is Ready" (format-specific)
  - Download link
  - Item count
  - File size
  - Expiration date (30 days)
  - Export format info
```

### 4.5: Job Failure & Retry
```
Test: Export job fails and retries
Scenario: Simulate failure (throw exception)
Expected:
  - Job retried 3 times
  - Export status: "failed" after retries exhausted
  - error_details populated
  - Failure email sent
```

### 4.6: Job Timeout Handling
```
Test: Long-running job timeout
Data: 100,000+ items (exceeds normal processing)
Expected:
  - Job timeout: 300 seconds
  - Job can be retried
  - Error logged
  - User notified
```

### 4.7: Concurrent Exports
```
Test: Multiple users export simultaneously
Action: Dispatch 5 concurrent exports
Expected:
  - All exports queued
  - Process independently
  - No conflicts or data corruption
  - All complete successfully
```

### 4.8: Failure Email Content
```
Test: Failure email notification
Expected Content:
  - Error occurred notification
  - Error details
  - Retry button/link
  - Contact support message
```

---

## 5. Export History & Management (6 cases)

### 5.1: List Export History
```
Test: Get user's export history
Request:
  GET /api/v1/exports

Expected:
  - 200 OK
  - Paginated results (20 per page default)
  - Latest exports first
  - Shows: export_id, status, format, item_count, file_size, created_at
```

### 5.2: Filter Export History
```
Test: Filter history by status
Request:
  GET /api/v1/exports?status=completed&format=csv&limit=50

Expected:
  - Only completed exports
  - Only CSV format
  - 50 items per page
```

### 5.3: Get Export Status
```
Test: Check specific export progress
Request:
  GET /api/v1/exports/export-648a3f2c

Expected:
  - 200 OK
  - export_id, status, progress info
  - If completed: download_url, file_size
  - If failed: error_details
  - download_count, expires_at
```

### 5.4: Export Logs
```
Test: View detailed export logs
Request:
  GET /api/v1/exports/export-xxx/logs?limit=20

Expected:
  - Chronological log entries
  - Events: initiated, processing, progress, completed, failed
  - Progress percentages
  - Batch information
```

### 5.5: Retry Failed Export
```
Test: Retry a failed export
Request:
  POST /api/v1/exports/export-xxx/retry

Expected:
  - Status: 202 ACCEPTED
  - New export job created
  - Original export unchanged
  - User notified of retry
```

### 5.6: Export Auto-Expiration
```
Test: Exports expire after 30 days
Setup: Create export 31 days ago
Action: Run: php artisan exports:cleanup
Expected:
  - File deleted from storage
  - Export status: "expired"
  - Cron job should schedule this
```

---

## 6. Permission & Authorization Tests (4 cases)

### 6.1: Unauthorized User Cannot Export
```
Test: Unauthenticated user attempts export
Request:
  POST /api/v1/assets/export (no auth header)

Expected:
  - Status: 401 Unauthorized
  - Error: "Unauthenticated"
```

### 6.2: User Isolation
```
Test: User cannot access other's exports
Scenario:
  - User A exports assets
  - User B tries GET /api/v1/exports/export-userA

Expected:
  - Status: 404 Not Found
  - No data leaked
```

### 6.3: Rate Limiting
```
Test: Bulk rate limiting applies
Action: 6 export requests in 1 minute
Expected:
  - First 5 succeed
  - 6th returns 429 Too Many Requests
  - Limit: 5 operations per minute (throttle:api-bulk)
```

### 6.4: Permission Check Export
```
Test: User needs export permission
Setup: Remove export permission from role
Action: Attempt export

Expected:
  - Status: 403 Forbidden
  - Error: "Unauthorized action"
```

---

## 7. Performance Tests (5 cases)

### 7.1: Small Export Performance
```
Test: <1,000 items export
Data: 500 assets
Expected Performance:
  - CSV: <500ms
  - Excel: <1000ms
  - Synchronous response
  - Memory: <50MB
```

### 7.2: Medium Export Performance
```
Test: 1,000-10,000 items export
Data: 5,000 tickets
Expected Performance:
  - CSV: <5 seconds
  - Excel: <10 seconds
  - Stream completed in <15 seconds
  - Memory: <100MB
```

### 7.3: Large Export Async Performance
```
Test: >10,000 items export
Data: 50,000 assets
Expected Performance:
  - Job queued immediately (<100ms)
  - Processing: <2 minutes
  - Memory: Streaming, never >100MB
  - Email sent within 5 minutes of completion
```

### 7.4: Database Query Performance
```
Test: Filter + export performance
Query: 100,000 items with 5 filters
Expected:
  - Query time: <5 seconds
  - Single query execution (optimized)
  - No N+1 queries
```

### 7.5: Concurrent Export Performance
```
Test: 10 simultaneous exports
Expected:
  - All queue within 1 second
  - Total processing time < 2 minutes
  - Server stable (CPU, memory within limits)
  - No data corruption
```

---

## 8. Data Integrity Tests (5 cases)

### 8.1: Data Accuracy
```
Test: Exported data matches database
Action: Export all assets, compare with SELECT *
Expected:
  - Row count matches
  - All values identical
  - No data loss
  - No data modification
```

### 8.2: Filtered Export Accuracy
```
Test: Filtered export matches filter query
Action: Export with filter, compare with WHERE clause
Expected:
  - Export rows match query results
  - No extra rows
  - No missing rows
```

### 8.3: Column Order Preservation
```
Test: Column order preserved
Request columns: ["asset_tag", "name", "serial_number"]
Expected: CSV header is asset_tag,name,serial_number
```

### 8.4: Relationship Data Export
```
Test: Export includes related data
Data: Asset with Location/Status/Manufacturer
Columns: ["id", "name", "location", "status"]
Expected:
  - location_id or location_name displayed
  - Relationship properly loaded
  - No NULL values when data exists
```

### 8.5: No Data Modification
```
Test: Export does not modify data
Before Export:
  - Asset updated_at: T1
  - Asset.count(): N

After Export:
  - Same updated_at: T1
  - Same count: N
  - No audit log entries created for export
```

---

## 9. Error Handling Tests (4 cases)

### 9.1: Invalid Filter Value
```
Test: Export with non-existent status
Request:
  POST /api/v1/assets/export
  { "filters": { "status_id": 99999 } }

Expected:
  - Status: 200 OK
  - Empty result set
  - No error, valid scenario
```

### 9.2: Missing Required Field
```
Test: Export without format field
Request:
  POST /api/v1/assets/export
  { "columns": ["id", "name"] }

Expected:
  - Status: 422 Unprocessable Entity
  - Error: "format field is required"
```

### 9.3: Invalid Column Name
```
Test: Export with non-existent column
Request:
  POST /api/v1/assets/export
  { "format": "csv", "columns": ["id", "invalid_field"] }

Expected:
  - Status: 422 Unprocessable Entity
  - Error: "invalid_field not a valid column"
```

### 9.4: Storage Disk Missing File
```
Test: Download export with missing file
Setup: Export exists, file deleted from storage
Action: GET /api/v1/exports/export-xxx/download

Expected:
  - Status: 404 Not Found
  - Error: "Export file not found"
```

---

## Test Execution Commands

### Run All Export Tests
```bash
php artisan test tests/Feature/ExportControllerTest.php
php artisan test tests/Feature/ExportBuilderTest.php
php artisan test tests/Feature/ExportNotificationTest.php
```

### Run Specific Test
```bash
php artisan test tests/Feature/ExportControllerTest.php::test_export_assets_to_csv
```

### With Coverage
```bash
php artisan test --coverage-html=coverage tests/Feature/Export*
```

### Queue Testing
```bash
# Process queued exports
php artisan queue:work --once

# Check job status
php artisan queue:monitor
```

---

## Success Criteria

✅ **All 40+ tests must pass**
✅ **0 syntax errors**
✅ **Performance targets met**
✅ **Authorization enforced**
✅ **Data integrity verified**
✅ **Async processing working**
✅ **Email notifications sent**
✅ **File formats valid**
✅ **Error handling comprehensive**

---

## Conclusion

Phase 3.6 testing validates:
- ✅ All export formats (CSV, Excel, JSON)
- ✅ Synchronous and asynchronous processing
- ✅ Advanced filtering and column selection
- ✅ Permission and authorization
- ✅ Performance and scalability
- ✅ Data integrity and accuracy
- ✅ Email notifications
- ✅ Error handling and recovery

**Ready for QA execution!**

