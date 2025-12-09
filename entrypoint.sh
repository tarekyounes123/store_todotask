#!/bin/sh

set -e

echo "Starting entrypoint script..."
echo "PORT=$PORT"

# Create basic .env if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "Created .env from .env.example"
    else
        echo "Creating basic .env file"
        cat > .env << EOF
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://\${RENDER_EXTERNAL_URL:-localhost}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_URL=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="\${APP_NAME}"
EOF
    fi
fi

# Set configuration from environment variables
if [ ! -z "$APP_KEY" ]; then
    sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
fi
if [ ! -z "$DB_CONNECTION" ]; then
    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=$DB_CONNECTION|" .env
    echo "DB_CONNECTION set to: $DB_CONNECTION"
else
    # Default to mysql if DB_HOST is provided
    if [ ! -z "$DB_HOST" ]; then
        sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
        echo "DB_CONNECTION set to: mysql (default)"
    fi
fi
if [ ! -z "$DB_HOST" ]; then
    sed -i "s|^DB_HOST=.*|DB_HOST=$DB_HOST|" .env
    echo "DB_HOST set to: $DB_HOST"
fi
if [ ! -z "$DB_PORT" ]; then
    sed -i "s|^DB_PORT=.*|DB_PORT=$DB_PORT|" .env
    echo "DB_PORT set to: $DB_PORT"
fi
if [ ! -z "$DB_DATABASE" ]; then
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|" .env
    echo "DB_DATABASE set to: $DB_DATABASE"
fi
if [ ! -z "$DB_USERNAME" ]; then
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|" .env
    echo "DB_USERNAME set to: $DB_USERNAME"
fi
if [ ! -z "$DB_PASSWORD" ]; then
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" .env
    echo "DB_PASSWORD is set"
else
    echo "DB_PASSWORD is not set"
fi
if [ ! -z "$REDIS_URL" ]; then
    sed -i "s|^REDIS_URL=.*|REDIS_URL=$REDIS_URL|" .env
    echo "REDIS_URL set to: $REDIS_URL"
fi
if [ ! -z "$APP_URL" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" .env
    echo "APP_URL set to: $APP_URL"
fi

# Ensure cache and session drivers are set appropriately
if [ ! -z "$REDIS_URL" ]; then
    sed -i "s|^CACHE_DRIVER=.*|CACHE_DRIVER=redis|" .env
    sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" .env
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|" .env
    echo "Cache, queue and session drivers set to redis"
else
    echo "Redis URL not provided, cache and session may use default drivers"
fi

# Check for APP_KEY, generate if missing
if ! grep -q "^APP_KEY=.*[[:alnum:]]" .env; then
    echo "APP_KEY not found in .env, generating..."
    php artisan key:generate --force
else
    echo "APP_KEY found in .env"
fi

# Clear and cache configuration
echo "Clearing and caching configuration..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan event:clear 2>/dev/null || true

# Check if we can connect to the database
if [ ! -z "$DB_HOST" ] && [ ! -z "$DB_USERNAME" ] && [ ! -z "$DB_PASSWORD" ] && [ ! -z "$DB_DATABASE" ]; then
    echo "Attempting database connection test..."
    php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; } catch (Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); }" 2>/dev/null || echo "Could not test database connection"
else
    echo "Database credentials not fully available, skipping connection test"
fi

# Cache configuration again after environment updates
php artisan config:cache 2>/dev/null || echo "Config cache failed"

# Run database migrations if application is ready
echo "Checking if migrations should run..."
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
else
    echo "Skipping migrations (set RUN_MIGRATIONS=true to run migrations)"
fi

# Seed the database if requested
if [ "$RUN_SEEDS" = "true" ]; then
    echo "Running database seeds..."
    php artisan db:seed --force
else
    echo "Skipping seeds (set RUN_SEEDS=true to run seeds)"
fi

# Run Laravel serve command
echo "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT