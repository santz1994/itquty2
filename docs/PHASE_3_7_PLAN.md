# 📋 PHASE 3.7 PLANNING: Import Validation

**Date:** October 30, 2025  
**Status:** Planning & Design  
**Estimated Duration:** 2-3 hours  
**Dependencies:** Phase 3.6 Export (✅ Complete)

---

## Executive Summary

**Phase 3.7: Import Validation** implements bulk import capability for Assets and Tickets with comprehensive data validation, error handling, conflict resolution, and import history tracking. This phase mirrors Phase 3.6 (Export) but handles data ingestion instead of extraction.

### Business Value
- ✅ Users can bulk import data from CSV/Excel files
- ✅ Comprehensive validation prevents bad data
- ✅ Clear error reporting for corrections
- ✅ Conflict handling (duplicate detection)
- ✅ Complete audit trail for compliance
- ✅ Progress tracking for large imports
- ✅ Retry capability for failed imports

### Key Features
- ✅ CSV & Excel import support
- ✅ Async processing (>10K rows)
- ✅ Real-time validation
- ✅ Error reporting & line-by-line feedback
- ✅ Duplicate detection & conflict resolution
- ✅ Import history & audit trail
- ✅ User-initiated and API import
- ✅ Data preview before commit
- ✅ Rollback capability
- ✅ Email notifications

---

## Deep Analysis

### 1. Business Requirements

#### Import Data Sources
1. **CSV Files**
   - Standard CSV format
   - UTF-8 encoding with BOM
   - Configurable delimiters (comma, semicolon, tab)
   - Header row required (column mapping)

2. **Excel Files**
   - .xlsx format (modern Excel)
   - First sheet used by default
   - Multiple sheets (future enhancement)
   - Auto-detection of header row

3. **API Direct Import**
   - JSON payload (array of objects)
   - Inline validation
   - Immediate processing (smaller batches)

#### Import Scope
- **Assets:** Create/Update existing assets with new data
- **Tickets:** Create/Update tickets with new data
- Future: Add support for other entities (Users, Locations, etc.)

#### User Workflows
1. **Simple Import**
   - Upload file → Validate → Confirm → Import → Done
   - Best for: <1,000 records, standard format

2. **Complex Import with Mapping**
   - Upload file → Map columns → Set rules → Preview → Import → Done
   - Best for: >1,000 records, custom formats, existing data update

3. **Scheduled Import**
   - Configure import schedule → Auto-import on interval
   - Best for: Recurring data feeds from external systems

#### Error Handling Strategy
- **Validation Errors:** Line-specific, user can correct and re-upload
- **Conflicts:** Offer options (skip, update, create new)
- **Database Errors:** Roll back transaction, report issue
- **Partial Success:** Import what's valid, report errors on others

### 2. Technical Architecture

#### Data Pipeline
```
File Upload → Parse → Validate → Transform → Conflict Check → Import
    ↓          ↓         ↓          ↓             ↓              ↓
   HTTP     CsvParser  Rules    Normalize   Duplicate      Database
  Storage   ExcelParser        Database     Detection     Transaction
            JsonParser         Check
```

#### Validation Layers

**Layer 1: File Level**
- File format validation (CSV/Excel)
- Encoding validation (UTF-8)
- File size check (<100MB)
- Header presence

**Layer 2: Column Level**
- Required columns present
- Column mapping valid
- Column count matches
- Header format

**Layer 3: Row Level**
- Data type validation (string, int, date, etc.)
- Required field check
- Length validation
- Format validation (email, phone, etc.)

**Layer 4: Business Level**
- Unique constraint check (serial number, email)
- Foreign key validation (status exists, location exists)
- Data consistency (date ranges, numeric ranges)
- Business rule validation

**Layer 5: Conflict Resolution**
- Duplicate detection (by unique fields)
- Update vs. create decision
- Related data handling

#### Data Models

