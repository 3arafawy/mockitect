# Mockitect

> Local-first HTTP mocking service built on Laravel 12.

## What is Mockitect?

Mockitect intercepts HTTP requests and returns configured responses. Change your application's external API host to Mockitect during development and test integration scenarios without relying on real services.

**Perfect for:**
- Testing API integrations locally
- Simulating third-party service failures
- Prototyping against non-existent APIs
- Debugging request/response cycles

## Quick Start

### Prerequisites
- Docker
- PHP 8.2+
- Node.js 18+

### Installation

```bash
# Clone repository
git clone https://github.com/3arafawy/mockitect.git
cd mockitect

# Install dependencies
composer install
npm install

# Start with Laravel Sail
./vendor/bin/sail up

# Run migrations
./vendor/bin/sail artisan migrate

# Build frontend
npm run dev
```

### Basic Usage

```bash
# Create a mock via CLI
./vendor/bin/sail artisan mockitect:create

# Or visit the UI at http://localhost/__mockitect

# Mock an endpoint: GET /api/users → 200 {"users": []}

# Point your app to http://localhost instead of the real API
# Request http://localhost/api/users
# Receive: {"users": []}
```

## Features

### Phase 1 (In Progress)
- 🔄 RESTful request matching (path, method, headers)
- 🔄 Static JSON/XML/text responses
- 🔄 Request logging and inspection
- 🔄 Web-based UI
- 🔄 CLI commands

### Phase 2 (Planned)
- Dynamic response templates with Faker
- JSONPath body matching
- Scenarios (stateful mocking)
- Proxy mode and recording
- File-based responses
- Failure simulation

### Phase 3+ (Future)
- Request diff and replay
- Import/Export (Postman, OpenAPI)
- Collections and organization
- Advanced CLI features

## Documentation

| Document | Description |
|----------|-------------|
| [ADR.md](docs/ADR.md) | Architecture Decision Records - all technical decisions with rationale |
| [PLAN.md](docs/PLAN.md) | Development Plan - phases, tasks, and implementation details |

## Architecture

```
HTTP Request → MockMatchingService → ResponseBuilderService → Response
                   ↓                                            ↓
              Matchers (path,                              Request Logging
                        method,
                        headers)
```

**Key Design Decisions:**
- **SQLite** - Zero-config, portable, fast enough for local dev
- **Strategy Pattern** - Extensible matchers without core changes
- **Event-Driven** - Clean hooks for logging, metrics, webhooks
- **Priority + Specificity** - Deterministic mock selection

See [ADR.md](docs/ADR.md) for complete architectural rationale.

## Development

### Getting Started

```bash
# Install dependencies
composer install
npm install

# Run tests
./vendor/bin/sail test

# Format code
./vendor/bin/sail pint

# Start dev server
./vendor/bin/sail up
npm run dev
```

### Branching Strategy

We follow **GitHub Flow**:

```
main (always deployable)
  ├── feature/database-setup
  ├── feature/matcher-system
  └── feature/inertia-ui
```

**Rules:**
- All work in feature branches
- Branches are short-lived (1-3 days)
- All changes via Pull Requests
- Docs updated before merge

### Creating a Feature Branch

```bash
git checkout main
git pull origin main
git checkout -b feature/your-feature-name

# Make changes, update docs, write tests
git add .
git commit -m "feat: your change"
git push origin feature/your-feature-name

# Create Pull Request
```

See [PLAN.md](docs/PLAN.md) for detailed task breakdown.

## CLI Reference

```bash
# List all mocks
php artisan mockitect:list

# List only active mocks
php artisan mockitect:list --active

# Create mock interactively
php artisan mockitect:create

# Import mocks from file
php artisan mockitect:import mocks.json

# Export all mocks
php artisan mockitect:export

# View recent request logs
php artisan mockitect:logs
```

## Example Workflow

### Scenario: Mocking a User API

```bash
# 1. Create mock
php artisan mockitect:create

# Follow prompts:
# Name: Get Users
# Path: /api/users
# Method: GET
# Status: 200
# Body: {"users": [{"id": 1, "name": "John"}]}

# 2. Configure your app to use Mockitect
# Change API base URL to http://localhost

# 3. Make requests from your app
# GET http://localhost/api/users
# Response: {"users": [{"id": 1, "name": "John"}]}

# 4. View logs at http://localhost/__mockitect/logs
```

### Scenario: Testing Error Handling

```bash
# Create mock for 500 error
php artisan mockitect:create

# Configure:
# Path: /api/external/critical
# Method: POST
# Status: 500
# Body: {"error": "Service unavailable"}

# Test your app's error handling
```

## Technology Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3, Inertia.js, Tailwind CSS v4
- **Database:** SQLite (migratable to PostgreSQL)
- **Container:** Laravel Sail (Docker)
- **Testing:** Pest v3

## Contributing

1. Read [ADR.md](docs/ADR.md) for architecture decisions
2. Check [PLAN.md](docs/PLAN.md) for current tasks
3. Create feature branch from `main`
4. Write tests (Pest)
5. Update documentation
6. Open Pull Request

## Roadmap

### Phase 1 (Week 1)
- Foundation: database, matchers, response builder, basic UI

### Phase 2 (Weeks 2-3)
- Powerful responses: templates, scenarios, proxy, failures

### Phase 3 (Weeks 4-5)
- Developer experience: inspector, import/export, collections

### Phase 4 (Week 6)
- Performance: caching, concurrency, data integrity

### Phase 5 (Weeks 7-8)
- Advanced protocols: queues, gRPC, WebSockets

See [PLAN.md](docs/PLAN.md) for detailed breakdown.

## License

MIT License - see LICENSE file for details

## Questions?

- Check [ADR.md](docs/ADR.md) for architecture
- Check [PLAN.md](docs/PLAN.md) for implementation plan
- Open an issue for bugs or feature requests
