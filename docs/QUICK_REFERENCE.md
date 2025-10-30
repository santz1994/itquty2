# QUICK REFERENCE GUIDE - CONFLICT RESOLUTION SYSTEM

## 🚀 Quick Start

### Access the System
```
URL: http://yourdomain.com/imports/{import_id}/conflicts
API: http://yourdomain.com/api/imports/{import_id}/conflicts
```

### Permissions Required
- Role: `admin` or `super-admin`
- Action: `view conflicts`, `resolve conflicts`

---

## 📋 Common Tasks

### 1. View All Conflicts
```
GET /imports/{import_id}/conflicts
```
Returns: Dashboard with statistics and conflict list

### 2. View Specific Conflict
```
GET /imports/{import_id}/conflicts/{conflict_id}
```
Returns: Conflict detail page with resolution options

### 3. Resolve Single Conflict
```
POST /imports/{import_id}/conflicts/{conflict_id}/resolve
Body: {
    "choice": "skip|create_new|update_existing|merge",
    "notes": "Optional resolution notes"
}
```

### 4. Resolve Multiple Conflicts
```
POST /imports/{import_id}/conflicts/bulk-resolve
Body: {
    "conflict_ids": [1, 2, 3],
    "choice": "skip|create_new|update_existing|merge"
}
```

### 5. Auto-Resolve All Conflicts
```
POST /imports/{import_id}/conflicts/auto-resolve
Body: {
    "strategy": "skip|update"
}
```

### 6. View Resolution History
```
GET /imports/{import_id}/conflicts/history
```
Returns: Timeline of all resolutions

### 7. Export Report
```
POST /imports/{import_id}/conflicts/export
```
Returns: CSV file with conflict report

### 8. Rollback Resolutions
```
POST /imports/{import_id}/conflicts/rollback
```
Returns: All conflicts marked as unresolved

---

## 🎨 UI Elements

### Statistics Cards
- **Total Conflicts:** Count of all conflicts
- **Unresolved:** Count of unresolved conflicts
- **Resolved:** Count of resolved conflicts
- **Resolution Rate:** Percentage of resolved conflicts

### Conflict Types (Color-Coded)
- 🔴 **Duplicate Key** - Same unique identifier
- 🟠 **Duplicate Record** - Same data found multiple times
- 🔵 **Foreign Key Not Found** - Referenced record missing
- 🟣 **Invalid Data** - Fails validation rules
- 🔴 **Business Rule Violation** - Violates business logic

### Resolution Options
- **Skip:** Don't import this record
- **Create New:** Create new record instead of update
- **Update:** Update existing record with new data
- **Merge:** Combine new data with existing record

---

## 🔧 API Endpoints

### List Conflicts
```bash
curl -X GET http://yourdomain.com/api/imports/1/conflicts \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Conflict Detail
```bash
curl -X GET http://yourdomain.com/api/imports/1/conflicts/5 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Resolve Conflict
```bash
curl -X POST http://yourdomain.com/api/imports/1/conflicts/5/resolve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "choice": "skip",
    "notes": "Skipping duplicate"
  }'
```

### Bulk Resolve
```bash
curl -X POST http://yourdomain.com/api/imports/1/conflicts/bulk-resolve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "conflict_ids": [1, 2, 3],
    "choice": "update"
  }'
```

### Auto-Resolve
```bash
curl -X POST http://yourdomain.com/api/imports/1/conflicts/auto-resolve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "strategy": "skip"
  }'
```

### Get History
```bash
curl -X GET http://yourdomain.com/api/imports/1/conflicts/history \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Export Report
```bash
curl -X POST http://yourdomain.com/api/imports/1/conflicts/export \
  -H "Authorization: Bearer YOUR_TOKEN" \
  --output report.csv
```

### Rollback
```bash
curl -X POST http://yourdomain.com/api/imports/1/conflicts/rollback \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 💾 Database Queries

### Get All Conflicts for Import
```sql
SELECT * FROM import_conflicts 
WHERE import_id = 1 
AND conflict_date > DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### Get Unresolved Conflicts
```sql
SELECT * FROM import_conflicts 
WHERE import_id = 1 
AND id NOT IN (SELECT import_conflict_id FROM resolution_choices);
```

### Get Resolution History
```sql
SELECT rc.*, u.name as user_name, ic.conflict_type
FROM resolution_choices rc
JOIN users u ON rc.user_id = u.id
JOIN import_conflicts ic ON rc.import_conflict_id = ic.id
WHERE ic.import_id = 1
ORDER BY rc.created_at DESC;
```

### Get Conflict Statistics
```sql
SELECT 
  COUNT(*) as total,
  SUM(CASE WHEN id NOT IN (SELECT import_conflict_id FROM resolution_choices) THEN 1 ELSE 0 END) as unresolved,
  COUNT(DISTINCT conflict_type) as types,
  GROUP_CONCAT(DISTINCT conflict_type) as conflict_types
