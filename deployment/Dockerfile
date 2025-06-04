# Use a more recent Alpine version that might have better proxy support
FROM alpine:3.19 as dependencies

# Install all system dependencies in a separate stage
RUN apk update && \
    apk add --no-cache \
    zip unzip git curl \
    libpng-dev libzip-dev icu-dev oniguruma-dev \
    postgresql-dev zlib-dev supervisor \
    nodejs npm \
    autoconf gcc g++ make \
    postgresql-client

# Main application stage
FROM dunglas/frankenphp:latest-alpine

# Setup proxy settings using build arguments
ARG HTTP_PROXY
ARG HTTPS_PROXY
ARG NO_PROXY
ENV http_proxy=$HTTP_PROXY
ENV https_proxy=$HTTPS_PROXY
ENV no_proxy=$NO_PROXY

# Setup working directory
WORKDIR /var/www/html

# Copy pre-installed dependencies from the first stage
COPY --from=dependencies /usr/lib /usr/lib
COPY --from=dependencies /usr/bin /usr/bin
COPY --from=dependencies /usr/share /usr/share
COPY --from=dependencies /usr/include /usr/include

# Install PHP extensions (this shouldn't require network access)
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl zip

# Install APCu and Redis via PECL
RUN pecl install apcu redis && \
    docker-php-ext-enable apcu redis

# Configure PHP
COPY docker/8.3/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set PHP configuration values
RUN echo 'max_execution_time = 3600' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini && \
    echo 'memory_limit = 4G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini && \
    echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/docker-php-uploadmaxfilesize.ini && \
    echo 'post_max_size = 250M' >> /usr/local/etc/php/conf.d/docker-php-postmaxsize.ini && \
    echo 'max_input_time = 3600' >> /usr/local/etc/php/conf.d/docker-php-maxinputtime.ini

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Create required directory for caddy
RUN mkdir -p /var/www/html/config/caddy && \
    chmod -R 775 /var/www/html/config

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/config /var/www/html/public && \
    chmod -R 775 /var/www/html/public

# Build frontend assets
RUN npm ci && npm run build

# Configure Supervisord
COPY deployment/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create required directories for Supervisor
RUN mkdir -p /var/log/supervisor /run/supervisor

# Expose port
EXPOSE 80 443

# Update FrankenPHP config
COPY deployment/Caddyfile /etc/caddy/Caddyfile

# Set Caddy permissions
RUN mkdir -p /data/caddy/data /data/caddy/config && \
    chmod -R 755 /data/caddy && \
    chown -R www-data:www-data /data/caddy

# Start FrankenPHP and supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]