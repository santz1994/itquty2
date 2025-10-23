# API Documentation (Summary)

This document provides a concise reference to key API endpoints used by the application. For a full OpenAPI spec consider exporting from controllers or documenting with Swagger.

## Authentication
- The application uses session-based auth for web endpoints.
- API tokens are not enabled by default; use session auth or extend with Laravel Sanctum if API tokens needed.

## Key Endpoints

### Assets
- `GET /assets` - Asset index (web)
- `GET /assets/{asset}` - Show asset details (web)
- `GET /assets/{asset}/history` - Asset history (movements & related tickets)
- `GET /assets/{asset}/ticket-history` - Ticket history for asset
- `GET /assets/{asset}/movements` - Show movement records
- `POST /assets/{asset}/assign` - Assign asset to a user
- `POST /assets/{asset}/unassign` - Unassign asset

### QR Codes
- `GET /assets/qr/{qrCode}` - Public route to look up an asset by QR
- `GET /assets/{asset}/qr-code` - Generate asset QR
- `GET /assets/{asset}/qr-download` - Download QR image

### Tickets
- `GET /tickets` - Ticket index
- `GET /tickets/{ticket}` - Show ticket details
- `POST /tickets` - Create ticket (web form expected)

### Attachments
- `POST /attachments/upload` - Upload file
- `GET /attachments/{id}/download` - Download file

## Example: Get asset history (server-side)

```php
$asset = App\Asset::find(82);
$history = $asset->getTicketHistory();
foreach ($history as $ticket) {
    // process
}
```

## Notes
- Endpoints above are primarily web-facing and rely on session authentication.
- For a RESTful API with token auth, add Laravel Sanctum and duplicate endpoints under `routes/api.php`.

## Recommended Next Steps
- Generate an OpenAPI (Swagger) spec for external integrations
- Add token-based auth (Sanctum) if mobile or third-party clients are expected

## DailyActivity API (JSON)

The application exposes a small JSON API for DailyActivity entries used by integrations and UI widgets.

- Base path: `GET|POST|PUT|DELETE /api/daily-activities`
- Authentication: session-based or token (if Sanctum enabled). In tests the API uses the default Laravel testing authentication.

Endpoints:

- `GET /api/daily-activities` — List all daily activities (supports optional query params `date`, `user_id`). Returns 200 with JSON array of activities.
- `GET /api/daily-activities/{id}` — Get a single activity by id. Returns 200 with JSON object or 404.
- `POST /api/daily-activities` — Create a new activity. Expects JSON body:

    {
        "user_id": 12,
        "date": "2025-10-23",
        "ticket_id": 55,        // optional
        "activity_type": "work", // optional
        "notes": "Did some work"
    }

    Returns 201 with created object on success or 422 and validation messages on error.

- `PUT /api/daily-activities/{id}` — Update an existing activity. Accepts same JSON body as POST. Returns 200 with updated object.
- `DELETE /api/daily-activities/{id}` — Delete activity. Returns 204 on success.

Validation rules used by the controller (summary): `user_id` required & exists, `date` required & date, `notes` optional string.

Example response (list):

{
    "data": [
        {"id": 1, "user_id": 12, "date": "2025-10-23", "notes": "Checked tickets"}
    ]
}

### DailyActivity API - Examples

Create an activity (POST /api/daily-activities)

Request (application/json):

{
    "user_id": 12,
    "date": "2025-10-23",
    "ticket_id": 55,
    "activity_type": "work",
    "notes": "Investigated login issue"
}

Response (201 Created):

{
    "id": 101,
    "user_id": 12,
    "date": "2025-10-23",
    "ticket_id": 55,
    "activity_type": "work",
    "notes": "Investigated login issue",
    "created_at": "2025-10-23T10:12:00Z"
}

Update an activity (PUT /api/daily-activities/{id}) — same payload shape as create.

Delete (DELETE /api/daily-activities/{id}) returns 204 No Content on success.

### Minimal OpenAPI snippet

This can be added to an OpenAPI doc as a starting point for the DailyActivity resource:

