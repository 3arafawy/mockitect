# Mockitect

> Local-first HTTP mocking service. Intercept requests, return configured responses.

This is a **documentation-first** project. Every architectural decision, development plan, and setup procedure is carefully documented before implementation. See [docs/ADR.md](docs/ADR.md) for our architectural principles.

## Philosophy

- **Local-first**: Runs entirely on your machine, zero external dependencies
- **Documentation-driven**: Architecture and design are documented before code
- **Containerized**: Single-command setup via Docker, no local toolchain required
- **Test-driven**: Comprehensive test coverage with Pest

## Quick Start

Mockitect runs entirely in Docker containers. No local PHP or Node.js installation required.

```bash
# Clone the repository
git clone https://github.com/3arafawy/mockitect.git
cd mockitect

# Start the application (Docker)
./vendor/bin/sail up -d

# Run database migrations
./vendor/bin/sail artisan migrate

# Install frontend dependencies (first time only)
./vendor/bin/sail npm install

# Build frontend assets
./vendor/bin/sail npm run build

# Access the application
open http://localhost/__mockitect
```

**Use it:** Point your application to `http://localhost` instead of real APIs. Mockitect intercepts HTTP requests and returns configured mock responses.

## Features

### ✅ Phase 1 - Foundation (Complete)

- **Request Matching**: Path (exact, prefix, regex, wildcard), HTTP Method, Headers
- **Static Responses**: JSON, plain text, HTML with configurable status codes
- **Request Logging**: Full request/response capture with timing metrics
- **Admin UI**: Vue 3 + Inertia.js dashboard for managing mocks
- **CLI Commands**: List, create, and import mocks via artisan commands
- **Priority System**: Explicit priority + automatic specificity scoring algorithm
- **Event-Driven**: Extensible architecture with Laravel events

### 🚧 Phase 2 - Powerful Responses (Planned)

- Dynamic templates with Faker integration
- Stateful scenarios with state machines
- Proxy mode & response recording
- Import/Export (Postman collections, OpenAPI/Swagger)

See [docs/PLAN.md](docs/PLAN.md) for full development roadmap.

## Documentation

| Document | Purpose |
|----------|---------|
| [docs/SETUP.md](docs/SETUP.md) | Detailed setup, testing, and development guide |
| [docs/ADR.md](docs/ADR.md) | Architecture Decision Records |
| [docs/PLAN.md](docs/PLAN.md) | Development phases and task tracking |

## Usage

### Web UI

Access the admin dashboard at `http://localhost/__mockitect`

- View dashboard with stats and recent activity
- Create, edit, and delete mocks
- Browse request logs
- Configure match rules and responses

### CLI Commands

```bash
# List all mocks
./vendor/bin/sail artisan mockitect:list

# List only active mocks
./vendor/bin/sail artisan mockitect:list --active

# Create mock interactively
./vendor/bin/sail artisan mockitect:create

# Import mocks from JSON file
./vendor/bin/sail artisan mockitect:import mocks.json
```

### API Usage

Once a mock is configured, make requests to any path:

```bash
# Example: Mock responds to GET /api/users
curl http://localhost/api/users
```

## Development

### Running Tests

```bash
# Run all tests
./vendor/bin/sail test

# Run specific test suite
./vendor/bin/sail test --testsuite=Feature

# Run specific test file
./vendor/bin/sail test tests/Feature/Phase1SuccessTest.php

# Run with coverage (requires Xdebug)
./vendor/bin/sail test --coverage
```

### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/sail pint

# Run static analysis (if configured)
./vendor/bin/sail phpstan analyse
```

### Development Mode

```bash
# Start services
./vendor/bin/sail up -d

# Watch for frontend changes
./vendor/bin/sail npm run dev
```

## Architecture

### Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue 3, Inertia.js, Tailwind CSS v4
- **Database**: SQLite (zero configuration)
- **Container**: Docker via Laravel Sail
- **Testing**: Pest v3
- **Build**: Vite

### Core Components

```
app/
├── Contracts/              # Interfaces
│   └── RequestMatcherInterface.php
├── Services/
│   ├── Matchers/          # Strategy Pattern matchers
│   │   ├── PathMatcher.php      # exact, prefix, regex, wildcard
│   │   ├── MethodMatcher.php    # exact, any
│   │   └── HeaderMatcher.php    # exact, contains, regex, exists
│   ├── MockMatchingService.php   # Priority + specificity algorithm
│   └── ResponseBuilderService.php
├── Http/Controllers/
│   ├── MockRequestHandler.php    # Main request handler (catch-all)
│   └── Mockitect/                # Admin UI controllers
├── Events/                # Laravel events
├── Listeners/             # Event listeners
└── Console/Commands/      # CLI commands
```

## Git Workflow

We follow [GitHub Flow](https://docs.github.com/en/get-started/quickstart/github-flow):

1. Create feature branch from `master`
2. Make changes with tests
3. Create Pull Request
4. Code review
5. Merge to `master`

```bash
# Create feature branch
git checkout -b feature/name

# Work on feature
# ... make changes, write tests ...

# Run tests
./vendor/bin/sail test

# Commit
git add .
git commit -m "feat: description"

# Push and create PR
git push origin feature/name
```

## Requirements

- Docker Desktop 4.0+ or Docker Engine 20.10+
- Docker Compose 2.0+
- Git

No local PHP, Node.js, or database installation required.

## License

MIT

---

<p align="center">
  Built with Laravel Sail · Local-first · Zero external dependencies
</p>
