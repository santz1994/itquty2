<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Bridge for legacy apps: if the old app/Http/routes.php exists, load it
// so older route definitions (from Laravel 5.x era) are picked up.
$legacy = base_path('app/Http/routes.php');
if (file_exists($legacy)) {
    require $legacy;
} else {
    // fallback: no legacy routes found — define a minimal home route so
    // that the application URL works during tests. Explicit auth routes
    // (login/register) are not required for the test-suite since tests
    // use actingAs.
    Route::get('/', function () {
        if (Auth::check()) {
            // Redirect users based on their role
            $user = Auth::user();
            if ($user->hasRole('user')) {
                return redirect('/tickets');
            }
            return redirect('/home');
        }
        return redirect('/login');
    });
}

// Always register admin index routes for priorities, statuses, and types
Route::middleware(['web'])->group(function () {
    Route::get('/admin/ticket-priorities', \App\Http\Controllers\TicketsPrioritiesController::class . '@index')->name('admin.ticket-priorities.index');
    Route::get('/admin/ticket-statuses', \App\Http\Controllers\TicketsStatusesController::class . '@index')->name('admin.ticket-statuses.index');
    Route::get('/admin/ticket-types', \App\Http\Controllers\TicketsTypesController::class . '@index')->name('admin.ticket-types.index');
    Route::get('/admin/assets-statuses', \App\Http\Controllers\StatusesController::class . '@index')->name('admin.assets-statuses.index');
    
    // Admin Assets and Tickets Routes
    Route::get('/admin/assets', [\App\Http\Controllers\InventoryController::class, 'index'])->name('admin.assets.index');
    Route::get('/admin/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('admin.tickets.show');
});

// Authentication Routes (Laravel 10 compatible)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/home');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// QR Code Routes (Public access for mobile scanning)
Route::get('/assets/qr/{qrCode}', [\App\Http\Controllers\QRCodeController::class, 'showAssetByQR'])->name('assets.qr');

// Authenticated Routes
Route::middleware(['web', 'auth'])->group(function () {
    
    // Home/Dashboard Routes
    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    
    // Management Dashboard Routes
    Route::middleware(['role:management|super-admin'])->prefix('management')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ManagementDashboardController::class, 'index'])->name('management.dashboard');
        Route::get('/admin-performance', [\App\Http\Controllers\ManagementDashboardController::class, 'adminPerformance'])->name('management.admin-performance');
        Route::get('/ticket-reports', [\App\Http\Controllers\ManagementDashboardController::class, 'ticketReports'])->name('management.ticket-reports');
        Route::get('/asset-reports', [\App\Http\Controllers\ManagementDashboardController::class, 'assetReports'])->name('management.asset-reports');
    });

    // Admin & SuperAdmin Routes (combined to avoid conflicts)
    Route::middleware(['role:admin|super-admin'])->group(function () {
        
        // Tickets Routes - Specific routes BEFORE wildcard routes
        Route::get('/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
        Route::get('/tickets/unassigned', [\App\Http\Controllers\TicketController::class, 'unassigned'])->name('tickets.unassigned');
        Route::get('/tickets/overdue', [\App\Http\Controllers\TicketController::class, 'overdue'])->name('tickets.overdue');
        Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
        Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/self-assign', [\App\Http\Controllers\TicketController::class, 'selfAssign'])->name('tickets.self-assign');
        Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assign'])->name('tickets.assign');
        Route::post('/tickets/{ticket}/complete', [\App\Http\Controllers\TicketController::class, 'complete'])->name('tickets.complete');
        Route::post('/tickets/{ticket}/force-assign', [\App\Http\Controllers\TicketController::class, 'forceAssign'])->name('tickets.force-assign');

        // Daily Activities - Specific routes BEFORE resource route
        Route::get('/daily-activities/calendar', [\App\Http\Controllers\DailyActivityController::class, 'calendar'])->name('daily-activities.calendar');
        Route::get('/daily-activities/calendar-events', [\App\Http\Controllers\DailyActivityController::class, 'getCalendarEvents'])->name('daily-activities.calendar-events');
        Route::get('/daily-activities/date-activities', [\App\Http\Controllers\DailyActivityController::class, 'getDateActivities'])->name('daily-activities.date-activities');
        Route::get('/daily-activities/daily-report', [\App\Http\Controllers\DailyActivityController::class, 'today'])->name('daily-activities.daily-report');
        Route::get('/daily-activities/weekly-report', [\App\Http\Controllers\DailyActivityController::class, 'weekly'])->name('daily-activities.weekly-report');
        Route::get('/daily-activities/export-pdf', [\App\Http\Controllers\DailyActivityController::class, 'export'])->name('daily-activities.export-pdf');
        Route::resource('daily-activities', \App\Http\Controllers\DailyActivityController::class);

        // Assets Routes
        Route::get('/assets/categories', [\App\Http\Controllers\InventoryController::class, 'categories'])->name('assets.categories');
        Route::get('/assets/requests', [\App\Http\Controllers\InventoryController::class, 'requests'])->name('assets.requests');
        Route::get('/assets/scan-qr', [\App\Http\Controllers\AssetsController::class, 'scanQR'])->name('assets.scan-qr');
        Route::post('/assets/process-scan', [\App\Http\Controllers\AssetsController::class, 'processScan'])->name('assets.process-scan');
        Route::get('/assets/my-assets', [\App\Http\Controllers\AssetsController::class, 'myAssets'])->name('assets.my-assets');
        Route::get('/assets/{asset}/qr-code', [\App\Http\Controllers\AssetsController::class, 'generateQR'])->name('assets.qr-code');
        Route::get('/assets/{asset}/qr-download', [\App\Http\Controllers\AssetsController::class, 'downloadQR'])->name('assets.qr-download');
        Route::get('/assets/{asset}/ticket-history', [\App\Http\Controllers\AssetsController::class, 'history'])->name('assets.ticket-history');
        Route::get('/assets/{asset}/movements', [\App\Http\Controllers\AssetsController::class, 'movements'])->name('assets.movements');
        Route::post('/assets/{asset}/assign', [\App\Http\Controllers\AssetsController::class, 'assign'])->name('assets.assign');
        Route::post('/assets/{asset}/unassign', [\App\Http\Controllers\AssetsController::class, 'unassign'])->name('assets.unassign');
        Route::post('/assets/bulk-qr-codes', [\App\Http\Controllers\QRCodeController::class, 'bulkGenerateQRCodes'])->name('assets.bulk-qr-codes');
        Route::post('/assets/{asset}/change-status', [\App\Http\Controllers\InventoryController::class, 'changeStatus'])->name('assets.change-status');
        Route::get('/assets', [\App\Http\Controllers\AssetsController::class, 'index'])->name('assets.index');
        
        // Assets CRUD Routes
        Route::resource('assets', \App\Http\Controllers\AssetsController::class)->except(['index']);

        // Asset Maintenance
        Route::get('/asset-maintenance', [\App\Http\Controllers\AssetMaintenanceController::class, 'index'])->name('asset-maintenance.index');
        Route::get('/asset-maintenance/analytics', [\App\Http\Controllers\AssetMaintenanceController::class, 'analytics'])->name('asset-maintenance.analytics');
        Route::get('/asset-maintenance/{asset}', [\App\Http\Controllers\AssetMaintenanceController::class, 'show'])->name('asset-maintenance.show');

        // Spares Management
        Route::get('/spares', [\App\Http\Controllers\SparesController::class, 'index'])->name('spares.index');
    });

    // SuperAdmin Only Routes
    Route::middleware(['role:super-admin'])->group(function () {
        
        // Asset Requests Management
        Route::resource('asset-requests', \App\Http\Controllers\AssetRequestController::class);
        Route::post('/asset-requests/{assetRequest}/approve', [\App\Http\Controllers\AssetRequestController::class, 'approve'])->name('asset-requests.approve');
        Route::post('/asset-requests/{assetRequest}/reject', [\App\Http\Controllers\AssetRequestController::class, 'reject'])->name('asset-requests.reject');
        Route::post('/asset-requests/{assetRequest}/fulfill', [\App\Http\Controllers\AssetRequestController::class, 'fulfill'])->name('asset-requests.fulfill');
        
        // Admin Configuration
        Route::get('/admin', [\App\Http\Controllers\PagesController::class, 'getTicketConfig'])->name('admin.config');
        
        // Master Data Management
        Route::resource('/models', \App\Http\Controllers\AssetModelsController::class, ['parameters' => ['models' => 'asset_model']]);
        Route::resource('/pcspecs', \App\Http\Controllers\PcspecsController::class);
        Route::resource('/manufacturers', \App\Http\Controllers\ManufacturersController::class);
        Route::resource('/asset-types', \App\Http\Controllers\AssetTypesController::class);
        Route::resource('/suppliers', \App\Http\Controllers\SuppliersController::class);
        Route::resource('/locations', \App\Http\Controllers\LocationsController::class);
        Route::resource('/divisions', \App\Http\Controllers\DivisionsController::class);
        Route::resource('/invoices', \App\Http\Controllers\InvoicesController::class);
        Route::resource('/budgets', \App\Http\Controllers\BudgetsController::class);
    });

    // NOTE: User and Management routes are now handled by the admin|super-admin group above
    // to avoid route conflicts and 403 errors. The controller handles role-based filtering internally.

    // Multi-role Routes (Management, Admin, SuperAdmin)
    Route::middleware(['role:management|admin|super-admin'])->group(function () {
        
        // Enhanced Ticket Creation (with asset selection)
        Route::get('/tickets/create-with-asset', [\App\Http\Controllers\TicketController::class, 'createWithAsset'])->name('tickets.create-with-asset');
    });

    // Activity Status Update (for all authenticated users)
    Route::post('/update-activity', [\App\Http\Controllers\ActivityController::class, 'updateActivity'])->name('update-activity');
});