```yaml
openapi: 3.0.0
info:
    title: Quty2 API
    version: 1.0.0
paths:
    /api/daily-activities:
        get:
            summary: List activities
            responses:
                '200':
                    description: OK
        post:
            summary: Create activity
            requestBody:
                required: true
                content:
                    application/json:
                        schema:
                            $ref: '#/components/schemas/DailyActivity'
            responses:
                '201':
                    description: Created
    /api/daily-activities/{id}:
        parameters:
            - name: id
                in: path
                required: true
                schema:
                    type: integer
        put:
            summary: Update activity
            requestBody:
                required: true
                content:
                    application/json:
                        schema:
                            $ref: '#/components/schemas/DailyActivity'
            responses:
                '200':
                    description: Updated
        delete:
            summary: Delete activity
            responses:
                '204':
                    description: No Content
components:
    schemas:
        DailyActivity:
            type: object
            properties:
                user_id:
                    type: integer
                date:
                    type: string
                    format: date
                ticket_id:
                    type: integer
                activity_type:
                    type: string
                notes:
                    type: string
            required:
                - user_id
                - date
```

## Assets CSV Import

The app supports importing assets from a CSV file via the web UI. When a CSV is uploaded the fallback importer (`app/Imports/AssetsCsvImport.php`) is used. The importer performs row-level validation and returns an `import_summary` which is flashed to the session for display.

Upload endpoint (web form):

- `POST /assets/import` — multipart form-data with `file` field. The controller validates the file mime (`csv,xlsx,xls`) and routes CSV files to the fallback importer.

CSV template (header row - column names):

Asset Tag,Serial Number,Model,Division,Supplier,Purchase Date,Warranty Months,IP Address,MAC Address,Status,Assigned To,Notes

Notes on columns:
- `Model` must match an existing `AssetModel.asset_model` value. The importer will NOT auto-create `AssetModel` (to avoid FK issues). If a model is missing the row is reported as an error.
- `Purchase Date` accepts ISO-like dates (YYYY-MM-DD) — invalid dates are reported as row errors.
- `Warranty Months` integer months.
- `Status` will be first-or-created by name.
- `Assigned To` expects a user name present in `users.name` (optional).

Import summary format (returned by the importer):

{
    "created": 12,
    "errors": [
        {"row": 4, "errors": ["Model not found: Foo"], "data": { ...row values... }},
        {"row": 7, "errors": ["The purchase date is not a valid date"], "data": { ... }}
    ]
}

UX tips:
- If `errors` is non-empty the controller redirects back to the import form and flashes `import_summary` to session; the view shows the errors.
- Recommended enhancement: provide a "Download errors CSV" button that exports failed rows with an `error` column. (If you want I can implement this.)

### Import errors download

The web UI now includes a controller action that allows downloading the import error rows as a CSV file when `import_summary` is present in the session.

- Endpoint: `GET /assets/import-errors-download` (route name: `assets.import-errors-download`)
- Behavior: if `session('import_summary')` exists and contains `errors`, the endpoint streams a CSV with columns `row,messages,data` where `messages` is a semicolon-separated list of validation messages (or the caught error) and `data` is the JSON-encoded row data.

Use case: after uploading a CSV that produces row-level errors the import form shows a "Download Errors CSV" button which links to the above endpoint.

### Asset KPI helper methods

The `AssetService` now exposes additional KPI helpers used by the assets index/dashboard views. These are cached for 5 minutes and invalidated when assets change.

- `AssetService::assetsByStatusBreakdown()` — returns a list of status names and counts (cache key: `assets_by_status`).
- `AssetService::monthlyNewAssets($months = 6)` — returns new assets counts per month for the last N months (cache keys: `assets_monthly_new_{n}`).

Cache invalidation: `AssetService::invalidateKpiCache()` clears KPI caches (called from `AssetObserver` on created/updated/deleted). The service method also clears monthly caches for common windows (1..12 months).

Security & data integrity:
- The importer intentionally refuses to auto-create `AssetModel` to avoid creating incomplete records that violate NOT NULL constraints (e.g., manufacturer required).
- Divisions and Suppliers are created lazily by name to ease imports.

