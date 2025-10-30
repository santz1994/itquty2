# 📋 PHASE 3.6 PLAN: Export Functionality

**Estimated Duration:** 3-4 hours  
**Status:** Planning Phase  
**Date:** October 30, 2025

---

## Executive Summary

Phase 3.6 implements **enterprise-grade export capabilities** allowing administrators to export filtered, sorted assets and tickets to CSV and Excel formats. Built on Phase 3.4 (Advanced Filtering) and Phase 3.5 (Bulk Operations), exports will support immediate download for small datasets and async processing with email delivery for large datasets (>10,000 items).

**Key Deliverables:**
- ✅ ExportBuilder trait (reusable export logic)
- ✅ Export request validators (CSV/Excel parameters)
- ✅ ExportController with 6 endpoints
- ✅ Async job for large exports
- ✅ Email notification for completed exports
- ✅ Database migration for export history
- ✅ Comprehensive test cases (30+)

---

## Deep Analysis: Export Requirements

### Business Requirements
1. **User Stories:**
   - "As a manager, I want to export filtered assets as CSV for offline analysis"
   - "As an admin, I want to export 10,000+ tickets to Excel with async processing"
   - "As a compliance officer, I want an audit trail of all exports"
   - "As a user, I want email notification when my export completes"
   - "As a data analyst, I want customizable column selection"

2. **User Flows:**
   - Simple: Filter assets → Export to CSV → Download immediately
   - Complex: Filter + Sort + Select columns → Excel → Async → Email notification
   - Retry: Failed export → View error → Retry with corrections

3. **Performance Requirements:**
   - Small exports (<1000 items): <500ms, immediate download
   - Medium exports (1000-10000): 1-5 seconds, stream download
   - Large exports (>10000): Async job, email notification
   - Memory: Stream processing, never load entire dataset in memory

4. **Compliance Requirements:**
   - Audit trail: Who exported what, when
   - Data security: Only authorized data
   - File retention: 30 days for async exports
   - User tracking: Link exports to user accounts

### Technical Requirements
1. **Format Support:**
   - CSV: Comma-separated, UTF-8 encoding, proper escaping
   - Excel: .xlsx format, multiple sheets possible, formatting support
   - JSON: For API responses (optional)

2. **Data Source Integration:**
   - Use Phase 3.4 FilterBuilder scopes
   - Apply Phase 3.5 batch processing logic
   - Leverage existing search indexes

3. **Performance:**
   - Streaming response for downloads
   - Async job queue for large exports
   - Database indexing on export history

4. **User Experience:**
   - Progress tracking for async exports
   - Email notifications
   - Download link provided
   - Error reporting with actionable messages

---

## Architecture Design

### Core Components

#### 1. ExportBuilder Trait (350-400 lines)
**Location:** `app/Traits/ExportBuilder.php`

**Public Methods:**
```php
// Direct exports (small datasets)
public function exportToCSV($filters, $columns, $dryRun = false)
public function exportToExcel($filters, $columns, $dryRun = false)
public function exportToJSON($filters, $columns, $dryRun = false)

// Async exports (large datasets)
public function exportToCSVAsync($filters, $columns, $fileName)
public function exportToExcelAsync($filters, $columns, $fileName)

// History & Management
public function getExportHistory($limit = 50, $status = null)
public function getExportStatus($exportId)
public function retryFailedExport($exportId)
public function deleteExpiredExports()
```

**Core Methods:**
```php
// Internal orchestration
protected function performExport($format, $filters, $columns, $isAsync)
protected function buildQuery($filters)
protected function selectColumns($query, $columns)
protected function formatData($data, $format)
protected function determineAsync($itemCount, $thresholdItems = 10000)
```

**Features:**
- Automatic async detection (>10,000 items)
- Custom column selection
- Filter application via Phase 3.4 FilterBuilder
- Memory-efficient streaming
- Error handling and recovery

#### 2. Export Model (120-150 lines)
**Location:** `app/Export.php`

**Purpose:** Track all exports for audit trail and history

