# IT Quty - Advanced Asset Management System

Comprehensive IT Asset Management System built with Laravel Framework featuring modern architecture, role-based access control, and advanced management capabilities.

## 🚀 Features

### Core Management
- **Asset Management**: Complete lifecycle tracking with QR codes, maintenance scheduling, and assignment notifications
- **User Management**: Role-based access control with Spatie Laravel Permission
- **Ticket System**: Enhanced support ticket system with priorities, categories, and automated workflows
- **Reporting & Analytics**: Comprehensive reporting with filters and data visualization

### Advanced Capabilities
- **Service Layer Architecture**: Clean separation of business logic
- **Repository Pattern**: Optimized data access with caching
- **View Composers**: Centralized form data management
- **Local Scopes**: Reusable query patterns for consistent data retrieval
- **Form Request Validation**: Standardized input validation across the system
- **Email Notifications**: Automated notifications for assignments and maintenance

## 🛠 Technology Stack

- **Framework**: Laravel (with modern architecture patterns)
- **Database**: MySQL/SQLite
- **Authentication**: Laravel built-in + Spatie Laravel Permission
- **Frontend**: Blade templates with Bootstrap/AdminLTE
- **Queue System**: Laravel Queues for background processing
- **Email**: Laravel Mail with queue support

## 📋 Requirements

- PHP >= 8.0 (recommended 8.1+)
- MySQL 5.7+ or SQLite
- Composer 2.2+
- Node.js 14+ and NPM/Yarn (for asset compilation)

## 🔧 Installation

### Quick Setup (Development)
Follow these steps to get a local development instance running.

```bash
# Clone repository
git clone <repository-url>
cd Quty1

# Install PHP dependencies
composer install

# Install Node dependencies
npm ci
# or: yarn install

# Environment setup
cp .env.example .env
# Configure database and mail settings in .env

# Generate application key
php artisan key:generate

# Database setup
php artisan migrate --seed

# Publish Spatie permissions (if not published by seed)
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"

# Compile assets (watch during development)
npm run dev

# Start development server
php artisan serve --host=0.0.0.0 --port=8000
```

### Production Setup (Recommendations)
These steps assume a production-ready server with PHP-FPM + Nginx/Apache.

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run prod

# Environment configuration (set proper values in .env)

# Run migrations and seeders (careful in production)
php artisan migrate --force

# Build caches for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Run queue worker (supervisor/systemd recommended)
php artisan queue:work --sleep=3 --tries=3
```

## 👥 Default Users (Seeded)

The seeders create a set of test accounts for development. After running `php artisan db:seed` you'll find the following example accounts. Passwords in the development seed are `123456` unless otherwise noted in the seeder files.

- **Super Admins**: daniel@quty.co.id, idol@quty.co.id, ridwan@quty.co.id (password: 123456)
- **Other test users** are created by `DummyDataSeeder` (see `database/seeders` for details)

If you need to change seeded passwords, update `database/seeders/TestUsersTableSeeder.php` and re-run the seeder for development database only.

## 🏗 Architecture Overview

### Service Layer
```
app/Services/
├── UserService.php          # User management business logic
├── AssetService.php         # Asset operations and notifications
└── TicketService.php        # Ticket workflow management
```

### View Composers
```
app/Http/ViewComposers/
├── FormDataComposer.php     # Global form dropdowns
├── AssetFormComposer.php    # Asset-specific form data
└── TicketFormComposer.php   # Ticket form data
```

### Model Scopes
Enhanced query capabilities in models:
```php
// Asset queries
Asset::inStock()->unassigned()->withRelations()->get();
Asset::byDivision($divisionId)->needsMaintenance()->get();

// Ticket queries  
Ticket::overdue()->highPriority()->withRelations()->get();
Ticket::byStatus('open')->recentlyUpdated()->get();
```

## 🔐 Role-Based Access Control

### Available Roles
- **Super Admin**: Full system access
- **Admin**: Management access excluding user management
- **Manager**: Department-level access  
- **User**: Basic access to assigned resources

### Usage Examples
```php
// In controllers
use App\Traits\RoleBasedAccessTrait;

class AssetController extends Controller
{
    use RoleBasedAccessTrait;
    
    public function destroy($id)
    {
        $this->requireRole(['admin', 'super_admin']);
        // deletion logic
    }
}

// In Blade templates
@hasrole('admin')
    <button class="btn btn-danger">Delete</button>
@endhasrole
```

## 📧 Email Notifications

Automated notifications for:
- Asset assignments and returns
- Maintenance reminders
- Ticket status updates
- System alerts

Configure mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test types
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Generate coverage report
php artisan test --coverage
```

## 🚀 Deployment

### Using Docker
```bash
# Build and run with Docker Compose
docker-compose up -d

# Run migrations in container
docker-compose exec app php artisan migrate
```

### Manual Deployment
1. Upload files to server
2. Run `composer install --no-dev --optimize-autoloader`
3. Configure web server (Apache/Nginx)
4. Set proper file permissions
5. Configure environment variables
6. Run migrations and optimizations

## 📊 Performance Optimization

### Database
- Eager loading implemented in model scopes
- Indexed foreign keys and frequently queried columns
- Query optimization through repository pattern

### Caching
```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize
```

### Queue Processing
Set up queue workers for better performance:
```bash
# Start queue worker
php artisan queue:work

# Monitor queue status
php artisan queue:monitor
```

## 🔧 Development

### Adding New Features
1. Follow the Service Layer pattern
2. Use Form Requests for validation
3. Implement proper authorization
4. Add local scopes for reusable queries
5. Write tests for new functionality

See `DEVELOPMENT_CHECKLIST.md` for detailed guidelines.

### Code Quality
```bash
# Format code
php artisan ide-helper:generate
php artisan ide-helper:models

# Static analysis
vendor/bin/phpstan analyse
```

## 📚 Documentation

This repository includes a set of user-facing and technical documents in the `docs/` folder. Key documents:

- `docs/Admin_Documentation.md` - Admin manual and operational playbook
- `docs/API.md` - Key API endpoints and examples
- `docs/Deployment_Guide.md` - Production deployment checklist and tips
- `docs/CHANGELOG.md` - Project changelog and release notes

Other implementation and development docs are kept in the project root as before (see `IMPLEMENTATION_REPORT.md`, `DEVELOPMENT_CHECKLIST.md`, etc.).

## 🐛 Troubleshooting

### Common Issues
- **Permission errors**: Run `php artisan permission:cache-reset`
- **Class not found**: Run `composer dump-autoload`
- **View errors**: Check ViewComposer registrations
- **Performance issues**: Enable query logging and check for N+1 queries

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Follow coding standards in `DEVELOPMENT_CHECKLIST.md`
4. Write tests for new features
5. Commit changes (`git commit -m 'Add amazing feature'`)
6. Push to branch (`git push origin feature/amazing-feature`)
7. Open Pull Request

## 📄 License

This project is proprietary software. All rights reserved.

## 📞 Support

For technical support or questions:
- Check the troubleshooting guide
- Review implementation documentation
- Contact system administrator

---

**Version**: 2.1.0  
**Last Updated**: October 2025
**Maintained By**: D-Riz