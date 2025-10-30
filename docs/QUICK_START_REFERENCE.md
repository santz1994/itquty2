# QUICK START REFERENCE - ITQuty2 Fix & Implementation Guide

**For:** IT Fullstack Developer  
**Status:** Ready to Start Implementation  
**Last Updated:** October 30, 2025  

---

## 🎯 START HERE - IMMEDIATE ACTIONS

### Day 1: Verification & Planning

#### 1. READ FIRST (30 minutes):
- [ ] Read `COMPREHENSIVE_CODE_REVIEW.md` (entire file)
- [ ] Read `MASTER_TODO_LIST.md` (Sections 1-3: CRITICAL items)
- [ ] Review `docs/Perbaikan Database/` (all 6 design documents)
- [ ] Review `docs/Perbaikan Database/Task/db_and_forms_tasks.md`

#### 2. VERIFY RECENT CHANGES (30 minutes):
```bash
# Check if migrations from Oct 29 are applied
php artisan migrate:status

# Look for these migrations:
# - 2025_10_29_160000_add_unique_serial_to_assets.php ✓
# - 2025_10_29_150000_create_purchase_orders_table.php ✓
# - 2025_10_29_150500_add_purchase_order_id_to_assets.php ✓
# - 2025_10_29_151000_add_request_number_to_asset_requests.php ✓

# If not applied, run:
php artisan migrate

# Check database for duplicates
php artisan tinker
> DB::table('assets')->select('serial_number')->groupBy('serial_number')->havingRaw('count(*) > 1')->get();
> exit
```

#### 3. ENVIRONMENT CHECK (15 minutes):
```bash
# Verify all dependencies installed
composer install
npm install

# Check PHP/Laravel version
php -v
php artisan -v

# Verify database connection
php artisan tinker
> DB::connection()->getPdo();  # Should not error
> exit

# Check for any outstanding issues
php artisan config:cache
php artisan route:cache
```

---

## 🔴 CRITICAL PHASE (Phase 1) - THIS WEEK

**These MUST be completed before anything else.**

### Task 1: Verify Serial Number UNIQUE Constraint
**File:** `MASTER_TODO_LIST.md` Item #1  
**Time:** 30 minutes

```bash
# Check if unique constraint exists
mysql> SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
       WHERE TABLE_NAME = 'assets' AND CONSTRAINT_TYPE = 'UNIQUE';

# Expected output should include: assets_serial_number_unique

# If missing, the Oct 29 migration didn't run. Check:
php artisan migrate:status | grep "2025_10_29_160000"

# If showing down, run:
php artisan migrate --path=database/migrations/2025_10_29_160000_add_unique_serial_to_assets.php

# Test it works:
php artisan tinker
> Asset::create(['serial_number' => 'TEST_UNIQUE', 'model_id' => 1, ...]);
> Asset::create(['serial_number' => 'TEST_UNIQUE', 'model_id' => 1, ...]);  // Should error!
> exit
```

✅ **Done when:** Duplicate serials are rejected with database error.

---

### Task 2: Verify Purchase Orders Implementation
**File:** `MASTER_TODO_LIST.md` Item #2  
**Time:** 45 minutes

```bash
# Check table exists
php artisan tinker
> Schema::hasTable('purchase_orders');  # Should return true
> Schema::getColumns('purchase_orders');  # Review columns
> exit

# Check FK relationship works
# Open browser to: /assets/create
# Verify "Purchase Order" dropdown appears

# Test storing with purchase order
php artisan tinker
> $po = PurchaseOrder::first();
> $asset = Asset::factory()->create(['purchase_order_id' => $po->id]);
> $asset->purchaseOrder()->exists();  # Should be true
> exit
```

✅ **Done when:** Asset can be created/edited with purchase order association.

---

### Task 3: Create ticket_assets Pivot Table
**File:** `MASTER_TODO_LIST.md` Item #3  
**Time:** 4-5 hours (Critical - affects SLA tracking)

