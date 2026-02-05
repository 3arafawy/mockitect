#!/bin/bash
# Git Commands for Documentation Update
# Run these commands in the project root

# Ensure you're on main and up to date
git checkout main
git pull origin main

# Create feature branch
git checkout -b feature/documentation-initial-setup

# Commit 1: README improvements
git add README.md
git commit -m "docs: fix README installation and status indicators

- Add missing composer install and npm install steps to installation
- Fix Phase 1 status from 'Current' to 'In Progress' with 🔄 indicators
- Ensure installation instructions are complete before running sail"

# Commit 2: ADR fixes
git add docs/ADR.md
git commit -m "docs: fix ADR-009 Pest example code

- Add missing $rule variable definition in Pest example
- Add consistent variable definitions in PHPUnit comparison
- Ensure code examples are complete and runnable"

# Commit 3: PLAN fixes
git add docs/PLAN.md
git commit -m "docs: fix PLAN success criteria test

- Change Mock::create() to Mock::factory()->create() for proper testing
- Align with Laravel factory pattern for test data generation
- Ensure success criteria test follows Pest conventions"

# Commit 4: Create docs directory structure (if not tracked)
git add docs/
git commit -m "docs: add initial documentation structure

- Add ADR.md with 10 architecture decision records
- Add PLAN.md with phased development plan
- Establish docs/ directory for project documentation
- Document GitHub Flow workflow and branching strategy"

# Push branch
git push origin feature/documentation-initial-setup

echo "Branch pushed! Create Pull Request at:"
echo "https://github.com/yourusername/mockitect/pull/new/feature/documentation-initial-setup"