// Debug route for assets issue
Route::get('/test-inventory-debug', function() {
    try {
        echo "<h2>Testing Inventory Controller Data</h2>";
        
        // Test each model one by one
        echo "<h3>Model Tests:</h3>";
        
        echo "Asset count: " . \App\Asset::count() . "<br>";
        echo "AssetType count: " . \App\AssetType::count() . "<br>";
        echo "Status count: " . \App\Status::count() . "<br>";
        echo "Location count: " . \App\Location::count() . "<br>";
        echo "Division count: " . \App\Division::count() . "<br>";
        
        // Test the problematic queries
        echo "<h3>Stats Queries:</h3>";
        
        $activeCount = \App\Asset::whereHas('status', function($q) {
            $q->where('name', 'Active');
        })->count();
        echo "Active assets: " . $activeCount . "<br>";
        
        $maintenanceCount = \App\Asset::whereHas('status', function($q) {
            $q->where('name', 'like', '%repair%');
        })->count();
        echo "Maintenance assets: " . $maintenanceCount . "<br>";
        
        // Check if AssetRequest model exists
        if (class_exists('\App\AssetRequest')) {
            $pendingCount = \App\AssetRequest::where('status', 'pending')->count();
            echo "Pending requests: " . $pendingCount . "<br>";
        } else {
            echo "AssetRequest model not found<br>";
        }
        
        echo "<h3>Controller Test:</h3>";
        
        // Test the actual controller
        $controller = new \App\Http\Controllers\InventoryController();
        $request = new \Illuminate\Http\Request();
        
        echo "Calling controller index method...<br>";
        $result = $controller->index($request);
        
        if ($result instanceof \Illuminate\View\View) {
            echo "Controller returned a view successfully!<br>";
            $viewData = $result->getData();
            echo "View data keys: " . implode(', ', array_keys($viewData)) . "<br>";
        } else {
            echo "Controller returned: " . gettype($result) . "<br>";
        }
        
    } catch (\Exception $e) {
        echo "<h3>Error:</h3>";
        echo "Message: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

// Debug authentication route (outside middleware)
Route::get('/debug-auth', function() {
    $user = auth()->user();
    if (!$user) {
        return '<h1>NOT LOGGED IN</h1><p>You need to log in first</p><a href="/login">Go to Login</a>';
    }
    
    $roles = $user->roles->pluck('name')->toArray() ?? [];
    
    return '<h1>Authentication Debug</h1>' .
           '<p><strong>User:</strong> ' . $user->name . '</p>' .
           '<p><strong>Email:</strong> ' . $user->email . '</p>' .
           '<p><strong>Roles:</strong> ' . implode(', ', $roles) . '</p>' .
           '<p><strong>Has admin role:</strong> ' . ($user->hasRole('admin') ? 'YES' : 'NO') . '</p>' .
           '<p><strong>Has super-admin role:</strong> ' . ($user->hasRole('super-admin') ? 'YES' : 'NO') . '</p>';
});

// Simple assets test route
Route::get('/assets-simple', function() {
    try {
        $pageTitle = 'Simple Assets Test';
        
        // Simple stats without complex relationships
        $stats = [
            'total_assets' => \App\Asset::count(),
            'active_assets' => 0, // Temporarily simplified
            'maintenance_assets' => 0, // Temporarily simplified  
            'pending_requests' => 0 // Temporarily simplified
        ];
        
        // Get basic data
        $assets = \App\Asset::with(['assetModel', 'status', 'division'])->paginate(25);
        $categories = \App\AssetType::all();
        $statuses = \App\Status::all();
        $locations = \App\Location::all();
        $divisions = \App\Division::all();
        $categoryStats = collect(); // Empty for now
        
        return view('inventory.index', compact(
            'assets', 'categories', 'statuses', 'locations', 'divisions', 
            'stats', 'categoryStats', 'pageTitle'
        ));
        
    } catch (\Exception $e) {
        return response("Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine(), 500);
    }
});

// Test Routes (untuk development)
Route::prefix('test')->group(function () {
    Route::get('/qr', [\App\Http\Controllers\TestController::class, 'testQrCode'])->name('test.qr');
    Route::get('/asset-qr', [\App\Http\Controllers\TestController::class, 'testAssetQrCode'])->name('test.asset-qr');
    Route::get('/status', [\App\Http\Controllers\TestController::class, 'systemStatus'])->name('test.status');
    
    // UAC Test Routes
    Route::get('/super-admin-test', function() {
        return 'Super Admin Access Working!';
    })->middleware(['auth', 'role:super-admin']);
    
    Route::get('/admin-test', function() {
        return 'Admin Access Working!';
    })->middleware(['auth', 'role:admin']);
    
    Route::get('/management-test', function() {
        return 'Management Access Working!';
    })->middleware(['auth', 'role:management']);
    
    // Debug current user roles
    Route::get('/debug-my-roles', function() {
        $user = auth()->user();
        if (!$user) {
            return 'Not logged in';
        }
        
        $output = "<h2>Current User Debug Info</h2>";
        $output .= "<p><strong>Name:</strong> " . $user->name . "</p>";
        $output .= "<p><strong>Email:</strong> " . $user->email . "</p>";
        $output .= "<p><strong>ID:</strong> " . $user->id . "</p>";
        
        // Check roles using different methods
        $output .= "<h3>Role Check Methods:</h3>";
        $output .= "<p><strong>hasRole('super-admin'):</strong> " . ($user->hasRole('super-admin') ? 'YES' : 'NO') . "</p>";
        $output .= "<p><strong>hasRole('admin'):</strong> " . ($user->hasRole('admin') ? 'YES' : 'NO') . "</p>";
        $output .= "<p><strong>hasRole('user'):</strong> " . ($user->hasRole('user') ? 'YES' : 'NO') . "</p>";
        $output .= "<p><strong>hasRole('management'):</strong> " . ($user->hasRole('management') ? 'YES' : 'NO') . "</p>";
        
        // Get all roles from database
        $roles = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\User')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->pluck('roles.name')
            ->toArray();
        
        $output .= "<h3>Database Roles:</h3>";
        $output .= "<pre>" . print_r($roles, true) . "</pre>";
        
        return $output;
    })->middleware(['auth']);
});


// Test routes for role middleware verification
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/test-role', function() {
        $user = Auth::user();
        echo "<h1>User Role Test</h1>";
        echo "Logged in as: " . $user->name . "<br>";
        echo "Email: " . $user->email . "<br>";
        echo "<hr>";
        
        echo "<h2>Role Check</h2>";
        echo "Has super-admin role: " . ($user->hasRole('super-admin') ? 'YES' : 'NO') . "<br>";
        echo "Has admin role: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "<br>";
        echo "Has management role: " . ($user->hasRole('management') ? 'YES' : 'NO') . "<br>";
        echo "Has user role: " . ($user->hasRole('user') ? 'YES' : 'NO') . "<br>";
        
        echo "<h2>Database Role Assignments</h2>";
        $spatieRoles = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\User')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name')
            ->get();
            
        $legacyRoles = DB::table('role_user')
            ->where('user_id', $user->id)
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('roles.name')
            ->get();
            
        echo "Spatie Roles: ";
        if ($spatieRoles->isEmpty()) {
            echo "None";
        } else {
            echo implode(", ", $spatieRoles->pluck('name')->toArray());
        }
        echo "<br>";
        
        echo "Legacy Roles: ";
        if ($legacyRoles->isEmpty()) {
            echo "None";
        } else {
            echo implode(", ", $legacyRoles->pluck('name')->toArray());
        }
        echo "<br>";
        
        echo "<h2>Protected Route Tests</h2>";
        echo "The following links test if the role middleware is working correctly:<br>";
        echo "<ul>";
        echo "<li><a href='/test-super-admin'>Test Super Admin Route</a> (requires super-admin role)</li>";
        echo "<li><a href='/test-admin'>Test Admin Route</a> (requires admin role)</li>";
        echo "<li><a href='/test-management'>Test Management Route</a> (requires management role)</li>";
        echo "<li><a href='/test-user'>Test User Route</a> (requires user role)</li>";
        echo "</ul>";
        
        return "";
    });
    
    // Test routes with different role middleware requirements
    Route::get('/test-super-admin', function() {
        return "You have the super-admin role!";
    })->middleware('role:super-admin');
    
    Route::get('/test-admin', function() {
        return "You have the admin role!";
    })->middleware('role:admin');
    
    Route::get('/test-management', function() {
        return "You have the management role!";
    })->middleware('role:management');
    
    Route::get('/test-user', function() {
        return "You have the user role!";
    })->middleware('role:user');
});
// Debug routes for role middleware issues
Route::get('/debug-roles', function () {
    $user = auth()->user();
    if (!$user) {
        return '<h1>❌ NOT LOGGED IN</h1><p>Please login first at: <a href="/login">/login</a></p>';
    }

    $html = '<h1>Role Debug Information</h1>';
    $html .= '<p><strong>User:</strong> ' . $user->name . ' (ID: ' . $user->id . ')</p>';
    $html .= '<p><strong>Email:</strong> ' . $user->email . '</p>';

    // Test if user has role directly from DB
    $directDbRoles = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', 'App\\User')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->pluck('roles.name')
        ->toArray();

    $html .= '<h2>Roles from DB:</h2>';
    if (empty($directDbRoles)) {
        $html .= '<p style="color:red;"><strong>❌ NO ROLES ASSIGNED TO THIS USER!</strong></p>';
        $html .= '<p>This is why you\'re getting 403 errors!</p>';
    } else {
        $html .= '<ul>';
        foreach ($directDbRoles as $role) {
            $html .= '<li><strong>' . $role . '</strong></li>';
        }
        $html .= '</ul>';
    }

    // Test HasRole method
    $html .= '<h2>Role Checks:</h2>';
    $html .= '<ul>';
    $html .= '<li>Has role "super-admin": ' . ($user->hasRole('super-admin') ? '✅ YES' : '❌ NO') . '</li>';
    $html .= '<li>Has role "admin": ' . ($user->hasRole('admin') ? '✅ YES' : '❌ NO') . '</li>';
    $html .= '<li>Has any role ["super-admin", "admin"]: ' . ($user->hasAnyRole(['super-admin', 'admin']) ? '✅ YES' : '❌ NO') . '</li>';
    $html .= '</ul>';
    
    $html .= '<h2>Expected Result:</h2>';
    $html .= '<p>For routes to work, you need: <strong>admin</strong> OR <strong>super-admin</strong> role</p>';
    
    if (in_array('super-admin', $directDbRoles) || in_array('admin', $directDbRoles)) {
        $html .= '<p style="color:green;"><strong>✅ YOU HAVE CORRECT ROLES - Routes should work!</strong></p>';
        $html .= '<p>If still getting 403, clear browser cache and try again.</p>';
    } else {
        $html .= '<p style="color:red;"><strong>❌ YOU DON\'T HAVE REQUIRED ROLES - This is why you get 403!</strong></p>';
        $html .= '<p>Login as: superadmin@quty.co.id or admin@quty.co.id</p>';
    }

    return $html;
});

