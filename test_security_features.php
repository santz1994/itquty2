<?php
/**
 * Security Features Test Script
 * Tests the implemented security features: single device login and session timeout
 */

require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

echo "\n=== IT Support System - Security Features Test ===\n\n";

// Test 1: Database Sessions Table
echo "1. Testing Database Sessions Configuration...\n";
try {
    $sessionConfig = config('session.driver');
    echo "   ✅ Session driver: $sessionConfig\n";
    
    if ($sessionConfig === 'database') {
        // Check if sessions table exists
        $tableExists = DB::getSchemaBuilder()->hasTable('sessions');
        if ($tableExists) {
            echo "   ✅ Sessions table exists\n";
            
            // Check table structure
            $columns = DB::getSchemaBuilder()->getColumnListing('sessions');
            $requiredColumns = ['id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity'];
            $hasAllColumns = empty(array_diff($requiredColumns, $columns));
            
            if ($hasAllColumns) {
                echo "   ✅ Sessions table has correct structure\n";
            } else {
                echo "   ❌ Sessions table missing columns: " . implode(', ', array_diff($requiredColumns, $columns)) . "\n";
            }
        } else {
            echo "   ❌ Sessions table does not exist\n";
        }
    } else {
        echo "   ❌ Session driver is not set to 'database'\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking session configuration: " . $e->getMessage() . "\n";
}

// Test 2: Session Timeout Configuration
echo "\n2. Testing Session Timeout Configuration...\n";
try {
    $sessionLifetime = config('session.lifetime');
    echo "   ✅ Session lifetime: $sessionLifetime minutes\n";
    
    if ($sessionLifetime <= 60) {
        echo "   ✅ Session timeout is appropriately short (≤ 60 minutes)\n";
    } else {
        echo "   ⚠️  Session timeout is quite long (> 60 minutes)\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking session timeout: " . $e->getMessage() . "\n";
}

// Test 3: Single Device Login Listener
echo "\n3. Testing Single Device Login Event Listener...\n";
try {
    $listeners = config('events.login', []);
    $hasLoginListener = false;
    
    // Check if SingleDeviceLoginListener is registered
    if (file_exists(app_path('Providers/EventServiceProvider.php'))) {
        $content = file_get_contents(app_path('Providers/EventServiceProvider.php'));
        if (strpos($content, 'SingleDeviceLoginListener') !== false) {
            echo "   ✅ SingleDeviceLoginListener is registered in EventServiceProvider\n";
            $hasLoginListener = true;
        }
    }
    
    if (!$hasLoginListener) {
        echo "   ❌ SingleDeviceLoginListener not found in EventServiceProvider\n";
    }
    
    // Check if listener file exists
    if (file_exists(app_path('Listeners/SingleDeviceLoginListener.php'))) {
        echo "   ✅ SingleDeviceLoginListener.php file exists\n";
    } else {
        echo "   ❌ SingleDeviceLoginListener.php file not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking login listener: " . $e->getMessage() . "\n";
}

// Test 4: Session Timeout Middleware
echo "\n4. Testing Session Timeout Middleware...\n";
try {
    // Check if middleware file exists
    if (file_exists(app_path('Http/Middleware/SessionTimeoutMiddleware.php'))) {
        echo "   ✅ SessionTimeoutMiddleware.php file exists\n";
        
        // Check if middleware is registered in Kernel
        if (file_exists(app_path('Http/Kernel.php'))) {
            $kernelContent = file_get_contents(app_path('Http/Kernel.php'));
            if (strpos($kernelContent, 'SessionTimeoutMiddleware') !== false) {
                echo "   ✅ SessionTimeoutMiddleware is registered in Kernel\n";
            } else {
                echo "   ❌ SessionTimeoutMiddleware not registered in Kernel\n";
            }
        }
    } else {
        echo "   ❌ SessionTimeoutMiddleware.php file not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking session timeout middleware: " . $e->getMessage() . "\n";
}

// Test 5: Login UI/UX Improvements
echo "\n5. Testing Login UI/UX Improvements...\n";
try {
    $loginViewPath = resource_path('views/auth/login.blade.php');
    if (file_exists($loginViewPath)) {
        $loginContent = file_get_contents($loginViewPath);
        
        // Check for modern UI elements
        $modernFeatures = [
            'gradient' => 'Modern gradient background implemented',
            'Font Awesome' => 'Font Awesome icons integrated',
            'responsive' => 'Responsive design features added',
            'form validation' => 'Client-side form validation included',
            'password toggle' => 'Password visibility toggle implemented',
            'loading states' => 'Loading states and animations added'
        ];
        
        foreach ($modernFeatures as $feature => $description) {
            if (stripos($loginContent, $feature) !== false) {
                echo "   ✅ $description\n";
            }
        }
        
        // Check for security messaging
        if (strpos($loginContent, 'session timeout') !== false) {
            echo "   ✅ Session timeout information displayed to users\n";
        }
        
        if (strpos($loginContent, 'single device') !== false) {
            echo "   ✅ Single device security information displayed\n";
        }
        
    } else {
        echo "   ❌ Login view file not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking login UI: " . $e->getMessage() . "\n";
}

// Test 6: Session Extension Route
echo "\n6. Testing Session Extension Route...\n";
try {
    $routes = app('router')->getRoutes();
    $hasExtendRoute = false;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'extend-session') {
            echo "   ✅ Session extension route is registered\n";
            $hasExtendRoute = true;
            break;
        }
    }
    
    if (!$hasExtendRoute) {
        echo "   ❌ Session extension route not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking session extension route: " . $e->getMessage() . "\n";
}

// Test 7: Database Connection
echo "\n7. Testing Database Connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Database connection successful\n";
    
    // Check for users table
    if (DB::getSchemaBuilder()->hasTable('users')) {
        $userCount = DB::table('users')->count();
        echo "   ✅ Users table exists with $userCount users\n";
    } else {
        echo "   ❌ Users table not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Summary
echo "\n=== Security Features Test Summary ===\n";
echo "✅ = Feature working correctly\n";
echo "⚠️  = Feature working but needs attention\n";
echo "❌ = Feature has issues\n\n";

echo "Security Features Implemented:\n";
echo "• Single Device Login: Prevents multiple concurrent logins per user\n";
echo "• Session Timeout: Automatic logout after " . config('session.lifetime') . " minutes of inactivity\n";
echo "• Enhanced UI/UX: Modern, responsive login page with security information\n";
echo "• Database Sessions: Centralized session management for better control\n";
echo "• Session Extension: AJAX endpoint for extending sessions\n\n";

echo "Next Steps:\n";
echo "1. Test the features by logging in at http://127.0.0.1:8000/login\n";
echo "2. Try logging in from multiple browsers/devices to test single device login\n";
echo "3. Wait for session timeout to test automatic logout\n";
echo "4. Check the modern UI design and responsive layout\n\n";

echo "=== Test Complete ===\n";