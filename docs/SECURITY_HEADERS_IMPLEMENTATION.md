# 🔒 SECURITY HEADERS IMPLEMENTATION GUIDE

**Purpose:** Add security headers to protect against XSS, clickjacking, and other attacks  
**Priority:** CRITICAL (Implement before production launch)  
**Time Required:** 15-20 minutes  
**Impact:** Increases security score from A+ (96%) to A+ (100%)

---

## Option 1: Apache (.htaccess) - RECOMMENDED

### File: `public/.htaccess`

Add these lines **after** the RewriteEngine section:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# ==========================================
# SECURITY HEADERS - ADD THIS SECTION
# ==========================================
<IfModule mod_headers.c>
    # Content Security Policy (CSP)
    # Adjust 'unsafe-inline' and 'unsafe-eval' based on your needs
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net; img-src 'self' data: https:; font-src 'self' data: https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'self';"
    
    # Prevent clickjacking attacks
    Header set X-Frame-Options "SAMEORIGIN"
    
    # Prevent MIME type sniffing
    Header set X-Content-Type-Options "nosniff"
    
    # Enable browser XSS protection
    Header set X-XSS-Protection "1; mode=block"
    
    # Referrer Policy - control referrer information
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    
    # Permissions Policy (formerly Feature Policy)
    Header set Permissions-Policy "geolocation=(), microphone=(), camera=(), payment=()"
    
    # HSTS (HTTP Strict Transport Security) - ONLY if using HTTPS
    # Uncomment the line below after HTTPS is configured
    # Header set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
</IfModule>
```

**Important Notes:**
- `unsafe-inline` and `unsafe-eval` are needed for inline scripts and eval() usage
- Adjust CDN domains if you use different CDN providers
- **DO NOT** enable HSTS until HTTPS is working correctly
- Test thoroughly after adding headers

---

## Option 2: Nginx (nginx.conf)

### File: `/etc/nginx/sites-available/itquty`

Add these lines inside the `server` block:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/itquty/public;
    
    index index.php index.html;
    
    # ==========================================
    # SECURITY HEADERS - ADD THIS SECTION
    # ==========================================
    
    # Content Security Policy (CSP)
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net; img-src 'self' data: https:; font-src 'self' data: https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'self';" always;
    
    # Prevent clickjacking attacks
    add_header X-Frame-Options "SAMEORIGIN" always;
    
    # Prevent MIME type sniffing
    add_header X-Content-Type-Options "nosniff" always;
    
    # Enable browser XSS protection
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Referrer Policy
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Permissions Policy
    add_header Permissions-Policy "geolocation=(), microphone=(), camera=(), payment=()" always;
    
    # HSTS (HTTP Strict Transport Security) - ONLY if using HTTPS
    # Uncomment the line below after HTTPS is configured
    # add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    
    # ==========================================
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

After editing, reload Nginx:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## Option 3: Laravel Middleware (Cross-Platform)

### File: `app/Http/Middleware/SecurityHeaders.php`

Create new middleware:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Content Security Policy
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.datatables.net; " .
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' data: https://cdn.jsdelivr.net; " .
            "connect-src 'self'; " .
            "frame-ancestors 'self';"
        );
        
        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');
        
        // HSTS - Only enable if HTTPS is configured
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }
        
        return $response;
    }
}
```

### Register Middleware: `app/Http/Kernel.php`

```php
protected $middleware = [
    // ... existing middleware
    \App\Http\Middleware\SecurityHeaders::class,
];
```

Or apply to specific route groups:

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\SecurityHeaders::class,
    ],
];
```

---

## HTTPS Enforcement

### File: `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
```

### File: `.env` (Production)

```env
# Production Environment Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

---

## Testing Security Headers

### Method 1: Browser DevTools
1. Open browser DevTools (F12)
2. Go to Network tab
3. Refresh page
4. Click on the main document request
5. Check **Response Headers** section
6. Verify all security headers are present

### Method 2: Command Line (curl)
```bash
curl -I https://yourdomain.com
```

Expected output:
```
HTTP/2 200
content-security-policy: default-src 'self'; script-src ...
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
x-xss-protection: 1; mode=block
referrer-policy: strict-origin-when-cross-origin
permissions-policy: geolocation=(), microphone=(), camera=(), payment=()
strict-transport-security: max-age=31536000; includeSubDomains; preload
```

