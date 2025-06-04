FROM dunglas/frankenphp:latest-alpine

# Setup proxy settings using build arguments
ARG HTTP_PROXY
ARG HTTPS_PROXY
ARG NO_PROXY
ENV http_proxy=$HTTP_PROXY \
    https_proxy=$HTTPS_PROXY \
    no_proxy=$NO_PROXY

# Setup working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions in combined layers
RUN apk update && apk add --no-cache \
    zip unzip git curl \
    libpng-dev libzip-dev icu-dev oniguruma-dev \
    postgresql-dev zlib-dev supervisor \
    nodejs npm \
    postgresql17-client --repository=https://dl-cdn.alpinelinux.org/alpine/edge/main && \
    # Install PHP extensions
    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl zip && \
    # Install APCu and Redis PHP extensions from source
    cd /tmp && \
    curl -L -o apcu.tar.gz https://github.com/krakjoe/apcu/archive/refs/tags/v5.1.22.tar.gz && \
    curl -L -o redis.tar.gz https://github.com/phpredis/phpredis/archive/refs/tags/5.3.7.tar.gz && \
    mkdir -p /tmp/apcu /tmp/redis && \
    tar -xf apcu.tar.gz -C /tmp/apcu --strip-components=1 && \
    tar -xf redis.tar.gz -C /tmp/redis --strip-components=1 && \
    # Build APCu
    cd /tmp/apcu && phpize && ./configure && make -j$(nproc) && make install && \
    # Build Redis
    cd /tmp/redis && phpize && ./configure && make -j$(nproc) && make install && \
    # Enable extensions
    docker-php-ext-enable apcu redis && \
    # Clean up
    rm -rf /tmp/apcu /tmp/redis /tmp/apcu.tar.gz /tmp/redis.tar.gz && \
    apk del --purge && \
    rm -rf /var/cache/apk/*

# Configure PHP in a single layer
RUN { \
    echo 'max_execution_time = 3600'; \
    echo 'memory_limit = 4G'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 250M'; \
    echo 'max_input_time = 3600'; \
} > /usr/local/etc/php/conf.d/docker-php-config.ini

# Copy PHP configuration
COPY docker/8.3/php.ini /usr/local/etc/php/conf.d/custom.ini

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy dependency files first for better caching
COPY composer.json composer.lock package.json package-lock.json ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Install Node dependencies
RUN npm ci --only=production

# Copy application code
COPY . .

# Create directories and set permissions in one layer
RUN mkdir -p /var/www/html/config/caddy \
             /var/log/supervisor \
             /run/supervisor \
             /data/caddy/data \
             /data/caddy/config && \
    # Build frontend assets
    npm run build && \
    # Clean up node_modules after build
    rm -rf node_modules && \
    # Set file permissions
    chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /var/www/html/config \
        /var/www/html/public \
        /data/caddy && \
    chmod -R 775 /var/www/html/public /var/www/html/config && \
    chmod -R 755 /data/caddy

# Copy configuration files
COPY deployment/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY deployment/Caddyfile /etc/caddy/Caddyfile

# Expose ports
EXPOSE 80 443

# Start services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]