#!/bin/sh

set -e

# Create basic .env if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "Created .env from .env.example"
    else
        echo "Creating basic .env file"
        touch .env
    fi
fi

# Check if APP_KEY exists, generate if not
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "undefined" ]; then
    if ! php artisan key:generate --show | grep -q "APP_KEY="; then
        echo "Generating application key..."
        php artisan key:generate --force
    fi
fi

# Clear and cache configuration
php artisan config:clear 2>/dev/null || true
php artisan config:cache 2>/dev/null || true

# Run Laravel serve command
echo "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT