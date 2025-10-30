# MASTER TODO LIST - ITQuty2 Implementation & Fixes
## Production-Ready Roadmap

**Created:** October 30, 2025  
**Total Items:** 48  
**Priority Breakdown:** 12 CRITICAL | 18 HIGH | 12 MEDIUM | 6 LOW  
**Estimated Effort:** 4-6 weeks for full implementation  

---

## 🔴 CRITICAL - MUST FIX BEFORE PRODUCTION

### Database Schema (CRITICAL)

#### ✅ 1. Verify Serial Number UNIQUE Migration (DONE - Oct 29)
- **Status:** Recently implemented in migration `2025_10_29_160000_add_unique_serial_to_assets.php`
- **Verification Needed:**
  - [ ] Connect to staging DB and verify migration applied successfully
  - [ ] Check `storage/app/serial_duplicates_before_unique.csv` for any duplicates
  - [ ] If duplicates found, resolve them before production
  - [ ] Run test to ensure duplicate serials are now rejected
- **Acceptance Criteria:** `INSERT INTO assets (serial_number) VALUES ('SAME_SERIAL')` fails with UNIQUE constraint error
- **Time Estimate:** 30 minutes

#### ✅ 2. Verify Purchase Orders Implementation (DONE - Oct 29)
- **Status:** Migrations created: `2025_10_29_150000`, `2025_10_29_150500`
- **Verification Needed:**
  - [ ] Confirm table structure matches design spec
  - [ ] Verify FK relationship `assets.purchase_order_id` → `purchase_orders.id`
  - [ ] Check on-delete rule (should be SET NULL or RESTRICT)
  - [ ] Test storing/updating asset with purchase_order
  - [ ] Verify views display purchase order info correctly
- **Acceptance Criteria:** Asset create/edit form shows purchase order selector; can save without error
- **Time Estimate:** 45 minutes

### 3. Create ticket_assets Pivot Table (CRITICAL - HIGH IMPACT)
- **Purpose:** Support many-to-many relationship between tickets and assets
- **Files to Create:**
  ```
  database/migrations/2025_10_30_XXXXXX_create_ticket_assets_table.php
  app/Listeners/TicketAssetChangeListener.php (optional - for logging)
  ```
- **Migration Content:**
  ```php
  Schema::create('ticket_assets', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('ticket_id');
      $table->unsignedBigInteger('asset_id');
      $table->text('notes')->nullable();
      $table->timestamps();
      
      $table->unique(['ticket_id', 'asset_id']);
      $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
      $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
      $table->index('asset_id');  // For reverse queries
  });
  ```
- **Steps:**
  1. Create migration file with above schema
  2. Create data migration to populate from existing `tickets.asset_id` (one-off)
  3. Update Ticket model: add `assets()` relationship
  4. Update Asset model: add `tickets()` relationship
  5. Update TicketController to handle multi-asset selection
  6. Update ticket create/edit views for multi-select
  7. Add tests for pivot operations
  8. Keep `tickets.asset_id` for 2-3 releases (deprecation window)
- **Acceptance Criteria:**
  - Can attach multiple assets to one ticket
  - Can attach one asset to multiple tickets
  - Pivot operations work correctly (attach, detach, sync)
  - No data loss from migration
- **Time Estimate:** 4-5 hours

### 4. Create ticket_history Immutable Audit Log (CRITICAL - COMPLIANCE)
- **Purpose:** Track all changes to tickets for SLA compliance and audit
- **Files to Create:**
  ```
  database/migrations/2025_10_30_XXXXXX_create_ticket_history_table.php
  app/Models/TicketHistory.php
  app/Listeners/TicketChangeLogger.php (or use model observer)
  ```
- **Migration Content:**
  ```php
  Schema::create('ticket_history', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('ticket_id');
      $table->string('field_changed');  // e.g., 'status_id', 'assigned_to', 'priority'
      $table->text('old_value')->nullable();
      $table->text('new_value')->nullable();
      $table->unsignedBigInteger('changed_by_user_id');
      $table->timestamp('changed_at')->useCurrent();
      
      $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
      $table->foreign('changed_by_user_id')->references('id')->on('users')->onDelete('restrict');
      $table->index(['ticket_id', 'changed_at']);
  });
  ```
- **Steps:**
  1. Create migration
  2. Create TicketHistory model (no mass assignment, immutable)
  3. Add observer to Ticket model that logs changes
  4. Wire into Ticket update operations
  5. Create query method `Ticket::getHistory()` 
  6. Display history in ticket show/edit views
  7. Create admin report for audit trail
- **Acceptance Criteria:**
  - Every ticket field change recorded with user and timestamp
  - Cannot edit history (immutable)
  - Can query full audit trail for any ticket
