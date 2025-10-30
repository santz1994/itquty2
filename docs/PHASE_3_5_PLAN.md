# Phase 3.5: Bulk Operations Implementation Plan

**Date Created:** October 30, 2025  
**Phase:** 3.5 of 8  
**Estimated Duration:** 3-4 hours  
**Status:** PLANNING & IMPLEMENTATION

---

## 1. Executive Summary

Phase 3.5 introduces **Bulk Operations** - enabling administrators and staff to perform mass updates on assets and tickets efficiently while maintaining transaction safety, audit compliance, and system integrity.

### Key Objectives
- ✅ Batch update operations (status, assignment, fields)
- ✅ Transaction safety with automatic rollback
- ✅ Comprehensive audit logging
- ✅ Validation at batch level before execution
- ✅ Performance: Process 10,000 items in <2 seconds
- ✅ Partial success handling (validate all before updating any)

### Business Value
- **Efficiency:** Update 100+ items in single API call
- **Compliance:** Complete audit trail for all bulk changes
- **Safety:** All-or-nothing transaction semantics
- **Control:** Dry-run capability before committing

---

## 2. Technical Architecture

### 2.1 Core Components

#### A. BulkOperationBuilder Trait (`app/Traits/BulkOperationBuilder.php`)
**Purpose:** Centralized bulk operation logic for assets and tickets  
**Scope:** 350-400 lines

```php
// Key Methods
- scopeBulkUpdateStatus($statusIds, $newStatus)
- scopeBulkUpdateAssignment($assetIds, $userId)
- scopeBulkUpdateField($assetIds, $field, $value)
- scopeBulkUpdateMultipleFields($assetIds, $fieldsArray)
- doBulkOperation($operation, $parameters)
- validateBulkUpdate($ids, $updateData)
- rollbackOnError()
- logBulkOperation($action, $data)
```

**Features:**
- Validation before any updates
- Transaction management (DB::beginTransaction, commit, rollback)
- Audit trail logging
- Error collection and partial success handling
- Performance optimization using chunk processing

#### B. Request Validators (120-140 lines each)

**`app/Http/Requests/AssetBulkUpdateRequest.php`**
- Validates asset IDs array
- Validates update payload (status_id, assigned_to, etc.)
- Cross-field validation (status consistency)
- Permission checks

**`app/Http/Requests/TicketBulkUpdateRequest.php`**
- Validates ticket IDs array
- Validates update payload (status_id, priority_id, assigned_to, etc.)
- Ticket-specific validation (resolution status)
- Permission checks

#### C. BulkOperationController (`app/Http/Controllers/API/BulkOperationController.php`)
**Purpose:** Handle all bulk operation endpoints  
**Scope:** 350-450 lines

```php
// Main Methods
- bulkUpdateStatus(Request $request)           // Batch status updates
- bulkUpdateAssignment(Request $request)       // Batch reassignments
- bulkUpdateFields(Request $request)           // Generic field updates
- bulkStatusCheck($operationId)                // Check bulk operation status
- bulkOperationHistory($resourceType)          // Audit history

// Helper Methods
- performBulkUpdate()
- processInChunks()
- generateOperationId()
- validateBeforeExecution()
```

**Features:**
- Pre-flight validation (dry-run mode)
- Async processing for large batches
- Real-time progress tracking
- Comprehensive error reporting
- Rollback on any failure

#### D. Models: BulkOperation & BulkOperationLog
**Purpose:** Track bulk operations for audit trail

**`app/BulkOperation.php`** - Tracks ongoing/completed operations
```php
Properties:
- id (UUID)
- resource_type (assets/tickets)
- operation_type (status, assignment, field_update)
- status (pending, processing, completed, failed)
- total_items, processed_items, failed_items
- created_by, created_at, completed_at
- error_details (JSON)
```

**`app/BulkOperationLog.php`** - Individual item records
```php
Properties:
- id
- bulk_operation_id (FK)
- resource_id (asset_id or ticket_id)
- old_values (JSON)
- new_values (JSON)
- status (success, failed)
- error_message
- created_at
```

---

## 3. API Endpoints Design

### 3.1 Asset Bulk Operations

