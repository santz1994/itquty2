# 🚀 Quick Reference - Database Improvements

**For:** Development & Operations Team  
**Updated:** October 30, 2025

---

## 🎯 What Changed?

### New Tables Created
1. **`purchase_orders`** - Track asset purchases and costs
2. **`ticket_assets`** - Many-to-many link between tickets and assets  
3. **`ticket_history`** - Immutable audit log of ticket changes

### Modified Tables
1. **`assets`** - Added `purchase_order_id` FK and unique constraint on `serial_number`
2. **`asset_requests`** - Added `request_number` auto-generated field

---

## 💻 For Developers

### New Model Relationships

```php
// Ticket Model
$ticket->assets()           // Many-to-many relationship
$ticket->history()          // Audit trail entries

// Asset Model  
$asset->tickets()           // Reverse relationship
$asset->purchaseOrder()     // Belongs to PO

// PurchaseOrder Model
$po->assets()               // Has many assets
$po->supplier()             // Belongs to supplier

// AssetRequest Model
$request->request_number    // Auto-generated (AR-2025-NNNN)
```

### Using Many-to-Many Ticket-Assets

```php
// Creating ticket with multiple assets
$ticket = Ticket::create([...]);
$ticket->assets()->attach([1, 2, 3]); // Attach 3 assets

// Updating ticket assets
$ticket->assets()->sync([2, 3, 4]); // Replace with new set

// In controllers (from form data)
$ticket->assets()->sync($request->input('asset_ids', []));
```

### Logging Ticket Changes

```php
use App\Listeners\TicketChangeLogger;

// Auto-logged on update (no manual action needed)
$ticket->update(['ticket_status_id' => 3]);
// History entry created automatically!

// Manual logging (optional)
TicketChangeLogger::logStatusChange($ticket, $oldStatus, $newStatus, 'User requested');
```

### TCO Calculation Example

```php
// Calculate Total Cost of Ownership
$asset = Asset::with(['purchaseOrder', 'tickets'])->find($id);

$purchaseCost = $asset->purchaseOrder->total_cost;
$supportTickets = $asset->tickets()->count();
$estimatedSupportCost = $supportTickets * 500000; // 500K per ticket

$tco = $purchaseCost + $estimatedSupportCost;
```

---

## 🎨 For Frontend Developers

### Multi-Asset Selection in Tickets

```html
<!-- Create/Edit Ticket Form -->
<select name="asset_ids[]" multiple class="form-control">
    @foreach($assets as $asset)
        <option value="{{ $asset->id }}" 
            {{ in_array($asset->id, old('asset_ids', $ticket->assets->pluck('id')->toArray())) ? 'selected' : '' }}>
            {{ $asset->asset_tag }} - {{ $asset->model_name }}
        </option>
    @endforeach
</select>

<script>
// Initialize Select2 for better UX
$('select[name="asset_ids[]"]').select2({
    placeholder: 'Select asset(s)',
    allowClear: true
});
</script>
```

### Display Purchase Order Info

```blade
<!-- Asset Detail View -->
@if($asset->purchaseOrder)
    <p>
        <strong>Purchase Order:</strong> 
        {{ $asset->purchaseOrder->po_number }}
        ({{ number_format($asset->purchaseOrder->total_cost) }} IDR)
    </p>
@endif
```

### Display Asset Request Number

```blade
<!-- Asset Request List -->
<td>{{ $request->request_number ?? 'N/A' }}</td>
<!-- Example: AR-2025-0042 -->
```

---

## 🗄️ For DBAs

### Important Constraints

```sql
-- Unique constraint on assets.serial_number
ALTER TABLE assets ADD UNIQUE INDEX assets_serial_number_unique (serial_number);

-- Foreign keys
ALTER TABLE assets 
    ADD FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE SET NULL;

ALTER TABLE ticket_assets 
    ADD FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    ADD FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE;
```

### Useful Queries