- **Time Estimate:** 5-6 hours

### 5. Fix Serial Number Validation in Forms (CRITICAL - DATA QUALITY)
- **Purpose:** Prevent duplicate serials at form validation level
- **Files to Modify:**
  - `app/Http/Requests/StoreAssetRequest.php`
  - `resources/assets/js/serial-validator.js` (new)
  - `resources/views/assets/create.blade.php`
  - `resources/views/assets/edit.blade.php`
- **Changes:**
  1. Fix validation rule to handle NULLs correctly:
     ```php
     'serial_number' => [
         'nullable',
         'string',
         'max:255',
         Rule::unique('assets', 'serial_number')
             ->ignore($this->route('asset')?->id)
             ->whereNotNull('serial_number')  // ← KEY FIX
     ]
     ```
  2. Add AJAX endpoint validation (wire existing `GET /api/assets/check-serial`)
  3. Add client-side JS to check serial on blur:
     ```javascript
     // Check serial uniqueness on blur
     document.getElementById('serial_number')?.addEventListener('blur', async function() {
         const serial = this.value;
         if (!serial) return;  // Skip empty
         
         const response = await fetch(`/api/assets/check-serial?serial=${serial}`);
         const data = await response.json();
         
         if (data.exists) {
             // Show error
         }
     });
     ```
  4. Add visual feedback in forms (error message display)
  5. Test: Try entering duplicate serial, verify rejection
- **Acceptance Criteria:**
  - Form shows error if serial exists (AJAX feedback)
  - Server validation rejects duplicates
  - NULL serials allowed (for non-hardware)
- **Time Estimate:** 2-3 hours

### 6. Fix Assets Database Foreign Key Constraints (CRITICAL - INTEGRITY)
- **Purpose:** Enforce data integrity with correct on-delete rules
- **Audit Required:**
  ```sql
  -- Check current rules in DB
  SHOW CREATE TABLE assets;
  SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
  WHERE TABLE_NAME = 'assets' AND COLUMN_NAME LIKE '%_id';
  ```
- **Rules to Enforce:**
  | FK Column | Table | OnDelete Rule | Reason |
  |-----------|-------|---------------|---------|
  | `assigned_to` | users | RESTRICT | Prevent losing asset ownership |
  | `model_id` | asset_models | RESTRICT | Prevent orphaned assets |
  | `division_id` | divisions | RESTRICT | Prevent losing organizational context |
  | `supplier_id` | suppliers | RESTRICT | Preserve purchase history |
  | `purchase_order_id` | purchase_orders | SET NULL | Allow PO cleanup |
  | `warranty_type_id` | warranty_types | RESTRICT | Keep warranty definitions |
  | `invoice_id` | invoices | SET NULL | Allow invoice cleanup |
- **Create Migration:**
  ```php
  // In migration file - drop old FKs, add new with correct rules
  Schema::table('assets', function (Blueprint $table) {
      // Drop existing FKs
      $table->dropForeign(['assigned_to']);
      
      // Re-add with correct rule
      $table->foreign('assigned_to')
            ->references('id')
            ->on('users')
            ->onDelete('restrict');
  });
  ```
- **Test:** Try deleting related records, verify expected constraints
- **Time Estimate:** 2-3 hours

---

## 🔴 HIGH PRIORITY - NEXT PHASE

### Model Relationships & Design

#### 7. Implement All Missing Model Relationships
- **File:** Update `app/Asset.php`, `app/Ticket.php`, `app/User.php`
- **Missing Relationships:**
  ```php
  // Asset.php
  public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
  public function tickets() { return $this->belongsToMany(Ticket::class, 'ticket_assets'); }
  public function movements() { return $this->hasMany(Movement::class); }
  public function maintenanceLogs() { return $this->hasMany(AssetMaintenanceLog::class); }
  public function location() { return $this->hasOneThrough(Location::class, Movement::class); }  // Latest movement
  
  // Ticket.php
  public function assets() { return $this->belongsToMany(Asset::class, 'ticket_assets'); }
  public function comments() { return $this->hasMany(TicketComment::class); }
  public function history() { return $this->hasMany(TicketHistory::class); }
  public function location() { return $this->belongsTo(Location::class); }
  
  // User.php
  public function createdTickets() { return $this->hasMany(Ticket::class, 'user_id'); }
  public function assignedTickets() { return $this->hasMany(Ticket::class, 'assigned_to'); }
  public function assignedAssets() { return $this->hasMany(Asset::class, 'assigned_to'); }
  ```
- **Acceptance Criteria:** All relationships load correctly; no n+1 queries
- **Time Estimate:** 2-3 hours