**Import Model** - Central tracking
```
import_id (UUID)
resource_type (assets, tickets)
import_format (csv, excel, json)
import_status (validating, validated, processing, completed, failed, rolled_back)
file_name
file_path
file_size
total_rows
validated_rows
imported_rows
failed_rows
conflicted_rows
created_by (FK → users)
created_at
completed_at
expires_at (30 days)
error_summary (JSON)
conflict_summary (JSON)
```

**ImportLog Model** - Event audit trail
```
import_id (FK)
event (file_uploaded, validation_started, validation_complete, 
       processing_started, row_imported, row_failed, 
       row_conflict, processing_complete, import_failed, rolled_back)
message
row_number
data (JSON of row being processed)
error_message
resolution (skip, create, update)
```

**ImportConflict Model** - Conflict tracking & resolution
```
import_id (FK)
row_number
conflict_type (duplicate_key, duplicate_record, foreign_key_not_found, 
              invalid_data, business_rule_violation)
existing_record_id
new_record_data (JSON)
suggested_resolution
user_resolution (skip, create_new, update_existing, merge)
resolution_choice_id (FK → new/existing record)
```

#### File Format Specifications

**CSV Format**
```
asset_tag,name,serial_number,status_id,location_id,assigned_to,manufacturer_id
ASSET001,Laptop Dell,SN12345,2,1,1,5
ASSET002,Monitor LG,SN12346,2,2,,
```
- Header row required (column name or column index)
- UTF-8 encoding with BOM
- Standard escaping for quotes and commas
- Date format: YYYY-MM-DD
- Foreign key by ID or by lookup (email, name)

**Excel Format**
```
| asset_tag  | name          | serial_number | status_id | location_id |
|------------|---------------|---------------|-----------|-------------|
| ASSET001   | Laptop Dell   | SN12345       | 2         | 1           |
| ASSET002   | Monitor LG    | SN12346       | 2         | 2           |
```
- First sheet used
- First row as header
- All standard Excel features supported
- Auto-type detection (date, number, text)

**JSON Format**
```json
[
  {
    "asset_tag": "ASSET001",
    "name": "Laptop Dell",
    "serial_number": "SN12345",
    "status_id": 2,
    "location_id": 1
  }
]
```

### 3. API Endpoints Design

#### Endpoint 1: Validate Import (Dry Run)
```
POST /api/v1/assets/import/validate
Content-Type: multipart/form-data

- file: [binary]
- column_mapping: {asset_tag: "A", name: "B", serial_number: "C"}
- import_strategy: create|update|create_if_not_exists
- conflict_resolution: ask|skip|create_new|update

Response (202 ACCEPTED):
{
  "import_id": "import-abc123",
  "status": "validating",
  "message": "Import validation in progress",
  "status_url": "/api/v1/imports/import-abc123"
}
```

#### Endpoint 2: Execute Import
```
POST /api/v1/assets/import
Content-Type: multipart/form-data

- file: [binary]
- column_mapping: {asset_tag: "A", name: "B"}
- import_strategy: create|update
- auto_resolve_conflicts: true|false
- email_on_complete: true

Response (202 ACCEPTED):
{
  "import_id": "import-abc123",
  "status": "processing",
  "message": "Import queued for processing"
}
```

#### Endpoint 3: Get Import Status
```
GET /api/v1/imports/import-abc123

Response (200 OK):
{
  "import_id": "import-abc123",
  "resource_type": "assets",
  "status": "processing",
  "total_rows": 1000,
  "processed_rows": 500,
  "imported_rows": 450,
  "failed_rows": 40,
  "conflicted_rows": 10,
  "progress": 50,
  "message": "Processing... (50% complete)",
  "created_at": "2025-10-30T14:15:00Z"
}
```

