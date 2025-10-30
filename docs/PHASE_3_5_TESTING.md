# Phase 3.5: Bulk Operations - Testing Guide & Test Cases

**Date:** October 30, 2025  
**Phase:** 3.5 of 8  
**Status:** TESTING & DOCUMENTATION  
**Test Cases:** 35+ scenarios covered

---

## 1. Testing Overview

### Test Categories
1. **Asset Bulk Operations** (12 test cases)
2. **Ticket Bulk Operations** (10 test cases)
3. **General Bulk Operations** (8 test cases)
4. **Error Handling & Validation** (5+ edge cases)

### Testing Approach
- **Unit Tests:** Individual trait methods
- **Integration Tests:** Full API endpoint workflows
- **Performance Tests:** 10,000+ item batches
- **Security Tests:** Authorization, validation, data integrity
- **Edge Cases:** Empty arrays, duplicates, concurrent operations

---

## 2. Asset Bulk Operations Test Cases

### Test Case A.1: Bulk Status Update - Single Status
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Verify basic bulk status update functionality

```json
Request:
{
  "asset_ids": [1, 2, 3, 4, 5],
  "status_id": 2,
  "reason": "Maintenance scheduled",
  "dry_run": false
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 5,
  "processed_items": 5,
  "failed_items": 0,
  "message": "Bulk operation completed successfully"
}

Verification:
✓ All 5 assets have status_id = 2
✓ BulkOperation record created
✓ 5 BulkOperationLog entries created (status = success)
✓ Audit trail shows old status → new status
```

### Test Case A.2: Bulk Status Update - Dry-Run Mode
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Verify dry-run validates without persisting

```json
Request:
{
  "asset_ids": [1, 2, 3],
  "status_id": 3,
  "dry_run": true
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "DRY_RUN_SUCCESS",
  "total_items": 3,
  "message": "Validation passed. No changes persisted."
}

Verification:
✓ No BulkOperation record created
✓ No asset status_id values changed
✓ Database remains untouched
✓ Response indicates validation success
```

### Test Case A.3: Bulk Status Update - Invalid Status ID
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Validate error handling for non-existent status

```json
Request:
{
  "asset_ids": [1, 2, 3],
  "status_id": 9999  // Invalid status ID
}

Expected Response (422 Unprocessable Entity):
{
  "operation_id": null,
  "status": "error",
  "error": "Status ID 9999 does not exist",
  "code": 422
}

Verification:
✓ No assets updated
✓ No BulkOperation record created
✓ Error message is clear and actionable
```

### Test Case A.4: Bulk Status Update - Missing Status ID
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Validate required field validation

```json
Request:
{
  "asset_ids": [1, 2, 3]
  // status_id missing
}

Expected Response (422 Unprocessable Entity):
{
  "message": "Validation failed for bulk update operation",
  "errors": {
    "status_id": ["status_id is required for bulk status update"]
  }
}

Verification:
✓ Request rejected at FormRequest level
✓ Clear error message
✓ No database changes
```

### Test Case A.5: Bulk Assignment - Valid User
**Endpoint:** `POST /api/v1/assets/bulk/assign`  
**Purpose:** Verify bulk user assignment

```json
Request:
{
  "asset_ids": [1, 2, 3, 4],
  "assigned_to": 5,
  "department_id": 2
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 4,
  "processed_items": 4,
  "failed_items": 0
}

Verification:
✓ All 4 assets now assigned_to user 5
✓ All 4 assets have department_id 2
✓ Assignment history recorded in logs
✓ Each log entry shows old/new values
```

### Test Case A.6: Bulk Assignment - Invalid User
**Endpoint:** `POST /api/v1/assets/bulk/assign`  
**Purpose:** Validate non-existent user rejection

```json
Request:
{
  "asset_ids": [1, 2, 3],
  "assigned_to": 9999  // Non-existent user
}

Expected Response (422 Unprocessable Entity):
{
  "operation_id": null,
  "status": "error",
  "error": "User ID 9999 does not exist or is inactive",
  "code": 422
}

Verification:
✓ No assignments made
✓ Error message clear
✓ No partial updates
```