#### Endpoint 1: Bulk Update Status
```
POST /api/v1/assets/bulk/status
Content-Type: application/json

Request Payload:
{
  "asset_ids": [1, 2, 3, 4, 5],
  "status_id": 2,
  "reason": "Decommissioned - Hardware failure",
  "dry_run": false
}

Response (200 OK):
{
  "operation_id": "bulk-op-uuid-1234",
  "resource_type": "assets",
  "operation_type": "status_update",
  "total_items": 5,
  "processed_items": 5,
  "failed_items": 0,
  "status": "completed",
  "results": {
    "success": [
      {
        "asset_id": 1,
        "old_status": "Active",
        "new_status": "Decommissioned"
      }
    ],
    "failed": []
  },
  "audit_id": "audit-log-xyz",
  "completed_at": "2025-10-30T14:30:00Z"
}

Error Response (422 Unprocessable Entity):
{
  "message": "Validation failed for bulk operation",
  "errors": {
    "asset_ids": ["Invalid asset ID: 999"],
    "status_id": ["Status cannot be changed to Archived for active assets"]
  },
  "operation_id": null
}
```

#### Endpoint 2: Bulk Update Assignment
```
POST /api/v1/assets/bulk/assign
Content-Type: application/json

Request Payload:
{
  "asset_ids": [1, 2, 3],
  "assigned_to": 5,
  "department_id": 2,
  "dry_run": false
}

Response (200 OK):
{
  "operation_id": "bulk-op-assign-1",
  "total_items": 3,
  "processed_items": 3,
  "failed_items": 0,
  "results": {
    "success": [
      {
        "asset_id": 1,
        "old_assignment": "User 3",
        "new_assignment": "User 5"
      }
    ]
  }
}
```

#### Endpoint 3: Bulk Update Custom Fields
```
POST /api/v1/assets/bulk/update-fields
Content-Type: application/json

Request Payload:
{
  "asset_ids": [1, 2, 3, 4, 5],
  "updates": {
    "location_id": 10,
    "warranty_expiry_date": "2026-12-31",
    "notes": "Bulk field update from inventory reconciliation"
  },
  "dry_run": false
}

Response (200 OK):
{
  "operation_id": "bulk-op-fields-1",
  "total_items": 5,
  "processed_items": 5,
  "failed_items": 0,
  "status": "completed"
}
```

### 3.2 Ticket Bulk Operations

#### Endpoint 4: Bulk Update Ticket Status
```
POST /api/v1/tickets/bulk/status
Content-Type: application/json

Request Payload:
{
  "ticket_ids": [1, 2, 3, 4],
  "status_id": 4,  // Resolved
  "is_resolved": true,
  "resolution_notes": "Bulk resolved - duplicate tickets",
  "dry_run": false
}

Response (200 OK):
{
  "operation_id": "bulk-ticket-op-1",
  "total_items": 4,
  "processed_items": 4,
  "failed_items": 0,
  "results": {
    "success": [...]
  }
}
```

#### Endpoint 5: Bulk Reassign Tickets
```
POST /api/v1/tickets/bulk/assign
Content-Type: application/json

Request Payload:
{
  "ticket_ids": [1, 2, 3],
  "assigned_to": 7,
  "priority_id": 2,  // Optional: also update priority
  "dry_run": false
}

Response (200 OK):
{
  "operation_id": "bulk-ticket-assign-1",
  "total_items": 3,
  "processed_items": 3
}
```

#### Endpoint 6: Bulk Update Multiple Fields
```
POST /api/v1/tickets/bulk/update-fields
Content-Type: application/json

Request Payload:
{
  "ticket_ids": [5, 6, 7, 8],
  "updates": {
    "priority_id": 1,
    "assigned_to": 3,
    "type_id": 2,
    "due_date": "2025-11-15"
  },
  "dry_run": false
}

Response (200 OK):
```

### 3.3 Utility Endpoints

#### Endpoint 7: Get Bulk Operation Status
```
GET /api/v1/bulk-operations/{operation_id}

Response (200 OK):
{
  "operation_id": "bulk-op-uuid",
  "resource_type": "assets",
  "operation_type": "status_update",
  "status": "completed",
  "total_items": 100,
  "processed_items": 100,
  "failed_items": 0,
  "progress_percentage": 100,
  "created_at": "2025-10-30T14:00:00Z",
  "completed_at": "2025-10-30T14:02:15Z",
  "duration_seconds": 135,
  "created_by": {
    "id": 1,
    "name": "Admin User"
  }
}
```

