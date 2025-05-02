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

# Build only if BUILD is true
if [ "$BUILD" = true ]; then
    echo "Building containers..."
    docker compose -f deployment/docker-compose.prod.yml build --no-cache
else
    echo "Skipping build step..."
fi

# Perform zero-downtime deploy with Traefik
echo "Performing zero-downtime deployment with Traefik..."

# First make sure Traefik is up and running
echo "Ensuring Traefik is running..."
docker compose -f deployment/docker-compose.prod.yml up -d traefik

# Create a temporary compose file for blue-green deployment
cat > deployment/docker-compose.override.yml <<EOF
version: '3'
services:
  app:
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.app.rule=PathPrefix(\`/\`)"
      - "traefik.http.routers.app.entrypoints=web"
      - "traefik.http.services.app.loadbalancer.server.port=80"
      - "traefik.http.services.app.loadbalancer.sticky=true"
      - "traefik.http.services.app.loadbalancer.sticky.cookie.name=nmrxiv_session"
EOF

# Start all services with the override
echo "Starting services with zero-downtime configuration..."
docker compose -f deployment/docker-compose.prod.yml -f deployment/docker-compose.override.yml up -d

# Clean up override file
rm deployment/docker-compose.override.yml

# Wait for all services to be ready
echo "Waiting for services to be ready..."
sleep 10

# Run migrations
docker compose -f deployment/docker-compose.prod.yml exec -T app php artisan migrate --force

# Clear and optimize cache
docker compose -f deployment/docker-compose.prod.yml exec -T app php artisan optimize:clear
docker compose -f deployment/docker-compose.prod.yml exec -T app php artisan optimize

# Show running services
docker compose -f deployment/docker-compose.prod.yml ps

echo "Deployment completed successfully!"
echo "Traefik dashboard is available at: http://localhost:8080" 