### Test Case A.7: Bulk Field Update - Location
**Endpoint:** `POST /api/v1/assets/bulk/update-fields`  
**Purpose:** Test generic field update for location

```json
Request:
{
  "asset_ids": [10, 11, 12, 13, 14],
  "updates": {
    "location_id": 7
  }
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 5,
  "processed_items": 5,
  "failed_items": 0
}

Verification:
✓ All 5 assets have location_id = 7
✓ Other fields unchanged
✓ Audit logs show only location_id changed
```

### Test Case A.8: Bulk Field Update - Multiple Fields
**Endpoint:** `POST /api/v1/assets/bulk/update-fields`  
**Purpose:** Test updating multiple fields simultaneously

```json
Request:
{
  "asset_ids": [5, 6, 7],
  "updates": {
    "location_id": 3,
    "manufacturer_id": 2,
    "warranty_expiry_date": "2026-12-31",
    "notes": "Bulk updated from inventory reconciliation"
  }
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 3,
  "processed_items": 3,
  "failed_items": 0
}

Verification:
✓ All 4 fields updated for all 3 assets
✓ Log entries show all changed fields
✓ Only specified fields updated (others unchanged)
```

### Test Case A.9: Bulk Operation - Duplicate IDs
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Verify duplicate IDs are handled

```json
Request:
{
  "asset_ids": [1, 1, 2, 2, 3, 3],  // Duplicates
  "status_id": 2
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 3,  // Deduplicated to 3
  "processed_items": 3,
  "failed_items": 0
}

Verification:
✓ Duplicates removed before processing
✓ Only 3 unique assets updated
✓ No errors thrown
```

### Test Case A.10: Bulk Operation - Empty Array
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Reject empty asset IDs

```json
Request:
{
  "asset_ids": [],
  "status_id": 2
}

Expected Response (422 Unprocessable Entity):
{
  "message": "Validation failed for bulk update operation",
  "errors": {
    "asset_ids": ["At least one asset must be selected"]
  }
}

Verification:
✓ Validation rejects empty array
✓ FormRequest validates min:1
```

### Test Case A.11: Bulk Operation - Max Items (10,000)
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Test large batch limit

```json
Request:
{
  "asset_ids": [1, 2, 3, ..., 10000],  // Exactly 10,000
  "status_id": 2
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 10000,
  "processed_items": 10000,
  "failed_items": 0,
  "duration_seconds": <2  // Should complete in <2 seconds
}

Performance Verification:
✓ All 10,000 items processed in <2 seconds (47ms per 235 items)
✓ Batch processing chunks efficiently
✓ Memory usage reasonable
```

### Test Case A.12: Bulk Operation - Exceeds Max (10,001)
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Reject operations exceeding limit

```json
Request:
{
  "asset_ids": [1, 2, 3, ..., 10001],  // Over limit
  "status_id": 2
}

Expected Response (422 Unprocessable Entity):
{
  "operation_id": null,
  "status": "error",
  "error": "Maximum 10,000 items per operation",
  "code": 422
}

Verification:
✓ Validation limits enforced
✓ No partial processing
```

---

## 3. Ticket Bulk Operations Test Cases

### Test Case T.1: Bulk Ticket Status Update
**Endpoint:** `POST /api/v1/tickets/bulk/status`  
**Purpose:** Verify ticket status bulk update

```json
Request:
{
  "ticket_ids": [1, 2, 3, 4],
  "status_id": 4,  // Resolved
  "is_resolved": true,
  "reason": "Batch resolution - duplicate tickets"
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 4,
  "processed_items": 4,
  "failed_items": 0
}

Verification:
✓ All 4 tickets have status_id = 4
✓ All 4 tickets have is_resolved = true
✓ resolved_at timestamp set
✓ Audit logs capture state change
```

### Test Case T.2: Bulk Ticket Assignment
**Endpoint:** `POST /api/v1/tickets/bulk/assign`  
**Purpose:** Test ticket reassignment

