# Migration Fixes for SQLite Compatibility

## Issues Fixed

### Issue 1: Duplicate Index in media_table Migration ❌ → ✅

**Error:**
```
SQLSTATE[HY000]: General error: 1 index media_model_type_model_id_index already exists
```

**Root Cause:**
- `$table->morphs('model')` automatically creates an index on `model_type` and `model_id`
- Migration tried to create the same index again explicitly

**Fix Applied:**
```php
// Before (Broken)
$table->morphs('model');
// ... other columns ...
$table->index(['model_type', 'model_id']); // ❌ Duplicate!

// After (Fixed)
$table->morphs('model');
// ... other columns ...
// Note: morphs() already creates index ✅
```

**File:** `database/migrations/2025_10_15_100912_create_media_table.php`

---

### Issue 2: MySQL-Specific Index Checks in performance_indexes Migration ❌ → ✅

**Error:**
```
SQLSTATE[HY000]: General error: 1 near "SHOW": syntax error
```

**Root Cause:**
- Migration used `SHOW INDEX FROM` which is MySQL-specific
- SQLite doesn't support `SHOW INDEX` syntax
- CI/CD uses SQLite for testing

**Fix Applied:**
```php
// Before (MySQL only)
$indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");

// After (Multi-database support)
$driver = Schema::getConnection()->getDriverName();

if ($driver === 'sqlite') {
    // SQLite: Query sqlite_master table
    $exists = DB::select(
        "SELECT name FROM sqlite_master WHERE type='index' AND name=?",
        [$indexName]
    );
} else {
    // MySQL: Use SHOW INDEX
    $exists = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");
}
```

**File:** `database/migrations/2025_10_08_034126_add_performance_indexes_properly.php`

---

## Changes Made

### 1. database/migrations/2025_10_15_100912_create_media_table.php
- ✅ Removed duplicate index creation
- ✅ Added comment explaining morphs() creates the index
- ✅ Migration now works with SQLite and MySQL

### 2. database/migrations/2025_10_08_034126_add_performance_indexes_properly.php
- ✅ Added database driver detection
- ✅ SQLite uses `sqlite_master` table to check indexes
- ✅ MySQL continues to use `SHOW INDEX`
- ✅ Both `addIndexIfNotExists()` and `dropIndexIfExists()` updated
- ✅ Migration now works with SQLite and MySQL

---

## Testing

### Before Fix:
```bash
php artisan migrate --env=testing --force
# ❌ FAIL: General error: 1 index media_model_type_model_id_index already exists
```

### After Fix:
```bash
php artisan migrate --env=testing --force
# ✅ DONE: All migrations successful
```

---

## Database Compatibility

| Database | Status | Notes |
|----------|--------|-------|
| MySQL | ✅ Works | Uses SHOW INDEX syntax |
| SQLite | ✅ Works | Uses sqlite_master table |
| PostgreSQL | ⚠️ Untested | May need additional driver check |

---

## CI/CD Impact

These fixes ensure:
- ✅ GitHub Actions workflows can run migrations successfully
- ✅ SQLite testing database works correctly
- ✅ Local development (MySQL) continues to work
- ✅ No duplicate index errors
- ✅ No SQL syntax errors

---

## Migration Order

All migrations run successfully in order:
1. ✅ 2016-2025 migrations (all pass)
2. ✅ 2025_10_08_034126 - Performance indexes (now SQLite compatible)
3. ✅ 2025_10_15_100912 - Media table (no duplicate index)

---

## Local Testing

To test locally with SQLite:
```bash
# Create SQLite database
touch database/database.sqlite

# Set DB_CONNECTION in .env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Run migrations
php artisan migrate:fresh
```

To test with MySQL (production):
```bash
# Use MySQL in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database

# Run migrations
php artisan migrate:fresh
```

---

## Status

✅ **All migration issues fixed**
✅ **SQLite compatibility achieved**
✅ **MySQL compatibility maintained**
✅ **CI/CD workflows will now pass**

---

**Fixed:** October 16, 2025
**Files Modified:** 2
**Impact:** Critical (blocks CI/CD)
**Status:** ✅ RESOLVED
