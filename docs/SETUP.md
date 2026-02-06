# Setup Guide

Complete guide for setting up and running Mockitect in a containerized environment.

## Prerequisites

- **Docker Desktop** 4.0+ (Mac/Windows) or **Docker Engine** 20.10+ (Linux)
- **Docker Compose** 2.0+
- **Git**

No local installation of PHP, Node.js, Composer, or database servers required.

## Quick Setup

### 1. Clone Repository

```bash
git clone https://github.com/3arafawy/mockitect.git
cd mockitect
```

### 2. Start Services

```bash
# Start all services in detached mode
./vendor/bin/sail up -d

# Or start with build (first time or after Dockerfile changes)
./vendor/bin/sail up -d --build
```

### 3. Initialize Database

```bash
# Run migrations (creates SQLite database)
./vendor/bin/sail artisan migrate

# Optionally seed with sample data
./vendor/bin/sail artisan db:seed
```

### 4. Install Frontend Dependencies

```bash
# Install Node.js packages
./vendor/bin/sail npm install

# Build assets for production
./vendor/bin/sail npm run build

# Or start development server with hot reload
./vendor/bin/sail npm run dev
```

### 5. Verify Installation

```bash
# Run tests
./vendor/bin/sail test

# Check application health
curl http://localhost/__mockitect
```

Access the application at: **http://localhost**

## Daily Development

### Start Working

```bash
# Start services
./vendor/bin/sail up -d

# Watch frontend changes (terminal 1)
./vendor/bin/sail npm run dev

# Run tests (terminal 2)
./vendor/bin/sail test
```

### Stop Working

```bash
# Stop services
./vendor/bin/sail down

# Stop and remove volumes (database reset)
./vendor/bin/sail down -v
```

## Testing

### Run All Tests

```bash
./vendor/bin/sail test
```

Expected output:
```
   PASS  Tests\Unit\Models\MockTest
   PASS  Tests\Unit\Models\ScenarioTest
   PASS  Tests\Unit\Models\RequestLogTest
   PASS  Tests\Unit\Matchers\PathMatcherTest
   PASS  Tests\Unit\Matchers\MethodMatcherTest
   PASS  Tests\Unit\Matchers\HeaderMatcherTest
   PASS  Tests\Unit\Services\MockMatchingServiceTest
   PASS  Tests\Feature\MockRequestHandlerTest
   PASS  Tests\Feature\Commands\MockitectListCommandTest
   PASS  Tests\Feature\Commands\MockitectImportCommandTest
   PASS  Tests\Feature\Phase1SuccessTest

  Tests:  45 passed
```

### Run Specific Tests

```bash
# Unit tests only
./vendor/bin/sail test --testsuite=Unit

# Feature tests only
./vendor/bin/sail test --testsuite=Feature

# Specific test file
./vendor/bin/sail test tests/Feature/Phase1SuccessTest.php

# Specific test method
./vendor/bin/sail test --filter=it_handles_complete_mock_flow
```

### Test Coverage

```bash
# Generate coverage report (requires Xdebug)
./vendor/bin/sail test --coverage

# HTML coverage report
./vendor/bin/sail test --coverage-html coverage
```

## CLI Commands

All artisan commands are available via Sail:

### Mock Management

```bash
# List all mocks
./vendor/bin/sail artisan mockitect:list

# List with filters
./vendor/bin/sail artisan mockitect:list --active
./vendor/bin/sail artisan mockitect:list --inactive

# Create mock interactively
./vendor/bin/sail artisan mockitect:create

# Import from JSON file
./vendor/bin/sail artisan mockitect:import path/to/mocks.json
```

### Database

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Fresh migration (reset database)
./vendor/bin/sail artisan migrate:fresh

# Rollback last migration
./vendor/bin/sail artisan migrate:rollback

# Check migration status
./vendor/bin/sail artisan migrate:status
```

### Cache & Optimization

```bash
# Clear caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear

# Optimize for production
./vendor/bin/sail artisan optimize
```

## Project Structure

```
mockitect/
├── app/
│   ├── Console/Commands/          # CLI commands
│   │   ├── MockitectListCommand.php
│   │   ├── MockitectCreateCommand.php
│   │   └── MockitectImportCommand.php
│   ├── Contracts/                 # Interfaces
│   │   └── RequestMatcherInterface.php
│   ├── Events/                    # Laravel events
│   │   ├── MockRequestMatched.php
│   │   └── MockRequestNotMatched.php
│   ├── Http/Controllers/
│   │   ├── MockRequestHandler.php       # Main request handler
│   │   └── Mockitect/                   # Admin UI controllers
│   │       ├── DashboardController.php
│   │       ├── MockController.php
│   │       └── RequestLogController.php
│   ├── Listeners/                 # Event listeners
│   │   └── LogMockRequestListener.php
│   ├── Models/                    # Eloquent models
│   │   ├── Mock.php
│   │   ├── Scenario.php
│   │   └── RequestLog.php
│   ├── Services/                  # Business logic
│   │   ├── Matchers/              # Strategy Pattern matchers
│   │   │   ├── PathMatcher.php    # exact, prefix, regex, wildcard
│   │   │   ├── MethodMatcher.php  # exact, any
│   │   │   └── HeaderMatcher.php  # exact, contains, regex, exists
│   │   ├── MockMatchingService.php
│   │   └── ResponseBuilderService.php
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── database.sqlite           # SQLite database (auto-created)
│   ├── factories/                # Model factories
│   └── migrations/               # Database migrations
├── docs/
│   ├── ADR.md                    # Architecture Decision Records
│   ├── PLAN.md                   # Development roadmap
│   └── SETUP.md                  # This file
├── resources/
│   ├── js/Pages/Mockitect/       # Vue 3 components
│   │   ├── Dashboard.vue
│   │   ├── Mocks/
│   │   │   ├── Index.vue
│   │   │   ├── Create.vue
│   │   │   └── Edit.vue
│   │   └── RequestLogs/
│   │       └── Index.vue
│   └── views/
├── routes/
│   ├── web.php                   # Catch-all route for mocks
│   └── mockitect.php             # Admin UI routes
├── storage/
├── tests/
│   ├── Feature/                  # Feature tests
│   └── Unit/                     # Unit tests
├── composer.json
├── compose.yaml                  # Docker Compose config
├── package.json
├── phpunit.xml
└── vite.config.js
```

## Configuration

### Environment Variables

Key variables in `.env`:

```env
APP_NAME=Mockitect
APP_ENV=local
APP_DEBUG=true

