
# OrderFlow Mini — Event-Driven Architecture Lab

Event-Driven Order Processing system built with Laravel using TDD and DDD principles.  
Built as part of an advanced DDD/TDD architecture training.

---

## Why This Project Exists

Not every company project allows full architectural exploration.

Deadlines, legacy code, and business pressure often prevent:
- Proper aggregate modeling
- Explicit domain events
- Clean dependency direction
- Strict Red → Green → Refactor discipline

This repository was intentionally created as a controlled engineering lab
to explore these concepts properly.

If a production project does not provide the opportunity, you build one.

---

## What This Demonstrates

### Rich Domain Model
- Order Aggregate enforces invariants
- State transitions are explicit
- Business rules live inside the domain

### Domain Events
- OrderSubmitted
- PaymentCaptured
- StockReserved
- Decoupled side effects

### Idempotent Webhook Handling
Simulated real-world payment provider behaviour:
- Signature verification
- Duplicate event protection
- Transaction-safe updates

### Asynchronous Processing
- Jobs dispatched after commit
- Event-driven side effects
- Clear separation between Domain and Infrastructure

---

## Architectural Principles

- Domain does not depend on Laravel
- No framework leakage into business logic
- Ports & Adapters for external integrations
- PHPUnit (classic style)
- Test-first development

---

## Order Lifecycle

```

Draft → Submitted → Paid → Reserved → Shipped → Cancelled

```

State transitions are strictly validated inside the Aggregate.

---

## Testing Strategy

- Unit tests for Domain
- Feature tests for API
- Bus::fake for queue verification
- No real HTTP calls in tests
- Every feature begins with a failing test