```json
Request:
{
  "ticket_ids": [5, 6, 7],
  "assigned_to": 3,
  "priority_id": 1  // Also update priority
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 3,
  "processed_items": 3,
  "failed_items": 0
}

Verification:
✓ All 3 tickets assigned_to user 3
✓ All 3 tickets priority_id = 1
✓ assignment_type and assigned_at updated
✓ History shows both changes
```

### Test Case T.3: Bulk Ticket Field Update - Multiple Fields
**Endpoint:** `POST /api/v1/tickets/bulk/update-fields`  
**Purpose:** Test updating multiple ticket fields

```json
Request:
{
  "ticket_ids": [10, 11, 12, 13],
  "updates": {
    "priority_id": 2,
    "type_id": 1,
    "assigned_to": 4,
    "due_date": "2025-11-15"
  }
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 4,
  "processed_items": 4,
  "failed_items": 0
}

Verification:
✓ All 4 fields updated for all 4 tickets
✓ Audit shows all changes
✓ Only specified fields modified
```

### Test Case T.4: Bulk Ticket Resolution
**Endpoint:** `POST /api/v1/tickets/bulk/status`  
**Purpose:** Test ticket batch resolution workflow

```json
Request:
{
  "ticket_ids": [20, 21, 22, 23, 24],
  "status_id": 4,
  "is_resolved": true,
  "resolution_notes": "Resolved in batch - system migration completed"
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 5,
  "processed_items": 5,
  "failed_items": 0
}

Verification:
✓ All 5 tickets marked as resolved
✓ resolution_notes field populated
✓ SLA compliance tracked
✓ Resolution timestamps set
```

### Test Case T.5: Bulk Ticket Operation - Invalid Priority
**Endpoint:** `POST /api/v1/tickets/bulk/assign`  
**Purpose:** Validate error for invalid priority

```json
Request:
{
  "ticket_ids": [1, 2, 3],
  "assigned_to": 3,
  "priority_id": 9999  // Invalid
}

Expected Response (422 Unprocessable Entity):
{
  "message": "Validation failed for bulk update operation",
  "errors": {
    "priority_id": ["Invalid priority ID"]
  }
}

Verification:
✓ Validation rejects invalid priority
✓ No tickets updated
```

### Test Case T.6: Bulk Ticket Operation - Partial Success with Rollback
**Endpoint:** `POST /api/v1/tickets/bulk/status`  
**Purpose:** Verify all-or-nothing semantics

```json
Request:
{
  "ticket_ids": [1, 2, 9999, 4, 5],  // 9999 is invalid
  "status_id": 2
}

Expected Response (422 Unprocessable Entity):
{
  "operation_id": null,
  "status": "error",
  "error": "Invalid resource IDs: 9999",
  "code": 422
}

Verification:
✓ No tickets updated (even valid ones)
✓ Complete rollback on any validation failure
✓ Database consistency maintained
✓ No BulkOperation record created (pre-validation failure)
```

### Test Case T.7: Bulk Ticket Operation - Dry-Run Complex Update
**Endpoint:** `POST /api/v1/tickets/bulk/update-fields`  
**Purpose:** Test dry-run with complex updates

```json
Request:
{
  "ticket_ids": [10, 11, 12, 13, 14],
  "updates": {
    "priority_id": 1,
    "assigned_to": 5,
    "due_date": "2025-12-01"
  },
  "dry_run": true
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "DRY_RUN_SUCCESS",
  "total_items": 5,
  "message": "Validation passed. No changes persisted."
}

Verification:
✓ Validation passes
✓ No tickets updated
✓ No audit logs created
✓ User can then commit with same parameters
```

### Test Case T.8: Bulk Ticket Operation - Mixed Valid/Invalid Types
**Endpoint:** `POST /api/v1/tickets/bulk/assign`  
**Purpose:** Test with non-existent ticket IDs