**Properties:**
```php
- export_id (UUID, unique)
- resource_type (enum: assets, tickets)
- export_format (enum: csv, excel, json)
- status (enum: pending, processing, completed, failed, expired)
- total_items (int)
- exported_items (int)
- failed_items (int)
- file_size (int, bytes)
- file_path (string, storage path)
- filter_config (JSON, applied filters)
- column_config (JSON, selected columns)
- created_by (FK → users)
- completed_at (timestamp)
- expires_at (timestamp, for async exports)
- download_count (int)
- error_details (JSON, error message if failed)
- email_sent_at (timestamp)
- created_at, updated_at
```

**Relationships:**
```php
public function creator(): BelongsTo  // User who initiated export
public function logs(): HasMany      // ExportLog records
```

**Scopes:**
```php
public function scopeByResourceType($query, $resourceType)
public function scopeByFormat($query, $format)
public function scopeByStatus($query, $status)
public function scopeByCreator($query, $userId)
public function scopeSuccessful($query)
public function scopeFailed($query)
public function scopeExpired($query)
public function scopeRecent($query, $days = 30)
```

**Methods:**
```php
public function isCompleted(): bool
public function isFailed(): bool
public function hasExpired(): bool
public function getDownloadUrl(): string
public function canDownload(): bool
public function incrementDownloadCount()
public function getDurationSeconds(): float
```

#### 3. ExportLog Model (100-130 lines)
**Location:** `app/ExportLog.php`

**Purpose:** Detailed audit trail for export operations

**Properties:**
```php
- export_id (FK)
- event (enum: initiated, processing, progress, completed, failed, expired, deleted)
- message (text)
- processed_items (int)
- current_batch (int)
- total_batches (int)
- error_message (text, nullable)
- duration_ms (int, duration of this step)
- created_at
```

**Methods:**
```php
public function export(): BelongsTo
public function isSuccess(): bool
public function isFailed(): bool
public function getProgress(): float
```

#### 4. ExportRequest Validators (200-250 lines)

**AssetExportRequest.php:**
```php
protected function rules()
{
    return [
        'format' => 'required|in:csv,excel,json',
        'columns' => 'required|array|min:1',
        'columns.*' => 'string|in:id,name,asset_tag,serial_number,status,...',
        'filters' => 'nullable|array',
        'filters.status_id' => 'nullable|integer|exists:statuses',
        'filters.location_id' => 'nullable|integer|exists:locations',
        'filters.date_from' => 'nullable|date',
        'filters.date_to' => 'nullable|date|after_or_equal:filters.date_from',
        'sort_by' => 'nullable|string',
        'sort_order' => 'nullable|in:asc,desc',
        'async' => 'nullable|boolean',
        'email_notification' => 'nullable|boolean',
    ];
}

public function getExportParams()
{
    return [
        'format' => $this->input('format'),
        'columns' => $this->input('columns'),
        'filters' => $this->input('filters', []),
        'sort' => [
            'by' => $this->input('sort_by'),
            'order' => $this->input('sort_order', 'asc'),
        ],
        'async' => $this->boolean('async'),
        'email_notification' => $this->boolean('email_notification'),
    ];
}
```

**TicketExportRequest.php:**
```php
// Similar structure with ticket-specific fields:
// - ticket_code, subject, priority_id, type_id, is_open, is_resolved
// - assigned_to, created_date, due_date
```

#### 5. ExportController (350-400 lines)
**Location:** `app/Http/Controllers/API/ExportController.php`

**Endpoints:**

1. **POST /api/v1/assets/export**
   ```
   Export assets with filters
   Request: AssetExportRequest
   Response: File download or job ID
   ```

2. **POST /api/v1/tickets/export**
   ```
   Export tickets with filters
   Request: TicketExportRequest
   Response: File download or job ID
   ```

3. **GET /api/v1/exports**
   ```
   List export history
   Query params: status, format, resource_type, limit, page
   Response: Paginated export list with metadata
   ```

4. **GET /api/v1/exports/{export_id}**
   ```
   Get specific export status
   Response: Export details, progress, download URL
   ```

5. **GET /api/v1/exports/{export_id}/download**
   ```
   Download exported file
   Response: File stream (binary)
   ```

6. **POST /api/v1/exports/{export_id}/retry**
   ```
   Retry failed export
   Response: New job ID or immediate file
   ```

