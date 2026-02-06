#!/bin/bash
# Quick setup script for Mockitect using Laravel Sail

set -e

echo "🚀 Setting up Mockitect (Containerized)..."
echo ""

# Check Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "✅ Docker is running"

# Install Composer dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing PHP dependencies..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
else
    echo "✅ PHP dependencies already installed"
fi

# Start Sail services
echo "🐳 Starting Docker containers..."
./vendor/bin/sail up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 3

# Create database file if it doesn't exist
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄️  Creating database file..."
    touch database/database.sqlite
fi

# Run migrations
echo "🔄 Running database migrations..."
./vendor/bin/sail artisan migrate --force

# Install NPM dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    ./vendor/bin/sail npm install
else
    echo "✅ Node.js dependencies already installed"
fi

# Build frontend assets
echo "🔨 Building frontend assets..."
./vendor/bin/sail npm run build

# Run tests
echo "🧪 Running tests..."
./vendor/bin/sail test

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Access the application:"
echo "   Admin UI: http://localhost/__mockitect"
echo "   API:      http://localhost"
echo ""
echo "📚 Useful commands:"
echo "   ./vendor/bin/sail up -d        # Start services"
echo "   ./vendor/bin/sail down         # Stop services"
echo "   ./vendor/bin/sail test         # Run tests"
echo "   ./vendor/bin/sail npm run dev  # Dev mode with hot reload"
echo ""
echo "📖 Documentation:"
echo "   README.md      # Quick start guide"
echo "   docs/SETUP.md  # Detailed setup guide"
echo ""
