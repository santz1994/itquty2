# Test Execution Guide & Troubleshooting

**Document Date:** October 27, 2025  
**Status:** Comprehensive Guide ✅  
**Version:** 1.0

---

## 📋 Quick Reference

### Running Tests Locally

```bash
# Run all custom test commands
php artisan test:critical-fixes
php artisan test:database-columns
php artisan test:view-fixes
php artisan test:all-view-fixes

# Run PHPUnit Feature tests
php vendor/bin/phpunit --testsuite=Feature

# Run with verbose output
php vendor/bin/phpunit --testsuite=Feature --verbose

# Run with stop on first failure
php vendor/bin/phpunit --testsuite=Feature --stop-on-failure
```

---

## 🧪 Test Suite Overview

### 1. Custom Artisan Test Commands

These commands validate specific fixes and data integrity:

#### `test:critical-fixes` ✅
**Purpose:** Validates critical bug fixes  
**Tests:**
- ViewComposer registration in AppServiceProvider
- Logout route configuration (POST method only)
- User query optimization
- Database connection

**Run:** `php artisan test:critical-fixes`  
**Expected Output:** 4 checks, all ✅ PASSED

**Troubleshooting:**
- If ViewComposer fails: Check `app/Providers/AppServiceProvider.php`
- If logout fails: Check `routes/web.php` for logout route definition
- If database fails: Verify database connection in `.env`

---

#### `test:database-columns` ✅
**Purpose:** Validates all model columns and accessors  
**Tests:**
- Location model with `name` accessor
- AssetType model with `name` accessor
- TicketsStatus model with `name` accessor
- TicketsPriority model with `name` accessor
- TicketsType model with `name` accessor

**Run:** `php artisan test:database-columns`  
**Expected Output:** 5 checks, all ✅ PASSED

**Troubleshooting:**
- If a model fails: Check the model's accessor definition in `app/Models/`
- If "Unknown column" error: Run migrations: `php artisan migrate`
- If accessor fails: Verify `Attribute::make()` syntax in model

---

#### `test:view-fixes` ✅
**Purpose:** Validates view data availability for calendar and assets  
**Tests:**
- User data loaded for calendar view
- Asset statistics calculated
- Correct object properties

**Run:** `php artisan test:view-fixes`  
**Expected Output:** 2 checks, all ✅ PASSED

**Troubleshooting:**
- If user data fails: Check View Composers in `app/Http/View/Composers/`
- If statistics fail: Check AssetService in `app/Services/AssetService.php`

---

#### `test:all-view-fixes` ✅
**Purpose:** Comprehensive view data validation for tickets and assets  
**Tests:**
- Ticket show view dropdown data
- Ticket create view data
- Asset create view data
- Asset index statistics

**Run:** `php artisan test:all-view-fixes`  
**Expected Output:** 4 checks, all ✅ PASSED

**Troubleshooting:**
- If dropdown data fails: Check `app/Http/View/Composers/`
- If statistics fail: Check Asset model relationships
- If view titles fail: Check controller's `view()` method

---

### 2. PHPUnit Feature Tests

Comprehensive API and feature tests using Laravel's testing framework.

#### Test Coverage

```
Tests:      33 total
Assertions: 77+ total
Suites:     1 (Feature)
Runtime:    ~2-3 minutes
```

#### Test Categories

**Authentication Tests:**
- User login validation
- Permission checks
- Role-based access control

**Ticket Management Tests:**
- Create ticket
- Update ticket
- Manage ticket status
- Asset request workflow

**Asset Management Tests:**
- Asset import
- Asset status updates
- Asset statistics

**Dashboard Tests:**
- Dashboard page loading
- Statistics calculation
- Permission-based visibility

#### Running PHPUnit Tests

```bash
# Run all Feature tests
php vendor/bin/phpunit --testsuite=Feature

# Run with verbose output (recommended for debugging)
php vendor/bin/phpunit --testsuite=Feature --verbose

# Stop on first failure
php vendor/bin/phpunit --testsuite=Feature --stop-on-failure

# Run specific test file
php vendor/bin/phpunit tests/Feature/ApiAutomatedTest.php

# Run specific test method
php vendor/bin/phpunit tests/Feature/ApiAutomatedTest.php::test_02_can_create_ticket
```

---

## 🔧 Common Issues & Solutions

### Issue 1: "Unknown column" errors

**Symptom:** Error like "Unknown column 'status_id' in where clause"

**Cause:** Controller using wrong column name

**Solution:**
```bash
# First, check table schema
php artisan tinker
> Schema::getColumnListing('tickets')

# Then update controller to use correct column
# Example: Use 'ticket_status_id' instead of 'status_id'
```

**Files Often Affected:**
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/TicketController.php`
- `app/Http/Controllers/AssetsController.php`

---

### Issue 2: "Column not found" in tests

**Symptom:** `SQLSTATE[42S22]: Column not found`

**Cause:** Test database schema doesn't match expectations

**Solution:**
```bash
# Refresh test database
php artisan migrate:refresh --env=testing

