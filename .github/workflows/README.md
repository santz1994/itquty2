# GitHub Actions Workflows

This directory contains automated CI/CD workflows for the ITQuty project.

## Workflows Overview

### 1. `automated-tests.yml` - Full Test Suite
**Triggers:**
- Push to `master`, `develop`, or `staging` branches
- Pull requests to `master` or `develop`
- Daily schedule (2 AM UTC)
- Manual trigger

**Jobs:**
1. **API Tests (2-3 minutes)** - Fast feature tests, runs on PHP 8.1 and 8.2
2. **Browser Tests (8-10 minutes)** - E2E tests with Laravel Dusk, only runs if API tests pass
3. **Test Summary** - Generates comprehensive test report
4. **Notify on Failure** - Sends notification if tests fail on master

**Target Metrics:**
- Success Rate: >95%
- False Positive Rate: <5%
- Total Tests: 30 (15 API + 15 Browser)
- Total Assertions: 145+

### 2. `quick-tests.yml` - Fast API Tests Only
**Triggers:**
- Push to `develop`, `feature/**`, or `bugfix/**` branches
- Pull requests to `develop`

**Jobs:**
1. **Quick API Tests (2 minutes)** - Only runs fast API tests for quick feedback

**Use Case:** Work-in-progress commits and draft PRs

## Workflow Execution Flow

```
Push/PR Event
    ↓
API Tests (PHP 8.1 & 8.2)
    ↓ (if pass)
Browser Tests (E2E)
    ↓
Test Summary
    ↓
PR Comment (if PR)
    ↓
Notify (if failure on master)
```

## Test Coverage

### API Tests (Feature Tests)
- ✅ Authentication (login/logout)
- ✅ Ticket CRUD + validation
- ✅ Asset CRUD + QR scanner
- ✅ Asset Request workflow
- ✅ User Management
- ✅ Authorization checks
- ✅ Dashboard loading
- ✅ Search functionality
- ✅ Notification API
- ✅ Audit logs

### Browser Tests (E2E Tests)
- ✅ Full user workflows
- ✅ JavaScript interactions
- ✅ Timer functionality
- ✅ QR scanner
- ✅ Search autocomplete
- ✅ Notification dropdown
- ✅ Responsive design (mobile)
- ✅ Button hover effects
- ✅ Color palette & badges
- ✅ Performance (page load <3s)

## Test Artifacts

When tests fail, the following artifacts are automatically uploaded:

### API Test Failures
- `storage/logs/` - Laravel application logs

### Browser Test Failures
- `tests/Browser/screenshots/` - Screenshots of failed tests
- `tests/Browser/console/` - Browser console logs
- `storage/logs/` - Laravel application logs

**Retention:** 7 days

## GitHub Actions Cache

Composer dependencies are cached to speed up workflow execution:

```yaml
Cache Key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
```

Cache is invalidated when `composer.lock` changes.

## Environment Configuration

### API Tests
```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Browser Tests
```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## Status Badges

Add these badges to your README.md:

```markdown
![Automated Tests](https://github.com/santz1994/itquty/actions/workflows/automated-tests.yml/badge.svg)
![Quick Tests](https://github.com/santz1994/itquty/actions/workflows/quick-tests.yml/badge.svg)
```

## Pull Request Integration

When a PR is created:

1. **Quick Tests** run immediately (2 min) for fast feedback
2. **Full Tests** run when ready for merge
3. **Automated comment** is posted with test results:
   ```
   ## ✅ Automated Test Results
   
   Status: PASSED
   
   | Test Suite | Status |
   |------------|--------|
   | API Tests | ✅ PASSED |
   | Browser Tests | ✅ PASSED |
   
   ✅ Ready to merge!
   ```

## Manual Trigger

You can manually trigger workflows from the Actions tab:

1. Go to **Actions** tab
2. Select workflow (e.g., "Automated Tests")
3. Click **Run workflow**
4. Select branch
5. Click **Run workflow** button

## Local Development

To run tests locally before pushing:

```bash
# API tests (fast)
php artisan test tests/Feature/ApiAutomatedTest.php

# Browser tests (thorough)
php artisan dusk tests/Browser/ComprehensiveAutomatedTest.php

# All tests
php artisan test && php artisan dusk
```

## Troubleshooting

### Tests failing on CI but pass locally?

1. **Cache issues**: Clear GitHub Actions cache
2. **Environment differences**: Check `.env.dusk.local` vs CI config
3. **Database state**: Ensure migrations are up to date
4. **ChromeDriver version**: CI uses auto-detect, may differ from local

### Tests timing out?

- API tests timeout: 10 minutes (increase in workflow if needed)
- Browser tests timeout: 20 minutes
- Individual test timeout: Configured in PHPUnit

### False positives?

Check test logs and screenshots in artifacts:
1. Go to failed workflow run
2. Scroll to bottom
3. Download artifacts
4. Review screenshots and console logs

## Monitoring

### Success Rate Tracking

Review the daily scheduled test runs to track:
- Overall success rate (target: >95%)
- False positive rate (target: <5%)
- Execution time trends
- Flaky tests

### Notification Setup

To receive notifications on test failures, add your service:

```yaml
# Example: Slack notification
- name: Send Slack notification
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    webhook_url: ${{ secrets.SLACK_WEBHOOK }}
  if: failure()
```

## Best Practices

1. ✅ Run API tests locally before pushing
2. ✅ Let CI run browser tests (resource-intensive)
3. ✅ Review test artifacts on failures
4. ✅ Keep tests fast and focused
5. ✅ Maintain <5% false positive rate
6. ✅ Update tests when adding features
7. ✅ Don't merge with failing tests

## Contributing

When adding new tests:

1. Add to appropriate test file (API or Browser)
2. Ensure test is deterministic (no random failures)
3. Use unique test data (timestamps)
4. Clean up after test (in tearDown)
5. Document expected success rate
6. Run locally 5+ times to verify stability

## Support

For issues with GitHub Actions:
- Check workflow logs
- Review artifacts
- Consult `.github/workflows/README.md`
- Contact DevOps team

---

**Last Updated:** October 16, 2025
**Maintained By:** ITQuty Development Team