#### Endpoint 8: Bulk Operations History
```
GET /api/v1/bulk-operations?resource_type=assets&limit=20

Response (200 OK):
{
  "data": [
    {
      "operation_id": "bulk-op-uuid-1",
      "resource_type": "assets",
      "operation_type": "status_update",
      "status": "completed",
      "total_items": 50,
      "failed_items": 0,
      "created_by": "Admin",
      "created_at": "2025-10-30T14:00:00Z"
    }
  ],
  "pagination": {...}
}
```

---

## 4. Implementation Strategy

### 4.1 Development Phases

#### Phase 4A: Core Infrastructure (60 minutes)
1. Create `BulkOperationBuilder` trait with base methods
2. Create `BulkOperation` and `BulkOperationLog` models
3. Create database migrations for bulk operation tracking
4. Set up request validators

**Files to Create:**
- `app/Traits/BulkOperationBuilder.php`
- `app/BulkOperation.php`
- `app/BulkOperationLog.php`
- `app/Http/Requests/AssetBulkUpdateRequest.php`
- `app/Http/Requests/TicketBulkUpdateRequest.php`
- `database/migrations/create_bulk_operations_table.php`
- `database/migrations/create_bulk_operation_logs_table.php`

#### Phase 4B: Controller & Routes (60 minutes)
1. Create `BulkOperationController` with 8 endpoints
2. Add model integration (Asset.php, Ticket.php)
3. Configure routes
4. Implement error handling

**Files to Create/Modify:**
- `app/Http/Controllers/API/BulkOperationController.php`
- Modify: `app/Asset.php` (add BulkOperationBuilder trait)
- Modify: `app/Ticket.php` (add BulkOperationBuilder trait)
- Modify: `routes/api.php` (add 8 bulk operation routes)

#### Phase 4C: Testing & Documentation (60 minutes)
1. Create comprehensive test cases (30+ scenarios)
2. Document all endpoints with examples
3. Performance testing (10,000 items)
4. Git commits and status reporting

---

## 5. Validation Strategy

### 5.1 Pre-Execution Validation

**Asset Bulk Update Validation:**
- ✅ All asset IDs exist and belong to same organization
- ✅ User has permission to modify assets
- ✅ Status transition is valid (status state machine)
- ✅ If assigning to user: user exists and is active
- ✅ If updating field values: values are valid type/range
- ✅ No circular assignments (asset assigned to itself, etc.)

**Ticket Bulk Update Validation:**
- ✅ All ticket IDs exist and belong to same organization
- ✅ User has permission to modify tickets
- ✅ Status transition is valid
- ✅ If assigning: user exists and is active
- ✅ Resolution status consistent with status_id
- ✅ Priority levels valid

### 5.2 Dry-Run Mode

All endpoints support `"dry_run": true`:
- Performs all validations
- Does NOT commit changes
- Returns success response with "DRY_RUN_SUCCESS" status
- Allows user to preview changes before committing
- Provides error details if validation fails

---

## 6. Transaction & Rollback Strategy

### 6.1 All-or-Nothing Semantics

```php
DB::transaction(function () {
    // Validate all items first
    foreach ($items as $item) {
        if (!$this->validate($item)) {
            throw new ValidationException(...);
        }
    }
    
    // Process all updates (if any validation fails, entire transaction rolls back)
    foreach ($items as $item) {
        $this->update($item);
    }
    
    // Log the operation
    $this->logBulkOperation(...);
});
```

**Benefits:**
- No partial updates on validation failure
- Automatic rollback on any error
- Consistent database state
- Clear success/failure status

---

## 7. Audit Logging

### 7.1 Bulk Operation Audit Trail

Every bulk operation creates:
1. **BulkOperation record** - High level operation tracking
2. **BulkOperationLog entries** - Individual item records with old/new values

```php
BulkOperation fields:
- operation_id (UUID)
- resource_type ('assets' or 'tickets')
- operation_type ('status_update', 'assignment', 'field_update')
- status ('completed', 'failed', 'partial')
- created_by (user_id)
- created_at
- completed_at
- total_items
- processed_items
- failed_items

BulkOperationLog fields (per item):
- bulk_operation_id (FK)
- resource_id (asset_id or ticket_id)
- old_values: JSON snapshot before update
- new_values: JSON snapshot after update
- status ('success' or 'failed')
- error_message (if failed)
```

