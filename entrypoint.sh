#!/bin/sh

set -e

echo "Starting entrypoint script..."

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

# Check if APP_KEY exists in .env file, generate if not
if ! grep -q "^APP_KEY=" .env || [ "$(grep "^APP_KEY=" .env | cut -d '=' -f2)" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
else
    echo "Using existing APP_KEY"
fi

# Set APP_KEY from environment if available
if [ ! -z "$APP_KEY" ] && [ "$APP_KEY" != "undefined" ]; then
    sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
fi

# Set database configuration from environment if available
if [ ! -z "$DB_HOST" ]; then
    sed -i "s|^DB_HOST=.*|DB_HOST=$DB_HOST|" .env
fi
if [ ! -z "$DB_PORT" ]; then
    sed -i "s|^DB_PORT=.*|DB_PORT=$DB_PORT|" .env
fi
if [ ! -z "$DB_DATABASE" ]; then
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|" .env
fi
if [ ! -z "$DB_USERNAME" ]; then
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|" .env
fi
if [ ! -z "$DB_PASSWORD" ]; then
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" .env
fi
if [ ! -z "$REDIS_URL" ]; then
    sed -i "s|^REDIS_URL=.*|REDIS_URL=$REDIS_URL|" .env
fi
if [ ! -z "$APP_URL" ]; then
    sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" .env
fi

# Clear and cache configuration
echo "Clearing and caching configuration..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan config:cache 2>/dev/null || true

# Run Laravel serve command
echo "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT