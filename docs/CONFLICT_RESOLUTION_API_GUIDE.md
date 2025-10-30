# Conflict Resolution API Usage Guide

## Overview
This guide demonstrates how to use the Conflict Resolution API endpoints for managing import conflicts programmatically.

## Authentication
All API endpoints require authentication via Sanctum token in the `Authorization` header:
```
Authorization: Bearer {api_token}
```

## Endpoint Examples

### 1. Get Conflicts for an Import
**GET** `/api/imports/{importId}/conflicts`

```bash
curl -X GET "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "import_id": "550e8400-e29b-41d4-a716-446655440000",
  "conflicts": [
    {
      "id": 1,
      "import_id": "550e8400-e29b-41d4-a716-446655440000",
      "row_number": 42,
      "conflict_type": "duplicate_key",
      "existing_record_id": 123,
      "new_record_data": {...},
      "suggested_resolution": "update_existing",
      "user_resolution": null
    }
  ],
  "count": 5
}
```

### 2. Get Conflict Statistics
**GET** `/api/imports/{importId}/conflicts/statistics`

```bash
curl -X GET "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/statistics" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "import_id": "550e8400-e29b-41d4-a716-446655440000",
  "statistics": {
    "total_conflicts": 10,
    "unresolved_conflicts": 7,
    "resolved_conflicts": 3,
    "resolution_rate": 30.00,
    "by_type": {
      "duplicate_key": {
        "count": 5,
        "conflicts": [...]
      },
      "foreign_key_not_found": {
        "count": 3,
        "conflicts": [...]
      }
    }
  }
}
```

### 3. Get Specific Conflict Detail
**GET** `/api/imports/{importId}/conflicts/{conflictId}`

```bash
curl -X GET "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/1" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### 4. Resolve a Single Conflict
**POST** `/api/imports/{importId}/conflicts/{conflictId}/resolve`

```bash
curl -X POST "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/1/resolve" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "resolution": "update_existing",
    "details": {
      "merge_strategy": "new_data_wins",
      "fields_to_update": ["status", "notes"]
    }
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Conflict resolved successfully",
  "resolution_choice": {
    "id": 1,
    "import_id": "550e8400-e29b-41d4-a716-446655440000",
    "conflict_id": 1,
    "user_id": 42,
    "choice": "update_existing",
    "choice_details": {...},
    "created_at": "2025-10-30T10:30:00Z"
  }
}
```

### 5. Bulk Resolve Conflicts
**POST** `/api/imports/{importId}/conflicts/bulk-resolve`

```bash
curl -X POST "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/bulk-resolve" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "resolutions": [
      {
        "conflict_id": 1,
        "resolution": "skip",
        "details": {}
      },
      {
        "conflict_id": 2,
        "resolution": "create_new",
        "details": {}
      },
      {
        "conflict_id": 3,
        "resolution": "update_existing",
        "details": {
          "merge_strategy": "new_data_wins"
        }
      }
    ]
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Resolved 3 conflicts",
  "count": 3
}
```

### 6. Auto-Resolve Conflicts
**POST** `/api/imports/{importId}/conflicts/auto-resolve`

```bash
curl -X POST "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/auto-resolve" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "strategy": "skip"
  }'
```

**Strategies:**
- `skip` - Skip all conflicts
- `update` - Update existing records for duplicate keys
- `merge` - Merge records intelligently

**Response:**
```json
{
  "success": true,
  "results": {
    "total": 10,
    "resolved": 10,
    "failed": 0,
    "errors": []
  }
}
```

### 7. Get Resolution History
**GET** `/api/imports/{importId}/conflicts/history`

```bash
curl -X GET "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/history" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "history": [
    {
      "id": 1,
      "import_id": "550e8400-e29b-41d4-a716-446655440000",
      "conflict_id": 1,
      "user_id": 42,
      "choice": "update_existing",
      "choice_details": null,
      "created_at": "2025-10-30T10:30:00Z",
      "user": {
        "id": 42,
        "name": "Admin User",
        "email": "admin@example.com"
      },
      "conflict": {...}
    }
  ],
  "count": 5
}
```

### 8. Export Conflict Report
**GET** `/api/imports/{importId}/conflicts/export`

```bash
curl -X GET "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/export" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "report": {
    "import_id": "550e8400-e29b-41d4-a716-446655440000",
    "resource_type": "assets",
    "file_name": "assets_import_2025_10_30.csv",
    "created_at": "2025-10-30T09:00:00Z",
    "statistics": {...},
    "conflicts": [...],
    "resolution_history": [...]
  }
}
```

### 9. Rollback Resolutions
**POST** `/api/imports/{importId}/conflicts/rollback`

```bash
curl -X POST "http://localhost/api/imports/550e8400-e29b-41d4-a716-446655440000/conflicts/rollback" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Rolled back 5 resolutions",
  "count": 5
}
```

## Error Responses

### Validation Error
```json
{
  "success": false,
  "message": "The resolution field is required",
  "status": 422
}
```

### Authorization Error
```json
{
  "message": "This action is unauthorized",
  "status": 403
}
```

### Not Found Error
```json
{
  "message": "Not found",
  "status": 404
}
```

## Resolution Types

| Type | Description |
|------|-------------|
| `skip` | Skip the row entirely (don't import) |
| `create_new` | Create a new record with the import data |
| `update_existing` | Update the existing record with new data |
| `merge` | Merge existing and new data intelligently |

## Conflict Types

| Type | Description |
|------|-------------|
| `duplicate_key` | A record with the same unique key already exists |
| `duplicate_record` | A duplicate record already exists |
| `foreign_key_not_found` | Referenced foreign key doesn't exist |
| `invalid_data` | Data fails validation rules |
| `business_rule_violation` | Violates business logic rules |

## Suggested Resolutions by Conflict Type

| Conflict Type | Suggested Resolution |
|---------------|-------------------|
| `duplicate_key` | `update_existing` |
| `duplicate_record` | `skip` |
| `foreign_key_not_found` | `skip` |
| `invalid_data` | `skip` |
| `business_rule_violation` | `skip` |

## Rate Limiting
- Standard API: 60 requests per minute
- Public endpoints: 10 requests per minute

## CORS
All API endpoints support CORS requests from allowed origins.

## Batch Operations
For bulk resolving more than 100 conflicts, consider:
1. Breaking into smaller batches (50-100 per request)
2. Using the auto-resolve endpoint for uniform strategy
3. Implementing server-side pagination for results