### Method 3: Online Security Scanner
- **Mozilla Observatory:** https://observatory.mozilla.org/
- **Security Headers:** https://securityheaders.com/
- **SSL Labs:** https://www.ssllabs.com/ssltest/

Expected Score: **A+** 🎉

---

## Troubleshooting

### Issue 1: CSP Blocking Inline Scripts
**Symptom:** Console errors like "Refused to execute inline script"

**Solution:**
- Option A: Add nonce to scripts (recommended)
- Option B: Use `'unsafe-inline'` (already included, but less secure)
- Option C: Move inline scripts to external .js files

### Issue 2: CSP Blocking External Resources
**Symptom:** CSS/JS from CDN not loading

**Solution:**
Add CDN domains to CSP:
```
script-src 'self' https://cdn.jsdelivr.net https://your-cdn.com;
```

### Issue 3: HSTS Warning in Browser
**Symptom:** "Your connection is not private" after enabling HSTS

**Solution:**
- Ensure valid SSL certificate installed
- Test HTTPS works correctly before enabling HSTS
- Clear browser HSTS cache if needed:
  - Chrome: `chrome://net-internals/#hsts`
  - Firefox: Clear site data for domain

### Issue 4: Mixed Content Errors
**Symptom:** "Mixed content blocked" errors

**Solution:**
- Ensure all resources use HTTPS
- Update hardcoded HTTP URLs to HTTPS or relative URLs
- Check database for HTTP URLs in content

---

## Content Security Policy (CSP) Tuning

### Current CSP (Permissive for Development)
```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net;
style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;
```

### Strict CSP (Recommended for Production)
```
default-src 'self';
script-src 'self' https://cdn.jsdelivr.net https://cdn.datatables.net;
style-src 'self' https://cdn.jsdelivr.net https://cdn.datatables.net;
img-src 'self' data: https:;
font-src 'self' data: https://cdn.jsdelivr.net;
connect-src 'self';
frame-ancestors 'self';
form-action 'self';
base-uri 'self';
upgrade-insecure-requests;
```

**To implement strict CSP:**
1. Move all inline scripts to external .js files
2. Use nonce or hash for inline scripts (if needed)
3. Remove `'unsafe-inline'` and `'unsafe-eval'`
4. Test thoroughly before deploying

---

## Verification Checklist ✅

Before deploying to production, verify:

- [ ] All security headers present in HTTP response
- [ ] HTTPS certificate installed and valid
- [ ] `APP_ENV=production` in .env
- [ ] `APP_DEBUG=false` in .env
- [ ] `SESSION_SECURE_COOKIE=true` in .env
- [ ] HSTS enabled (only after HTTPS verified)
- [ ] CSP not blocking legitimate resources
- [ ] No mixed content errors
- [ ] Security scanner shows A+ rating
- [ ] Application functions correctly with headers
- [ ] Rate limiting working
- [ ] CSRF protection working

---

## Maintenance Schedule 📅

### Weekly
- [ ] Review security logs for anomalies
- [ ] Check for failed authentication attempts
- [ ] Monitor rate limit violations

### Monthly
- [ ] Update dependencies (`composer update`)
- [ ] Review security advisories
- [ ] Test backup restore procedures

### Quarterly
- [ ] Run full security audit
- [ ] Update CSP if needed
- [ ] Review and rotate API tokens
- [ ] Penetration testing

### Annually
- [ ] Professional security audit
- [ ] SSL certificate renewal
- [ ] Security training for team
- [ ] Disaster recovery drill

---

## Additional Resources 📚

- **OWASP Security Headers:** https://owasp.org/www-project-secure-headers/
- **Content Security Policy:** https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
- **Laravel Security:** https://laravel.com/docs/security
- **Mozilla Observatory:** https://observatory.mozilla.org/
- **Security Headers Analyzer:** https://securityheaders.com/

---

**Status:** Ready for Implementation  
**Priority:** CRITICAL  
**Time Required:** 15-20 minutes  
**Expected Outcome:** Security score A+ (100%) 🎉