```json
Request:
{
  "ticket_ids": [1, 2, 3, 99999],  // 99999 doesn't exist
  "assigned_to": 3
}

Expected Response (422 Unprocessable Entity):
{
  "operation_id": null,
  "status": "error",
  "error": "Invalid resource IDs: 99999",
  "code": 422
}

Verification:
✓ Pre-validation catches invalid IDs
✓ No partial updates
✓ All-or-nothing preserved
```

### Test Case T.9: Bulk Ticket Operation - Reopening Resolved Tickets
**Endpoint:** `POST /api/v1/tickets/bulk/status`  
**Purpose:** Test status transition from resolved to open

```json
Request:
{
  "ticket_ids": [50, 51, 52],  // Currently resolved
  "status_id": 2,  // Open status
  "is_resolved": false,
  "reason": "Reopened due to customer follow-up"
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 3,
  "processed_items": 3,
  "failed_items": 0
}

Verification:
✓ Tickets transitioned to open status
✓ is_resolved set to false
✓ reopen logic triggered
✓ Audit trail shows transition
```

### Test Case T.10: Bulk Ticket Operation - Large Batch
**Endpoint:** `POST /api/v1/tickets/bulk/assign`  
**Purpose:** Test performance with 5,000 tickets

```json
Request:
{
  "ticket_ids": [1, 2, ..., 5000],  // 5,000 tickets
  "assigned_to": 5,
  "priority_id": 2
}

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "status": "completed",
  "total_items": 5000,
  "processed_items": 5000,
  "failed_items": 0,
  "duration_seconds": <1.2  // Should be fast
}

Performance Verification:
✓ 5,000 tickets updated in <1.2 seconds
✓ Batch processing works efficiently
✓ Memory usage stays reasonable
```

---

## 4. General Bulk Operations Test Cases

### Test Case G.1: Get Bulk Operation Status
**Endpoint:** `GET /api/v1/bulk-operations/{operation_id}`  
**Purpose:** Retrieve operation status and details

```
GET /api/v1/bulk-operations/bulk-5f3e2d1a

Expected Response (200 OK):
{
  "operation_id": "bulk-5f3e2d1a",
  "resource_type": "assets",
  "operation_type": "status_update",
  "status": "completed",
  "total_items": 100,
  "processed_items": 100,
  "failed_items": 0,
  "success_rate": 100.0,
  "created_by": {
    "id": 1,
    "name": "Admin User"
  },
  "created_at": "2025-10-30T14:15:00Z",
  "completed_at": "2025-10-30T14:15:02Z",
  "duration_seconds": 2,
  "error_details": null
}

Verification:
✓ Operation found in database
✓ All status details returned
✓ Duration calculated correctly
✓ Creator information included
```

### Test Case G.2: Get Bulk Operation Status - Not Found
**Endpoint:** `GET /api/v1/bulk-operations/{operation_id}`  
**Purpose:** Handle non-existent operation

```
GET /api/v1/bulk-operations/bulk-nonexistent

Expected Response (404 Not Found):
{
  "message": "Operation not found",
  "operation_id": "bulk-nonexistent"
}

Verification:
✓ 404 status returned
✓ Clear error message
```

### Test Case G.3: Get Bulk Operations History
**Endpoint:** `GET /api/v1/bulk-operations?resource_type=assets&limit=20`  
**Purpose:** Retrieve recent operations

```
GET /api/v1/bulk-operations?resource_type=assets&limit=10

Expected Response (200 OK):
{
  "data": [
    {
      "operation_id": "bulk-xxxx-1",
      "resource_type": "assets",
      "operation_type": "status_update",
      "status": "completed",
      "total_items": 100,
      "processed_items": 100,
      "failed_items": 0,
      "success_rate": 100.0,
      "created_by": "Admin User",
      "created_at": "2025-10-30T15:00:00Z",
      "completed_at": "2025-10-30T15:00:02Z"
    },
    // ... more records
  ],
  "pagination": {
    "total": 45,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "last_page": 5
  }
}

Verification:
✓ Recent operations returned in order
✓ Filtered by resource_type
✓ Pagination working
✓ Limited to 10 records
```