#### Endpoint 4: Get Import Conflicts
```
GET /api/v1/imports/import-abc123/conflicts?page=1&limit=50

Response (200 OK):
{
  "data": [
    {
      "row_number": 5,
      "conflict_type": "duplicate_key",
      "existing_record": {asset_id: 1, name: "Laptop"},
      "new_record": {asset_tag: "ASSET001", name: "Laptop Dell"},
      "suggested_resolution": "update_existing"
    }
  ],
  "pagination": {...}
}
```

#### Endpoint 5: Resolve Conflicts
```
POST /api/v1/imports/import-abc123/resolve-conflicts

{
  "resolutions": [
    {
      "row_number": 5,
      "resolution": "update_existing",
      "target_id": 1
    },
    {
      "row_number": 8,
      "resolution": "skip"
    }
  ]
}

Response (202 ACCEPTED):
{
  "message": "Conflicts resolved, continuing import",
  "status_url": "/api/v1/imports/import-abc123"
}
```

#### Endpoint 6: Get Import Logs
```
GET /api/v1/imports/import-abc123/logs?event=row_failed&limit=100

Response (200 OK):
{
  "data": [
    {
      "event": "row_failed",
      "row_number": 5,
      "message": "Serial number must be unique",
      "error_message": "Duplicate entry for serial_number",
      "timestamp": "2025-10-30T14:15:15Z"
    }
  ],
  "pagination": {...}
}
```

#### Endpoint 7: Cancel Import
```
POST /api/v1/imports/import-abc123/cancel

Response (202 ACCEPTED):
{
  "message": "Import cancellation requested"
}
```

#### Endpoint 8: Rollback Import
```
POST /api/v1/imports/import-abc123/rollback

Response (202 ACCEPTED):
{
  "message": "Import rolled back successfully"
}
```

### 4. Component Design

#### ImportValidator Trait
**Purpose:** Reusable import validation logic (mirrors ExportBuilder)

**Methods:**
```php
// Main entry points
importFromCSV($filePath, $columnMapping, $strategy)
importFromExcel($filePath, $columnMapping, $strategy)
importFromJSON($data, $strategy)

// Core orchestration
performImport($format, $data, $columnMapping, $strategy)

// Validation
validateFile($filePath, $format)
validateRow($row, $columnMapping, $rowNumber)
validateBusinessRules($row, $rowNumber)
checkForConflicts($row, $strategy)

// Processing
parseFile($filePath, $format)
transformRow($row, $columnMapping)
persistRow($row, $strategy, $conflict)

// Utilities
getColumnMapping($header, $autoDetect)
getImportHistory($limit, $status)
getImportStatus($importId)
retryFailedImport($importId)
rollbackImport($importId)
```

#### ImportDataJob
**Purpose:** Async background processing (mirrors ExportDataJob)

**Process:**
1. Mark import as processing
2. Read file/data in chunks
3. For each chunk:
   - Parse rows
   - Validate rows
   - Check conflicts
   - Transform data
   - Insert/update in database
   - Log progress
4. Handle failures
5. Generate summary
6. Send notification
7. Set expiration

**Configuration:**
- Timeout: 600 seconds (10 min)
- Retries: 3
- Backoff: 60 seconds
- Chunk size: 100-1000 rows

#### ImportCompleted Notification
**Variants:**
- Success email (imported count, conflicts handled, file expires in 30 days)
- Partial success (some imported, some failed with error summary)
- Failure email (error details, retry option)

### 5. Data Transformation

#### Column Mapping Strategy
**Auto-Detection:**
- By column name (case-insensitive)
- By column index (A, B, C or 1, 2, 3)
- By header similarity (fuzzy match)

**User-Provided Mapping:**
```json
{
  "asset_tag": "A",
  "name": "B", 
  "serial_number": "C",
  "status_id": "D",
  "location_id": "E"
}
```

