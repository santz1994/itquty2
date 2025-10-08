# Laravel IT Asset Management System - API Documentation

**Version:** 1.0  
**Base URL:** `{your-domain}/api`  
**Authentication:** Bearer Token (Laravel Sanctum)

---

## Table of Contents

1. [Authentication](#authentication)
2. [Rate Limiting](#rate-limiting)
3. [Response Format](#response-format)
4. [Error Handling](#error-handling)
5. [Endpoints](#endpoints)
   - [Authentication](#authentication-endpoints)
   - [Assets](#assets-endpoints)
   - [Tickets](#tickets-endpoints)
   - [Users](#users-endpoints)
   - [Daily Activities](#daily-activities-endpoints)
   - [Notifications](#notifications-endpoints)
   - [Dashboard](#dashboard-endpoints)
   - [System](#system-endpoints)

---

## Authentication

This API uses **Laravel Sanctum** for authentication. All protected endpoints require a valid Bearer token.

### Getting Started

1. **Login** to get an access token
2. **Include the token** in all subsequent requests
3. **Token expires** after 30 days (configurable)

### Headers Required

```http
Authorization: Bearer {your-access-token}
Content-Type: application/json
Accept: application/json
```

---

## Rate Limiting

The API implements different rate limiting strategies:

| Rate Limiter | Limit | Description |
|--------------|-------|-------------|
| `api-auth` | 5/minute | Authentication endpoints |
| `api` | 20-60/minute | Standard API endpoints |
| `api-admin` | 30-120/minute | Admin operations |
| `api-frequent` | 50-200/minute | High-frequency ops (notifications) |
| `api-public` | 10/minute | Public endpoints |
| `api-bulk` | 3-10/minute | Bulk operations |

**Note:** Higher limits apply to authenticated users vs. guest users.

---

## Response Format

All API responses follow a consistent JSON structure:

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data here
  },
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Validation errors (if applicable)
  }
}
```

### Pagination Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      // Array of items
    ],
    "first_page_url": "http://example.com/api/assets?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://example.com/api/assets?page=5",
    "next_page_url": "http://example.com/api/assets?page=2",
    "path": "http://example.com/api/assets",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 67
  },
  "message": "Data retrieved successfully"
}
```

---

## Error Handling

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

---

## Endpoints

## Authentication Endpoints

### POST /auth/login
Authenticate user and receive access token.

**Rate Limit:** `api-auth` (5/minute)

**Request:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "roles": ["admin"],
      "permissions": ["view-assets", "create-assets"],
      "primary_role": "admin",
      "initials": "AU"
    },
    "token": "1|abc123...",
    "token_type": "Bearer",
    "expires_at": "2024-01-15T10:30:00.000000Z"
  },
  "message": "Login successful"
}
```

### POST /auth/logout
Revoke current access token.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### GET /auth/user
Get authenticated user details.

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "roles": ["admin"],
      "permissions": ["view-assets", "create-assets"],
      "is_online": true,
      "last_login_at": "2024-01-15T08:30:00.000000Z"
    }
  }
}
```

### POST /auth/refresh
Refresh access token (revokes current token).

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "2|def456...",
    "token_type": "Bearer",
    "expires_at": "2024-02-15T10:30:00.000000Z"
  },
  "message": "Token refreshed successfully"
}
```

---

## Assets Endpoints

### GET /assets
Retrieve paginated list of assets.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `view-assets`

**Query Parameters:**
- `per_page` (int): Items per page (default: 15)
- `status_id` (int): Filter by status
- `division_id` (int): Filter by division
- `assigned_to` (int|string): Filter by assigned user or "unassigned"
- `search` (string): Search in asset_tag, name, serial_number

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "asset_tag": "IT-001",
        "name": "Dell Laptop",
        "serial_number": "DL123456",
        "status": {
          "name": "Deployed",
          "badge": "<span class='badge badge-success'>Deployed</span>"
        },
        "assigned_user": {
          "id": 2,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "warranty_status": "Active",
        "created_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "total": 150
  }
}
```

### POST /assets
Create a new asset.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `create-assets`

**Request:**
```json
{
  "asset_tag": "IT-002",
  "name": "HP Laptop",
  "serial_number": "HP789012",
  "location_id": 1,
  "division_id": 1,
  "status_id": 1,
  "model_id": 1,
  "assigned_to": 2
}
```

### GET /assets/{id}
Get specific asset details.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `view-assets`

### PUT /assets/{id}
Update asset information.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `edit-assets`

### DELETE /assets/{id}
Delete an asset.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `delete-assets`

### POST /assets/{id}/assign
Assign asset to a user.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `assign-assets`

**Request:**
```json
{
  "user_id": 2,
  "notes": "Assigned for remote work setup"
}
```

### POST /assets/{id}/unassign
Unassign asset from current user.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `assign-assets`

### GET /assets/{id}/history
Get asset movement and ticket history.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `view-assets`

---

## Tickets Endpoints

### GET /tickets
Retrieve paginated list of tickets.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `view-tickets`

**Query Parameters:**
- `per_page` (int): Items per page
- `status_id` (int): Filter by status
- `priority_id` (int): Filter by priority
- `assigned_to` (int|string): Filter by assigned user
- `user_id` (int): Filter by ticket creator
- `search` (string): Search in title, description
- `overdue` (boolean): Show only overdue tickets

### POST /tickets
Create a new ticket.

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `create-tickets`

**Request:**
```json
{
  "title": "Laptop not starting",
  "description": "Employee reports laptop won't power on",
  "asset_id": 1,
  "ticket_type_id": 1,
  "ticket_priority_id": 2,
  "assigned_to": 3
}
```

### POST /tickets/{id}/assign
Assign ticket to a user.

**Request:**
```json
{
  "user_id": 3,
  "notes": "Escalating to senior technician"
}
```

### POST /tickets/{id}/resolve
Mark ticket as resolved.

**Request:**
```json
{
  "resolution_notes": "Replaced faulty charger. Issue resolved."
}
```

### POST /tickets/{id}/close
Close a ticket.

**Request:**
```json
{
  "closure_notes": "Confirmed working with user."
}
```

### GET /tickets/{id}/timeline
Get ticket entry timeline.

---

## Users Endpoints

### GET /users
List all users (admin only).

**Headers:** `Authorization: Bearer {token}`  
**Permissions:** `view-users`

### GET /users/{id}/performance
Get user performance metrics.

**Query Parameters:**
- `days` (int): Number of days to analyze (default: 30)

**Response:**
```json
{
  "success": true,
  "data": {
    "tickets_resolved": 25,
    "tickets_created": 5,
    "average_resolution_time": 4.2,
    "assets_managed": 12,
    "activities_completed": 45
  }
}
```

### GET /users/{id}/workload
Get current user workload.

---

## Daily Activities Endpoints

### GET /daily-activities
List daily activities.

**Query Parameters:**
- `user_id` (int): Filter by user
- `activity_type` (string): Filter by type
- `date_from` (date): Start date filter
- `date_to` (date): End date filter
- `is_completed` (boolean): Filter by completion status

### POST /daily-activities
Create new activity.

**Request:**
```json
{
  "title": "Server maintenance",
  "description": "Monthly server updates",
  "activity_type": "asset_maintenance",
  "activity_date": "2024-01-15",
  "estimated_duration": 120
}
```

### POST /daily-activities/{id}/complete
Mark activity as completed.

**Request:**
```json
{
  "actual_duration": 90,
  "completion_notes": "Completed ahead of schedule"
}
```

---

## Notifications Endpoints

**Rate Limit:** `api-frequent` (50-200/minute)

### GET /notifications
Get user notifications.

**Query Parameters:**
- `is_read` (boolean): Filter by read status
- `type` (string): Filter by notification type
- `priority` (string): Filter by priority

### POST /notifications/{id}/read
Mark notification as read.

### POST /notifications/mark-all-read
Mark all notifications as read.

### GET /notifications/unread-count
Get count of unread notifications.

**Response:**
```json
{
  "success": true,
  "data": {
    "unread_count": 5
  }
}
```

---

## Dashboard Endpoints

**Rate Limit:** `api-admin` (30-120/minute)

### GET /dashboard/stats
Get personal dashboard statistics.

**Response:**
```json
{
  "success": true,
  "data": {
    "my_tickets": {
      "total": 15,
      "open": 5,
      "assigned_to_me": 8,
      "overdue": 2
    },
    "my_assets": {
      "assigned_to_me": 3,
      "warranty_expiring": 1
    },
    "recent_activities": 12,
    "notifications": {
      "unread": 3
    }
  }
}
```

### GET /dashboard/kpi
Get KPI dashboard data (admin only).

**Permissions:** `view-kpi-dashboard`

---

## System Endpoints

**Rate Limit:** `api-public` (10/minute)

### GET /system/status
Get API status information.

**Response:**
```json
{
  "status": "online",
  "version": "1.0.0",
  "api_version": "1.0",
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### GET /system/health
Get system health check.

**Response:**
```json
{
  "status": "healthy",
  "checks": {
    "database": "connected",
    "cache": "active",
    "storage": "accessible"
  },
  "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

---

## Usage Examples

### JavaScript/Axios Example

```javascript
// Login
const loginResponse = await axios.post('/api/auth/login', {
  email: 'admin@example.com',
  password: 'password'
});

const token = loginResponse.data.data.token;

// Set default header
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Get assets
const assetsResponse = await axios.get('/api/assets', {
  params: {
    per_page: 10,
    search: 'laptop'
  }
});

// Create ticket
const newTicket = await axios.post('/api/tickets', {
  title: 'Network Issue',
  description: 'Cannot connect to wifi',
  ticket_type_id: 1,
  ticket_priority_id: 2
});
```

### cURL Examples

```bash
# Login
curl -X POST http://your-domain/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Get assets (replace TOKEN with actual token)
curl -X GET "http://your-domain/api/assets?per_page=10" \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"

# Create asset
curl -X POST http://your-domain/api/assets \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "asset_tag": "IT-003",
    "name": "MacBook Pro",
    "location_id": 1,
    "division_id": 1,
    "status_id": 1,
    "model_id": 2
  }'
```

---

## Error Examples

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 6 characters."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "Unauthorized to perform this action"
}
```

### Rate Limit Exceeded (429)
```json
{
  "success": false,
  "message": "Too Many Attempts."
}
```

---

## Best Practices

1. **Always check response status** before processing data
2. **Handle rate limiting** with exponential backoff
3. **Store tokens securely** and refresh when needed
4. **Use pagination** for large datasets
5. **Include proper error handling** in your applications
6. **Respect rate limits** to ensure API availability

---

## Support

For API support and questions:
- Check the application logs for detailed error information
- Ensure you have the required permissions for each endpoint
- Verify your authentication token is valid and not expired

---

*This documentation is for Laravel IT Asset Management System API v1.0*