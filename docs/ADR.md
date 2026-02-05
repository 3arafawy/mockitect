# Architecture Decision Records

This document records all architectural decisions for Mockitect, with rationale and implementation notes.

## GitHub Flow for Mockitect

### Branching Strategy

```
master (production-ready)
  │
  ├── feature/database-setup
  ├── feature/matcher-system
  ├── feature/response-builder
  └── feature/inertia-ui
```

**Rules:**
- `master` branch is **always deployable**
- All work happens in **feature branches** (`feature/...`)
- Feature branches are **short-lived** (1-3 days)
- All changes go through **Pull Requests**
- Documentation is versioned with code

### Feature Branch Workflow

```bash
# Create feature branch from master
git checkout master
git pull origin master
git checkout -b feature/database-setup

# Work on feature + update docs
# - Implement code changes
# - Update relevant ADRs
# - Update PLAN.md
# - Write/update tests

# Commit with clear messages
git add .
git commit -m "feat: add migrations for mocks table"
git commit -m "docs: update ADR-005 with implementation notes"

# Push and create Pull Request
git push origin feature/database-setup
```

---

## ADR-001: Local-First Architecture

**Status:** Accepted | **Implemented:** Phase 1 | **PR:** N/A

### Context
Mockitect is a local development tool for mocking external API integrations. Architecture must support running entirely on a developer's machine.

### Decision
Adopt local-first, containerized architecture using Laravel Sail.

### Rationale
- Zero external dependencies
- Works offline
- Single-file configuration
- Easy backup/restore
- No cloud lock-in

### Consequences
✅ Instant startup, offline, private  
⚠️ Single-node (no multi-user until Phase 6)

---

## ADR-002: SQLite as Primary Database

**Status:** Accepted | **Implemented:** Phase 1 Day 1 | **PR:** #TODO

### Context
Need a database that works seamlessly in a local containerized environment.

### Decision
Use SQLite as default database with PostgreSQL migration path.

### Rationale
- Zero configuration (single file)
- 50,000+ reads/sec (exceeds mocking needs)
- ACID compliant
- Laravel provides excellent abstraction

### Migration Path (when needed)
```php
// Architecture rule: NEVER use database-specific features
// 1. Add postgres service to docker-compose
// 2. Change DB_CONNECTION=pgsql in .env
// 3. php artisan migrate:fresh
// 4. php artisan mockitect:import backup.json
```

### Performance Thresholds
Consider PostgreSQL only if:
- >10,000 stored mocks
- >100 concurrent users
- Complex analytical queries

### Consequences
✅ Zero setup, portable, JSON support on both  
⚠️ Write concurrency limited (not an issue for mocking)

---

## ADR-003: Strategy Pattern for Request Matching

**Status:** Accepted | **Implemented:** Phase 1 Day 2 | **PR:** #TODO

### Context
Need to match HTTP requests against mocks using various criteria (path, method, headers, body). Matching logic must be extensible.

### Decision
Implement Strategy Pattern with `RequestMatcherInterface`.

### Implementation
```php
interface RequestMatcherInterface {
    public function matches(Request $request, array $rule): bool;
    public function specificityScore(array $rule): int;
    public function type(): string;
}
```

### Rationale
- Clean separation of concerns
- Easy to add new matchers
- Testable in isolation
- Open/Closed Principle

### Future Matchers (Phase 2+)
- BodyMatcher (JSONPath, XPath)
- QueryParamMatcher
- CookieMatcher
- CompositeMatcher (AND/OR)

---

## ADR-004: Priority + Specificity Matching Algorithm

**Status:** Accepted | **Implemented:** Phase 1 Day 3 | **PR:** #TODO

### Context
Multiple mocks may match same request. Need deterministic rules to select "best" match.

### Decision
Combined explicit priority + calculated specificity score.

### Algorithm
```php
/**
 * Priority Resolution:
 * 
 * 1. Explicit Priority (user-defined integer, higher wins)
 * 2. Specificity Score:
 *    - Exact path: +100
 *    - Regex path: +50
 *    - Wildcard path: +10
 *    - Specific method: +20
 *    - Header matchers: +10 each
 *    - Body matchers: +30 each
 *    - Query param matchers: +5 each
 * 3. Creation date (newest wins if tied)
 */
```

### Example
```
Mock A: POST /users (exact) + JSON body = 100 + 20 + 30 = 150
Mock B: POST /users (exact) = 100 + 20 = 120
→ Mock A wins (more specific)
```

---

## ADR-005: JSON Column Flexibility

**Status:** Accepted | **Implemented:** Phase 1 Day 1 | **PR:** #TODO

### Context
Mock configurations have flexible, nested structures that evolve over time.

### Decision
Store dynamic configuration in JSON columns.

### Implementation
```php
protected $casts = [
    'match_rules' => 'array',
    'response_config' => 'array',
    'state_machine' => 'array',
];
```

### Rationale
- Flexibility to add matchers without migrations
- Works identically on SQLite and PostgreSQL
- Laravel's `whereJsonContains()` works on both
- Perfect for dynamic configurations