// Test route protected by role middleware
Route::get('/test-super-admin', function () {
    return '<h1>You have super-admin access!</h1>';
})->middleware('role:super-admin');

// Debug menu - shows all available debug routes
Route::get('/debug-menu', function() {
    $html = '<h2>🔧 Debug Menu</h2>';
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Authentication Debug:</h3>';
    $html .= '<p><a href="/debug-current-user" style="background: #17a2b8; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🔍 Check Current User</a></p>';
    $html .= '<p><a href="/check-users" style="background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">👥 List All Users</a></p>';
    $html .= '</div>';
    
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Quick Login (for testing):</h3>';
    $html .= '<p><a href="/quick-login-superadmin" style="background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🔑 Login as Super Admin</a></p>';
    $html .= '<p><a href="/quick-login-admin" style="background: #ffc107; color: black; padding: 8px 15px; text-decoration: none; margin: 5px;">🔑 Login as Admin</a></p>';
    $html .= '<p><a href="/quick-login-user" style="background: #28a745; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🔑 Login as User</a></p>';
    $html .= '</div>';
    
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Page Access:</h3>';
    $html .= '<p><a href="/assets" style="background: #007bff; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">📦 Assets Page</a></p>';
    $html .= '<p><a href="/tickets" style="background: #6f42c1; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🎫 Tickets Page</a></p>';
    $html .= '<p><a href="/daily-activities" style="background: #fd7e14; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">📅 Daily Activities</a></p>';
    $html .= '</div>';
    
    $html .= '<div style="margin: 20px 0;">';
    $html .= '<h3>Logout:</h3>';
    $html .= '<p><a href="/force-relogin" style="background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; margin: 5px;">🚪 Logout & Clear Session</a></p>';
    $html .= '</div>';
    
    return $html;
});