**Transformation Rules:**
```php
// Type conversion
"2" → 2 (int)
"2025-01-15" → Carbon (date)
"active" → 1 (lookup value by name)

// Reference resolution
"New York" → 1 (location_id from location name)
"john@example.com" → 5 (user_id from email)

// Default values
null → use default if configured
"" → null or default

// Custom transformations
"$500.00" → 500 (remove currency)
"2025-01-15 14:30" → 2025-01-15 (just date)
```

### 6. Conflict Resolution

#### Conflict Types

1. **Duplicate Key**
   - Same unique field value exists
   - Options: skip, update existing, create new

2. **Duplicate Record**
   - Similar record exists (fuzzy match)
   - Options: skip, merge, create new

3. **Foreign Key Not Found**
   - Referenced record doesn't exist
   - Options: skip, create referenced record, use default

4. **Invalid Data**
   - Data type mismatch or validation failure
   - Options: skip, use default, manual correction

5. **Business Rule Violation**
   - Custom validation rule failed
   - Options: skip, manual review, override

#### Resolution Strategies

**Strategy 1: Create (Default)**
- Insert new records if doesn't exist
- Skip if already exists
- Best for: New data imports

**Strategy 2: Update**
- Update existing records
- Create if doesn't exist
- Best for: Data synchronization

**Strategy 3: Create If Not Exists**
- Mix of create and skip
- Never update existing
- Best for: Avoiding overwrites

**Strategy 4: Manual Review**
- Flag conflicts for user review
- Don't auto-resolve
- Best for: Critical data

### 7. Performance Considerations

#### Large File Handling (>100K rows)
- Stream processing (not load entire file)
- Batch database inserts (100-1000 rows per transaction)
- Progress tracking
- Estimated time calculation
- Memory management

#### Query Optimization
- Single query for conflict detection per row
- Batch lookup for foreign keys
- Index usage for unique checks
- Connection pooling

#### Concurrency
- Handle 5+ simultaneous imports
- Per-user import queue
- Prevent duplicate imports

### 8. Security & Compliance

#### Input Validation
- File type validation (whitelist)
- File size limit (100MB)
- Encoding validation
- SQL injection prevention (all data parameterized)
- XSS prevention in error messages

#### Authorization
- User can only import to their own account
- Admin can import on behalf of others
- Audit trail of who imported what

#### Data Privacy
- No sensitive data in error messages
- Temporary file cleanup
- 30-day retention policy
- Compliance with data protection

#### Error Tracking
- Detailed logging of all failures
- Error patterns identification
- User feedback collection

### 9. Testing Strategy

#### Test Categories

1. **File Parsing (8 cases)**
   - CSV parsing with various delimiters
   - Excel parsing (multiple sheets)
   - JSON parsing
   - Encoding detection
   - Large file handling
   - Malformed file handling
   - Empty file handling

2. **Validation Tests (12 cases)**
   - Required fields
   - Data types
   - Format validation
   - Length validation
   - Unique constraints
   - Foreign keys
   - Date ranges
   - Custom rules

3. **Conflict Resolution (8 cases)**
   - Duplicate detection
   - Fuzzy matching
   - Conflict resolution options
   - User choice persistence
   - Rollback conflict state

4. **Data Transformation (6 cases)**
   - Type conversion
   - Reference lookup
   - Default values
   - Custom transformations
   - Null handling

5. **Import Strategies (6 cases)**
   - Create strategy
   - Update strategy
   - Create-if-not-exists
   - Manual review
   - Error recovery

6. **Async Processing (8 cases)**
   - Job dispatch
   - Chunk processing
   - Progress tracking
   - Timeout handling
   - Retry mechanism
   - Email notifications

7. **Performance (4 cases)**
   - Large file import (100K rows)
   - Concurrent imports
   - Query optimization
   - Memory efficiency

8. **API Integration (6 cases)**
   - Validation endpoint
   - Import endpoint
   - Status tracking
   - Conflict resolution
   - Logs retrieval
   - Rollback operation