**Create Migration File:**
```bash
php artisan make:migration create_ticket_assets_table
```

**Edit:** `database/migrations/2025_10_XX_XXXXXX_create_ticket_assets_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('asset_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['ticket_id', 'asset_id']);
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->index('asset_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ticket_assets');
    }
};
```

**Migrate:**
```bash
php artisan migrate
```

**Update Models:**

*`app/Ticket.php` - Add relationship:*
```php
public function assets()
{
    return $this->belongsToMany(Asset::class, 'ticket_assets');
}
```

*`app/Asset.php` - Add relationship:*
```php
public function tickets()
{
    return $this->belongsToMany(Ticket::class, 'ticket_assets');
}
```

**Create Data Migration:**
```bash
php artisan make:migration migrate_ticket_asset_data
```

**Edit migration to backfill data:**
```php
// In up():
DB::table('ticket_assets')->insertOrIgnore(
    DB::table('tickets')
        ->whereNotNull('asset_id')
        ->select(['id as ticket_id', 'asset_id', DB::raw('NOW() as created_at'), DB::raw('NOW() as updated_at')])
        ->get()
        ->toArray()
);

// In down():
DB::table('ticket_assets')->delete();
```

**Test:**
```bash
php artisan migrate
php artisan tinker
> $ticket = Ticket::first();
> $ticket->assets()->attach(Asset::first());  // Should work
> $ticket->assets;  # Should show attached assets
> exit
```

✅ **Done when:** Tickets can have multiple assets; pivot table populated.

---

### Task 4: Create ticket_history Audit Log
**File:** `MASTER_TODO_LIST.md` Item #4  
**Time:** 5-6 hours (Critical - compliance)

**Create Migration:**
```bash
php artisan make:migration create_ticket_history_table
```

**Edit:** `database/migrations/2025_10_XX_XXXXXX_create_ticket_history_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('field_changed');
            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();
            $table->unsignedBigInteger('changed_by_user_id');
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
            
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('changed_by_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->index(['ticket_id', 'changed_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('ticket_history');
    }
};
```

**Create Model:**
```bash
php artisan make:model TicketHistory
```

**Edit:** `app/TicketHistory.php`
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    protected $fillable = [
        'ticket_id', 'field_changed', 'old_value', 'new_value', 'changed_by_user_id', 'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
```

**Add Observer:**
```bash
php artisan make:observer TicketObserver
```

**Edit:** `app/Observers/TicketObserver.php`
```php
<?php

namespace App\Observers;

use App\Ticket;
use App\TicketHistory;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    public function updating(Ticket $ticket)
    {
        if (!Auth::check()) return;

        $changes = $ticket->getDirty();
        
        foreach ($changes as $field => $newValue) {
            $oldValue = $ticket->getOriginal($field);
            
            // Skip timestamp fields
            if (in_array($field, ['created_at', 'updated_at'])) continue;
            
            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'field_changed' => $field,
                'old_value' => json_encode($oldValue),
                'new_value' => json_encode($newValue),
                'changed_by_user_id' => Auth::id(),
                'changed_at' => now(),
            ]);
        }
    }
}
```

**Register Observer in:** `app/Providers/AppServiceProvider.php`
```php
use App\Ticket;
use App\Observers\TicketObserver;

public function boot()
{
    Ticket::observe(TicketObserver::class);
}
```

**Test:**
```bash
php artisan migrate
php artisan tinker
> $ticket = Ticket::first();
> $ticket->update(['ticket_status_id' => 2]);
> TicketHistory::where('ticket_id', $ticket->id)->get();  # Should show change
> exit
```

✅ **Done when:** Every ticket change creates audit record.

---

### Task 5: Fix Serial Number Validation
**File:** `MASTER_TODO_LIST.md` Item #5  
**Time:** 2-3 hours

**Edit:** `app/Http/Requests/StoreAssetRequest.php`

Find the serial_number rule and change it to:
```php
use Illuminate\Validation\Rule;