// Check users in database
Route::get('/check-users', function() {
    $users = \App\User::take(5)->get(['id', 'name', 'email']);
    $html = '<h2>Users in Database (' . \App\User::count() . ' total)</h2>';
    foreach($users as $user) {
        $html .= '<p>ID: ' . $user->id . ', Name: ' . $user->name . ', Email: ' . $user->email . '</p>';
    }
    return $html;
});

// Test hasRole method
Route::get('/test-has-role', function() {
    $html = '<h2>Test hasRole Method</h2>';
    
    try {
        $user = \App\User::find(1);
        if ($user) {
            $html .= '<p>User found: ' . $user->name . '</p>';
            $html .= '<p>hasRole method exists: ' . (method_exists($user, 'hasRole') ? '✅ YES' : '❌ NO') . '</p>';
            
            if (method_exists($user, 'hasRole')) {
                $html .= '<p>Has super-admin role: ' . ($user->hasRole('super-admin') ? '✅ YES' : '❌ NO') . '</p>';
                $html .= '<p>Has admin role: ' . ($user->hasRole('admin') ? '✅ YES' : '❌ NO') . '</p>';
                $html .= '<p>Has user role: ' . ($user->hasRole('user') ? '✅ YES' : '❌ NO') . '</p>';
            }
            
            // Test getRoleNames if available
            if (method_exists($user, 'getRoleNames')) {
                $roles = $user->getRoleNames();
                $html .= '<p>All roles: ' . $roles->implode(', ') . '</p>';
            }
        } else {
            $html .= '<p>❌ User not found</p>';
        }
    } catch (\Exception $e) {
        $html .= '<p>❌ Error: ' . $e->getMessage() . '</p>';
    }
    
    return $html;
});

