# 🧹 Code Cleanup Checklist
**Project:** IT Asset Management System  
**Date Created:** October 15, 2025  
**Purpose:** Identify and remove duplicated/deprecated code during testing

---

## 📋 Overview

This document tracks all duplicated, deprecated, or redundant code discovered during the Master Task execution. As we test each feature, we'll identify cleanup opportunities and execute them systematically.

---

## 🎯 Cleanup Categories

### 1. Duplicate Routes
**Status:** 🔍 In Progress

#### Routes to Investigate:
- [ ] **Check for duplicate ticket routes**
  ```powershell
  # Search for duplicate route definitions
  php artisan route:list | Select-String "tickets" | Sort-Object
  ```

- [ ] **Check for duplicate asset routes**
  ```powershell
  php artisan route:list | Select-String "assets" | Sort-Object
  ```

- [ ] **Check for duplicate API routes**
  ```powershell
  php artisan route:list | Select-String "/api/" | Sort-Object
  ```

#### Found Duplicates:
| Route Pattern | File | Line | Action | Status |
|--------------|------|------|--------|--------|
| - | - | - | - | - |

---

### 2. Duplicate Controllers/Methods
**Status:** 🔍 In Progress

#### Controllers to Review:
- [ ] **AssetsController vs InventoryController**
  - Check if methods overlap
  - Identify which one should be primary
  - Merge or deprecate redundant methods

- [ ] **TicketController**
  - Check for duplicate methods
  - Look for unused methods

- [ ] **Check for duplicate controller files**
  ```powershell
  Get-ChildItem -Path "app\Http\Controllers" -Recurse -Filter "*.php" | 
    Group-Object Name | Where-Object { $_.Count -gt 1 }
  ```

#### Found Duplicates:
| Controller | Method | Issue | Action | Status |
|-----------|--------|-------|--------|--------|
| - | - | - | - | - |

---

### 3. Duplicate Views/Blades
**Status:** 🔍 In Progress

#### Views to Check:
- [ ] **Ticket views**
  ```powershell
  Get-ChildItem -Path "resources\views" -Recurse -Filter "*ticket*.blade.php"
  ```

- [ ] **Asset views**
  ```powershell
  Get-ChildItem -Path "resources\views" -Recurse -Filter "*asset*.blade.php"
  ```

- [ ] **Dashboard views**
  ```powershell
  Get-ChildItem -Path "resources\views" -Recurse -Filter "*dashboard*.blade.php"
  ```

- [ ] **Check for duplicate view names**
  ```powershell
  Get-ChildItem -Path "resources\views" -Recurse -Filter "*.blade.php" | 
    Group-Object Name | Where-Object { $_.Count -gt 1 }
  ```

#### Found Duplicates:
| View File | Location | Issue | Action | Status |
|-----------|----------|-------|--------|--------|
| - | - | - | - | - |

---

### 4. Duplicate Migrations
**Status:** 🔍 In Progress

#### Migrations to Review:
- [ ] **Check for duplicate table creations**
  ```powershell
  php artisan migrate:status
  Get-Content database\migrations\*.php | Select-String "create.*table"
  ```

- [ ] **Check for duplicate column additions**
  ```powershell
  Get-Content database\migrations\*.php | Select-String "table.*add"
  ```

- [ ] **Identify rollback migrations vs new migrations**

#### Found Duplicates:
| Migration | Date | Issue | Action | Status |
|-----------|------|-------|--------|--------|
| - | - | - | - | - |

---

### 5. Deprecated Code Patterns
**Status:** 🔍 In Progress

#### Laravel Deprecated Patterns:
- [ ] **Check for old authentication (pre-Laravel 8)**
  ```powershell
  Select-String -Path "app\Http\Controllers\*.php" -Pattern "Auth::attempt" -Recurse
  ```

- [ ] **Check for deprecated Entrust package usage**
  ```powershell
  Select-String -Path "app\*.php" -Pattern "Entrust|hasRole.*Entrust" -Recurse
  ```
  - ✅ Already migrated to Spatie, but verify removal

- [ ] **Check for deprecated helpers**
  ```powershell
  Select-String -Path "app\*.php" -Pattern "array_get|str_contains|starts_with|ends_with" -Recurse
  ```

- [ ] **Check for deprecated Eloquent methods**
  ```powershell
  Select-String -Path "app\*.php" -Pattern "->lists\(|->where.*null" -Recurse
  ```

#### Found Issues:
| File | Line | Issue | Fix | Status |
|------|------|-------|-----|--------|
| - | - | - | - | - |

---

### 6. Unused CSS/JS Files
**Status:** 🔍 In Progress

#### Frontend Assets to Review:
- [ ] **Check for unused CSS files**
  ```powershell
  Get-ChildItem -Path "public\css" -Filter "*.css"
  # Cross-reference with blade files
  ```

- [ ] **Check for unused JS files**
  ```powershell
  Get-ChildItem -Path "public\js" -Filter "*.js"
  # Cross-reference with blade files
  ```

- [ ] **Check for duplicate jQuery/Bootstrap versions**
  ```powershell
  Select-String -Path "resources\views\*.blade.php" -Pattern "jquery|bootstrap" -Recurse
  ```

#### Found Issues:
| File | Size | Last Used | Action | Status |
|------|------|-----------|--------|--------|
| - | - | - | - | - |

---

### 7. Duplicate Database Seeders
**Status:** 🔍 In Progress

#### Seeders to Check:
- [ ] **List all seeders**
  ```powershell
  Get-ChildItem -Path "database\seeders" -Filter "*.php"
  ```

- [ ] **Check for duplicate data seeding**
  - Users seeder
  - Roles seeder
  - Permissions seeder
  - Test data seeders

#### Found Duplicates:
| Seeder | Issue | Action | Status |
|--------|-------|--------|--------|
| - | - | - | - |

---

### 8. Unused Models/Traits
**Status:** 🔍 In Progress

#### Models to Review:
- [ ] **Check for unused models**
  ```powershell
  $models = Get-ChildItem -Path "app" -Filter "*.php" | Select-Object -ExpandProperty BaseName
  # Cross-reference with controllers/views
  ```

- [ ] **Check for unused traits**
  ```powershell
  Get-ChildItem -Path "app\Traits" -Filter "*.php"
  ```

#### Found Issues:
| Model/Trait | References | Action | Status |
|------------|-----------|--------|--------|
| - | - | - | - |

---

### 9. Code Smells & Anti-Patterns
**Status:** 🔍 In Progress

#### Patterns to Find:
- [ ] **Dead code (commented out blocks)**
  ```powershell
  Select-String -Path "app\*.php" -Pattern "^[\s]*//.*TODO|^[\s]*//.*FIXME|^[\s]*/\*" -Recurse
  ```

- [ ] **Debug statements left in code**
  ```powershell
  Select-String -Path "app\*.php" -Pattern "dd\(|dump\(|var_dump|print_r" -Recurse
  ```

- [ ] **Long methods (> 50 lines)**
  - Refactor into smaller methods

- [ ] **God classes (> 500 lines)**
  - Split into multiple classes

- [ ] **Duplicate code blocks**
  - Extract into reusable methods

#### Found Issues:
| File | Line | Issue | Action | Status |
|------|------|-------|--------|--------|
| - | - | - | - | - |

---

### 10. Configuration Duplicates
**Status:** 🔍 In Progress

#### Config Files to Review:
- [ ] **Check for duplicate config keys**
  ```powershell
  Get-ChildItem -Path "config" -Filter "*.php"
  ```

- [ ] **Check .env vs config/app.php**
  - Ensure no hardcoded values in config files

- [ ] **Check for unused config files**

#### Found Issues:
| Config File | Issue | Action | Status |
|------------|-------|--------|--------|
| - | - | - | - |

---

## 🔍 Automated Cleanup Commands

### Run These During Testing:

```powershell
# 1. Find duplicate route names
php artisan route:list --json | ConvertFrom-Json | Group-Object name | Where-Object { $_.Count -gt 1 }

# 2. Find unused views (views not referenced in controllers)
$views = Get-ChildItem -Path "resources\views" -Recurse -Filter "*.blade.php" | Select-Object -ExpandProperty Name
$controllers = Get-Content "app\Http\Controllers\*.php" -Raw
# Compare and identify unreferenced views

# 3. Find large files (potential refactoring candidates)
Get-ChildItem -Path "app" -Recurse -Filter "*.php" | 
  Where-Object { (Get-Content $_.FullName | Measure-Object -Line).Lines -gt 300 } | 
  Select-Object Name, @{Name="Lines"; Expression={(Get-Content $_.FullName | Measure-Object -Line).Lines}} | 
  Sort-Object Lines -Descending

# 4. Find TODO/FIXME comments
Select-String -Path "app\*.php" -Pattern "TODO|FIXME|HACK|XXX" -Recurse

# 5. Find duplicate method names across controllers
Get-ChildItem -Path "app\Http\Controllers" -Filter "*.php" | 
  ForEach-Object { Select-String -Path $_.FullName -Pattern "public function (\w+)" -AllMatches } | 
  Group-Object { $_.Matches[0].Groups[1].Value } | 
  Where-Object { $_.Count -gt 5 }

# 6. Check for unused use statements
# Requires PHP CS Fixer or similar tool

# 7. Find duplicate CSS classes
Get-Content "public\css\*.css" -Raw | Select-String -Pattern "\.[\w-]+\s*\{" -AllMatches

# 8. Find JavaScript errors/console.log
Select-String -Path "public\js\*.js" -Pattern "console\.|debugger" -Recurse

# 9. Check for SQL queries in controllers (should be in repositories)
Select-String -Path "app\Http\Controllers\*.php" -Pattern "DB::|->select\(|->where\(" -Recurse

# 10. Find hardcoded strings (should be in lang files)
Select-String -Path "resources\views\*.blade.php" -Pattern "Ticket|Asset|User" -Recurse | 
  Where-Object { $_ -notmatch "@lang|trans\(|__\(" }
```

---

## 📊 Cleanup Progress Tracker

| Category | Total Issues | Fixed | Remaining | Progress |
|----------|-------------|-------|-----------|----------|
| Duplicate Routes | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Duplicate Controllers | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Duplicate Views | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Duplicate Migrations | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Deprecated Patterns | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Unused Assets | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Duplicate Seeders | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Unused Models | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Code Smells | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| Config Duplicates | 0 | 0 | 0 | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 0% |
| **TOTAL** | **0** | **0** | **0** | ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ **0%** |

---

## 🚀 Cleanup Execution Plan

### Phase 1: Discovery (During Testing - Week 1-2)
- Run automated detection scripts
- Document all findings in this checklist
- Prioritize by severity:
  - 🔴 Critical: Breaking duplicates, security issues
  - 🟠 High: Performance impact, maintainability issues
  - 🟡 Medium: Code smells, minor duplicates
  - 🟢 Low: Cosmetic issues, comments

### Phase 2: Cleanup (Week 3)
- Fix critical issues immediately
- Create backup branch before cleanup
- Test after each cleanup
- Document all changes

### Phase 3: Validation (Week 4)
- Re-run test suite
- Verify no regressions
- Update documentation

---

## 🛡️ Safety Rules

1. **NEVER delete without backup**
   ```powershell
   git checkout -b cleanup-backup-$(Get-Date -Format "yyyyMMdd")
   git add -A
   git commit -m "Backup before cleanup"
   ```

2. **Test after every deletion**
   ```powershell
   php artisan test
   # Or manual smoke test
   ```

3. **Document why you're removing something**
   - Add to Git commit message
   - Update this checklist

4. **One category at a time**
   - Don't mix route cleanup with view cleanup
   - Easier to rollback if needed

5. **Ask before removing**
   - If unsure, mark as "Review Required"
   - Don't assume code is unused

---

## 📝 Cleanup Log

### 2025-10-15: Initial Setup
- ✅ Created cleanup checklist
- ✅ Defined cleanup categories
- ✅ Added automated detection scripts

### [Date]: [Category] Cleanup
- Issue: [Description]
- Files affected: [List]
- Action taken: [What was done]
- Test result: [Pass/Fail]

---

## 🔗 Integration with Master Task Plan

**Cleanup runs parallel to testing:**

1. While testing **Ticket Management** → Check for ticket-related duplicates
2. While testing **Asset Management** → Check for asset-related duplicates
3. While testing **Daily Activities** → Check for activity-related duplicates
4. And so on...

**Update Master Task Plan after each cleanup:**
- Mark cleanup tasks as completed
- Update progress percentages
- Document any issues found

---

## 📚 References

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [PHPStan Static Analysis](https://phpstan.org/)
- [Laravel Pint (Code Style)](https://laravel.com/docs/10.x/pint)

---

*Created: October 15, 2025*  
*Last Updated: October 15, 2025*  
*Version: 1.0*
