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

- PHP >= 7.4 (recommended 8.0+)
- MySQL 5.7+ or SQLite
- Composer 2.0+
- Node.js & NPM (for asset compilation)

## 🔧 Installation

### Quick Setup
```bash
# Clone repository
git clone <repository-url>
cd Quty1

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
# Configure database and mail settings in .env

# Generate application key
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Install Spatie permissions
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Compile assets
npm run dev

# Start development server
php artisan serve
```

### Production Setup
```bash
# Additional production steps
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Queue worker (recommended for production)
php artisan queue:work --daemon
```

## 👥 Default Users

After seeding, you can login with:

- **Super Admin**: admin@example.com / password
- **Admin**: manager@example.com / password  
- **User**: user@example.com / password

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

- `IMPLEMENTATION_REPORT.md` - Detailed implementation documentation
- `DEVELOPMENT_CHECKLIST.md` - Development standards and guidelines
- `PERFORMANCE_SECURITY_IMPROVEMENTS.md` - Performance and security enhancements

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

**Version**: 2.0.0  
**Last Updated**: October 2025
**Maintained By**: IT Development Team