// Debug current user info
Route::get('/debug-current-user', function() {
    // Log the authentication check
    \Illuminate\Support\Facades\Log::info('Debug route accessed', [
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'session_id' => session()->getId()
    ]);
    
    $html = '<h2>Current User Debug Info</h2>';
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Log detailed user info
        \Illuminate\Support\Facades\Log::info('User authenticated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);
        
        $html .= '<div style="background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;">';
        $html .= '<h3>✅ LOGGED IN</h3>';
        $html .= '<p><strong>User ID:</strong> ' . $user->id . '</p>';
        $html .= '<p><strong>Name:</strong> ' . $user->name . '</p>';
        $html .= '<p><strong>Email:</strong> ' . $user->email . '</p>';
        
        // Check roles if available
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames();
            $html .= '<p><strong>Roles:</strong> ' . ($roles->count() > 0 ? $roles->implode(', ') : 'None') . '</p>';
        }
        
        if (method_exists($user, 'hasRole')) {
            $html .= '<p><strong>Has super-admin role:</strong> ' . ($user->hasRole('super-admin') ? '✅ YES' : '❌ NO') . '</p>';
            $html .= '<p><strong>Has admin role:</strong> ' . ($user->hasRole('admin') ? '✅ YES' : '❌ NO') . '</p>';
            $html .= '<p><strong>Has management role:</strong> ' . ($user->hasRole('management') ? '✅ YES' : '❌ NO') . '</p>';
        }
        
        $html .= '</div>';
        $html .= '<p><a href="/assets" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none;">Go to Assets Page</a></p>';
        $html .= '<p><a href="/tickets" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none;">Go to Tickets Page</a></p>';
    } else {
        $html .= '<div style="background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;">';
        $html .= '<h3>❌ NOT LOGGED IN</h3>';
        $html .= '<p>You need to log in first</p>';
        $html .= '</div>';
        $html .= '<p><a href="/login" style="background: #dc3545; color: white; padding: 10px 20px; text-decoration: none;">Go to Login</a></p>';
    }
    
    $html .= '<hr>';
    $html .= '<p><strong>Session ID:</strong> ' . session()->getId() . '</p>';
    $html .= '<p><strong>CSRF Token:</strong> ' . csrf_token() . '</p>';
    
    return $html;
});

