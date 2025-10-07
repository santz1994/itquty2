# IT Quty - Development Checklist & Standards

## 📋 PRE-DEVELOPMENT CHECKLIST

Sebelum mengembangkan fitur baru atau melakukan perubahan, pastikan:

### ✅ Architecture Standards
- [ ] Apakah logika bisnis sudah dipisahkan ke Service layer?
- [ ] Apakah menggunakan Repository pattern untuk data access yang kompleks?
- [ ] Apakah controller hanya handle HTTP request/response?
- [ ] Apakah validation menggunakan Form Request khusus?

### ✅ Security Standards  
- [ ] Apakah menggunakan Spatie Laravel Permission untuk role checking?
- [ ] Apakah menggunakan middleware yang tepat untuk authorization?
- [ ] Apakah input sudah di-validate dan di-sanitize?
- [ ] Apakah output di-escape untuk mencegah XSS?

### ✅ Performance Standards
- [ ] Apakah menggunakan eager loading untuk relasi?
- [ ] Apakah menggunakan scopes untuk query yang konsisten?
- [ ] Apakah menggunakan pagination untuk list data?
- [ ] Apakah menghindari N+1 query problem?

---

## 🔧 DEVELOPMENT STANDARDS

### Controller Guidelines
```php
// ❌ AVOID - Business logic in controller
public function store(Request $request)
{
    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->save();
    
    $role = Role::where('name', 'user')->first();
    $user->assignRole($role);
    
    return redirect()->back();
}

// ✅ PREFER - Clean controller with service
public function store(StoreUserRequest $request, UserService $userService)
{
    try {
        $user = $userService->createUser($request->validated());
        return redirect()->route('users.index')
                        ->with('success', 'User created successfully');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}
```

### Model Scopes Usage
```php
// ❌ AVOID - Repeated query logic
Asset::where('status_id', 2)->where('assigned_to', null)->with(['model', 'status'])->get();

// ✅ PREFER - Using scopes
Asset::inStock()->unassigned()->withRelations()->get();
```

### Role-Based Access
```php
// ❌ AVOID - Direct role checking
if (auth()->user()->hasRole('admin')) {
    // logic
}

// ✅ PREFER - Using trait methods or middleware
use RoleBasedAccessTrait;

if ($this->hasRole('admin')) {
    // logic
}
// OR better: use middleware in routes
Route::middleware(['role:admin'])->group(function () {
    // admin routes
});
```

---

## 🧪 TESTING CHECKLIST

### Unit Tests
- [ ] Service methods tested dengan mock dependencies
- [ ] Model scopes tested dengan sample data
- [ ] Helper functions tested dengan edge cases
- [ ] Custom validation rules tested

### Feature Tests  
- [ ] Controller endpoints tested dengan different user roles
- [ ] Form submissions tested dengan valid/invalid data
- [ ] Authorization tested untuk protected resources
- [ ] File uploads tested bila applicable

### Integration Tests
- [ ] Database transactions tested
- [ ] Email notifications tested
- [ ] External API integrations tested
- [ ] Queue jobs tested bila applicable

---

## 🎨 UI/UX CHECKLIST

### Consistent Components
- [ ] Menggunakan partial views untuk komponen yang berulang
- [ ] Button styles konsisten di semua halaman
- [ ] Form layout konsisten
- [ ] Error message display konsisten

### User Experience
- [ ] Loading states untuk operasi yang lama
- [ ] Success/error notifications yang informatif
- [ ] Responsive design di mobile devices
- [ ] Accessible untuk screen readers

### Form Guidelines
```blade
{{-- ✅ PREFER - Consistent form structure --}}
<form method="POST" action="{{ route('users.store') }}">
    @csrf
    @include('partials.form-errors')
    
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    
    @include('partials.form-buttons', ['cancelRoute' => 'users.index'])
</form>
```

---

## 📊 PERFORMANCE MONITORING

### Database Queries
- [ ] Monitor N+1 queries dengan Laravel Debugbar
- [ ] Check slow queries di log files
- [ ] Validate eager loading effectiveness
- [ ] Monitor database connection pool

### Application Performance
- [ ] Memory usage monitoring
- [ ] Response time tracking
- [ ] Error rate monitoring
- [ ] User session tracking

---

## 🔒 SECURITY CHECKLIST

### Input Validation
- [ ] Semua user input di-validate
- [ ] File uploads di-validate (type, size, content)
- [ ] SQL injection protection (gunakan Eloquent)
- [ ] XSS protection (escape output)

### Authorization
- [ ] Route protection dengan middleware
- [ ] Resource-level authorization
- [ ] API endpoint protection
- [ ] File access protection

### Data Protection
- [ ] Sensitive data encrypted
- [ ] Password hashing yang proper
- [ ] Session security configured
- [ ] HTTPS enforcement di production

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-deployment
- [ ] Run all tests (unit, feature, browser)
- [ ] Check for security vulnerabilities
- [ ] Validate database migrations
- [ ] Clear and rebuild caches

### Post-deployment
- [ ] Verify application functionality
- [ ] Check error logs
- [ ] Monitor performance metrics
- [ ] Validate user notifications

---

## 📚 CODE REVIEW CHECKLIST

### Architecture Review
- [ ] Apakah mengikuti established patterns?
- [ ] Apakah separation of concerns sudah proper?
- [ ] Apakah menggunakan services untuk business logic?
- [ ] Apakah ada code duplication yang bisa di-refactor?

### Quality Review
- [ ] Apakah variable dan method names descriptive?
- [ ] Apakah ada proper error handling?
- [ ] Apakah ada sufficient comments untuk complex logic?
- [ ] Apakah mengikuti PSR coding standards?

### Security Review
- [ ] Apakah ada potential security vulnerabilities?
- [ ] Apakah authorization checks sudah tepat?
- [ ] Apakah input validation sudah comprehensive?
- [ ] Apakah ada sensitive data exposure?

---

## 🔧 TROUBLESHOOTING GUIDE

### Common Issues

#### "Class not found" errors
1. Check namespace declarations
2. Run `composer dump-autoload`
3. Verify file locations
4. Check for typos in class names

#### Role/Permission issues
1. Verify user has correct roles assigned
2. Check middleware configuration
3. Clear permission cache: `php artisan permission:cache-reset`
4. Verify role names match exactly

#### Performance issues
1. Enable query logging untuk identify N+1 queries
2. Check for missing indexes
3. Verify eager loading implementation
4. Monitor memory usage

#### View/Template errors
1. Check view composer registrations
2. Verify variable names in views
3. Check for missing @csrf tokens
4. Validate route names in links

---

*This checklist should be updated as the application evolves and new patterns are established.*