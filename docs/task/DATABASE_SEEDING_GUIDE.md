# 🌱 Database Seeding Guide

**Created:** January 2025  
**Purpose:** Complete guide for seeding the database with dummy data for development, staging, and demo environments

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [What Gets Seeded](#what-gets-seeded)
3. [Quick Start](#quick-start)
4. [Seeding Commands](#seeding-commands)
5. [Super Admin Users](#super-admin-users)
6. [Data Details](#data-details)
7. [Troubleshooting](#troubleshooting)
8. [Important Notes](#important-notes)

---

## 🎯 Overview

The database seeding system populates your application with **realistic dummy data** for:

- **Development Environment**: Local development with test data
- **Staging Environment**: Pre-production testing with realistic scenarios
- **Demo Environment**: Showcase features to stakeholders
- **Training Environment**: Let users practice without affecting production

### Two-Stage Seeding Process

1. **Core Data Seeding** (Required)
   - Roles and permissions
   - Ticket statuses, types, priorities
   - Asset statuses, types, warranty types
   - Manufacturers, locations
   - Initial system configuration

2. **Dummy Data Seeding** (Optional - NEW!)
   - Super admin users (daniel, idol, ridwan)
   - Test users with various roles
   - Sample assets, tickets, activities
   - Realistic relationships between entities

---

## 📊 What Gets Seeded

### Core System Data (Always Required)
```
✓ Roles: super-admin, management, admin, user
✓ Permissions: 50+ permissions for all modules
✓ Ticket Statuses: Open, In Progress, Closed, etc.
✓ Ticket Types: Incident, Request, Problem, etc.
✓ Ticket Priorities: Critical, High, Medium, Low
✓ Asset Statuses: Available, In Use, Maintenance, etc.
✓ Asset Types: Laptop, Desktop, Monitor, Printer, etc.
✓ Warranty Types: Standard, Extended, Premium
✓ Manufacturers: Dell, HP, Lenovo, etc.
✓ Initial Locations: Default locations
```

### Test Users (TestUsersTableSeeder)
```
🔐 Super Admins (3 real team members):
   • daniel@quty.co.id (Password: 123456)
   • idol@quty.co.id (Password: 123456)
   • ridwan@quty.co.id (Password: 123456)

🔐 Test Super Admin:
   • superadmin@quty.co.id (Password: superadmin)

👤 Test Management User:
   • management@quty.co.id (Password: 123456)

👤 Test Admin:
   • admin@quty.co.id (Password: admin)

👤 Test Regular User:
   • user@quty.co.id (Password: user)
```

### Dummy Data (DummyDataSeeder) - NEW!
```
📍 15 Locations (across multiple cities)
🏢 8 Divisions (various business units)
👥 20 Additional Users:
   • 5 Admin users
   • 15 Regular users
🏭 10 Manufacturers
📦 8 Suppliers
📜 5 Warranty Types
🔧 12 Asset Types
💻 25 Asset Models
📊 8 Asset Statuses
💼 100 Assets (assigned to users, various statuses)
🎫 50 Tickets (various priorities, statuses, types)
📝 30 Asset Requests
📅 100 Daily Activities (for calendar)
```

**Total Records Created:** ~450+ records across all tables

---

## 🚀 Quick Start

### Option 1: Fresh Start (⚠️ DELETES ALL DATA)

```bash
# Drop all tables, run migrations, and seed everything
php artisan migrate:fresh --seed
```

**⚠️ WARNING:** This command will:
- Drop ALL database tables
- Recreate all tables from migrations
- Seed all core data
- Create all test users
- Generate all dummy data

**Use this for:**
- Initial development setup
- Resetting to clean state
- When database structure changes

### Option 2: Seed Only (Keeps Existing Data)

```bash
# Add seeded data to existing database
php artisan db:seed
```

**✅ SAFE:** This command will:
- Keep existing data
- Add new seeded data
- May create duplicates if you run it multiple times

**Use this for:**
- Adding more test data
- Refreshing demo data
- After manual data cleanup

### Option 3: Seed Specific Seeder

```bash
# Seed only dummy data (assumes core data exists)
php artisan db:seed --class=Database\\Seeders\\DummyDataSeeder

# Seed only test users
php artisan db:seed --class=Database\\Seeders\\TestUsersTableSeeder

# Seed only roles
php artisan db:seed --class=Database\\Seeders\\RolesTableSeeder
```

**Use this for:**
- Testing specific seeders
- Adding specific data types
- Debugging seeder issues

---

## 🔐 Super Admin Users

### Real Team Members (Production Access)

Three team members have been added with super-admin privileges:

| Name | Email | Password | Access Level |
|------|-------|----------|--------------|
| Daniel | daniel@quty.co.id | `123456` | Super Admin |
| Idol | idol@quty.co.id | `123456` | Super Admin |
| Ridwan | ridwan@quty.co.id | `123456` | Super Admin |

**⚠️ IMPORTANT SECURITY NOTES:**

1. **Change Passwords Immediately**
   ```bash
   # After seeding, change all passwords through:
   # Profile > Change Password
   # Or use this command for each user:
   php artisan tinker
   >>> $user = User::where('email', 'daniel@quty.co.id')->first();
   >>> $user->password = bcrypt('your-secure-password');
   >>> $user->save();
   ```

2. **For Production Deployment:**
   - ❌ DO NOT use default passwords (`123456`)
   - ✅ Generate strong passwords (12+ characters, mixed case, numbers, symbols)
   - ✅ Enable two-factor authentication if available
   - ✅ Remove test users (superadmin@quty.co.id, etc.)

3. **For Development/Staging:**
   - ✅ Default passwords OK for local development
   - ✅ Default passwords OK for staging (if secured)
   - ⚠️ Document credentials for team access

### Super Admin Capabilities

Super admins can:
- ✅ Manage all users, roles, and permissions
- ✅ Access all tickets, assets, and locations
- ✅ View audit logs and system activity
- ✅ Configure system settings
- ✅ Delete any data
- ✅ Export/import data
- ✅ Access admin dashboard and reports

---

## 📦 Data Details

### Locations (15 Total)

Realistic locations across Indonesia:
- Jakarta Office (HQ)
- Bandung Branch
- Surabaya Branch
- Medan Office
- Semarang Branch
- And 10 more...

Each location has:
- Name, address, city
- Contact information
- Active status

### Assets (100 Total)

Diverse asset types:
- **Laptops** (30): Dell, HP, Lenovo models
- **Desktops** (25): Various configurations
- **Monitors** (20): 21"-27" displays
- **Printers** (15): Laser, inkjet, multifunction
- **Network Equipment** (10): Routers, switches

Asset statuses:
- 40% Available (ready to assign)
- 35% In Use (assigned to users)
- 15% Maintenance (being serviced)
- 5% Damaged (needs repair)
- 5% Retired (end of life)

Each asset includes:
- Unique asset tag
- Serial number
- Purchase date and price
- Warranty information
- Assignment history
- Location tracking

### Tickets (50 Total)

Realistic ticket scenarios:
- **Critical** (5): Server down, network outage
- **High** (10): System errors, urgent requests
- **Medium** (20): Standard requests, minor issues
- **Low** (15): Questions, feature requests

Ticket statuses:
- 30% Open (new tickets)
- 25% In Progress (being worked on)
- 20% Pending (waiting for info)
- 15% Resolved (fixed, awaiting confirmation)
- 10% Closed (completed)

Each ticket includes:
- Title and detailed description
- Priority and type
- Assigned technician
- Location and user information
- Created/updated timestamps

### Users (27 Total)

**Super Admins (4):**
- daniel@quty.co.id ⭐
- idol@quty.co.id ⭐
- ridwan@quty.co.id ⭐
- superadmin@quty.co.id (test account)

**Management (1):**
- management@quty.co.id

**Admins (6):**
- admin@quty.co.id (test account)
- Plus 5 generated admin users

**Regular Users (16):**
- user@quty.co.id (test account)
- Plus 15 generated regular users

Each user has:
- Name and email
- Encrypted password
- API token
- Role assignment
- Creation timestamp

### Asset Requests (30 Total)

Various request scenarios:
- New equipment requests
- Replacement requests
- Upgrade requests
- Bulk orders

Request statuses:
- 40% Pending approval
- 30% Approved, awaiting procurement
- 20% In progress
- 10% Completed

### Daily Activities (100 Total)

Calendar entries for:
- Maintenance schedules
- Site visits
- Training sessions
- Meetings
- Installation appointments

Distributed across:
- Past month (30%)
- Current month (40%)
- Next month (30%)

---

## 🐛 Troubleshooting

### Problem: "SQLSTATE[23000]: Integrity constraint violation"

**Cause:** Trying to create duplicate records (e.g., duplicate email, asset tag)

**Solutions:**

```bash
# Option 1: Fresh start (deletes all data)
php artisan migrate:fresh --seed

# Option 2: Clear specific table first
php artisan tinker
>>> DB::table('users')->delete();
>>> exit;
php artisan db:seed --class=Database\\Seeders\\TestUsersTableSeeder

# Option 3: Check for existing data
php artisan tinker
>>> User::where('email', 'daniel@quty.co.id')->first();
>>> // If exists, delete or update instead
```

### Problem: "Class 'Database\Seeders\DummyDataSeeder' not found"

**Cause:** Autoloader hasn't picked up the new seeder

**Solution:**

```bash
# Regenerate Composer autoload files
composer dump-autoload

# Then try seeding again
php artisan db:seed --class=Database\\Seeders\\DummyDataSeeder
```

### Problem: Seeder runs but creates no data

**Cause:** Required dependencies missing (e.g., roles not created)

**Solution:**

```bash
# Always run full seeding to ensure dependencies
php artisan migrate:fresh --seed

# Or seed in order:
php artisan db:seed --class=Database\\Seeders\\RolesTableSeeder
php artisan db:seed --class=Database\\Seeders\\TestUsersTableSeeder
php artisan db:seed --class=Database\\Seeders\\DummyDataSeeder
```

### Problem: "Call to undefined method [factory]"

**Cause:** Model doesn't have factory defined

**Solution:**

```bash
# Check if factory exists
ls database/factories/

# If missing, create it:
php artisan make:factory YourModelFactory --model=YourModel
```

### Problem: Seeding is very slow

**Cause:** Creating many records without disabling events/logging

**Solution:**

```bash
# Disable query logging during seeding
php artisan tinker
>>> DB::connection()->disableQueryLog();
>>> exit;

# Or add to seeder:
# DB::connection()->disableQueryLog();
# // ... seeding code ...
# DB::connection()->enableQueryLog();
```

### Problem: "Foreign key constraint fails"

**Cause:** Trying to create related records without parent records

**Solution:**

```bash
# Ensure correct seeding order:
# 1. Roles
# 2. Users
# 3. Locations, Divisions
# 4. Manufacturers, Suppliers
# 5. Asset Types, Models
# 6. Assets
# 7. Tickets
# 8. Asset Requests

# Run full seeding:
php artisan migrate:fresh --seed
```

---

## ⚠️ Important Notes

### For Development Environment

✅ **DO:**
- Use `migrate:fresh --seed` freely
- Use default passwords for convenience
- Create additional test data as needed
- Experiment with different scenarios

❌ **DON'T:**
- Use production database credentials
- Seed production-like sensitive data
- Commit `.env` with database credentials

### For Staging Environment

✅ **DO:**
- Use `migrate:fresh --seed` for clean state
- Change super admin passwords
- Keep test users for QA team
- Document seeded credentials for team

❌ **DON'T:**
- Use production data snapshots without anonymizing
- Share staging credentials publicly
- Leave default passwords indefinitely

### For Demo Environment

✅ **DO:**
- Use `migrate:fresh --seed` before demos
- Create realistic, impressive data
- Show variety of features
- Clean up between demos

❌ **DON'T:**
- Use real customer data
- Leave demo data in production
- Forget to reset between demos

### For Production Environment

❌ **NEVER:**
- Run `migrate:fresh` (deletes all data!)
- Seed dummy data
- Use test users
- Keep default passwords

✅ **INSTEAD:**
- Use migrations only (`php artisan migrate`)
- Create real users through registration/admin
- Import real data through proper channels
- Use strong passwords and 2FA

---

## 📈 Verification Commands

After seeding, verify your data:

```bash
# Check user counts
php artisan tinker
>>> User::count(); // Should be 27
>>> User::role('super-admin')->count(); // Should be 4

# Check asset counts
>>> Asset::count(); // Should be 100+
>>> Asset::where('status_id', 1)->count(); // Available assets

# Check ticket counts
>>> Ticket::count(); // Should be 50+
>>> Ticket::where('ticket_status_id', 1)->count(); // Open tickets

# Verify super admins exist
>>> User::whereIn('email', ['daniel@quty.co.id', 'idol@quty.co.id', 'ridwan@quty.co.id'])->get(['name', 'email']);

# Check locations
>>> Location::count(); // Should be 15+

# Check activities
>>> DailyActivity::count(); // Should be 100+
```

---

## 🎯 Next Steps

After successful seeding:

1. **Login as Super Admin**
   ```
   URL: http://your-app-url/login
   Email: daniel@quty.co.id
   Password: 123456
   ```

2. **Change Your Password**
   - Go to Profile > Change Password
   - Use strong password

3. **Explore Seeded Data**
   - Dashboard: See ticket statistics
   - Assets: Browse 100+ seeded assets
   - Tickets: Check 50+ sample tickets
   - Calendar: View 100+ activities
   - Reports: Generate KPI reports

4. **Customize Super Admin Accounts**
   - Update names if needed
   - Add profile photos
   - Set notification preferences
   - Configure email signatures

5. **Remove Test Accounts (Optional)**
   ```bash
   php artisan tinker
   >>> User::whereIn('email', ['superadmin@quty.co.id', 'admin@quty.co.id', 'user@quty.co.id'])->delete();
   ```

6. **Create Additional Users**
   - Use admin panel to create real users
   - Or import from CSV/LDAP

---

## 📞 Support

If you encounter issues with seeding:

1. **Check Laravel Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check Database Connectivity**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

3. **Verify Migrations Are Current**
   ```bash
   php artisan migrate:status
   ```

4. **Contact Development Team**
   - Email: dev@quty.co.id
   - Slack: #dev-support
   - GitHub Issues: [Your Repo URL]

---

**Document Version:** 1.0  
**Last Updated:** January 2025  
**Maintained By:** IT Department - QUTY