// Quick login routes for testing
Route::get('/quick-login-superadmin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    \Illuminate\Support\Facades\Log::info('Quick login as Super Admin', ['user_id' => 1]);
    return redirect('/debug-current-user')->with('message', 'Logged in as Super Admin');
});

Route::get('/quick-login-admin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(2);
    \Illuminate\Support\Facades\Log::info('Quick login as Admin', ['user_id' => 2]);
    return redirect('/debug-current-user')->with('message', 'Logged in as Admin');
});

Route::get('/quick-login-user', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(3);
    \Illuminate\Support\Facades\Log::info('Quick login as User', ['user_id' => 3]);
    return redirect('/debug-current-user')->with('message', 'Logged in as User');
});

// Browser-friendly login and redirect
Route::get('/browser-login-superadmin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    \Illuminate\Support\Facades\Log::info('Browser login as Super Admin', ['user_id' => 1]);
    return redirect('/assets')->with('success', 'Logged in as Super Admin - you should now see the assets page!');
});

Route::get('/browser-login-admin', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(2);
    \Illuminate\Support\Facades\Log::info('Browser login as Admin', ['user_id' => 2]);
    return redirect('/assets')->with('success', 'Logged in as Admin - you should now see the assets page!');
});

// Test all major controllers
Route::get('/test-all-controllers', function() {
    // Login first
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    
    $html = '<h2>Controller Tests (as Super Admin)</h2>';
    
    $controllers = [
        'InventoryController@index' => '/assets',
        'TicketController@index' => '/tickets', 
        'DailyActivityController@index' => '/daily-activities',
        'HomeController@index' => '/home'
    ];
    
    foreach($controllers as $controller => $route) {
        try {
            $request = \Illuminate\Http\Request::create($route, 'GET');
            $request->setUserResolver(function() {
                return \Illuminate\Support\Facades\Auth::user();
            });
            
            // Parse controller and method
            list($controllerClass, $method) = explode('@', $controller);
            $fullControllerClass = "\\App\\Http\\Controllers\\{$controllerClass}";
            
            if (class_exists($fullControllerClass)) {
                $controllerInstance = app($fullControllerClass);
                
                if (method_exists($controllerInstance, $method)) {
                    $response = $controllerInstance->$method($request);
                    $html .= "<p>✅ <strong>{$controller}</strong> ({$route}): SUCCESS</p>";
                } else {
                    $html .= "<p>❌ <strong>{$controller}</strong> ({$route}): Method not found</p>";
                }
            } else {
                $html .= "<p>❌ <strong>{$controller}</strong> ({$route}): Controller not found</p>";
            }
            
        } catch (\Exception $e) {
            $html .= "<p>❌ <strong>{$controller}</strong> ({$route}): ERROR - " . $e->getMessage() . "</p>";
        }
    }
    
    return $html;
});

