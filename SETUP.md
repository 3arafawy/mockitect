# Setup Guide

Please see [docs/SETUP.md](docs/SETUP.md) for the complete setup guide.

## Quick Start

```bash
# Start containers
./vendor/bin/sail up -d

# Setup database
./vendor/bin/sail artisan migrate

# Install frontend dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# Run tests
./vendor/bin/sail test

# Access at http://localhost/__mockitect
```
