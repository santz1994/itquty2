# Manual Testing Checklist - Routes Refactoring

## 📋 Testing Overview

Test all major pages to verify routes are working after refactoring.

**Test URL**: http://192.168.1.122:80

---

## ✅ Authentication Routes (auth.php)

### Login/Logout
- [ ] Navigate to `/login` - Should show login page
- [ ] Login with valid credentials - Should redirect to `/home`
- [ ] Click logout - Should redirect to `/login`
- [ ] Try invalid credentials - Should show error message

### Password Reset
- [ ] Navigate to `/password/reset` - Should show password reset form
- [ ] Submit email - Should send reset link
- [ ] Click reset link - Should show new password form

### Session Extension
- [ ] Wait for session timeout warning
- [ ] AJAX session extension should work

**Status**: ⏳ PENDING

---

## ✅ Ticket Routes (modules/tickets.php)

### Main CRUD Operations
- [ ] `/tickets` - List all tickets (admin/super-admin)
- [ ] `/tickets/create` - Create new ticket form
- [ ] `/tickets/{id}` - View ticket details
- [ ] `/tickets/{id}/edit` - Edit ticket form
- [ ] Submit new ticket - Should save and redirect
- [ ] Update existing ticket - Should save changes

### Assignment Operations
- [ ] Self-assign ticket - POST `/tickets/{id}/self-assign`
- [ ] Assign to technician - POST `/tickets/{id}/assign`
- [ ] Force assign - POST `/tickets/{id}/force-assign`

### Status Management
- [ ] Update ticket status - POST `/tickets/{id}/update-status`
- [ ] Complete ticket - POST `/tickets/{id}/complete`
- [ ] Complete with resolution - POST `/tickets/{id}/complete-with-resolution`

### Time Tracking
- [ ] Start timer - POST `/tickets/{id}/start-timer`
- [ ] Stop timer - POST `/tickets/{id}/stop-timer`
- [ ] View timer status - GET `/tickets/{id}/timer-status`
- [ ] View work summary - GET `/tickets/{id}/work-summary`

### Filters & Views
- [ ] `/tickets/unassigned` - Show unassigned tickets
- [ ] `/tickets/overdue` - Show overdue tickets
- [ ] `/tickets/export` - Export tickets to Excel/CSV
- [ ] `/tickets/{id}/print` - Print ticket

### Bulk Operations
- [ ] Bulk assign tickets
- [ ] Bulk update status
- [ ] Bulk update priority
- [ ] Bulk delete

**Status**: ⏳ PENDING

---

## ✅ Asset Routes (modules/assets.php)

### Main CRUD Operations
- [ ] `/assets` - List all assets
- [ ] `/assets/create` - Create new asset form
- [ ] `/assets/{id}` - View asset details
- [ ] `/assets/{id}/edit` - Edit asset form
- [ ] Submit new asset - Should save and redirect
- [ ] Update existing asset - Should save changes

### QR Code Operations
- [ ] `/assets/{id}/qr-code` - Generate QR code
- [ ] `/assets/{id}/qr-download` - Download QR code
- [ ] `/assets/qr/{code}` - Scan QR code (public access)
- [ ] `/assets/scan-qr` - QR scanner page
- [ ] Bulk generate QR codes

### Asset Management
- [ ] `/assets/{id}/ticket-history` - View ticket history
- [ ] `/assets/{id}/movements` - View asset movements
- [ ] Assign asset to user
- [ ] Unassign asset from user
- [ ] Change asset status

### Import/Export
- [ ] `/assets/export` - Export assets to Excel
- [ ] `/assets/import-form` - Show import form
- [ ] `/assets/download-template` - Download import template
- [ ] Upload and import assets
- [ ] `/assets/{id}/print` - Print asset details

### Maintenance
- [ ] `/maintenance` - List maintenance logs
- [ ] `/maintenance/create` - Create maintenance log
- [ ] `/asset-maintenance` - Legacy maintenance page
- [ ] `/asset-maintenance/analytics` - Maintenance analytics

### Spares & Attachments
- [ ] `/spares` - Spares inventory
- [ ] `/attachments/upload` - Upload file
- [ ] `/attachments/{id}/download` - Download file

**Status**: ⏳ PENDING

---

## ✅ Admin Routes (modules/admin.php)

### Dashboard & Reports
- [ ] `/home` - Main dashboard (all authenticated users)
- [ ] `/management/dashboard` - Management dashboard
- [ ] `/management/admin-performance` - Admin performance report
- [ ] `/management/ticket-reports` - Ticket reports
- [ ] `/management/asset-reports` - Asset reports
- [ ] `/kpi-dashboard` - KPI dashboard
- [ ] `/kpi-data` - KPI data (AJAX)

### Daily Activities
- [ ] `/daily-activities` - List daily activities
- [ ] `/daily-activities/calendar` - Calendar view
- [ ] `/daily-activities/daily-report` - Daily report
- [ ] `/daily-activities/weekly-report` - Weekly report
- [ ] `/daily-activities/export-pdf` - Export to PDF

### Notifications
- [ ] `/notifications` - List notifications
- [ ] `/notifications/recent` - Recent notifications (AJAX)
- [ ] `/notifications/unread-count` - Unread count (AJAX)
- [ ] Mark notification as read
- [ ] Mark all as read