// Test login and immediately access assets in same request
Route::get('/test-login-and-assets', function() {
    $html = '<h2>Login and Assets Test (Single Request)</h2>';
    
    // Step 1: Login
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    $html .= '<h3>Step 1: Login as Super Admin</h3>';
    $html .= '<p>Auth::check(): ' . (\Illuminate\Support\Facades\Auth::check() ? '✅ TRUE' : '❌ FALSE') . '</p>';
    $html .= '<p>Auth::id(): ' . (\Illuminate\Support\Facades\Auth::id() ?? 'NULL') . '</p>';
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $html .= '<p>User: ' . $user->name . ' (' . $user->email . ')</p>';
    }
    
    // Step 2: Test Assets Controller
    $html .= '<h3>Step 2: Test Assets Controller</h3>';
    try {
        $controller = new \App\Http\Controllers\InventoryController();
        $request = \Illuminate\Http\Request::create('/assets', 'GET');
        
        // Manually set the authenticated user for the request
        $request->setUserResolver(function() {
            return \Illuminate\Support\Facades\Auth::user();
        });
        
        $response = $controller->index($request);
        $html .= '<p>✅ SUCCESS: Controller returned response</p>';
        $html .= '<p>Response type: ' . get_class($response) . '</p>';
        
        // Try to get the view content
        if (method_exists($response, 'render')) {
            $content = $response->render();
            $html .= '<p>Content length: ' . strlen($content) . ' characters</p>';
            $html .= '<p>Contains "Total Assets": ' . (strpos($content, 'Total Assets') !== false ? '✅ YES' : '❌ NO') . '</p>';
        }
        
    } catch (\Exception $e) {
        $html .= '<p>❌ ERROR: ' . $e->getMessage() . '</p>';
        $html .= '<p>File: ' . $e->getFile() . ':' . $e->getLine() . '</p>';
    }
    
    // Step 3: Direct link test
    $html .= '<h3>Step 3: Test Links</h3>';
    $html .= '<p><a href="/assets" style="background: #007bff; color: white; padding: 10px; text-decoration: none;">Go to Assets Page</a></p>';
    $html .= '<p><strong>Note:</strong> If the link above shows login page, then the session is not persisting between requests.</p>';
    
    return $html;
});