'serial_number' => [
    'nullable',
    'string',
    'max:255',
    Rule::unique('assets', 'serial_number')
        ->ignore($this->route('asset')?->id)
        ->whereNotNull('serial_number')  // ← KEY FIX for NULLs
],
```

**Add AJAX Validation Script:**

Create: `resources/assets/js/serial-validator.js`
```javascript
document.addEventListener('DOMContentLoaded', function() {
    const serialInput = document.getElementById('serial_number');
    if (!serialInput) return;

    serialInput.addEventListener('blur', async function() {
        const serial = this.value.trim();
        if (!serial) return;

        const assetId = document.querySelector('input[name="asset_id"]')?.value;
        const url = `/api/assets/check-serial?serial=${encodeURIComponent(serial)}` + 
                    (assetId ? `&exclude_id=${assetId}` : '');

        try {
            const response = await fetch(url);
            const data = await response.json();

            const feedbackEl = document.getElementById('serial-feedback');
            if (data.exists) {
                feedbackEl.textContent = '⚠️ Serial number already exists!';
                feedbackEl.className = 'text-danger small';
            } else {
                feedbackEl.textContent = '✓ Serial number is unique';
                feedbackEl.className = 'text-success small';
            }
        } catch (error) {
            console.error('Validation error:', error);
        }
    });
});
```

**Update View:** `resources/views/assets/create.blade.php`

Add feedback element next to serial input:
```blade
<div class="form-group">
    <label for="serial_number">Serial Number</label>
    <input type="text" class="form-control" id="serial_number" name="serial_number" 
           placeholder="Manufacturer serial" maxlength="100">
    <small id="serial-feedback" class="form-text text-muted">Checking...</small>
    @error('serial_number')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

@section('scripts')
    <script src="{{ asset('assets/js/serial-validator.js') }}"></script>