### Audit Logs
- [ ] `/audit-logs` - List audit logs
- [ ] `/audit-logs/{id}` - View log details
- [ ] `/audit-logs/export/csv` - Export logs
- [ ] `/api/audit-logs/statistics` - Statistics (AJAX)

### System Settings (Super Admin Only)
- [ ] `/system-settings` - Settings overview
- [ ] `/system-settings/ticket-statuses` - Ticket statuses
- [ ] `/system-settings/ticket-priorities` - Ticket priorities
- [ ] `/system-settings/asset-statuses` - Asset statuses
- [ ] `/system-settings/divisions` - Divisions
- [ ] `/system-settings/suppliers` - Suppliers

### Master Data (Super Admin Only)
- [ ] `/models` - Asset models
- [ ] `/manufacturers` - Manufacturers
- [ ] `/asset-types` - Asset types
- [ ] `/suppliers` - Suppliers
- [ ] `/locations` - Locations
- [ ] `/divisions` - Divisions
- [ ] `/budgets` - Budgets

### SLA Management (Super Admin Only)
- [ ] `/sla/dashboard` - SLA dashboard
- [ ] `/sla` - List SLA policies
- [ ] `/sla/create` - Create SLA policy
- [ ] `/sla/{id}/edit` - Edit SLA policy

### System Management (Super Admin Only)
- [ ] `/system/settings` - System settings
- [ ] `/system/permissions` - Permissions management
- [ ] `/system/roles` - Roles management
- [ ] `/system/logs` - System logs
- [ ] `/admin/dashboard` - Admin dashboard
- [ ] `/admin/database` - Database management

**Status**: ⏳ PENDING

---

## ✅ User Portal Routes (modules/user-portal.php)

### User Self-Service
- [ ] `/tiket-saya` - My tickets (user role)
- [ ] `/tiket-saya/buat` - Create new ticket (user)
- [ ] `/tiket-saya/{id}` - View my ticket details
- [ ] Submit new ticket - Should create ticket
- [ ] Add response to ticket

### User Assets
- [ ] `/aset-saya` - My assets (user role)
- [ ] `/aset-saya/{id}` - View my asset details

**Status**: ⏳ PENDING

---

## ✅ Web API Routes (api/web-api.php)

### Search API
- [ ] `/api/search?q=test` - Global search
- [ ] `/api/quick-search?q=test` - Quick search

### Validation API
- [ ] `/api/validate/asset-tag?tag=TEST123` - Validate asset tag
- [ ] `/api/validate/serial-number?serial=ABC123` - Validate serial
- [ ] `/api/validate/email?email=test@example.com` - Validate email
- [ ] `/api/validate/ip-address?ip=192.168.1.1` - Validate IP
- [ ] `/api/validate/mac-address?mac=00:00:00:00:00:00` - Validate MAC

### SLA API
- [ ] `/api/sla/ticket/{id}/status` - Get ticket SLA status
- [ ] `/api/sla/ticket/{id}/breach` - Check SLA breach
- [ ] `/api/sla/metrics` - Get SLA metrics

**Status**: ⏳ PENDING

---

## ✅ Debug Routes (debug.php) - LOCAL ONLY

### Authentication Debug
- [ ] `/debug-menu` - Debug menu homepage
- [ ] `/debug-current-user` - Show current user info
- [ ] `/debug-auth` - Authentication status
- [ ] `/debug-roles` - Role information
- [ ] `/check-users` - List users in database

### Quick Login (Testing Only)
- [ ] `/quick-login-superadmin` - Login as super admin
- [ ] `/quick-login-admin` - Login as admin
- [ ] `/quick-login-user` - Login as user

### Test Routes
- [ ] `/test/qr` - Test QR code generation
- [ ] `/test/status` - System status
- [ ] `/test-inventory-debug` - Inventory controller test
- [ ] `/test-all-controllers` - Test all controllers

**Status**: ⏳ PENDING (Only in local environment)

---

## 🎯 Priority Testing

### **HIGH PRIORITY** (Core Functionality)
1. ✅ Login/Logout
2. ✅ Tickets list page (`/tickets`)
3. ✅ Assets list page (`/assets`)
4. ✅ Dashboard (`/home`)
5. ✅ Create new ticket
6. ✅ Create new asset

### **MEDIUM PRIORITY** (Common Features)
1. ✅ Ticket assignment
2. ✅ Asset QR codes
3. ✅ Daily activities
4. ✅ Notifications
5. ✅ User portal

### **LOW PRIORITY** (Admin/Reports)
1. ✅ System settings
2. ✅ Master data management
3. ✅ Audit logs
4. ✅ Reports & analytics

---

## 📝 Testing Notes

### Issues Found
- [ ] None yet

### Performance Notes
- [ ] Page load times acceptable
- [ ] No console errors
- [ ] AJAX requests working

### Browser Compatibility
- [ ] Chrome ✅
- [ ] Firefox ⏳
- [ ] Edge ⏳

---

## ✅ Final Verification

- [ ] All 355 routes loaded successfully
- [ ] No 404 errors on major pages
- [ ] No 403 authorization errors
- [ ] Session management working
- [ ] CSRF tokens valid
- [ ] Database queries executing
- [ ] File uploads working
- [ ] QR code generation working

---

**Testing Date**: $(Get-Date -Format "yyyy-MM-dd HH:mm")
**Tester**: [Your Name]
**Environment**: Local Development (http://192.168.1.122:80)
**Status**: ⏳ IN PROGRESS
