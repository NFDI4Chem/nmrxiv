#!/bin/bash

# Exit on error
set -e

# Set project root directory
PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")
cd $PROJECT_ROOT

echo "Project root: $PROJECT_ROOT"

# Parse command line arguments
BUILD=true
while [[ $# -gt 0 ]]; do
    case $1 in
        --no-build)
            BUILD=false
            shift
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Copy production env file
# cp .env.production .env

# Export environment variables from .env
set -a
source .env
set +a

# Setup proxy environment variables from .env or use defaults
export HTTP_PROXY="${HTTP_PROXY:-$http_proxy}"
export HTTPS_PROXY="${HTTPS_PROXY:-$https_proxy}"
export NO_PROXY="${NO_PROXY:-$no_proxy}"

# Print each command before executing (after loading env vars)
set -x

# Call docker-compose with explicit env file parameter
export COMPOSE_PROJECT_NAME=nmrxiv
docker compose -f deployment/docker-compose.prod.yml down --remove-orphans

# Build only if BUILD is true
if [ "$BUILD" = true ]; then
    echo "Building containers..."
    docker compose -f deployment/docker-compose.prod.yml build --no-cache
else
    echo "Skipping build step..."
fi

docker compose -f deployment/docker-compose.prod.yml up -d

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 10

# Run migrations
docker compose -f deployment/docker-compose.prod.yml exec -T app php artisan migrate --force

# Clear and optimize cache
docker compose -f deployment/docker-compose.prod.yml exec -T app php artisan optimize:clear
docker compose -f deployment/docker-compose.prod.yml exec -T app php artisan optimize

# Set up MeiliSearch indexes
# docker-compose -f deployment/docker-compose.prod.yml exec -T app php artisan scout:sync-index-settings

# Show running services
docker compose -f deployment/docker-compose.prod.yml ps

echo "Deployment completed successfully!" 