@endsection
```

**Test:**
```bash
php artisan serve
# Visit http://localhost:8000/assets/create
# Enter a serial number, hit Tab
# Verify AJAX feedback appears
```

✅ **Done when:** Serial uniqueness checked both client and server side.

---

### Task 6: Fix Foreign Key Constraints
**File:** `MASTER_TODO_LIST.md` Item #6  
**Time:** 2-3 hours

**First, audit current constraints:**
```bash
php artisan tinker
> $constraints = DB::select("
    SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'assets' 
    AND REFERENCED_TABLE_NAME IS NOT NULL
  ");
> foreach ($constraints as $c) {
    echo "{$c->COLUMN_NAME} -> {$c->REFERENCED_TABLE_NAME}\n";
  }
> exit
```

**Create Migration to Fix:**
```bash
php artisan make:migration fix_asset_foreign_key_constraints
```

**Edit Migration:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('assets', function (Blueprint $table) {
            // Drop existing FKs (adjust names based on your audit)
            try {
                $table->dropForeign(['assigned_to']);
            } catch (\Exception $e) {}
            
            // Re-add with correct rules
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
            
            // Similar for other FKs - use RESTRICT for important relationships
        });
    }

    public function down(): void {
        // Reverse changes
    }
};
```

**Verify:**
```bash
php artisan migrate
php artisan tinker
> $user = User::first();
> // Try to delete: should fail if assets assigned to this user
> exit
```

✅ **Done when:** FK constraints enforce business rules correctly.

---

## 🟠 NEXT PHASE (Phase 2) - If Phase 1 Complete

**After completing Phase 1, proceed with:**
1. Model relationships (Items #7-9)
2. Form validation (Items #10-11)
3. View cleanup (Items #12-15)

**See:** `MASTER_TODO_LIST.md` for detailed specifications.

---

## 📋 COMMANDS REFERENCE

```bash
# Database
php artisan migrate                          # Run all pending migrations
php artisan migrate:rollback               # Undo last migration batch
php artisan migrate:status                 # Check which migrations ran
php artisan tinker                         # Interactive PHP shell

# Testing
php artisan test                           # Run all tests
php artisan test --filter AssetTest        # Run specific test
php artisan test --coverage                # With coverage report

# Code
php artisan cache:clear                    # Clear application cache
php artisan config:cache                   # Cache configuration
php artisan route:cache                    # Cache routes
php artisan make:model ModelName -m        # Create model with migration
php artisan make:migration migration_name  # Create migration
php artisan make:controller ControllerName # Create controller
php artisan make:observer ObserverName     # Create observer

# Server
php artisan serve                          # Start dev server (localhost:8000)
```

---

## 🔗 IMPORTANT FILES

| Path | Purpose | Status |
|------|---------|--------|
| `COMPREHENSIVE_CODE_REVIEW.md` | Detailed analysis of current state | ✅ Done |
| `MASTER_TODO_LIST.md` | 48 prioritized tasks with specs | ✅ Done |
| `docs/Perbaikan Database/` | 6 design specification docs | ✅ Reference |
| `app/Asset.php` | Asset model (needs relationships) | ⚠️ Update |
| `app/Ticket.php` | Ticket model (needs relationships) | ⚠️ Update |
| `app/Http/Controllers/AssetsController.php` | Asset CRUD | ⚠️ Verify |
| `app/Http/Requests/StoreAssetRequest.php` | Asset validation | ⚠️ Fix |
| `resources/views/assets/create.blade.php` | Asset create form | ⚠️ Clean |
| `resources/views/assets/edit.blade.php` | Asset edit form | ⚠️ Clean |

---

## ✅ SUCCESS CHECKLIST - Phase 1

- [ ] All Oct 29 migrations verified applied
- [ ] Serial number UNIQUE constraint working
- [ ] Purchase orders table & relationship verified
- [ ] ticket_assets pivot table created & populated
- [ ] ticket_history audit log implemented
- [ ] Serial validation fixed (server + client)
- [ ] FK constraints verified/fixed
- [ ] All new tests passing
- [ ] No errors in logs
- [ ] Data integrity verified (SHOW TABLE STATUS)

---

## 🆘 TROUBLESHOOTING

### Migration Won't Run
```bash
# Check table doesn't exist
SELECT TABLES FROM information_schema.TABLES WHERE TABLE_NAME = 'table_name';

# Mark as run manually (last resort)
php artisan migrate:refresh --step=1

# Or delete and re-run
php artisan migrate:rollback
php artisan migrate
```

### Foreign Key Error: 1452
Trying to insert FK reference that doesn't exist in parent table.
```bash
# Check if referenced record exists
SELECT * FROM referenced_table WHERE id = X;
```

### Unique Constraint Error: 1062
Duplicate value in unique column.
```bash
# Find duplicates
SELECT serial_number, COUNT(*) FROM assets GROUP BY serial_number HAVING COUNT(*) > 1;

# Merge or delete duplicates before re-running migration
```

### Test Failures
```bash
# Run with verbose output
php artisan test --verbose

# Run single test
php artisan test tests/Feature/AssetTest.php::test_can_create_asset
```

---

## 📞 NEED HELP?

Before asking for help, provide:
1. **What you're trying to do** (e.g., "Create ticket_assets pivot")
2. **What error you got** (paste full error message)
3. **Commands you ran** (in order)
4. **Expected vs actual behavior**

Example:
> "When running `php artisan migrate`, I get 'Unique constraint failed: assets.serial_number'. I've already checked for duplicates and found none. Running PHP 8.2, Laravel 10.x"

---

**Document Version:** 1.0  
**Created:** October 30, 2025  
**Status:** Ready to Execute  

**Next:** Start with Task 1 (Verify Serial UNIQUE) - 30 minutes!