#### 8. Create TicketComment Model & Relationship
- **Purpose:** Separate comment handling from ticket entries
- **Files to Create:**
  ```
  app/Models/TicketComment.php
  database/migrations/2025_10_30_XXXXXX_create_ticket_comments_table.php
  ```
- **Schema:**
  ```php
  Schema::create('ticket_comments', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('ticket_id');
      $table->unsignedBigInteger('user_id');
      $table->text('comment');
      $table->boolean('is_internal')->default(false);  // Internal notes vs customer visible
      $table->timestamps();
      
      $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
  });
  ```
- **Accept Criteria:** Comments attached to tickets; can filter internal vs external
- **Time Estimate:** 3-4 hours

#### 9. Create DailyActivity Complete Integration
- **Purpose:** Fully implement time tracking for KPI calculations
- **Current:** Model exists but not wired into controllers
- **Files to Modify:**
  - `app/Http/Controllers/TicketController.php` - Log time on ticket close
  - `app/Http/Controllers/AssetsController.php` - Log maintenance activities
  - `routes/api.php` - Add activity logging endpoints
- **Operations to Log:**
  ```php
  // When ticket status → resolved
  DailyActivity::create([
      'activity_type_id' => ActivityType::find('ticket_resolution')->id,
      'user_id' => auth()->id(),
      'ticket_id' => $ticket->id,
      'start_time' => $ticket->created_at,  // or tracked start_time
      'end_time' => now(),
      'duration_minutes' => $ticket->created_at->diffInMinutes(now()),
  ]);
  
  // For asset maintenance
  DailyActivity::create([
      'activity_type_id' => ActivityType::find('maintenance')->id,
      'user_id' => auth()->id(),
      'asset_id' => $asset->id,
      'start_time' => $request->start_time,
      'end_time' => $request->end_time,
  ]);
  ```
- **Time Estimate:** 4-5 hours

### Form Validation Hardening

