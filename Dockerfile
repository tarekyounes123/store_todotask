# Base image
FROM php:8.2-fpm-alpine

# Install system dependencies and Node.js
RUN apk update && apk add --no-cache \
    postgresql-dev \
    sqlite-dev \
    curl \
    git \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs-lts \
    npm \
    supervisor \
    $PHPIZE_DEPS

# Install PHP extensions and Redis
RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo pdo_mysql mbstring tokenizer xml ctype exif pcntl bcmath \
    && apk del $PHPIZE_DEPS

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer

# Install Laravel PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy remaining application files
COPY . .

# Copy package.json and install Node.js dependencies
COPY package*.json ./
RUN npm ci --only=production

# Build frontend assets
RUN npm run build

# Set proper permissions
RUN mkdir -p /var/www/public/uploads \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/uploads \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache /var/www/public/uploads

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose PHP-FPM port
EXPOSE 9000

# Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