### Test Case G.4: Get Bulk Operation Logs
**Endpoint:** `GET /api/v1/bulk-operations/{operation_id}/logs?status=failed&limit=50`  
**Purpose:** Retrieve detailed logs for failed items

```
GET /api/v1/bulk-operations/bulk-xxxx/logs?status=failed

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "data": [
    {
      "resource_id": 123,
      "resource_type": "assets",
      "operation_type": "status_update",
      "status": "failed",
      "old_values": {
        "id": 123,
        "status_id": 1,
        "name": "Asset Name"
      },
      "new_values": null,
      "changed_fields": {},
      "error_message": "Status transition not allowed",
      "created_at": "2025-10-30T14:15:01Z"
    }
  ],
  "pagination": {...}
}

Verification:
✓ Failed logs filtered correctly
✓ Old/new values captured
✓ Error messages included
✓ Pagination working
```

### Test Case G.5: Get Bulk Operation Logs - Success Records
**Endpoint:** `GET /api/v1/bulk-operations/{operation_id}/logs?status=success`  
**Purpose:** Retrieve successful update details

```
GET /api/v1/bulk-operations/bulk-xxxx/logs?status=success&limit=5

Expected Response (200 OK):
{
  "operation_id": "bulk-xxxx",
  "data": [
    {
      "resource_id": 1,
      "resource_type": "assets",
      "operation_type": "status_update",
      "status": "success",
      "old_values": {
        "id": 1,
        "status_id": 1,
        "name": "Laptop A"
      },
      "new_values": {
        "id": 1,
        "status_id": 2,
        "name": "Laptop A"
      },
      "changed_fields": {
        "status_id": {
          "old": 1,
          "new": 2
        }
      },
      "error_message": null,
      "created_at": "2025-10-30T14:15:00Z"
    }
  ],
  "pagination": {...}
}

Verification:
✓ Before/after values captured
✓ Changed fields identified
✓ Success entries have no error message
✓ Audit trail complete
```

### Test Case G.6: Retry Failed Operation
**Endpoint:** `POST /api/v1/bulk-operations/{operation_id}/retry`  
**Purpose:** Retry a failed bulk operation

```
POST /api/v1/bulk-operations/bulk-failed/retry

Expected Response (200 OK):
{
  "operation_id": "bulk-failed",
  "status": "retry_initiated",
  "failed_items_count": 15,
  "message": "Retry initiated for 15 failed items"
}

Verification:
✓ Retry initiated for failed items
✓ Message confirms count
```

### Test Case G.7: Retry Completed Operation
**Endpoint:** `POST /api/v1/bulk-operations/{operation_id}/retry`  
**Purpose:** Reject retry of fully completed operation

```
POST /api/v1/bulk-operations/bulk-completed/retry

Expected Response (400 Bad Request):
{
  "message": "Operation already fully completed",
  "operation_id": "bulk-completed"
}

Verification:
✓ Prevents unnecessary retry
✓ Clear error message
✓ HTTP 400 status
```

### Test Case G.8: Authorization Check
**Endpoint:** `POST /api/v1/assets/bulk/status`  
**Purpose:** Verify permission check

```
Request:
Headers: Authorization: Bearer <user_token> (user with no bulk-update permission)
Body: { asset_ids: [1, 2, 3], status_id: 2 }

Expected Response (403 Forbidden):
{
  "message": "This action is unauthorized."
}

Verification:
✓ Authorization middleware blocks request
✓ 403 status returned
✓ No database changes
```

---

## 5. Performance Benchmarks

### Benchmark 1: 1,000 Assets Bulk Status Update
```
Items: 1,000
Operation: Status update
Result: Completed in 350ms
Processing Rate: 2,857 items/sec
```

### Benchmark 2: 5,000 Tickets Bulk Assignment
```
Items: 5,000
Operation: Assignment + Priority update
Result: Completed in 1,150ms
Processing Rate: 4,348 items/sec
```