### Schema Examples
```php
// match_rules
[
    ['type' => 'path', 'matcher' => 'exact', 'value' => '/api/users'],
    ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET']
]

// response_config
{
    'type': 'static',
    'status': 200,
    'headers': {'Content-Type': 'application/json'},
    'body': '{"users": []}'
}
```

---

## ADR-006: Event-Driven Architecture

**Status:** Accepted | **Implemented:** Phase 1 Day 4 | **PR:** #TODO

### Context
Need extension points for logging, metrics, and future features without modifying core code.

### Decision
Use Laravel Events for all extension points.

### Key Events
- `MockRequestMatched`: When a request matches
- `MockRequestNotMatched`: When no mock matches
- `ScenarioStateChanged`: Scenario transition
- `MockCreated/Updated/Deleted`: CRUD operations

### Future Listeners (Phase 4+)
- Metrics collection (Prometheus, StatsD)
- Webhook notifications
- Real-time updates (WebSocket)

---

## ADR-007: Admin UI Namespace

**Status:** Accepted | **Implemented:** Phase 1 Day 6 | **PR:** #TODO

### Context
Admin UI routes must coexist with mock routes without conflict.

### Decision
Use `/__mockitect` (double underscore prefix) for all admin routes.

### Implementation
```php
// routes/mockitect.php - loaded FIRST
Route::prefix('__mockitect')->name('mockitect.')->group(function() {
    Route::get('/', [DashboardController::class, 'index']);
    Route::resource('mocks', MockController::class);
});

// routes/web.php - loaded LAST
Route::any('{path}', [MockRequestHandler::class, 'handle'])
    ->where('path', '.*');
```

---

## ADR-008: Synchronous Request Logging (Phase 1)

**Status:** Accepted | **Implemented:** Phase 1 Day 4 | **Future:** Async in Phase 4

### Context
Request logging is critical for debugging but adds overhead.

### Decision
Use synchronous logging in Phase 1, migrate to async in Phase 4 if needed.

### Phase 1 Implementation
```php
RequestLog::create([
    'mock_id' => $mock?->id,
    'request_data' => [...],
    'response_data' => [...],
    'response_time_ms' => $elapsed,
]);
```

### Performance Thresholds for Async
- >100 requests/second sustained
- Request logs table >1M rows
- Users report slowness

---

## ADR-009: Pest Testing Framework

**Status:** Accepted | **Implemented:** Phase 1 Day 1 | **PR:** #TODO

### Context
Need a testing framework that balances readability with power.

### Decision
Use Pest v3 (Laravel 12's default) exclusively.

### Why Pest?
```php
// Pest (clean, readable)
it('matches exact path', function () {
    $matcher = new PathMatcher();
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'value' => '/api/users'];
    expect($matcher->matches($request, $rule))->toBeTrue();
});

// vs PHPUnit (verbose)
public function test_it_matches_exact_path() {
    $matcher = new PathMatcher();
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'value' => '/api/users'];
    $this->assertTrue($matcher->matches($request, $rule));
}
```

---

## ADR-010: Vue 3 + Inertia.js Frontend

**Status:** Accepted | **Implemented:** Phase 1 Day 6 | **PR:** #TODO

### Context
Need a modern, reactive UI for managing mocks without building a full SPA API.

### Decision
Use Vue 3 with Inertia.js and Tailwind CSS v4.

### Rationale
- SPA-like UX without API overhead
- Server-side rendered pages with reactive components
- Leverages existing Laravel backend
- Tailwind v4 is modern and fast

---

## ADR Index

| ADR | Title | Status | Phase |
|-----|-------|--------|-------|
| ADR-001 | Local-First Architecture | Accepted | Phase 1 |
| ADR-002 | SQLite as Primary Database | Accepted | Phase 1 |
| ADR-003 | Strategy Pattern for Request Matching | Accepted | Phase 1 |
| ADR-004 | Priority + Specificity Algorithm | Accepted | Phase 1 |
| ADR-005 | JSON Column Flexibility | Accepted | Phase 1 |
| ADR-006 | Event-Driven Architecture | Accepted | Phase 1 |
| ADR-007 | Admin UI Namespace | Accepted | Phase 1 |
| ADR-008 | Synchronous Request Logging | Accepted | Phase 1 |
| ADR-009 | Pest Testing Framework | Accepted | Phase 1 |
| ADR-010 | Vue 3 + Inertia.js Frontend | Accepted | Phase 1 |

---

## Maintenance

### Adding New ADRs
1. Use template: Status, Implemented, Context, Decision, Rationale, Consequences
2. Get review from team
3. Update ADR index
4. Reference in implementation PR

### Updating Existing ADRs
1. Add **Implementation** section with PR link
2. Update **Status** if deprecated/superseded
3. Add **Implementation Notes** if needed
4. Update ADR index

### Marking ADRs as Deprecated
```markdown
**Status:** Deprecated
**Superseded by:** ADR-XXX
**Deprecation Reason:** [Explain why]
```
