# 🌱 Quick Seeding Reference Card

**For Development/Staging/Demo Environments Only**

---

## 🚀 Most Common Commands

### Fresh Start (⚠️ DELETES ALL DATA)
```bash
php artisan migrate:fresh --seed
```
Creates: Core data + Test users (daniel, idol, ridwan) + 350+ dummy records

### Add Dummy Data Only (Safe)
```bash
php artisan db:seed --class="Database\Seeders\DummyDataSeeder"
```
Adds: 15 locations, 8 divisions, 20 users, 100 assets, 50 tickets, 30 requests

---

## 👥 Super Admin Login Credentials

| Email | Password | Name |
|-------|----------|------|
| daniel@quty.co.id | `123456` | Daniel |
| idol@quty.co.id | `123456` | Idol |
| ridwan@quty.co.id | `123456` | Ridwan |

⚠️ **CHANGE PASSWORDS BEFORE PRODUCTION!**

---

## 📊 What Gets Created

### Core System Data (Required)
- Roles & Permissions
- Ticket/Asset Statuses, Types, Priorities
- Manufacturers, Warranty Types

### Test Users (7 Users)
- 4 Super Admins (daniel, idol, ridwan, superadmin)
- 1 Management User
- 1 Admin User
- 1 Regular User

### Dummy Data (350+ Records)
- 15 Locations
- 8 Divisions
- 20 Additional Users
- 100 Assets
- 50 Tickets
- 30 Asset Requests
- Supporting data (manufacturers, suppliers, types, models)

---

## ✅ Quick Verification

```bash
php artisan tinker

# Check totals
>>> User::count(); // Should be 27+
>>> Asset::count(); // Should be 100+
>>> Ticket::count(); // Should be 50+

# Verify super admins
>>> User::whereIn('email', ['daniel@quty.co.id', 'idol@quty.co.id', 'ridwan@quty.co.id'])->pluck('name');
```

---

## 🐛 Common Issues

### "Integrity constraint violation: 1062 Duplicate entry"
**Solution:** Run `php artisan migrate:fresh --seed` (starts clean)

### "Class not found"
**Solution:** Run `composer dump-autoload` then try again

### "Foreign key constraint fails"
**Solution:** Make sure you seed in correct order (use full `db:seed` command)

---

## 📚 Full Documentation

See: `task/DATABASE_SEEDING_GUIDE.md` (20+ pages)

---

**Quick Help:** For issues, check `storage/logs/laravel.log`
