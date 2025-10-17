# Admin Documentation

This admin manual documents common administrative tasks, workflows, and troubleshooting steps for IT Quty.

## Overview

Admins are responsible for: user and role management, asset lifecycle management, ticket oversight, maintenance scheduling, and audit log review.

> Important: Use the admin UI for standard operations. Direct database changes are not recommended unless instructed.

## Access & Roles

IT Quty ships with four primary roles. Each role is implemented using Spatie's permission package and can be extended via the Admin → Roles UI.

- Super Admin
	- Full system access: manage users, roles & permissions, system settings, data export/import, audit logs, and all asset/ticket workflows.
	- Primary responsibilities: onboarding admins, troubleshooting system-wide issues, running migrations/maintenance.

- Management
	- Department/division-level management: can view and manage assets and tickets in assigned divisions, approve asset requests, and run reports.
	- Primary responsibilities: approve transfers, review maintenance schedules, and produce operational reports.

- Admin
	- Site management role focused on asset and ticket operations: create/edit assets, manage maintenance logs, run imports/exports, and manage spare parts.
	- Limited access to system-wide configuration compared to Super Admin.

- User
	- Basic user role: submit tickets, request assets, view assigned assets and personal ticket history.
	- Intended for regular staff who do not perform administrative tasks.

> Note: Role and permission names are stored in the `permissions` and `roles` tables. If menus are missing for a user, check their assigned roles and permissions, then run `php artisan permission:cache-reset` and clear view cache.

### Assigning Roles

Use the Users page (Admin → Users) to assign roles. Roles are managed by the Spatie Permission package.

```php
// Example: assign role via tinker
$user = App\User::where('email', 'daniel@quty.co.id')->first();
$user->assignRole('super-admin');
```

You can also assign roles via the admin UI (Admin → Users → Edit → Roles). After changing roles/permissions, advise the user to log out and log back in so session permissions refresh.

---

## User Management (Admin tasks)

This section describes common user administration flows.

### Create a user

1. Navigate to Admin → Users → Create User.
2. Fill in name, email, division (optional), and temporary password (communicate securely).
3. Assign one or more roles from the Roles multi-select.
4. Save and advise user to reset password via the 'Forgot Password' link.

Or via tinker / seeder for testing:

```php
App\User::create([
	'name' => 'Test Admin',
	'email' => 'test.admin@example.com',
	'password' => bcrypt('123456')
])->assignRole('admin');
```

### Edit a user

- Use Admin → Users → Edit. You can update profile info, change division, and modify roles.
- If changing roles, run `php artisan permission:cache-reset` and ask the user to re-login.

### Deactivate / Delete a user

- Prefer disabling or removing roles instead of deleting in production. To delete, use the Delete action in Admin → Users.
- Deleting a user will not automatically reassign historical tickets; reassign tickets before deletion if necessary.

---

## Asset Workflows (Detailed)

### Asset lifecycle overview

1. Creation / Import: Assets are created individually or imported in bulk via CSV. Required fields include `asset_tag`, `asset_model_id`, `asset_type_id`, and `status_id`.
2. Assignment: Assign to users with the Assign action; this creates an `assigned_to` relation and movement record.
3. Movement: Use Move to change location/division; movement history is recorded and visible on the asset history page.
4. Maintenance: Log maintenance records; maintenance events can have follow-up tickets.
5. Disposal / Retire: Mark asset status as `Retired` or `Disposed` and archive records as needed.

### Importing assets

- Use Assets → Import Form and upload the provided CSV template. The template includes column headers that map to asset model fields.
- Common import errors:
	- Missing required columns: `asset_tag`, `asset_model`, `asset_type`
	- Invalid foreign keys: ensure referenced models (manufacturers, suppliers) exist prior to import

### Asset History and Ticket History pages

- `GET /assets/{asset}/history` shows the asset summary, movements and related tickets.
- `GET /assets/{asset}/ticket-history` is an alias that exposes ticket-specific history for the asset.

### QR Codes and Printing

- Use the QR action to generate and download QR codes. QRs map to `assets/qr/{code}` for quick lookup.

---

## Ticket Workflows

### Creating and Assigning Tickets

- Tickets can be created from the Tickets UI or from the asset detail pages.
- When assigning a ticket, select an assignee and a ticket priority. Notifications will be sent if mail/queue is configured.

### SLA and Priorities

- Priorities are configured in System Settings. The ticket list supports filters for `Urgent`, `High`, `Medium`, and `Low`.

### Ticket History in Asset view

- The Asset History page includes a table of related tickets. Use the View button to open the ticket detail.

---

## Maintenance Logs

- Maintenance entries are stored in the `asset_maintenance_logs` table and accessible through the Maintenance menu.
- For scheduled maintenance, set reminders or integrate calendar/email notifications using queued jobs.

---

## System Settings and Configuration

- System Settings controls global options such as allowed file types, ticket configurations, and SMTP settings.
- Only `super-admin` should change mail configuration, queue drivers, or cron settings.

---

## Audit Logs & Compliance

- Audit logs are available under System → Audit Logs. They include user actions like create/update/delete for critical models.
- Use filters to narrow results by user, date, or action type.

---

## Troubleshooting & Diagnostic Commands

Common commands for admins and support engineers:

```bash
# Reset permission cache
php artisan permission:cache-reset

# Clear application caches
php artisan optimize:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Logs are stored in `storage/logs/laravel.log`. For view errors, open the blade stack trace and find missing relationships; the usual quick fix is to add null checks in the view or ensure related records exist.

---

## Appendix: Seeded Accounts & Test Data

- Super Admins (seeded): `daniel@quty.co.id`, `idol@quty.co.id`, `ridwan@quty.co.id` (password: `123456` in development seed)
- Dummy data: `DummyDataSeeder` creates sample locations, manufacturers, models, assets, tickets, and requests for testing.

---

If you want, I can expand each section with screenshots and step-by-step UI flows (recommended for the full admin manual). Tell me which sections to expand first and whether you prefer step-by-step screenshots or a printable PDF export.

## Asset Management

### Import / Export
- Use the `Assets → Import` UI to upload CSV templates.
- The `Download Template` button provides the expected columns.
- On import, the seeder will map columns to expected fields; errors are shown per-row.

### Move / Assign
- Use the `Move` action on the asset list to create a movement record.
- Assigning an asset creates `assigned_to` relationship and triggers notifications if configured.

### QR Codes
- Generate QR codes from the asset list and print via `Print` action.
- API endpoints exist for retrieving QR by code.

## Tickets

- Tickets tie to assets and users. Use the Tickets UI for assignment and SLA tracking.
- Ticket priorities and statuses are configurable from System Settings → Tickets.

## Maintenance Logs

- Each asset can have maintenance logs. Use the Maintenance menu to review and create logs.
- Legacy maintenance endpoints (if any) are under `asset-maintenance`.

## Audit Logs

- Audit logs capture important actions. Use System Settings → Audit Logs to filter by user, action, and date.

## Common Admin Commands

```bash
# Reset permission cache (when roles/permissions changed)
php artisan permission:cache-reset

# Clear application caches
php artisan optimize:clear
```

## Troubleshooting

- 500 errors often indicate missing relationships in Blade views. Check `storage/logs/laravel.log` for stack traces.
- If menus are missing, re-run the permission creation script and clear caches.

## Further Reading

- `docs/API.md` for programmatic access
- `docs/Deployment_Guide.md` for production deployments
