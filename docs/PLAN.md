# Development Plan

> **Version:** 1.0 | **Last Updated:** 2026-02-06

## Overview

Mockitect follows a phased approach to building a local-first HTTP mocking service. Each phase delivers working functionality while maintaining architectural integrity.

## GitHub Flow

All development follows strict GitHub Flow:

```
main (always deployable)
  ├── feature/database-setup
  ├── feature/matcher-system
  └── feature/inertia-ui
```

**Rules:**
- `main` is **always production-ready**
- All work in **feature branches** (1-3 days)
- All changes via **Pull Requests**
- Documentation versioned with code

## Technology Stack

| Layer | Technology | Purpose |
|--------|-----------|----------|
| Backend | Laravel 12 | Framework |
| PHP | 8.2+ | Language |
| Frontend | Vue 3 + Inertia.js | UI |
| CSS | Tailwind CSS v4 | Styling |
| Database | SQLite | Storage |
| Container | Laravel Sail | Docker |
| Testing | Pest v3 | Tests |
| Build | Vite | Assets |

---

## Phase 1: Foundation (7 Days)

**Goal:** Prove core concept end-to-end. Functional MVP.

### Day 1: Database + Models
**Branch:** `feature/database-setup`

**Tasks:**
- [ ] Create `mocks` table migration
- [ ] Create `scenarios` table migration
- [ ] Create `request_logs` table migration
- [ ] Create `Mock` model with casts/relationships
- [ ] Create `Scenario` model
- [ ] Create `RequestLog` model
- [ ] Write model tests

**Related ADRs:** ADR-002, ADR-005

### Day 2: Matcher System
**Branch:** `feature/matcher-system`

**Tasks:**
- [ ] Create `RequestMatcherInterface`
- [ ] Implement `PathMatcher` (exact, regex, wildcard)
- [ ] Implement `MethodMatcher`
- [ ] Implement `HeaderMatcher`
- [ ] Write matcher tests

**Related ADRs:** ADR-003, ADR-009

### Day 3: Matching Service
**Branch:** `feature/matching-service`

**Tasks:**
- [ ] Implement `MockMatchingService`
- [ ] Add priority resolution
- [ ] Add specificity scoring
- [ ] Write integration tests

**Related ADRs:** ADR-004

### Day 4: Response Builder + Handler
**Branch:** `feature/response-builder`

**Tasks:**
- [ ] Implement `ResponseBuilderService`
- [ ] Create `MockRequestHandler` controller
- [ ] Add request logging (synchronous)
- [ ] Create events/listeners
- [ ] Write feature tests

**Related ADRs:** ADR-006, ADR-008

### Day 5: CLI Commands
**Branch:** `feature/cli-commands`

**Tasks:**
- [ ] Create `MockitectListCommand`
- [ ] Create `MockitectCreateCommand`
- [ ] Create `MockitectImportCommand`
- [ ] Write command tests

### Day 6: Inertia.js + Basic UI
**Branch:** `feature/inertia-ui`

**Tasks:**
- [ ] Install Inertia.js + Vue 3
- [ ] Create `Dashboard.vue` (list mocks)
- [ ] Create `MockForm.vue` (create/edit)
- [ ] Create `RequestLogs.vue` (view logs)
- [ ] Create controllers
- [ ] Add Tailwind styling

**Related ADRs:** ADR-007, ADR-010

### Day 7: Integration + Testing
**Branch:** `feature/integration-testing`

**Tasks:**
- [ ] Write E2E smoke test
- [ ] Fix integration issues
- [ ] Verify success criteria
- [ ] Update docs

---

## Phase 1 Success Criteria

All must pass:

```php
it('handles complete mock flow', function () {
    // 1. Create mock via UI
    $mock = Mock::factory()->create([
        'name' => 'Get Users',
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => '/api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'body' => '{"users": []}',
        ],
        'is_active' => true,
    ]);
    
    // 2. Make request
    $response = $this->get('/api/users');
    
    // 3. Verify response
    expect($response->status())->toBe(200)
        ->and($response->json())->toBe(['users' => []]);
    
    // 4. Verify logging
    expect(RequestLog::count())->toBe(1)
        ->and(RequestLog::first()->mock_id)->toBe($mock->id);
});
```

**Manual Checklist:**
- [ ] Can create mock via UI: `GET /api/users → 200 {"users": []}`
- [ ] Visiting `http://localhost/api/users` returns mocked response
- [ ] Request is logged to database
- [ ] Can see request log in UI
- [ ] CLI `php artisan mockitect:list` works
- [ ] All tests pass
- [ ] Docs updated