9. **Error Handling (6 cases)**
   - Invalid format
   - Missing required columns
   - Data type mismatch
   - Foreign key error
   - Timeout error
   - Disk space error

**Total: 54+ test cases**

### 10. Implementation Phases

#### Phase 1: Core Infrastructure (45 min)
- ✅ Create Import model
- ✅ Create ImportLog model
- ✅ Create ImportConflict model
- ✅ Create database migrations
- ✅ Create request validators

#### Phase 2: Import Logic (45 min)
- ✅ Create ImportValidator trait
- ✅ Implement CSV parser
- ✅ Implement Excel parser
- ✅ Implement JSON parser
- ✅ Implement validation logic
- ✅ Implement conflict detection

#### Phase 3: API & Processing (30 min)
- ✅ Create ImportController (8 endpoints)
- ✅ Create ImportDataJob
- ✅ Create ImportCompleted notification
- ✅ Configure routes
- ✅ Test endpoints

---

## Integration Points

### With Phase 3.6 (Export)
- Inverse operation (export → import)
- Similar data models (Import mirrors Export)
- Reuse ExportLog pattern for ImportLog
- Shared error handling patterns

### With Phase 3.4 (Filtering)
- Use FilterBuilder for validation rules
- Apply same filters to import preview

### With Phase 3.5 (Bulk Operations)
- Reuse async framework
- Reuse batch processing patterns
- Reuse error recovery

### With Phase 3.1-3.3 (Core)
- Use existing models & relationships
- Leverage optimized queries
- Use search indexing for lookups

---

## Dependency Requirements

✅ **Met from Phase 3.6:**
- Async job architecture
- Error handling patterns
- Notification system
- File handling approach
- Audit trail logging

✅ **Met from Phase 3.5:**
- Batch processing framework
- User attribution
- Error recovery

✅ **Met from Phase 3.4:**
- Advanced validation rules
- Complex filtering

---

## Success Criteria

### Implementation Criteria
- ✅ CSV import (3+ delimiters)
- ✅ Excel import (.xlsx format)
- ✅ JSON import (API)
- ✅ Async processing (>10K rows)
- ✅ Conflict detection & resolution
- ✅ Email notifications
- ✅ Import history tracking
- ✅ Complete audit trail
- ✅ 8 API endpoints
- ✅ 3 database tables

### Quality Criteria
- ✅ 0 syntax errors
- ✅ 40+ test cases documented
- ✅ 100% backward compatible
- ✅ Performance: <5 min for 100K rows
- ✅ Security: Authorization, input validation
- ✅ Comprehensive documentation

### Deployment Criteria
- ✅ All code committed
- ✅ Migrations ready
- ✅ Routes configured
- ✅ Integration verified
- ✅ Ready for production

---

## Estimated Timeline

| Component | Estimated Time | Status |
|-----------|----------------|--------|
| Planning & Design | 30 min | 🔄 In Progress |
| Models & Migrations | 30 min | ⏳ Not Started |
| Import Logic | 45 min | ⏳ Not Started |
| Controllers & API | 30 min | ⏳ Not Started |
| Testing Docs | 30 min | ⏳ Not Started |
| Documentation | 45 min | ⏳ Not Started |
| **Total** | **2.5 hours** | 🔄 Planning |

---

## Next Steps

1. ✅ **Complete planning** (this document)
2. ⏳ Create database models and migrations
3. ⏳ Implement ImportValidator trait
4. ⏳ Create CSV/Excel/JSON parsers
5. ⏳ Implement conflict detection
6. ⏳ Create ImportController (8 endpoints)
7. ⏳ Create ImportDataJob
8. ⏳ Create ImportCompleted notification
9. ⏳ Configure routes
10. ⏳ Create comprehensive testing documentation
11. ⏳ Write implementation report
12. ⏳ Create project status update

---

**Plan Status:** 📋 **COMPLETE**  
**Ready for Implementation:** ✅ YES  
**Next Action:** Create database models and migrations