// Temporary test route to see assets page without authentication (REMOVE IN PRODUCTION)
Route::get('/test-assets-no-auth', function() {
    // Temporarily login as super admin
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    return redirect('/assets');
});

// Test multiple pages with auth
Route::get('/test-all-pages', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    
    $pages = [
        '/assets' => 'Assets Page',
        '/daily-activities' => 'Daily Activities', 
        '/tickets' => 'Tickets',
        '/home' => 'Home/Dashboard'
    ];
    
    $html = '<h2>Testing Pages (logged in as Super Admin)</h2>';
    foreach($pages as $url => $name) {
        try {
            $response = file_get_contents('http://192.168.1.122' . $url);
            $status = strpos($response, 'form-control name=email') === false ? '✅ WORKS' : '❌ LOGIN PAGE';
            $html .= '<p><strong>' . $name . '</strong> (' . $url . '): ' . $status . '</p>';
        } catch (Exception $e) {
            $html .= '<p><strong>' . $name . '</strong> (' . $url . '): ❌ ERROR</p>';
        }
    }
    return $html;
});

// Force re-login route to clear session
Route::get('/force-relogin', function() {
    Auth::logout();
    session()->flush();
    session()->regenerate();
    return redirect('/login')->with('message', '<strong>Session cleared!</strong> Please login again to refresh your roles.');
});

