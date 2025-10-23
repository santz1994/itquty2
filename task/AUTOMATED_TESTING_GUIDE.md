# Automated Testing Suite Documentation

## Overview
This automated testing suite provides comprehensive end-to-end testing with a target false positive rate of <5%.

## Technology Stack
- **Framework**: Laravel Dusk (Selenium WebDriver)
- **Browser**: Chrome (Headless mode)
- **Database**: SQLite (in-memory for tests)
- **Test Runner**: PHPUnit

## Installation

### Step 1: Install Laravel Dusk
```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

### Step 2: Install ChromeDriver
```bash
php artisan dusk:chrome-driver
```

For Windows, download ChromeDriver manually:
- Visit: https://chromedriver.chromium.org/downloads
- Download version matching your Chrome browser
- Place `chromedriver.exe` in `vendor/laravel/dusk/bin/`

### Step 3: Environment Configuration
Add to `.env.dusk.local`:
```env
APP_URL=http://192.168.1.122
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
DUSK_DRIVER_URL=http://localhost:9515
```

### Step 4: Create Test Database
```bash
php artisan config:clear
php artisan migrate --env=dusk.local
```

## Running Tests

### Run All Automated Tests
```bash
php artisan dusk
```

### Run Specific Test
```bash
php artisan dusk --filter test_01_authentication_and_authorization
```

### Run with Browser Visible (Debug Mode)
```bash
DUSK_HEADLESS_DISABLED=1 php artisan dusk
```

### Run in Parallel (Faster)
```bash
php artisan dusk --parallel
```

## Test Suite Structure

### 15 Comprehensive Tests (104+ Assertions)

| Test # | Test Name | Coverage | Expected Success Rate |
|--------|-----------|----------|----------------------|
| 1 | Authentication & Authorization | Login, logout, role-based menu | 100% |
| 2 | Ticket Management CRUD | Create, view, edit, timer | >95% |
| 3 | Asset Management CRUD | Create, view, QR scanner | >95% |
| 4 | Asset Request Workflow | Create, approve, reject | >95% |
| 5 | User Management CRUD | Create, edit, roles | >95% |
| 6 | Dashboard Loading | KPI cards, widgets | >98% |
| 7 | Search Functionality | Global search, autocomplete | >95% |
| 8 | Notification System | Bell, dropdown, mark as read | >95% |
| 9 | Audit Logs | View, filter, export | >95% |
| 10 | Daily Activities | Create, view, edit | >95% |
| 11 | SLA Management | Policies, dashboard | >95% |
| 12 | Responsive Design | Mobile view, 375px width | >98% |
| 13 | Button Consistency | Button styles, hover | >98% |
| 14 | Color Palette | Badges, accessibility | >98% |
| 15 | Performance | Page load <3s | >90% |

**Overall Target Success Rate: >95%**
**Overall Target False Positive Rate: <5%**

## Test Strategies to Minimize False Positives

### 1. Wait Strategies
```php
// Good - Wait for specific text
$browser->waitForText('Dashboard', 10);

// Good - Wait for element
$browser->waitFor('.kpi-card', 5);

// Bad - Fixed pause (unreliable)
$browser->pause(1000);
```

### 2. Dynamic Data
```php
// Use timestamps to avoid conflicts
$ticketSubject = 'Test Ticket ' . time();
$assetTag = 'TEST-' . time();
```

### 3. Cleanup
```php
// Always clean up test data
protected function tearDown(): void
{
    Ticket::where('subject', 'like', 'Test Ticket%')->delete();
    parent::tearDown();
}
```

### 4. Retry Mechanisms
```php
// Laravel Dusk automatically retries failed assertions for 5 seconds
$browser->assertSee('Expected Text'); // Will retry for 5s
```

### 5. Specific Selectors
```php
// Good - Specific ID
$browser->click('#notification-bell');

// Good - Specific class
$browser->assertVisible('.notification-dropdown.active');

// Bad - Generic selector
$browser->click('button'); // Too ambiguous
```

## Continuous Integration (CI)

### GitHub Actions Workflow
Create `.github/workflows/automated-tests.yml`:

```yaml
name: Automated Tests