```sql
-- Find duplicate serial numbers (should return empty)
SELECT serial_number, COUNT(*) as count 
FROM assets 
WHERE serial_number IS NOT NULL 
GROUP BY serial_number 
HAVING count > 1;

-- Check ticket_assets pivot data
SELECT t.ticket_code, a.asset_tag 
FROM ticket_assets ta
JOIN tickets t ON ta.ticket_id = t.id
JOIN assets a ON ta.asset_id = a.id
LIMIT 10;

-- Verify request numbers generated
SELECT id, request_number 
FROM asset_requests 
ORDER BY id DESC 
LIMIT 10;

-- Check ticket history logging
SELECT th.*, t.ticket_code, u.name 
FROM ticket_history th
JOIN tickets t ON th.ticket_id = t.id
LEFT JOIN users u ON th.changed_by_user_id = u.id
ORDER BY th.changed_at DESC
LIMIT 20;
```

### Performance Indexes

All critical indexes are already created by migrations:
- `assets.serial_number` (unique)
- `assets.purchase_order_id`
- `ticket_assets.ticket_id`
- `ticket_assets.asset_id`
- `ticket_history.ticket_id, changed_at`
- `asset_requests.request_number`

---

## 🧪 For QA Team

### Test Scenarios

#### 1. Serial Number Uniqueness
- ✅ Create asset with serial "SN123"
- ❌ Try to create another asset with "SN123" (should fail with error)
- ✅ Update asset with unique serial

#### 2. Multi-Asset Tickets
- ✅ Create ticket, select 3 assets
- ✅ Verify all 3 appear in ticket detail
- ✅ Edit ticket, add 2 more assets
- ✅ Verify 5 assets total

#### 3. Ticket History Audit
- ✅ Create ticket with status "Open"
- ✅ Change status to "In Progress"
- ✅ Verify history entry created with old/new values
- ✅ Try to manually edit history record (should fail - immutable)

#### 4. Purchase Orders
- ✅ Create PO with supplier and cost
- ✅ Create asset linked to PO
- ✅ View asset detail, verify PO info displays
- ✅ Calculate TCO: PO cost + support tickets

#### 5. Asset Request Numbering
- ✅ Create asset request
- ✅ Verify request_number auto-generated (AR-YYYY-NNNN format)
- ✅ Create another request
- ✅ Verify sequential numbering

---

## 🚨 Troubleshooting

### Issue: "Duplicate serial number" error
**Cause:** Unique constraint now enforced  
**Solution:** Each asset must have unique serial, or leave blank

### Issue: Can't select multiple assets in ticket form
**Cause:** Browser cache or JS not loaded  
**Solution:** Hard refresh (Ctrl+F5), verify Select2 initialized

### Issue: Ticket history not logging
**Cause:** Model event not firing  
**Solution:** Check `Ticket::boot()` method has `static::updated()` handler

### Issue: Request number not generated
**Cause:** Model boot method not executing  
**Solution:** Verify `AssetRequest::boot()` has `static::creating()` handler

---

## 📚 Related Documentation

- **Full Implementation:** `docs/Perbaikan Database/IMPLEMENTATION_STATUS.md`
- **Executive Summary:** `docs/Perbaikan Database/EXECUTIVE_SUMMARY.md`
- **Task Completion:** `docs/Perbaikan Database/Task/db_and_forms_tasks.md`
- **Design Specs:** `docs/Perbaikan Database/` (Bab 1-6)

---

## 🔗 Quick Links

### Commands
```bash
# Check migration status
php artisan migrate:status

# Run pending migrations
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run tests
vendor/bin/phpunit --filter DatabaseImprovementsTest
```

### Database
```sql
-- Important tables to monitor
SELECT COUNT(*) FROM purchase_orders;     -- Should have PO data
SELECT COUNT(*) FROM ticket_assets;       -- Should have pivot data
SELECT COUNT(*) FROM ticket_history;      -- Should grow with ticket changes
```

---

## ✅ Production Checklist (Simplified)

Before deployment:
- [ ] Backup database
- [ ] Check for duplicate serials
- [ ] Review `IMPLEMENTATION_STATUS.md` Section 8

During deployment:
- [ ] `php artisan down`
- [ ] `git pull`
- [ ] `php artisan migrate --force`
- [ ] `php artisan cache:clear`
- [ ] `php artisan up`

After deployment:
- [ ] Test ticket creation with multiple assets
- [ ] Test asset creation (verify serial uniqueness)
- [ ] Monitor `storage/logs/laravel.log`

---

**Questions?** Check `IMPLEMENTATION_STATUS.md` or contact the dev team.

---

*Quick Reference v1.0 - October 30, 2025*
