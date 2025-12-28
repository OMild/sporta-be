###############################################
# Stage 1: Build frontend assets
###############################################
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY src/package*.json ./

# Install dependencies
RUN npm install

# Copy source files for building
COPY src/ ./

# Build assets
RUN npm run build

###############################################
# Stage 2: Install PHP dependencies
###############################################
FROM composer:2 AS composer-builder

WORKDIR /app

# Copy composer files
COPY src/composer.json src/composer.lock ./

# Install dependencies without dev packages for production
ARG APP_ENV=production
RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --no-scripts --no-autoloader --prefer-dist; \
    else \
        composer install --no-scripts --no-autoloader --prefer-dist; \
    fi

# Copy application code
COPY src/ ./

# Generate optimized autoload
RUN if [ "$APP_ENV" = "production" ]; then \
        composer dump-autoload --optimize --classmap-authoritative; \
    else \
        composer dump-autoload --optimize; \
    fi

###############################################
# Stage 3: Final application image
###############################################
FROM php:8.3-fpm

# Arguments
ARG user=sporta
ARG uid=1000
ARG APP_ENV=production

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Get latest Composer (useful for development)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user || true
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy application from builder stages (for production)
COPY --from=composer-builder --chown=$user:www-data /app /var/www
COPY --from=node-builder --chown=$user:www-data /app/public/build /var/www/public/build

# Set proper permissions
RUN chown -R $user:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