**Methods:**
```php
// Endpoints
public function exportAssets(AssetExportRequest $request)
public function exportTickets(TicketExportRequest $request)
public function listExports(Request $request)
public function getExportStatus($exportId)
public function downloadExport($exportId)
public function retryExport($exportId)

// Internal helpers
protected function executeExport($resourceType, $params, $userId)
protected function determineAsync($itemCount)
protected function queueAsyncExport($params, $resourceType, $userId)
protected function streamDownload($export)
protected function handleStreamError($export, $error)
```

#### 6. Async Export Job (200-250 lines)
**Location:** `app/Jobs/ExportDataJob.php`

**Purpose:** Handle large exports asynchronously

**Properties:**
```php
public $export;          // Export model
public $resourceType;    // assets or tickets
public $filters;         // Applied filters
public $columns;         // Selected columns
public $format;          // csv, excel, json
public $userId;          // User ID who initiated
public $timeout = 300;   // 5 minute timeout
public $tries = 3;       // Retry 3 times
```

**Methods:**
```php
public function handle()
{
    // 1. Log start
    // 2. Build query with filters
    // 3. Stream data in batches (500 items)
    // 4. Write to temporary file
    // 5. Move to storage
    // 6. Update export status
    // 7. Send email notification
    // 8. Clean up
}

public function failed(Throwable $exception)
{
    // Log error
    // Update export status to failed
    // Send error email to user
}
```

#### 7. Notification: ExportCompleted (100-150 lines)
**Location:** `app/Notifications/ExportCompleted.php`

**Purpose:** Email notification when async export completes

**Features:**
- Download link (valid 30 days)
- Export details (format, item count, file size)
- Export history link
- Error details if failed

---

## Database Schema

### Migration: create_exports_table (70 lines)
```php
// Columns
- id (PK)
- export_id (UUID, unique) - For API reference
- resource_type (enum: assets, tickets)
- export_format (enum: csv, excel, json)
- status (enum: pending, processing, completed, failed, expired)
- total_items (unsigned int)
- exported_items (unsigned int)
- failed_items (unsigned int)
- file_size (unsigned int)
- file_path (string)
- filter_config (JSON)
- column_config (JSON)
- created_by (FK → users)
- completed_at (nullable timestamp)
- expires_at (nullable timestamp)
- download_count (unsigned int, default 0)
- error_details (JSON, nullable)
- email_sent_at (nullable timestamp)
- created_at, updated_at

// Indexes
- export_id (unique)
- resource_type, status
- created_by, created_at
- expires_at (for cleanup)
- status, completed_at

// Foreign Keys
- created_by → users.id (cascade delete)
```

### Migration: create_export_logs_table (60 lines)
```php
// Columns
- id (PK)
- export_id (FK)
- event (enum: initiated, processing, progress, completed, failed, expired, deleted)
- message (text)
- processed_items (int)
- current_batch (int)
- total_batches (int)
- error_message (text, nullable)
- duration_ms (int)
- created_at

// Indexes
- export_id
- (export_id, event)
- created_at (for cleanup)

// Foreign Keys
- export_id → exports.export_id (cascade delete)
```

---

## API Specifications

### 1. Export Assets to CSV
```http
POST /api/v1/assets/export
Authorization: Bearer {token}
Content-Type: application/json

{
  "format": "csv",
  "columns": ["id", "name", "asset_tag", "status", "location", "assigned_to"],
  "filters": {
    "status_id": 2,
    "location_id": 5,
    "date_from": "2025-01-01",
    "date_to": "2025-10-30"
  },
  "sort_by": "created_at",
  "sort_order": "desc",
  "async": false
}

Response (200 OK) - Small dataset:
Content-Type: text/csv
Content-Disposition: attachment; filename="assets_2025-10-30.csv"

[CSV data stream]

Response (202 ACCEPTED) - Large dataset:
{
  "export_id": "export-648a3f2c",
  "status": "pending",
  "message": "Export queued for processing. Email notification will be sent when complete.",
  "check_status_url": "/api/v1/exports/export-648a3f2c"
}
```