### 7.2 Auditability
- All changes attributable to specific user
- Timestamp of operation
- Complete before/after values
- Support for compliance reporting

---

## 8. Performance Optimization

### 8.1 Bulk Operation Performance Targets

**Target:** Process 10,000 items in <2 seconds

**Optimization Strategies:**

1. **Batch Processing**
   ```php
   // Process in chunks of 500
   foreach ($ids->chunk(500) as $chunk) {
       Model::whereIn('id', $chunk)->update([...]);
   }
   ```

2. **Minimal Query Overhead**
   - Single UPDATE query per batch
   - No individual item queries
   - Bulk insert for logs

3. **Connection Pooling**
   - Reuse database connection
   - Prepare statement caching

4. **Indexing Strategy**
   - Foreign key indexes on bulk_operation_id
   - Composite index (bulk_operation_id, resource_id)

### 8.2 Expected Performance

| Operation | Items | Time |
|-----------|-------|------|
| Bulk status update | 100 | ~150ms |
| Bulk status update | 1,000 | ~800ms |
| Bulk status update | 10,000 | ~1,500ms |
| Bulk field update | 10,000 | ~1,800ms |

---

## 9. Error Handling

### 9.1 Error Categories

**Validation Errors (422):**
```json
{
  "message": "Validation failed",
  "errors": {
    "asset_ids": ["Asset ID 999 does not exist"],
    "status_id": ["Status transition from Active to Reserved not allowed"]
  }
}
```

**Permission Errors (403):**
```json
{
  "message": "Unauthorized",
  "reason": "User does not have permission to update assets"
}
```

**Operation Errors (500):**
```json
{
  "message": "Bulk operation failed",
  "operation_id": "bulk-op-xyz",
  "error_details": {
    "total_items": 100,
    "processed_items": 45,
    "failed_at_item": 46,
    "error": "Database connection lost"
  }
}
```

### 9.2 Retry Strategy
- Failed operations can be retried
- Already-processed items are skipped (operation_id tracking)
- Resume from failure point with minimal re-processing

---

## 10. Security Considerations

### 10.1 Authorization Checks

- ✅ User must have bulk-update permission
- ✅ Organization boundary validation (can't update another org's assets)
- ✅ Role-based restrictions (some users can only update certain statuses)
- ✅ Audit trail for compliance

### 10.2 Input Validation

- ✅ Array size limits (max 10,000 items per operation)
- ✅ Type validation for all fields
- ✅ Range validation for numeric fields
- ✅ Enum validation for status/priority values
- ✅ SQL injection prevention (parameterized queries)

### 10.3 Rate Limiting

- ✅ Bulk operations use aggressive rate limiting
- ✅ Max 5 operations per user per minute
- ✅ Queue for large operations (>1000 items)

---

## 11. Testing Strategy

### 11.1 Test Cases (30+ scenarios)

**Asset Bulk Operations:**
1. Bulk update status - single status to multiple items
2. Bulk update status - invalid status ID
3. Bulk update status - status not allowed for current status
4. Bulk update assignment - reassign to valid user
5. Bulk update assignment - user doesn't exist
6. Bulk update assignment - user inactive
7. Bulk update fields - update location_id
8. Bulk update fields - invalid location_id
9. Bulk update fields - warranty_expiry_date in past
10. Dry-run status update (no database changes)
11. Dry-run with validation errors
12. Mix valid and invalid IDs (should fail entire batch)
13. Empty asset_ids array
14. asset_ids with duplicates
15. Large batch (1000 items)

**Ticket Bulk Operations:**
1. Bulk update status - resolve multiple tickets
2. Bulk update status - invalid status transition
3. Bulk update assignment - reassign to user
4. Bulk reassign and update priority together
5. Bulk update resolution status
6. Dry-run ticket update
7. Mixed valid/invalid ticket IDs
8. Empty batch

**General:**
1. Unauthorized user (permission denied)
2. Cross-organization boundary violation
3. Operation history retrieval
4. Operation status polling
5. Audit log verification
6. Database rollback on error
7. Concurrent bulk operations
8. Query performance benchmark

---

## 12. Success Criteria

### 12.1 Code Quality
- ✅ 0 syntax errors
- ✅ All files <500 lines (maintainability)
- ✅ 8+ test cases per feature
- ✅ Code coverage >85%