#### 10. Fix Form Validation - Asset Request Form
- **File:** `app/Http/Requests/StoreAssetRequest.php`
- **Issues to Fix:**
  1. Serial number UNIQUE rule with NULL handling (see #5)
  2. Cross-field validation:
     ```php
     'warranty_months' => 'nullable|integer|min:0|max:84',
     'warranty_expiry_date' => 'nullable|date|after:purchase_date',
     // If warranty_months > 0, warranty_type_id required
     ```
  3. Asset tag max length validation
  4. Model ID / Asset Model ID consistency
- **Acceptance Criteria:** All edge cases handled; validation messages clear
- **Time Estimate:** 2-3 hours

#### 11. Fix Form Validation - Ticket Request Form
- **Files:** `app/Http/Requests/StoreTicketRequest.php` (create if missing)
- **Rules Needed:**
  ```php
  'title' => 'required|string|max:255',
  'description' => 'required|string|min:10',
  'priority' => 'required|exists:ticket_priorities,id',
  'type' => 'required|exists:ticket_types,id',
  'asset_ids' => 'nullable|array',  // For many-to-many
  'asset_ids.*' => 'exists:assets,id',
  'location_id' => 'required|exists:locations,id',
  ```
- **Time Estimate:** 2-3 hours

### View Improvements

#### 12. Cleanup Asset Create View
- **File:** `resources/views/assets/create.blade.php`
- **Issues to Fix:**
  1. Remove duplicate `purchase_date` input fields
  2. Remove duplicate `warranty_type_id` blocks
  3. Standardize `asset_tag` maxlength (should be 50 or verify DB max)
  4. Add visual sections (tabs or card groups):
     - Basic Information
     - Purchase Information
     - Warranty Information
     - Location & Assignment
     - Additional Details
  5. Add help text for complex fields
  6. Add validation error styling consistency
  7. Add success/error flash messages
- **Acceptance Criteria:** No duplicate fields; consistent styling; error feedback visible
- **Time Estimate:** 3-4 hours

#### 13. Cleanup Asset Edit View
- **File:** `resources/views/assets/edit.blade.php`
- **Same issues as #12 plus:**
  1. Show asset's current values
  2. Show asset creation date (read-only)
  3. Show last modified date/user
  4. Add "Viewing serial: XXXXX" hint
  5. Add maintenance history sidebar
- **Time Estimate:** 3-4 hours

#### 14. Cleanup Ticket Create View
- **File:** `resources/views/tickets/create.blade.php`
- **Changes:**
  1. Replace single asset selector with multi-select (if pivot implemented)
  2. Add real-time SLA calculation display:
     ```blade
     SLA Due: <span id="sla-due">{{ $ticket->sla_due ?? 'Not calculated' }}</span>
     ```
  3. Add priority → SLA mapping help text
  4. Add asset search/filter
  5. Add description character counter
- **Time Estimate:** 3-4 hours

#### 15. Cleanup Ticket Edit View
- **File:** `resources/views/tickets/edit.blade.php`
- **Changes:**
  1. Add read-only ticket history section (scrollable)
  2. Show current SLA status (on-track, at-risk, overdue)
  3. Show time invested (sum of daily activities)
  4. Show all comments (internal & external with filtering)
  5. Add "Mark as Resolved" button with modal
  6. Show related assets with click-to-view links
- **Time Estimate:** 4-5 hours

---

## 🟠 MEDIUM PRIORITY - QUALITY & FEATURES

### 16. Create Location Tracking Decision & Implementation
- **Decision:** Denormalize location to assets table
- **Rationale:** 
  - Current: Query requires JOIN with movements table
  - With denormalization: Direct access, much faster
  - Trade-off: Must maintain consistency
- **Implementation:**
  1. Create migration to add `location_id` to assets
  2. Create data migration to backfill from latest movement
  3. Create migration hook to update asset location on movement create
  4. Update Asset model with accessor/mutator
  5. Update Movement controller to update asset location
  6. Add index on location_id
- **Time Estimate:** 3-4 hours

### 17. Implement Request Numbering for Asset Requests
- **Status:** Migration created (2025_10_29_151000)
- **Verify & Complete:**
  1. Check migration applied correctly
  2. Verify `request_number` format (AR-YYYY-NNNN)
  3. Test backfill of existing records
  4. Display request_number in views
  5. Add unique index if not present
  6. Create test for numbering
- **Time Estimate:** 2-3 hours

### 18. Create Comprehensive Index Strategy
- **File to Create:**
  ```
  database/migrations/2025_10_30_XXXXXX_optimize_database_indexes.php
  ```
- **Indexes to Add:**
  ```php
  // Assets table
  Schema::table('assets', function (Blueprint $table) {
      $table->index('model_id');  // Ensure exists
      $table->index('status_id');
      $table->index('assigned_to');
      $table->index('location_id');  // If denormalized
      $table->index(['model_id', 'status_id']);  // Composite
      $table->index(['location_id', 'status_id']);  // Composite
  });
  
  // Tickets table
  Schema::table('tickets', function (Blueprint $table) {
      $table->index('ticket_status_id');
      $table->index('assigned_to');
      $table->index('location_id');
      $table->index('user_id');  // Reporter
      $table->index(['assigned_to', 'ticket_status_id']);  // Composite
      $table->index(['location_id', 'ticket_status_id']);  // Composite
  });
  
  // Daily Activities
  Schema::table('daily_activities', function (Blueprint $table) {
      $table->index('user_id');
      $table->index('ticket_id');
      $table->index('asset_id');
      $table->index('activity_type_id');
      $table->index(['user_id', 'created_at']);  // For analytics
  });
  
  // Ticket History
  Schema::table('ticket_history', function (Blueprint $table) {
      $table->index(['ticket_id', 'changed_at']);
      $table->index('changed_by_user_id');
  });
  
  // Ticket Assets Pivot
  Schema::table('ticket_assets', function (Blueprint $table) {
      $table->index('asset_id');  // For reverse queries
  });
  ```
- **Acceptance:** Run EXPLAIN queries to verify indexes used
- **Time Estimate:** 2-3 hours

### 19. Create KPI Dashboard Backend
- **Purpose:** Calculate and display KPI metrics per design spec
- **Files to Create:**
  ```
  app/Http/Controllers/KPIDashboardController.php
  app/Services/KPICalculationService.php
  app/Queries/KPIQueries.php
  routes/modules/kpi-dashboard.php
  ```
- **KPIs to Implement:**
  1. MTTR (Mean Time To Resolution)
  2. FCR (First Contact Resolution)
  3. Ticket Backlog
  4. Technician Utilization
  5. SLA Compliance
  6. Support Cost per Asset
  7. Asset Utilization by Location
- **Database Queries:**
  ```php
  // MTTR
  SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as mttr_minutes
  FROM tickets
  WHERE ticket_type = 'Incident' AND resolved_at IS NOT NULL;
  
  // FCR
  SELECT 
    ROUND(100.0 * COUNT(DISTINCT CASE WHEN assignment_count = 1 THEN id END) / COUNT(*), 2) as fcr_percent
  FROM (
      SELECT t.id, COUNT(DISTINCT th.changed_by) as assignment_count
      FROM tickets t
      LEFT JOIN ticket_history th ON t.id = th.ticket_id AND th.field_changed = 'assigned_to'
      GROUP BY t.id
  ) subq;
  ```
- **Time Estimate:** 6-8 hours

### 20. Add Comprehensive PHPUnit Tests
- **Files to Create:**
  ```
  tests/Feature/AssetCRUDTest.php
  tests/Feature/TicketCRUDTest.php
  tests/Feature/AssetRequestTest.php
  tests/Feature/SerialNumberValidationTest.php
  tests/Feature/SLACalculationTest.php
  tests/Unit/Models/AssetTest.php
  tests/Unit/Models/TicketTest.php
  ```
- **Test Coverage:**
  1. Asset creation with serial validation
  2. Ticket creation with multi-asset support (after #3)
  3. SLA calculation on ticket creation
  4. Ticket status transitions
  5. User role-based access
  6. Serial number AJAX validation
  7. Relationship integrity
  8. Cascade delete behavior
- **Acceptance Criteria:** `php artisan test` passes 100%
- **Time Estimate:** 8-10 hours

### 21. Create API Documentation
- **Options:**
  1. **OpenAPI/Swagger** (Recommended)
     - Use Laravel package: `laravel-open-api` or manual YAML
     - File: `docs/api-documentation.yaml` or `/api/docs`
  2. **Postman Collection**
     - Export from API platform
  3. **Simple Markdown**
     - File: `docs/API.md`
- **Endpoints to Document:**
  - Asset CRUD endpoints
  - Ticket CRUD endpoints
  - Asset serial check endpoint
  - Activity logging endpoints
  - KPI endpoints
- **Time Estimate:** 3-4 hours

### 22. Create Deployment Plan & Runbook
- **Files to Create:**
  ```
  docs/DEPLOYMENT_PLAN.md
  docs/ROLLBACK_PROCEDURES.md
  docs/STAGING_CHECKLIST.md
  scripts/pre-deployment-checks.php
  ```
- **Content:**
  1. Pre-deployment checks (DB connectivity, migrations dry-run, disk space)
  2. Migration order (critical: FK constraints first)
  3. Data migration procedures (ticket_assets population)
  4. Rollback steps for each migration
  5. Rollback window (max time before rollback)
  6. Post-deployment verification (sanity checks)
  7. Monitoring during/after (error logs, slow queries)
  8. Communication plan (notify stakeholders)
- **Example Deployment Order:**
  ```
  1. Backup database
  2. Run pre-deployment checks
  3. Apply FK constraint migrations
  4. Apply table creation migrations (ticket_assets, ticket_history)
  5. Apply index migrations
  6. Run data migrations (backfill)
  7. Test critical flows in staging
  8. Update views/forms
  9. Warm up caches
  10. Switch to production
  11. Monitor for 2 hours
  ```
- **Time Estimate:** 3-4 hours

### 23. Create Data Dictionary & Schema Documentation
- **File:** `docs/Perbaikan Database/Task/KAMUS_DATA.md`
- **Content:**
  1. Complete table listing with descriptions
  2. Column definitions (name, type, constraints)
  3. Foreign key relationships with on-delete rules
  4. Indexes (why each one exists)
  5. Example queries for common use cases
  6. Sample data for testing
- **Format:**
  ```markdown
  ## Table: assets
  
  | Column | Type | Constraints | Description |
  |--------|------|-------------|-------------|
  | id | BIGINT | PK, auto-increment | Unique identifier |
  | asset_tag | VARCHAR(50) | UNIQUE, NOT NULL | Business identifier (barcode) |
  | serial_number | VARCHAR(100) | UNIQUE, NULL | Manufacturer serial |
  
  ### Foreign Keys
  - model_id → asset_models.id (RESTRICT)
  
  ### Indexes
  - idx_asset_tag (unique)
  - idx_model_status (composite)
  ```
- **Time Estimate:** 3-4 hours

---

## 🟡 LOW PRIORITY - ENHANCEMENTS

### 24. Add Soft Deletes for Auditable Records
- **Purpose:** Never permanently delete; preserve history
- **Files to Modify:**
  - `database/migrations/*` - Add `soft_deletes()` to asset, ticket tables
  - `app/Asset.php` - Add `SoftDeletes` trait
  - `app/Ticket.php` - Add `SoftDeletes` trait
- **Consideration:** Update queries to include `withoutTrashed()` where needed
- **Time Estimate:** 2-3 hours

### 25. Create Asset Lifecycle Events (Model Observers)
- **Purpose:** Trigger actions on key asset state changes
- **Events to Trigger:**
  1. Asset created → Generate QR code, send notification
  2. Asset assigned → Send email to assignee
  3. Asset warranty expiring (< 30 days) → Send alert
  4. Asset status → "In Repair" → Create maintenance log
  5. Asset status → "Disposed" → Archive from main views
- **File:** `app/Observers/AssetObserver.php`
- **Time Estimate:** 3-4 hours

### 26. Create Ticket Lifecycle Events (Model Observers)
- **Purpose:** Trigger actions on ticket state changes
- **Events:**
  1. Ticket created → Auto-assign based on location/type
  2. Ticket priority increased → Escalate + notify
  3. Ticket SLA approaching → Send alert
  4. Ticket resolved → Request user feedback (survey)
  5. Ticket closed → Archive, calculate metrics
- **File:** `app/Observers/TicketObserver.php`
- **Time Estimate:** 3-4 hours

### 27. Implement Caching Strategy
- **Purpose:** Reduce DB queries for frequently accessed data
- **Items to Cache:**
  - Asset counts by status
  - Ticket statistics (total open, overdue, etc.)
  - KPI values (MTTR, FCR, etc.)
  - User's assigned assets/tickets
  - Master data (locations, statuses, types, etc.)
- **Implementation:**
  ```php
  // In AssetController::index()
  $stats = Cache::remember('asset_statistics', 3600, function () {
      return [
          'total' => Asset::count(),
          'deployed' => Asset::assigned()->count(),
          'ready' => Asset::inStock()->count(),
      ];
  });
  
  // Invalidate on create/update
  Cache::forget('asset_statistics');
  ```
- **TTL Strategy:**
  - Master data: 24 hours
  - Statistics: 1 hour
  - User-specific: 30 minutes
- **Time Estimate:** 2-3 hours

### 28. Add Advanced Search/Filtering
- **Purpose:** Help users find assets/tickets quickly
- **Features:**
  1. Full-text search (asset_tag, serial_number, model)
  2. Advanced filters (date ranges, multi-select status, location)
  3. Saved filters (for frequent searches)
  4. Search result pagination
- **Implementation:** Use Laravel Scout (recommended) or manual filters
- **Time Estimate:** 4-6 hours

---

## 📋 TESTING & QUALITY ASSURANCE

### 29. Setup PHPUnit & Create Test Suite
- **Framework:** PHPUnit (Laravel built-in)
- **Coverage Target:** 70%+
- **Configuration File:** `phpunit.xml` (already exists)
- **Test Database:** SQLite (in-memory for speed)
- **Commands:**
  ```bash
  php artisan test                    # Run all tests
  php artisan test --coverage         # With coverage report
  ```
- **Time Estimate:** 2-3 hours

### 30. Create Integration Tests
- **Purpose:** Test workflows end-to-end
- **Examples:**
  ```php
  test('can create asset with serial and verify uniqueness', function () {
      $asset1 = Asset::factory()->create(['serial_number' => 'ABC123']);
      
      // Try to create duplicate
      $this->assertThrows(Exception::class, function () {
          Asset::create(['serial_number' => 'ABC123']);
      });
  });
  
  test('ticket status transition creates history record', function () {
      $ticket = Ticket::factory()->create();
      $ticket->update(['ticket_status_id' => 2]);
      
      $this->assertDatabaseHas('ticket_history', [
          'ticket_id' => $ticket->id,
          'field_changed' => 'ticket_status_id'
      ]);
  });
  ```
- **Time Estimate:** 4-6 hours

### 31. Create API Endpoint Tests
- **Purpose:** Verify API contracts
- **Examples:**
  ```php
  test('GET /api/assets returns paginated results', function () {
      Asset::factory(5)->create();
      
      $response = $this->getJson('/api/assets');
      $response->assertStatus(200)
               ->assertJsonStructure(['data', 'links', 'meta']);
  });
  
  test('POST /api/assets rejects duplicate serial', function () {
      Asset::factory()->create(['serial_number' => 'SERIAL1']);
      
      $response = $this->postJson('/api/assets', [
          'serial_number' => 'SERIAL1'
      ]);
      $response->assertStatus(422);
  });
  ```
- **Time Estimate:** 3-5 hours

### 32. Create Database Migration Safety Tests
- **Purpose:** Verify migrations won't break production
- **Coverage:**
  1. Migration up() works correctly
  2. Migration down() restores original state
  3. Data integrity preserved (no orphaned FKs)
  4. Indexes created as expected
  5. Constraints enforced
- **Time Estimate:** 2-3 hours

---

## 📊 MONITORING & OBSERVABILITY

### 33. Implement Application Logging
- **Current:** Laravel default logging to `storage/logs/`
- **Enhancements:**
  1. Add contextual logging (user ID, request ID)
  2. Log all CRUD operations with old/new values
  3. Log permission denials
  4. Log slow queries (> 1 second)
  5. Log failed validations
- **File:** `.env` - Set `LOG_CHANNEL=stack` or `single`
- **Time Estimate:** 1-2 hours

### 34. Setup Query Logging & Optimization
- **Purpose:** Identify slow queries
- **Implementation:**
  ```php
  // In AppServiceProvider::boot()
  DB::listen(function ($query) {
      if ($query->time > 1000) {  // Queries > 1 second
          Log::warning('Slow Query', [
              'query' => $query->sql,
              'time' => $query->time . 'ms'
          ]);
      }
  });
  ```
- **Usage:** Run `tail -f storage/logs/laravel.log` to monitor
- **Time Estimate:** 1-2 hours

### 35. Create Health Check Endpoints
- **Purpose:** Monitor application status
- **Endpoints:**
  - `GET /health` - Basic check (DB connectivity, disk space)
  - `GET /health/database` - DB-specific checks
  - `GET /health/cache` - Cache system status
- **Response Format:**
  ```json
  {
    "status": "healthy",
    "timestamp": "2025-10-30T10:30:00Z",
    "checks": {
      "database": { "status": "ok", "latency_ms": 5 },
      "cache": { "status": "ok" },
      "disk": { "status": "ok", "free_gb": 50 }
    }
  }
  ```
- **Time Estimate:** 1-2 hours

---

## 📚 DOCUMENTATION & TRAINING

### 36. Create User Documentation
- **Files:**
  - `docs/USER_GUIDE.md` - How to use the system
  - `docs/TROUBLESHOOTING.md` - Common issues and fixes
  - `docs/FAQ.md` - Frequently asked questions
- **Topics to Cover:**
  1. Creating an asset
  2. Creating a ticket
  3. Managing assignments
  4. Running reports
  5. Mobile QR code scanning
- **Time Estimate:** 2-3 hours

### 37. Create Administrator Guide
- **File:** `docs/ADMIN_GUIDE.md`
- **Topics:**
  1. User management & roles
  2. System settings
  3. Backup & recovery procedures
  4. Log file analysis
  5. Performance tuning
  6. Troubleshooting common issues
- **Time Estimate:** 2-3 hours

### 38. Create Developer Setup Guide
- **File:** `docs/DEVELOPER_SETUP.md`
- **Content:**
  1. Clone repository
  2. Install dependencies (`composer install`, `npm install`)
  3. Setup `.env` file
  4. Run migrations & seeders
  5. Start development server
  6. Running tests
  7. Code style guidelines
  8. Git workflow
- **Time Estimate:** 1-2 hours

### 39. Create API Integration Guide
- **File:** `docs/API_INTEGRATION_GUIDE.md`
- **Content:**
  1. Authentication (Sanctum tokens)
  2. Common endpoints examples
  3. Error handling
  4. Rate limiting
  5. Webhook support (if implemented)
- **Time Estimate:** 2-3 hours

---

## 🔧 PERFORMANCE & OPTIMIZATION

### 40. Implement Eager Loading Strategy
- **Purpose:** Eliminate n+1 queries
- **Pattern:** Create `withRelations()` scope on all models
  ```php
  public function scopeWithRelations($query) {
      return $query->with([
          'model',
          'status',
          'division',
          'assignedUser'
      ]);
  }
  ```
- **Usage:** `Asset::withRelations()->get()` instead of `Asset::all()`
- **Time Estimate:** 2-3 hours

### 41. Optimize N+1 Query Scenarios
- **Audit all controllers for:**
  ```php
  // ❌ BAD
  foreach ($assets as $asset) {
      $asset->model->name;  // Query per iteration!
  }
  
  // ✅ GOOD
  $assets->load('model');  // Single query, already loaded
  foreach ($assets as $asset) {
      $asset->model->name;
  }
  ```
- **Time Estimate:** 2-3 hours

### 42. Implement Database Query Optimization
- **Identify Slow Queries:**
  ```bash
  # Enable slow query log in MySQL
  SET GLOBAL slow_query_log = 'ON';
  SET GLOBAL long_query_time = 1;  # 1 second
  
  # Review log
  tail -f /var/log/mysql/slow-query.log
  ```
- **Common Optimizations:**
  1. Add missing indexes (see #18)
  2. Use `COUNT(*)` directly instead of loading all records
  3. Limit result sets appropriately
  4. Use select() to fetch specific columns
- **Time Estimate:** 2-3 hours

---

## 🔐 SECURITY HARDENING

### 43. Implement API Rate Limiting
- **Purpose:** Prevent abuse
- **Implementation:**
  ```php
  // In routes/api.php
  Route::middleware('throttle:60,1')->group(function () {  // 60 requests per minute
      Route::apiResource('assets', AssetController::class);
  });
  ```
- **Time Estimate:** 1 hour

### 44. Add Input Sanitization & Escaping
- **Audit all forms for:**
  - XSS vulnerabilities (use `{{ }}` in Blade, not `{!! !!}`)
  - SQL injection (use parameterized queries)
  - CSRF protection (verify CSRF tokens)
- **Time Estimate:** 2-3 hours

### 45. Implement Field Encryption for Sensitive Data
- **Candidates for Encryption:**
  - User email (if needed)
  - Phone numbers
  - API tokens
  - Personal identification numbers
- **Implementation:**
  ```php
  // In model
  protected $encrypted = ['phone', 'id_number'];
  ```
- **Time Estimate:** 2-3 hours

### 46. Add Multi-Factor Authentication (Optional)
- **Purpose:** Enhance security for admin accounts
- **Package:** `laravel-fortify` or `laravel-jetstream`
- **Time Estimate:** 4-6 hours (if implemented)

---

## 🚀 PRODUCTION READINESS

### 47. Create Production Checklist
- **File:** `docs/PRODUCTION_CHECKLIST.md`
- **Items:**
  - [ ] All critical migrations applied
  - [ ] Database backups configured
  - [ ] Error logging setup
  - [ ] SSL certificates installed
  - [ ] CORS configured
  - [ ] Rate limiting enabled
  - [ ] Cache warmed
  - [ ] Slow query log enabled
  - [ ] Monitoring alerts configured
  - [ ] Incident response plan documented
  - [ ] Rollback procedures tested
  - [ ] User & admin documentation complete
- **Time Estimate:** 1-2 hours

### 48. Final Integration Testing
- **Purpose:** Verify everything works in staging environment
- **Test Scenarios:**
  1. Complete asset lifecycle (create → deploy → maintain → retire)
  2. Complete ticket lifecycle (create → assign → resolve → close)
  3. User role-based access (verify all roles)
  4. API endpoints (manual testing with Postman)
  5. Bulk operations (if implemented)
  6. Report generation
  7. Export/import functionality
  8. Email notifications
  9. Backup & restore procedures
- **Duration:** 1-2 days
- **Time Estimate:** 8-12 hours

---

## 📈 IMPLEMENTATION TIMELINE

### Phase 1: CRITICAL (Week 1-2)
- Items: 1-6 (Database & Validation)
- Effort: 20-25 hours
- Risk: HIGH - These affect data integrity

### Phase 2: HIGH PRIORITY (Week 2-3)
- Items: 7-15 (Relationships & Views)
- Effort: 30-40 hours
- Risk: MEDIUM - Core functionality

### Phase 3: QUALITY (Week 3-4)
- Items: 16-23 (Features & Documentation)
- Effort: 30-35 hours
- Risk: MEDIUM - Improves stability

### Phase 4: OPTIMIZATION (Week 4-5)
- Items: 24-35 (Performance & Monitoring)
- Effort: 20-30 hours
- Risk: LOW - Nice-to-have but valuable

### Phase 5: PRODUCTION (Week 5-6)
- Items: 36-48 (Documentation & Testing)
- Effort: 20-25 hours
- Risk: LOW - Final polish

**Total Effort:** 120-155 hours (~4-6 weeks with proper testing & review)

---

## 🎯 SUCCESS CRITERIA

✅ **Application is Production-Ready when:**

1. ✓ All CRITICAL items completed and tested
2. ✓ Database integrity verified (no orphaned FKs, duplicate serials impossible)
3. ✓ All models have correct relationships
4. ✓ Forms have consistent validation (server + client)
5. ✓ Views are clean and user-friendly
6. ✓ All CRUD operations work end-to-end
7. ✓ Tests pass (70%+ coverage)
8. ✓ No N+1 queries in production
9. ✓ Deployment procedures documented & tested
10. ✓ Team trained on system usage

---

## 📞 QUESTIONS & DECISIONS NEEDED

Before starting, clarify:

1. **Location Tracking:** Should location be denormalized to assets table? (Recommended: YES)
2. **Soft Deletes:** Should deleted records be archived or permanently removed? (Recommended: Archive)
3. **SLA Escalation:** Should system auto-escalate overdue tickets? (Recommended: YES)
4. **Notifications:** Email, SMS, in-app, or combination? (Recommendation: Email + in-app)
5. **Audit Trail:** How long to keep ticket history? (Recommended: 7 years for compliance)
6. **Role-Based Access:** Should technicians see only their assigned tickets? (Recommended: YES)

---

**Document Version:** 1.0  
**Last Updated:** October 30, 2025  
**Next Review:** After Phase 1 completion