### 2. Export Tickets to Excel
```http
POST /api/v1/tickets/export
Authorization: Bearer {token}
Content-Type: application/json

{
  "format": "excel",
  "columns": ["id", "ticket_code", "subject", "status", "priority", "assigned_to", "created_at"],
  "filters": {
    "status_id": [1, 2],
    "priority_id": 1,
    "is_open": true,
    "date_from": "2025-10-01"
  },
  "async": true,
  "email_notification": true
}

Response (202 ACCEPTED):
{
  "export_id": "export-75d9b4e1",
  "status": "pending",
  "message": "Export job created. Processing started. You will receive an email when ready.",
  "estimated_wait": "2-5 minutes for 15,000 items"
}
```

### 3. Get Export Status
```http
GET /api/v1/exports/export-648a3f2c
Authorization: Bearer {token}

Response (200 OK):
{
  "export_id": "export-648a3f2c",
  "resource_type": "assets",
  "format": "csv",
  "status": "completed",
  "total_items": 500,
  "exported_items": 500,
  "failed_items": 0,
  "success_rate": 100.0,
  "file_size": "125000 bytes (122 KB)",
  "created_at": "2025-10-30T14:15:00Z",
  "completed_at": "2025-10-30T14:15:02Z",
  "duration_seconds": 2,
  "download_url": "/api/v1/exports/export-648a3f2c/download",
  "download_count": 3,
  "expires_at": "2025-11-29T14:15:00Z"
}
```

### 4. List Export History
```http
GET /api/v1/exports?status=completed&format=csv&limit=20&page=1
Authorization: Bearer {token}

Response (200 OK):
{
  "data": [
    {
      "export_id": "export-648a3f2c",
      "resource_type": "assets",
      "format": "csv",
      "status": "completed",
      "total_items": 500,
      "file_size": "125 KB",
      "created_at": "2025-10-30T14:15:00Z",
      "download_url": "/api/v1/exports/export-648a3f2c/download"
    }
  ],
  "pagination": {
    "total": 45,
    "per_page": 20,
    "current_page": 1,
    "last_page": 3
  }
}
```

### 5. Download Export File
```http
GET /api/v1/exports/export-648a3f2c/download
Authorization: Bearer {token}

Response (200 OK):
Content-Type: text/csv
Content-Disposition: attachment; filename="assets_2025-10-30.csv"
Content-Length: 125000

[Binary file stream]
```

### 6. Retry Failed Export
```http
POST /api/v1/exports/export-75d9b4e1/retry
Authorization: Bearer {token}

Response (202 ACCEPTED):
{
  "export_id": "export-75d9b4e1",
  "status": "pending",
  "message": "Failed export retry queued. Processing started.",
  "check_status_url": "/api/v1/exports/export-75d9b4e1"
}
```

---

## Implementation Strategy

### Phase 1: Core Infrastructure (90 minutes)
1. ✅ Create ExportBuilder trait (350 lines)
2. ✅ Create Export model (150 lines)
3. ✅ Create ExportLog model (130 lines)
4. ✅ Create request validators (250 lines)

**Deliverable:** Models and validation layer ready

### Phase 2: API & Controller (90 minutes)
1. ✅ Create ExportController (400 lines)
2. ✅ Create ExportDataJob (250 lines)
3. ✅ Create ExportCompleted notification (150 lines)
4. ✅ Create database migrations (130 lines)
5. ✅ Configure routes (10 endpoints)

**Deliverable:** Complete API endpoints functional

### Phase 3: Integration & Testing (60 minutes)
1. ✅ Add ExportBuilder trait to Asset model
2. ✅ Add ExportBuilder trait to Ticket model
3. ✅ Integrate Phase 3.4 filters
4. ✅ Create test cases (40+ scenarios)
5. ✅ Syntax validation
6. ✅ Performance testing

**Deliverable:** Production-ready export system

---

## Test Strategy

### Test Categories (40+ cases)

1. **Asset Export Tests (10 cases)**
   - Export all columns
   - Export selected columns
   - Export with filters
   - Export with sorting
   - Export empty result
   - Export large dataset (async)
   - File format validation
   - Memory efficiency

2. **Ticket Export Tests (10 cases)**
   - Similar to asset tests
   - Multi-value filter support
   - Resolution status handling
   - Assignment tracking