# Database (SQLite - zero configuration)
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### Customizing Ports

Edit `.env` to change ports:

```env
APP_PORT=8080          # Change from default 80
VITE_PORT=3000         # Change Vite dev server port
```

Then restart:
```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### Database Persistence

The SQLite database is stored in `database/database.sqlite` and persisted in the container volume. To reset:

```bash
# Delete and recreate database
rm database/database.sqlite
./vendor/bin/sail artisan migrate
```

## Phase 1 Validation

To verify Phase 1 is working correctly:

```bash
# Run the Phase 1 success criteria test
./vendor/bin/sail test tests/Feature/Phase1SuccessTest.php
```

Expected output:
```
PASS  Tests\Feature\Phase1SuccessTest
✓ it handles complete mock flow
✓ it can create mock via UI endpoint
✓ it can access admin dashboard
✓ it can list mocks via CLI command
✓ it matches requests with priority resolution
✓ it logs unmatched requests

Tests:  6 passed
```

## Troubleshooting

### Permission Issues

```bash
# Fix ownership (Linux/Mac)
./vendor/bin/sail bash
chown -R sail:sail /var/www/html/storage
chown -R sail:sail /var/www/html/bootstrap/cache
exit
```

### Port Conflicts

```bash
# If port 80 is in use, change in .env
APP_PORT=8080
./vendor/bin/sail up -d
```

### Container Won't Start

```bash
# Rebuild containers
./vendor/bin/sail down
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

### Database Locked

```bash
# Restart services
./vendor/bin/sail down
./vendor/bin/sail up -d

# Or remove database and recreate
rm database/database.sqlite
./vendor/bin/sail artisan migrate
```

### Frontend Build Issues

```bash
# Clear npm cache
./vendor/bin/sail npm cache clean --force

# Reinstall dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### Tests Failing

```bash
# Ensure fresh database
./vendor/bin/sail artisan migrate:fresh

# Run tests with verbose output
./vendor/bin/sail test --verbose

# Run specific failing test
./vendor/bin/sail test --filter=test_name
```

## Advanced Usage

### Running Artisan Commands

```bash
# Any artisan command
./vendor/bin/sail artisan [command]

# Examples:
./vendor/bin/sail artisan route:list
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan queue:work
```

### Running Composer Commands

```bash
./vendor/bin/sail composer [command]

# Examples:
./vendor/bin/sail composer install
./vendor/bin/sail composer update
./vendor/bin/sail composer require package/name
```

### Running NPM Commands

```bash
./vendor/bin/sail npm [command]

# Examples:
./vendor/bin/sail npm install
./vendor/bin/sail npm update
./vendor/bin/sail npm run build
./vendor/bin/sail npm run dev
```

### Shell Access

```bash
# Enter container shell
./vendor/bin/sail bash

# Run commands inside container
php artisan migrate
exit
```

### Viewing Logs

```bash
# Application logs
./vendor/bin/sail logs

# Follow logs in real-time
./vendor/bin/sail logs -f

# View specific service
./vendor/bin/sail logs laravel.test
```

## Production Deployment

For production deployment:

1. Copy `.env.example` to `.env` and configure
2. Set `APP_ENV=production`
3. Set `APP_DEBUG=false`
4. Generate application key: `./vendor/bin/sail artisan key:generate`
5. Run migrations: `./vendor/bin/sail artisan migrate --force`
6. Build assets: `./vendor/bin/sail npm run build`
7. Optimize: `./vendor/bin/sail artisan optimize`

## Git Workflow

### Committing Changes

```bash
# Check status
git status

# Add changes
git add .

# Commit with clear message
git commit -m "feat: add new matcher type

- Implement JSON body matcher
- Add tests for new matcher
- Update documentation"

# Push to feature branch
git push origin feature/matcher-json-body
```

### Before Committing

Always run tests and code quality checks:

```bash
# Run tests
./vendor/bin/sail test

# Format code
./vendor/bin/sail pint

# Check for errors
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan config:clear
```

## Next Steps

After successful setup:

1. **Read ADRs**: Review architecture decisions in `docs/ADR.md`
2. **Check PLAN**: View roadmap in `docs/PLAN.md`
3. **Create First Mock**: Use CLI or web UI
4. **Run Tests**: Ensure everything works
5. **Start Development**: Create feature branches

## Support

- **Laravel Docs**: https://laravel.com/docs/12.x
- **Laravel Sail**: https://laravel.com/docs/12.x/sail
- **Pest Testing**: https://pestphp.com/docs
- **Inertia.js**: https://inertiajs.com/
- **Vue 3**: https://vuejs.org/

---

<p align="center">
  Questions? Check the <a href="../README.md">README</a> or open an issue.
</p>
