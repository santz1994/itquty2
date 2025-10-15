# Task Feature Testing Checklist

## How to Use This Checklist
1. Access the application in your browser
2. Log in with appropriate user roles (user, admin, super-admin, management)
3. Test each feature listed below
4. Mark items as ✅ (working) or ❌ (broken) with notes

---

## Task #1: Enhanced Ticket Management System

### Timer Functionality
- [ ] Navigate to a ticket detail page
- [ ] Click "Start Timer" button - verify timer starts
- [ ] Wait a few seconds, click "Stop Timer" - verify time is recorded
- [ ] Refresh page - verify timer state persists
- [ ] Check work summary displays accurate time

### Bulk Operations
- [ ] Go to `/tickets` listing page
- [ ] Select multiple tickets using checkboxes
- [ ] Test "Bulk Assign" - assign to a user
- [ ] Test "Bulk Update Status" - change status
- [ ] Test "Bulk Update Priority" - change priority
- [ ] Test "Bulk Update Category" - change category
- [ ] Verify all changes are applied correctly

### Advanced Filtering
- [ ] Use status filter dropdown
- [ ] Use priority filter dropdown
- [ ] Use assigned user filter
- [ ] Use date range filter
- [ ] Verify results match the filters

---

## Task #2: Admin Online Status Tracking

### Status Indicators
- [ ] Log in as admin
- [ ] Check dashboard for online status indicator
- [ ] Verify your status shows as "Online"
- [ ] Open another browser/incognito, log in as another admin
- [ ] Verify both admins show online in the list

### Last Seen
- [ ] Log out one admin
- [ ] Check the other admin's dashboard
- [ ] Verify the logged-out admin shows "Offline" with last seen time

---

## Task #3: Daily Activity Logging

### Activity Creation
- [ ] Navigate to `/daily-activities`
- [ ] Click "Create Activity"
- [ ] Fill in title, description, activity type, date
- [ ] Submit form - verify activity is created
- [ ] Edit the activity - verify changes save
- [ ] Mark activity as complete
- [ ] Delete the activity

### Calendar View
- [ ] Navigate to `/daily-activities/calendar`
- [ ] Verify calendar displays current month
- [ ] Check that dates with activities show indicators
- [ ] Click on a date - verify activities for that date appear
- [ ] Navigate to previous/next months

### Reporting
- [ ] Go to `/daily-activities/daily-report`
- [ ] Verify today's activities are listed
- [ ] Go to `/daily-activities/weekly-report`
- [ ] Verify this week's activities are listed
- [ ] Click "Export PDF" - verify PDF downloads with activities

---

## Task #4: Enhanced Asset Management

### QR Code Functionality
- [ ] Navigate to an asset detail page (`/assets/{id}`)
- [ ] Click "Generate QR Code" button
- [ ] Verify QR code image displays
- [ ] Download QR code - verify file is saved
- [ ] Go to `/assets/scan-qr`
- [ ] Use QR scanner (or upload QR image) - verify asset is found
- [ ] Test bulk QR generation for multiple assets

### Asset Import/Export
- [ ] Go to `/assets/export`
- [ ] Click export - verify Excel/CSV file downloads
- [ ] Go to `/assets/download-template`
- [ ] Download template - verify template structure
- [ ] Go to `/assets/import-form`
- [ ] Upload valid Excel/CSV file - verify assets are imported
- [ ] Upload invalid file - verify validation errors are shown

### My Assets View
- [ ] Navigate to `/assets/my-assets`
- [ ] Verify only assets assigned to you are listed
- [ ] Click on an asset - verify you can view details
- [ ] Verify asset history is displayed

---

## Task #5: Asset Request System

### Request Creation
- [ ] Navigate to `/asset-requests/create`
- [ ] Fill in asset type, justification, quantity
- [ ] Submit request - verify request is created
- [ ] Verify you receive a notification

### Request Management (Admin/Super-Admin)
- [ ] Log in as admin
- [ ] Navigate to `/asset-requests`
- [ ] View a pending request
- [ ] Click "Approve" - verify status changes to "Approved"
- [ ] Create another request, then click "Reject" - verify status changes
- [ ] Approve a request, then click "Fulfill" - verify status changes to "Fulfilled"

### Notifications
- [ ] Create a request as user
- [ ] Verify admin receives notification
- [ ] Admin approves request
- [ ] Verify user receives approval notification

---

## Task #6: Management Dashboard

### Dashboard Overview
- [ ] Log in as management role
- [ ] Navigate to `/management/dashboard`
- [ ] Verify KPI metrics are displayed (ticket count, asset count, etc.)
- [ ] Verify charts and graphs render correctly
- [ ] Check trend analysis sections

### Admin Performance
- [ ] Navigate to `/management/admin-performance`
- [ ] Verify list of admins with performance metrics
- [ ] Check ticket resolution counts
- [ ] Check average response times
- [ ] Verify performance rankings are accurate

### Reports
- [ ] Navigate to `/management/ticket-reports`
- [ ] Verify ticket statistics and charts
- [ ] Test export to Excel/PDF
- [ ] Navigate to `/management/asset-reports`
- [ ] Verify asset statistics and charts
- [ ] Test export to Excel/PDF

---

## Task #7: Global Search System