### 12.2 Performance
- ✅ 10,000 items in <2 seconds
- ✅ Individual operations <50ms (status, assignment)
- ✅ Query count <5 per operation
- ✅ Memory usage <100MB for 10,000 items

### 12.3 Reliability
- ✅ 0 database inconsistencies
- ✅ All-or-nothing transaction semantics
- ✅ Complete audit trail
- ✅ Graceful error handling

### 12.4 Documentation
- ✅ 2,000+ lines of documentation
- ✅ API examples for all 8 endpoints
- ✅ 30+ test case scenarios documented
- ✅ Architecture diagrams

---

## 13. Deployment Checklist

### Pre-Deployment
- [ ] Run migrations (create_bulk_operations_table, create_bulk_operation_logs_table)
- [ ] Run all test cases (30+)
- [ ] Performance benchmark (10,000 items)
- [ ] Security audit (authorization checks)
- [ ] Code review

### Post-Deployment
- [ ] Monitor bulk operation success rates
- [ ] Check audit logs for proper recording
- [ ] Monitor performance metrics
- [ ] Gather user feedback
- [ ] Create documentation for users

---

## 14. Next Phase Preview (Phase 3.6)

After Phase 3.5 completion, Phase 3.6 (Export Functionality) becomes ready:
- Build on filtering (Phase 3.4) + bulk operations (Phase 3.5)
- Export filtered/bulk-selected data to CSV/Excel
- Async export for large datasets
- Email delivery option

---

## 15. File Manifest

**Files to Create This Phase:**
1. `app/Traits/BulkOperationBuilder.php` (350-400 lines)
2. `app/BulkOperation.php` (60-80 lines)
3. `app/BulkOperationLog.php` (60-80 lines)
4. `app/Http/Requests/AssetBulkUpdateRequest.php` (120-140 lines)
5. `app/Http/Requests/TicketBulkUpdateRequest.php` (120-140 lines)
6. `app/Http/Controllers/API/BulkOperationController.php` (350-450 lines)
7. `database/migrations/create_bulk_operations_table.php` (60-80 lines)
8. `database/migrations/create_bulk_operation_logs_table.php` (70-90 lines)

**Files to Modify:**
9. `app/Asset.php` (add BulkOperationBuilder trait)
10. `app/Ticket.php` (add BulkOperationBuilder trait)
11. `routes/api.php` (add 8 bulk operation routes)

**Documentation Files:**
12. `docs/PHASE_3_5_TESTING.md` (30+ test cases)
13. `docs/PHASE_3_5_COMPLETE.md` (implementation report)

**Total Code Lines:** 1,600+ lines  
**Total Documentation Lines:** 1,500+ lines

---

## 16. Timeline Estimate

| Task | Duration | Cumulative |
|------|----------|-----------|
| Create core infrastructure | 60 min | 60 min |
| Create controller & routes | 60 min | 120 min |
| Documentation & testing | 60 min | 180 min |
| **Total Estimated** | **180 min** | **3 hours** |

Actual time may vary based on debugging and refinements.

---

## Deep Thinking Analysis

### Why This Architecture?

1. **BulkOperationBuilder Trait**
   - Reusable across Asset and Ticket models
   - Follows Phase 3.4 pattern (FilterBuilder trait)
   - Keeps models focused on domain logic
   - Enables future models to use bulk operations

2. **Separate Request Validators**
   - Domain-specific validation (assets vs tickets)
   - Cleaner than generic validator
   - Extensible for future fields
   - Clear error messaging

3. **Transaction Safety**
   - All-or-nothing semantics prevent partial updates
   - Database consistency guaranteed
   - Simplifies error handling
   - Aligns with ACID principles

4. **Audit Logging**
   - Complete compliance trail
   - Before/after values captured
   - User accountability
   - Supports regulatory requirements

5. **Performance Optimization**
   - Batch processing prevents N+1 queries
   - Chunking strategy prevents memory overflow
   - Single UPDATE query per batch
   - Scales to 10,000+ items

### Risk Mitigation

- **Data Loss:** Transaction rollback + audit trail
- **Performance:** Batch processing + connection pooling
- **Security:** Permission checks + input validation
- **Usability:** Dry-run mode + detailed error messages

---

**Status:** READY FOR IMPLEMENTATION ✅

Next: Begin Phase 3.5A (Core Infrastructure)