---

## Phase 2: Powerful Responses (Weeks 2-3)

**Goal:** Dynamic, realistic responses.

### 2.1: Advanced Matching
- JSONPath body matching
- XPath for XML
- Query parameter matching
- Cookie matching
- Composite matchers (AND/OR)

### 2.2: Dynamic Templates
- Request data access
- Faker integration
- Date/time helpers
- Template preview

### 2.3: Scenarios
- State machine model
- State transitions
- Mock-scenario linkage
- Visual builder

### 2.4: File Responses
- File upload system
- Binary file support
- Template references

### 2.5: Proxy Mode
- Forward unmatched requests
- Response recording
- Shadow mode comparison

### 2.6: Failure Simulation
- Network failures
- HTTP error codes
- Rate limiting
- Random failures

---

## Phase 3: Developer Experience (Weeks 4-5)

**Goal:** Make developers love using it.

### 3.1: Request Inspector
- Real-time logs (WebSocket)
- Filter/search
- Diff view
- Request replay
- Export (HAR/Postman)

### 3.2: Import/Export
- JSON/YAML format
- Postman collections
- OpenAPI/Swagger
- cURL import

### 3.3: Collections
- Projects/collections
- Tags and search
- Duplicate/clone
- Bulk operations

### 3.4: Enhanced CLI
- Mock validation
- CI/CD integration
- Watch mode
- Diff tool

---

## Phase 4: Performance (Week 6)

**Goal:** Handle real-world scale.

### 4.1: Caching
- Redis integration
- Template cache
- Cache warming

### 4.2: Concurrency
- Connection pooling
- Rate limiting
- Profiling

### 4.3: Data Integrity
- Migration system
- Backup/restore
- SQLite → PostgreSQL

---

## Phase 5: Advanced Protocols (Weeks 7-8)

### 5.1: Queues
- RabbitMQ/SQS
- Consumer/producer
- Message publishing

### 5.2: gRPC
- Protocol buffers
- Method mocking
- Streaming

### 5.3: WebSockets
- Server integration
- Bidirectional messaging

---

## Phase 6: Optional Features (Future)

Multi-tenancy, authentication, team collaboration, cloud deployment.

---

## File Structure

```
mockitect/
├── app/
│   ├── Contracts/RequestMatcherInterface.php
│   ├── Events/MockRequestMatched.php
│   ├── Http/Controllers/Mockitect/
│   ├── Models/Mock.php, Scenario.php, RequestLog.php
│   ├── Services/Matchers/
│   └── Console/Commands/Mockitect*.php
├── docs/
│   ├── ADR.md (this file)
│   └── PLAN.md (this file)
├── resources/js/Pages/Mockitect/
├── routes/mockitect.php
├── tests/
│   ├── Unit/Matchers/
│   └── Feature/
└── README.md
```

---

## Testing Strategy

### Unit Tests (Pest)
```php
it('matches exact path', function () {
    $matcher = new PathMatcher();
    $request = Request::create('/api/users', 'GET');
    expect($matcher->matches($request, $rule))->toBeTrue();
});
```

### Feature Tests
```php
it('returns mocked response', function () {
    Mock::factory()->create([...]);
    
    $response = $this->get('/api/users');
    
    expect($response->status())->toBe(200);
});
```

---

## CLI Commands

```bash
# List all mocks
php artisan mockitect:list

# List active only
php artisan mockitect:list --active

# Create interactively
php artisan mockitect:create

# Import from file
php artisan mockitect:import mocks.json

# Export all
php artisan mockitect:export
```

---

## Migration Path: SQLite → PostgreSQL

```bash
# 1. Add postgres to docker-compose
# 2. Update .env: DB_CONNECTION=pgsql
# 3. Run: php artisan migrate:fresh
# 4. Import: php artisan mockitect:import backup.json
```

---

## Progress

### Phase 1
- [ ] Day 1: Database + Models
- [ ] Day 2: Matcher System
- [ ] Day 3: Matching Service
- [ ] Day 4: Response Builder + Handler
- [ ] Day 5: CLI Commands
- [ ] Day 6: Inertia.js + Basic UI
- [ ] Day 7: Integration + Testing

### Future Phases
- [ ] Phase 2: Powerful Responses
- [ ] Phase 3: Developer Experience
- [ ] Phase 4: Performance
- [ ] Phase 5: Advanced Protocols
- [ ] Phase 6: Optional Features

---

**Ready to build?** Create your first feature branch:

```bash
git checkout main
git pull origin main
git checkout -b feature/database-setup
```

Let's go! 🚀