on:
  push:
    branches: [ master, develop ]
  pull_request:
    branches: [ master ]
  schedule:
    - cron: '0 0 * * *' # Daily at midnight

jobs:
  dusk-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, dom, fileinfo, sqlite
        
    - name: Install Dependencies
      run: composer install --no-interaction --prefer-dist
      
    - name: Setup Environment
      run: |
        cp .env.example .env.dusk.local
        php artisan key:generate
        
    - name: Install ChromeDriver
      run: php artisan dusk:chrome-driver --detect
      
    - name: Start Chrome Driver
      run: ./vendor/laravel/dusk/bin/chromedriver-linux &
      
    - name: Run Laravel Server
      run: php artisan serve --no-reload &
      
    - name: Run Dusk Tests
      run: php artisan dusk
      
    - name: Upload Screenshots
      if: failure()
      uses: actions/upload-artifact@v3
      with:
        name: screenshots
        path: tests/Browser/screenshots
        
    - name: Upload Console Logs
      if: failure()
      uses: actions/upload-artifact@v3
      with:
        name: console
        path: tests/Browser/console
```

## Monitoring & Reporting

### Test Results Dashboard
After each run, view results:
```bash
php artisan dusk --log-junit=storage/logs/junit.xml
```

### Generate HTML Report
```bash
vendor/bin/phpunit --testdox-html tests/report.html tests/Browser
```

### False Positive Analysis
Create a log to track failures:

```bash
# Run tests with detailed logging
php artisan dusk --verbose > storage/logs/test-results.log 2>&1
```

## Common Issues & Solutions

### Issue 1: Chrome Driver Version Mismatch
**Symptom**: `SessionNotCreatedException`
**Solution**:
```bash
# Check Chrome version
google-chrome --version

# Install matching driver
php artisan dusk:chrome-driver --detect
```

### Issue 2: Element Not Found
**Symptom**: `NoSuchElementException`
**Solution**: Increase wait time
```php
$browser->waitForText('Expected', 15); // Increase to 15s
```

### Issue 3: Database Conflicts
**Symptom**: Duplicate key errors
**Solution**: Use transactions
```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MyTest extends DuskTestCase
{
    use DatabaseTransactions;
}
```

### Issue 4: Slow Tests
**Symptom**: Tests timeout
**Solution**: Run in parallel
```bash
php artisan dusk --parallel --processes=4
```

## Performance Benchmarks

Expected test execution times:
- **Single Test**: 5-30 seconds
- **Full Suite (15 tests)**: 5-10 minutes
- **Parallel (4 processes)**: 2-3 minutes

## Maintenance Schedule

### Daily
- Run full test suite via CI
- Review failed tests
- Update test data if needed

### Weekly
- Review test coverage
- Add new tests for new features
- Update ChromeDriver if needed

### Monthly
- Analyze false positive rate
- Refactor slow tests
- Update documentation

## Test Coverage Report

Generate coverage report:
```bash
php artisan test --coverage
```

Target coverage:
- **Overall**: >80%
- **Critical Paths**: >95%
- **UI Components**: >90%

## Best Practices

### ✅ DO
- Use `waitForText()` instead of `pause()`
- Clean up test data in `tearDown()`
- Use unique identifiers (timestamps)
- Test critical user journeys
- Run tests before each deployment

### ❌ DON'T
- Use fixed `pause()` delays
- Test implementation details
- Hard-code test data IDs
- Skip cleanup in tearDown
- Ignore intermittent failures

## Support

For issues or questions:
1. Check `storage/logs/laravel.log`
2. Check `tests/Browser/console/*.log`
3. Review screenshots in `tests/Browser/screenshots/`
4. Enable debug mode: `DUSK_HEADLESS_DISABLED=1`

## Next Steps

1. ✅ Install Laravel Dusk
2. ✅ Configure ChromeDriver
3. ✅ Run first test
4. ✅ Set up CI pipeline
5. ✅ Monitor false positive rate
6. ✅ Expand test coverage to new features