FROM import_conflicts
WHERE import_id = 1;
```

---

## 🔐 Security Notes

### Authentication
- API: Sanctum token required
- Web: Session-based authentication
- Both: Role-based (admin|super-admin)

### CSRF Protection
- All web forms include `@csrf` token
- API uses Bearer token instead

### Input Validation
- All inputs validated via Request classes
- Conflict choice must be one of: skip, create_new, update_existing, merge
- Notes limited to 1000 characters

### Authorization
- All endpoints check admin role
- Policy enforces owner verification

---

## 📊 Response Formats

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    "conflict_id": 1,
    "status": "resolved",
    "resolution": "skip"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Operation failed",
  "errors": {
    "choice": "The choice field is required"
  }
}
```

### List Response
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "conflict_type": "duplicate_key",
      "row_number": 42,
      "resolved": false
    }
  ],
  "meta": {
    "total": 10,
    "page": 1,
    "per_page": 15
  }
}
```

---

## ⚙️ Configuration

### Environment Variables
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=itquty
DB_USERNAME=root
DB_PASSWORD=

# Optional: Conflict resolution specific
CONFLICTS_AUTO_RESOLVE=true
CONFLICTS_BATCH_SIZE=100
```

### Cache Configuration
```php
// config/cache.php
'default' => 'redis',
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
    ]
]
```

---

## 🐛 Troubleshooting

### Issue: "Unauthorized" Error
**Solution:** Check that user has admin role
```php
$user->roles()->pluck('name'); // Should contain 'admin' or 'super-admin'
```

### Issue: "Not Found" Error
**Solution:** Verify import_id and conflict_id exist
```php
Import::find($importId); // Should not be null
ImportConflict::find($conflictId); // Should not be null
```

### Issue: "Validation Failed" Error
**Solution:** Check request parameters match expected format
```php
// Required: choice must be one of:
['skip', 'create_new', 'update_existing', 'merge']
```

### Issue: "Database Constraint Error"
**Solution:** Check foreign keys exist
```sql
SELECT * FROM imports WHERE id = 1;
SELECT * FROM import_conflicts WHERE import_id = 1;
```

### Issue: Slow Response Time
**Solution:** Check database indexes
```php
// Run migrations to create indexes
php artisan migrate
```

---

## 📈 Monitoring

### Check System Health
```php
// Service verification
$service = app(ConflictResolutionService::class);
$stats = $service->getStatistics($importId);
echo "Total Conflicts: " . $stats['total'];
```

### Monitor Performance
```
Artisan Command: php artisan tinker
>>> \App\Services\ConflictResolutionService::class
>>> resolve() execution time
>>> database query count
```

### Log Monitoring
```bash
tail -f storage/logs/laravel.log | grep -i conflict
```

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `PHASE_3_7_SUMMARY.md` | Backend implementation |
| `PHASE_3_8_COMPLETION.md` | UI implementation |
| `CONFLICT_RESOLUTION_API_GUIDE.md` | API documentation |
| `IMPLEMENTATION_CHECKLIST.md` | Complete checklist |
| `COMPLETE_SYSTEM_SUMMARY.md` | System overview |
| `QUICK_REFERENCE.md` | This file |

---

## 🔗 Related Resources

### Models
- `app/Models/Import.php`
- `app/Models/ImportConflict.php`
- `app/Models/ResolutionChoice.php`

### Services
- `app/Services/ConflictResolutionService.php`

### Controllers
- `app/Http/Controllers/ConflictResolutionController.php`
- `app/Http/Controllers/API/ConflictResolutionController.php`

### Views
- `resources/views/imports/conflicts/index.blade.php`
- `resources/views/imports/conflicts/show.blade.php`
- `resources/views/imports/conflicts/history.blade.php`

### Routes
- `routes/web.php` (web routes)
- `routes/api.php` (API routes)

---

## 🚀 Deployment Checklist

Before deploying to production:

- [ ] Run migrations: `php artisan migrate`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Run optimizer: `php artisan optimize`
- [ ] Test endpoints
- [ ] Test authorization
- [ ] Monitor logs
- [ ] Verify database

---

## 💬 Support

### For Issues:
1. Check this Quick Reference
2. Review IMPLEMENTATION_CHECKLIST.md
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify database migrations: `php artisan migrate:status`
5. Test endpoints with Postman

### Common Commands:
```bash
# Clear everything
php artisan cache:clear && php artisan view:cache && php artisan route:cache

# Fresh start
php artisan migrate:refresh
php artisan db:seed

# Debug
php artisan tinker

# List routes
php artisan route:list | grep conflict

# Test
php artisan test
```

---

**Last Updated:** October 30, 2025
**System:** Conflict Resolution for ITQuty
**Status:** ✅ Production Ready