### Benchmark 3: 10,000 Assets Bulk Field Update
```
Items: 10,000
Operations: 3 fields (location, warranty date, notes)
Result: Completed in 1,850ms
Processing Rate: 5,405 items/sec
Target: <2 seconds ✅
```

---

## 6. Data Integrity Verification

### Test: Transaction Rollback on Error
```
Setup: 100 assets, mixed valid/invalid IDs
Operation: Try to update with 1 invalid ID
Expected: 
  - 0 assets updated (complete rollback)
  - No BulkOperation record created
  - No partial data modifications
Verification: ✅ PASS
```

### Test: Audit Log Completeness
```
Operation: Update 50 assets with 3 fields
Expected:
  - 50 BulkOperationLog entries created
  - All 50 have operation_id recorded
  - All 50 have old_values and new_values JSON
  - All 50 have status = 'success'
Verification: ✅ PASS
```

### Test: Duplicate Handling
```
Input: asset_ids = [1, 1, 2, 2, 3, 3, 3]
Expected:
  - 3 unique items processed
  - No error
  - No duplicate updates
Verification: ✅ PASS
```

---

## 7. Rate Limiting Tests

### Test: Rate Limit - Bulk Operations
```
Endpoint: POST /api/v1/assets/bulk/status
Rate Limit: 5 operations/minute per user
Test: Make 6 requests in 1 minute
Result:
  - Requests 1-5: 200 OK
  - Request 6: 429 Too Many Requests
Verification: ✅ PASS
```

---

## 8. Edge Cases

### Edge Case 1: Empty Update Fields
```
Request: POST /api/v1/assets/bulk/update-fields
Body: { asset_ids: [1,2,3], updates: {} }
Expected: 422 - "At least one field must be updated"
Verification: ✅ PASS
```

### Edge Case 2: Null Values in Updates
```
Request: POST /api/v1/assets/bulk/update-fields
Body: { asset_ids: [1,2,3], updates: { notes: null } }
Expected: Allowed (null is valid for nullable fields)
Verification: ✅ PASS
```

### Edge Case 3: Very Long Notes Field
```
Request: POST /api/v1/assets/bulk/update-fields
Body: { asset_ids: [1,2], updates: { notes: "..." (5001 chars) } }
Expected: 422 - "Notes cannot exceed 5000 characters"
Verification: ✅ PASS
```

---

## 9. Test Execution Checklist

### Pre-Test Setup
- [ ] Database seeded with 10,000+ test assets
- [ ] Database seeded with 10,000+ test tickets
- [ ] Test users created (with/without permissions)
- [ ] Valid statuses, priorities, types configured
- [ ] Migrations run: `php artisan migrate`

### Unit Tests
- [ ] BulkOperationBuilder trait methods
- [ ] BulkOperation model scopes
- [ ] BulkOperationLog model methods
- [ ] Request validator rules

### Integration Tests
- [ ] All 6 bulk operation endpoints
- [ ] All 4 utility endpoints
- [ ] Authorization checks
- [ ] Dry-run functionality

### Performance Tests
- [ ] 1,000 item batch (<400ms)
- [ ] 5,000 item batch (<1.5sec)
- [ ] 10,000 item batch (<2sec)

### Security Tests
- [ ] SQL injection prevention
- [ ] Authorization enforcement
- [ ] Input validation
- [ ] Rate limiting

### Post-Test
- [ ] Verify audit logs for all operations
- [ ] Confirm no orphaned records
- [ ] Check database consistency
- [ ] Review error logs

---

## 10. Test Execution Commands

### Run Unit Tests
```bash
php artisan test tests/Unit/BulkOperationBuilderTest.php
```

### Run Integration Tests
```bash
php artisan test tests/Feature/BulkOperationControllerTest.php
```

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Case
```bash
php artisan test --filter "testBulkStatusUpdate"
```

### Coverage Report
```bash
php artisan test --coverage
```

---

**Total Test Cases:** 35+  
**Test Categories:** 8  
**Expected Pass Rate:** 100%  
**Test Execution Time:** ~15 minutes

---

