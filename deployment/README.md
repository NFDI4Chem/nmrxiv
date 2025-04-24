# NMRXiv Production Deployment

This folder contains everything needed to deploy NMRXiv in a production environment using Docker.

## Services

The deployment includes the following services:

1. **App** - Laravel application with FrankenPHP server
2. **Worker** - Laravel queue worker and scheduler with Horizon for queue management
3. **PostgreSQL** - Based on informaticsmatters/rdkit-cartridge-debian:latest
4. **Redis** - For caching and queue
5. **MeiliSearch** - For search functionality

## Deployment Steps

### 1. Environment Setup

Create a `.env` file in the project root with your production settings. You can use the following as a template:

```
# Basic Application Configuration
APP_NAME=NMRXiv
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com
APP_PORT=80

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=nmrxiv
DB_USERNAME=nmrxiv
DB_PASSWORD=your_secure_password
DB_VERSION=13

# Redis Configuration
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# MeiliSearch Configuration
MEILISEARCH_HOST=http://meilisearch:7700
MEILI_MASTER_KEY=your_secure_meilisearch_key

# Horizon Settings
HORIZON_PREFIX=nmrxiv_horizon
HORIZON_BALANCE=auto

# Queue and Session Drivers
BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Note:** If you prefer, you can name the file `.env.production`. The deployment script will automatically use it if no `.env` file exists.

### 2. Build and Deploy

You can use the provided deployment script which will:
- Create a default `.env` file if none exists
- Load the environment variables for Docker Compose
- Build and start all services
- Run database migrations
- Optimize the application cache

```bash
# Make the script executable if needed
chmod +x deployment/deploy.sh

# Run the deployment script
./deployment/deploy.sh
```

Alternatively, you can run the commands manually:

```bash
# Start the services
docker-compose -f deployment/docker-compose.prod.yml up -d

# Generate application key (if not done during build)
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan key:generate

# Run migrations
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan migrate --force

# Clear cache and optimize
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan optimize:clear
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan optimize
```

### 3. Post-Deployment Configuration

```bash
# Set up MeiliSearch indexes
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan scout:sync-index-settings
```

## Accessing Laravel Horizon

Laravel Horizon provides a dashboard to monitor your queue jobs. To access it:

1. Navigate to `http://your-domain.com/horizon` in your browser
2. You'll need to be authenticated with a user that has the necessary permissions

Horizon will automatically start processing jobs as they're dispatched to the queue.

## Scaling (Optional)

To scale the worker processes:

```bash
docker-compose -f deployment/docker-compose.prod.yml up -d --scale worker=3
```

## Maintenance

### Updates

```bash
# Pull latest changes
git pull

# Rebuild containers
docker-compose -f deployment/docker-compose.prod.yml build --no-cache

# Restart services
docker-compose -f deployment/docker-compose.prod.yml up -d

# Run migrations
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan migrate --force

# Clear and optimize cache
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan optimize:clear
docker-compose -f deployment/docker-compose.prod.yml exec app php artisan optimize
```

### Backups

```bash
# Backup database
docker-compose -f deployment/docker-compose.prod.yml exec pgsql pg_dump -U nmrxiv nmrxiv > backup_$(date +%Y%m%d).sql
```

## Health Checks

The deployment includes health checks for all services. You can check their status with:

```bash
docker-compose -f deployment/docker-compose.prod.yml ps
``` 