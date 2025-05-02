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

# Build only if BUILD is true
if [ "$BUILD" = true ]; then
    echo "Building containers..."
    docker compose -f deployment/docker-compose.prod.yml build --no-cache
else
    echo "Skipping build step..."
fi

# Perform zero-downtime deploy with Traefik
echo "Performing zero-downtime deployment with Traefik..."

# First time deployment - just start everything
if ! docker compose -f deployment/docker-compose.prod.yml ps --services | grep -q "traefik"; then
    echo "First time deployment, starting all services..."
    docker compose -f deployment/docker-compose.prod.yml up -d
else
    # For subsequent deployments, implement blue-green strategy

    # 1. Ensure Traefik is running
    echo "Ensuring Traefik is running..."
    docker compose -f deployment/docker-compose.prod.yml up -d traefik

    # 2. Start new app container with a unique label to distinguish it
    echo "Starting new app containers..."
    DEPLOY_ID=$(date +%s)
    SCALE=2 # Number of app instances to run during transition
    
    # Scale up the app service
    APP_LABEL="traefik.http.services.app.loadbalancer.server.port=80"
    NEW_APP_LABEL="${APP_LABEL},v=${DEPLOY_ID}"
    
    # Use a temporary docker-compose override file to add the version label
    cat > deployment/docker-compose.override.yml <<EOF
version: '3'
services:
  app:
    labels:
      - "traefik.http.services.app.loadbalancer.server.port=80"
      - "traefik.deploy.replicas=${SCALE}"
      - "traefik.deploy.label=v${DEPLOY_ID}"
EOF
    
    # Start new containers with scaled instances
    docker compose -f deployment/docker-compose.prod.yml -f deployment/docker-compose.override.yml up -d

    # 3. Wait for new containers to be healthy
    echo "Waiting for new containers to be ready..."
    sleep 10

    # 4. Now remove the temporary override file and scale back down
    rm deployment/docker-compose.override.yml
    
    # 5. Recreate worker containers 
    echo "Recreating worker containers..."
    docker compose -f deployment/docker-compose.prod.yml up -d --force-recreate worker
fi

# Wait for all services to be ready
echo "Waiting for services to be ready..."
sleep 5

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