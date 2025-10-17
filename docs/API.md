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