### Search Functionality
- [ ] Click on the global search bar in the header (or press Ctrl+K)
- [ ] Type a search query (e.g., asset tag, ticket number, user name)
- [ ] Verify real-time suggestions appear as you type
- [ ] Press Enter or click search
- [ ] Verify results from multiple entity types (tickets, assets, users)

### Search Results
- [ ] Verify results are grouped by entity type
- [ ] Click on a result - verify you navigate to the correct page
- [ ] Test advanced filters (entity type, date range)
- [ ] Verify pagination works for large result sets

---

## Task #8: Advanced Validation & SLA Management

### Real-time Validation
- [ ] Go to `/assets/create`
- [ ] Enter an asset tag that already exists - verify error message
- [ ] Enter a valid unique asset tag - verify green checkmark
- [ ] Enter an invalid serial number format - verify error
- [ ] Enter an invalid email - verify error message
- [ ] Enter an invalid IP address - verify error
- [ ] Enter an invalid MAC address - verify error

### SLA Policies
- [ ] Log in as super-admin
- [ ] Navigate to `/sla`
- [ ] Click "Create SLA Policy"
- [ ] Define response time (e.g., 2 hours)
- [ ] Define resolution time (e.g., 24 hours)
- [ ] Assign to a priority level
- [ ] Save policy - verify it's created

### SLA Tracking
- [ ] Navigate to `/sla/dashboard`
- [ ] Verify SLA metrics are displayed (breach rate, average response time)
- [ ] Create a ticket with a priority that has an SLA
- [ ] View ticket detail - verify SLA status badge (On Track, At Risk, Breached)
- [ ] Wait for SLA to approach breach time - verify status changes

---

## Task #9: Comprehensive Audit Log System

### Automatic Logging
- [ ] Create a new asset
- [ ] Navigate to `/audit-logs`
- [ ] Search for logs related to the asset - verify "created" event is logged
- [ ] Edit the asset
- [ ] Check audit logs - verify "updated" event with before/after values
- [ ] Delete the asset
- [ ] Check audit logs - verify "deleted" event

### Audit Log Viewing
- [ ] Navigate to `/audit-logs`
- [ ] Verify list of all audit logs
- [ ] Filter by event type (e.g., "created", "updated", "deleted")
- [ ] Filter by user - verify only logs for that user appear
- [ ] Filter by model (e.g., "Asset", "Ticket")
- [ ] Filter by date range - verify results match

### Audit Log Details
- [ ] Click on an audit log entry
- [ ] Navigate to `/audit-logs/{id}`
- [ ] Verify log details page shows all information
- [ ] For "updated" events, verify before/after comparison is displayed
- [ ] Verify user information (name, email, IP address)
- [ ] Verify timestamp is accurate

### Export & Cleanup
- [ ] Navigate to `/audit-logs`
- [ ] Click "Export CSV"
- [ ] Verify CSV file downloads with all log data
- [ ] Click "Cleanup Logs"
- [ ] Select date range for cleanup
- [ ] Confirm cleanup - verify old logs are removed

---

## Menu and Navigation

### Sidebar Menu Items
- [ ] Log in as user - verify appropriate menu items (Tickets, My Assets, etc.)
- [ ] Log in as admin - verify additional menu items (All Assets, Asset Requests, Daily Activities, Audit Logs)
- [ ] Log in as management - verify Management Dashboard menu
- [ ] Log in as super-admin - verify all menu items including System Settings, SLA Management

### Global Search Bar
- [ ] Verify global search bar is visible in header
- [ ] Test keyboard shortcut (Ctrl+K) to focus search bar
- [ ] Test search functionality

---

## API Endpoints (for developers/API users)

### Test API Endpoints
Run these in Postman or similar tool (with appropriate auth tokens):

```
GET /api/search?q=test
GET /api/quick-search?q=test
GET /api/validate/asset-tag?tag=ABC123
GET /api/validate/email?email=test@example.com
GET /api/sla/metrics
GET /api/audit-logs/statistics
GET /api/audit-logs/my-logs
```

---

## Performance and Responsiveness

### Page Load Times
- [ ] Home dashboard loads quickly (< 2 seconds)
- [ ] Large asset listings load quickly with pagination
- [ ] Ticket listings load quickly with filters
- [ ] Reports generate in reasonable time

### Mobile Responsiveness
- [ ] Open application on mobile device or resize browser
- [ ] Verify sidebar collapses on small screens
- [ ] Verify tables are scrollable/responsive
- [ ] Verify forms are usable on mobile
- [ ] Test QR code scanner on mobile device

---

## Error Handling

### Test Error Scenarios
- [ ] Try accessing a route you don't have permission for - verify 403 error
- [ ] Try accessing a non-existent resource - verify 404 error
- [ ] Submit a form with missing required fields - verify validation errors
- [ ] Try bulk operation with no items selected - verify error message
- [ ] Test with poor internet connection - verify graceful degradation

---

## Notes and Issues Found

(Add notes here during testing)

---

## Sign-off

- Tester Name: _______________
- Date: _______________
- Overall Status: ⬜ All Pass ⬜ Issues Found ⬜ Major Issues
- Approved for Production: ⬜ Yes ⬜ No ⬜ With Conditions