# Or use the full reset with seeds
php artisan migrate:fresh --seed --env=testing
```

---

### Issue 3: "No tests executed"

**Symptom:** PHPUnit runs but shows "No tests executed!"

**Cause:** `phpunit.xml` testsuite configuration missing

**Solution:**
Verify `phpunit.xml` has:
```xml
<testsuites>
    <testsuite name="Feature">
        <directory suffix="Test.php">./tests/Feature</directory>
    </testsuite>
</testsuites>
```

---

### Issue 4: View Composer data not available

**Symptom:** Dropdown data missing in views, tests fail

**Cause:** View Composer not registered

**Solution:**
Check `app/Providers/AppServiceProvider.php`:
```php
// Verify this exists in boot() method
View::composer(['tickets.create', 'tickets.edit'], TicketFormComposer::class);
View::composer(['assets.create', 'assets.edit'], AssetFormComposer::class);
```

---

### Issue 5: Tests pass locally but fail in CI/CD

**Symptom:** Green locally, red in GitHub Actions

**Common Causes:**
- Different PHP version
- Different database (SQLite vs MySQL)
- Missing environment variables
- Timezone differences

**Solutions:**
```bash
# Test with same PHP version
php -v  # Check local version
# Update .github/workflows to use same PHP version

# Test with SQLite (what CI uses)
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test:all-view-fixes

# Check .env.testing
# Ensure all required variables are set
```

---

## 📊 Test Results Interpretation

### Successful Test Run

```
✅ Database Column Tests: PASSED
✅ Critical Fixes Tests: PASSED
✅ View Fixes Tests: PASSED
✅ All View Fixes Tests: PASSED
✅ API/Feature Tests: PASSED (33 tests, 77 assertions)

Time: 00:2:45.123, Memory: 88.00 MB
```

### What This Means
- ✅ Database schema is correct
- ✅ All critical bugs are fixed
- ✅ View data is properly loaded
- ✅ API endpoints are functional
- ✅ Permission system is working
- ✅ All relationships are intact

---

## 🚀 Running Tests in Different Environments

### Local Development

```bash
# Full test suite
php vendor/bin/phpunit --testsuite=Feature --stop-on-failure

# Quick validation
php artisan test:all-view-fixes

# Watch mode (if using alternative test runner)
php artisan test --watch
```

### GitHub Actions CI/CD

Tests run automatically on:
- Push to master, develop, staging
- Pull requests
- Daily schedule (2 AM UTC)
- Manual workflow dispatch

**View Results:** GitHub Actions tab → Workflow runs → Click run → Artifacts

### Staging/Production Pre-Deploy

```bash
# Before deployment, run
php artisan migrate
php artisan test:all-view-fixes
php vendor/bin/phpunit --testsuite=Feature

# If all pass, safe to deploy
```

---

## 📈 Performance Optimization

### Test Execution Time

| Suite | Time | Optimization |
|-------|------|--------------|
| Database Columns | ~5s | No optimization needed |
| Critical Fixes | ~3s | No optimization needed |
| View Fixes | ~8s | Cached data already |
| All View Fixes | ~12s | Cached data already |
| PHPUnit Feature | ~2-3m | Parallel jobs available |
| **Total** | **~4 min** | **Parallelizable** |

### Making Tests Faster

```yaml
# In .github/workflows/automated-tests.yml
# Use matrix for parallel execution
strategy:
  matrix:
    php-version: ['8.3']
    # Can add multiple versions for parallel execution
```

---

## ✅ Pre-Deployment Checklist

Before deploying to production:

- [ ] All local tests pass: `php vendor/bin/phpunit --testsuite=Feature`
- [ ] Database columns validated: `php artisan test:database-columns`
- [ ] Critical fixes verified: `php artisan test:critical-fixes`
- [ ] View data available: `php artisan test:all-view-fixes`
- [ ] Migrations applied: `php artisan migrate`
- [ ] No uncommitted changes: `git status`
- [ ] CI/CD workflow passed in GitHub
- [ ] Staging environment tested
- [ ] Rollback plan documented

---

## 📞 Getting Help

### If Tests Fail

1. **Read the error message carefully** - it often tells you exactly what's wrong
2. **Run locally first** - reproduces the issue in controlled environment
3. **Check recent changes** - review git diff for clues
4. **Check logs** - `storage/logs/laravel.log`
5. **Run with verbose** - `php vendor/bin/phpunit --testsuite=Feature --verbose`

### Common Places to Check

| Error Type | File to Check |
|-----------|-------------|
| Column not found | Migration files, Model definitions |
| View not found | Routes, Controller view paths |
| Class not found | Namespace in use statement |
| Permission denied | Middleware, Policy files |
| Database connection | .env file, database.php config |

---

## 📚 Related Documentation

- **WORKFLOW_ANALYSIS.md** - GitHub Actions workflow improvements
- **FINAL_STATUS.txt** - Project completion status
- **NEXT_STEPS_ACTION_PLAN.md** - Next phase guidance

---

**Document Created:** October 27, 2025  
**Last Updated:** October 27, 2025  
**Status:** ✅ COMPLETE & READY FOR USE