3. **Format Tests (8 cases)**
   - CSV encoding (UTF-8, escaping)
   - Excel formatting
   - JSON structure
   - Special characters handling
   - Empty cells
   - Large strings

4. **Async Job Tests (8 cases)**
   - Queue processing
   - Batch chunking (500 items)
   - Email notification sending
   - Failure and retry
   - File storage
   - Expiration cleanup

5. **Integration Tests (6 cases)**
   - Filter + Export combination
   - Sort + Export combination
   - Multiple exports simultaneously
   - Concurrent downloads

---

## Performance Targets

### Export Performance
```
Small Export (< 1,000 items):
- CSV: < 500ms
- Excel: < 1,000ms
- Target: Immediate download

Medium Export (1,000 - 10,000 items):
- CSV: < 5 seconds
- Excel: < 10 seconds
- Target: Stream download

Large Export (> 10,000 items):
- Async job
- Processing: 1-10 seconds
- Email delivery: < 5 minutes after completion
- Target: 0ms for user (async)
```

### Memory Usage
```
Single export: < 50MB (streaming approach)
Concurrent exports: < 200MB (5 concurrent)
Database: < 10MB (export history)
```

---

## Security & Compliance

### Authorization
- ✅ User can only export authorized data
- ✅ Permission checks: assets.export, tickets.export
- ✅ Role-based filtering (manager sees only team's data)

### Data Security
- ✅ Files stored securely (not in public)
- ✅ Download tokens (signed URLs)
- ✅ 30-day expiration on async exports
- ✅ Automatic cleanup of expired files

### Audit Trail
- ✅ Export creation logged
- ✅ Downloads tracked
- ✅ File deletion logged
- ✅ Failures documented

### Compliance
- ✅ GDPR: User can request data export
- ✅ Audit: Complete history available
- ✅ Data retention: 30 days for exports
- ✅ Encryption: At rest and in transit

---

## Dependencies & Prerequisites

### From Phase 3.4 (Advanced Filtering)
- ✅ FilterBuilder trait with scopes
- ✅ Filter logic and validation
- ✅ DateRange handling
- ✅ Multi-select support

### From Phase 3.5 (Bulk Operations)
- ✅ Batch processing logic
- ✅ Transaction handling
- ✅ Error management patterns
- ✅ User attribution methods

### External Packages
- ✅ league/csv - CSV handling
- ✅ phpoffice/phpspreadsheet - Excel generation
- ✅ Laravel Queue - Async processing

### Database
- ✅ MySQL/MariaDB
- ✅ Filesystem storage

---

## Deployment Checklist

- [ ] Create ExportBuilder trait
- [ ] Create Export model
- [ ] Create ExportLog model
- [ ] Create request validators
- [ ] Create ExportController
- [ ] Create ExportDataJob
- [ ] Create ExportCompleted notification
- [ ] Create migrations
- [ ] Configure routes
- [ ] Add traits to Asset/Ticket models
- [ ] Test all endpoints
- [ ] Performance validation
- [ ] Syntax validation
- [ ] Security audit
- [ ] Documentation
- [ ] Git commit
- [ ] Ready for QA

---

## Success Criteria

✅ **Must Have:**
1. CSV export working for both assets and tickets
2. Excel export working for both assets and tickets
3. Async processing for > 10,000 items
4. Email notifications working
5. All 6 API endpoints functional
6. 0 syntax errors
7. 40+ test cases passing
8. Performance targets met

✅ **Should Have:**
1. Download history tracking
2. Export expiration (30 days)
3. Retry mechanism for failures
4. Advanced filtering integration
5. Sorting support

✅ **Nice to Have:**
1. JSON export format
2. Multiple sheet support in Excel
3. Custom formatting in Excel
4. Export scheduling
5. Export templates

---

## Conclusion

Phase 3.6 builds on the solid foundations of Phase 3.1-3.5, providing **professional-grade export capabilities** with both synchronous and asynchronous processing. The architecture leverages existing patterns (traits, request validators, controllers) while introducing new concepts (file streaming, async jobs, notifications).

**Expected Outcome:**
- Production-ready export system
- Enterprise-grade async processing
- Complete audit trail
- User-friendly error handling
- Extensible for future formats

**Ready to proceed with implementation!**

