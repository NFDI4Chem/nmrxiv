#!/bin/bash

set -e

COMPOSE_FILE="/mnt/docker/nmrxiv/deployment/docker-compose.prod.yml"
APP_IMAGE="nfdi4chem/nmrxiv:app-dev-latest"
WORKER_IMAGE="nfdi4chem/nmrxiv:worker-dev-latest"
NEW_CONTAINER_ID=""
BACKUP_DIR="./backups"
BUILD=false
DEPLOY=false
MULTI_PLATFORM=false

LOG_FILE="$HOME/nmrxiv-deploy.log"

# Create log file if it doesn't exist
if [ ! -f "$LOG_FILE" ]; then
    touch "$LOG_FILE"
    chmod 644 "$LOG_FILE"
fi

# Unified logging function
log_message() {
    echo "$1"
    echo "$(date '+%Y-%m-%d %H:%M:%S') $1" >> "$LOG_FILE"
}

# === Start of script ===
log_message "🚀 === START of deployment script ==="

# === Load environment ===
PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")
cd "$PROJECT_ROOT"
log_message "Project root: $PROJECT_ROOT"

set -a
source .env
set +a

export HTTP_PROXY="${HTTP_PROXY:-$http_proxy}"
export HTTPS_PROXY="${HTTPS_PROXY:-$https_proxy}"
export NO_PROXY="${NO_PROXY:-$no_proxy}"
export COMPOSE_PROJECT_NAME=nmrxiv-dev

# === Functions ===
check_health() {
    HEALTH=$(docker inspect --format='{{json .State.Health.Status}}' "$NEW_CONTAINER_ID")
    log_message "Health status for $NEW_CONTAINER_ID: $HEALTH"
    [[ "$HEALTH" == *"healthy"* ]]
}

wait_for_health() {
    log_message "⏳ Waiting for new container to pass health check (up to 10 retries)..."
    for i in {1..10}; do
        if check_health; then
            log_message "✅ Container is healthy."
            return 0
        else
            log_message "Retry $i/10: Waiting 60s..."
            sleep 60
        fi
    done
    log_message "❌ Container health check failed after 10 retries."
    return 1
}

remove_old_containers() {
    local name_prefix=$1
    log_message "🧼 Removing old ${name_prefix} container(s)..."

    container_ids=$(docker ps -a --filter "name=${name_prefix}" --format "{{.ID}}")
    sorted_container_ids=$(echo "$container_ids" | xargs docker inspect --format='{{.Created}} {{.ID}}' | sort | awk '{print $2}')
    oldest_container_id=$(echo "$sorted_container_ids" | head -n 1)

    if [ -z "$oldest_container_id" ]; then
        log_message "❌ No containers found with name prefix: ${name_prefix}"
        exit 1
    fi

    docker stop "$oldest_container_id"
    cleanup

    log_message "✅ Deleted old container ID: $oldest_container_id"
}

cleanup() {
    log_message "Cleaning up..."

    docker container prune -f >/dev/null 2>&1 || true
    docker image prune -f >/dev/null 2>&1 || true
    
    if [[ -d "$BACKUP_DIR" ]]; then
        find "$BACKUP_DIR" -name "*.sql" -type f | sort -r | tail -n +6 | xargs -r rm -f
        log_message "Old backups pruned (kept last 5 backups)"
    fi

    log_message "Cleanup completed"
}

deploy_service() {
    local service=$1
    local image=$2
    local run_healthcheck=$3

    log_message "Checking for new image: $image"

    if [ "$(docker pull "$image" | grep -c "Status: Image is up to date")" -eq 0 ]; then
        log_message "📦 New ${service^^} image available."

        backup_database
        
        docker compose -f "$COMPOSE_FILE" up -d "$service" --scale "$service"=2 --no-deps --no-recreate
        NEW_CONTAINER_ID=$(docker ps -q -l)
        log_message "🔍 New container ID: $NEW_CONTAINER_ID"

        if wait_for_health; then
            remove_old_containers "$service"
            log_message "✅ Deployment of $service done successfully.."
            run_migration_and_clear_cache
            log_message "Application is available at: https://dev.nmrxiv.org"
        else
            log_message "❌ Deployment aborted: new $service container is unhealthy."
            docker stop "$NEW_CONTAINER_ID"
            docker rm "$NEW_CONTAINER_ID"
        fi
    else
        log_message "✅ No update for $service Skipping deployment."
    fi
}

backup_database() {
    log_message "Creating database backup..."

    mkdir -p "$BACKUP_DIR"
    local backup_file="$BACKUP_DIR/db_backup_$(date +%Y%m%d_%H%M%S).sql"

    if docker compose -p "$COMPOSE_PROJECT_NAME" -f "$COMPOSE_FILE" exec -T pgsql \
        pg_dump -h localhost -U "${DB_USERNAME}" "${DB_DATABASE}" > "$backup_file" 2>/dev/null; then
        log_message "Database backup created: $backup_file"
    else
        log_message "Database backup failed. Please check your database connection and credentials."
    fi
}

run_migration_and_clear_cache() {
    log_message "Running database migration..."

    docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan cache:clear
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan optimize:clear
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan optimize
    
    log_message "Database migration completed successfully"
}

build_multi_platform() {
    log_message "🔨 Building multi-platform Docker images..."
    
    # Create and use a new builder instance for multi-platform builds
    docker buildx create --name multi-platform-builder --use --driver docker-container || true
    
    # Build app image for multiple platforms
    log_message "Building app image for multiple platforms..."
    docker buildx build \
        --platform linux/amd64,linux/arm64 \
        --tag nfdi4chem/nmrxiv:app-dev-latest \
        --tag nfdi4chem/nmrxiv:app-dev-$(date +%Y%m%d-%H%M%S) \
        --file deployment/Dockerfile \
        --push \
        .
    
    # Build worker image for multiple platforms
    log_message "Building worker image for multiple platforms..."
    docker buildx build \
        --platform linux/amd64,linux/arm64 \
        --tag nfdi4chem/nmrxiv:worker-dev-latest \
        --tag nfdi4chem/nmrxiv:worker-dev-$(date +%Y%m%d-%H%M%S) \
        --file deployment/Dockerfile.worker \
        --push \
        .
    
    log_message "✅ Multi-platform builds completed successfully!"
}

# === Parse arguments ===
while [[ $# -gt 0 ]]; do
    case $1 in
        --build) BUILD=true; shift ;;
        --deploy) DEPLOY=true; shift ;;
        --backup) BACKUP=true; shift ;;
        --multi-platform) MULTI_PLATFORM=true; shift ;;
        *) log_message "Unknown option: $1"; exit 1 ;;
    esac
done

# === Deployment Flow ===
if [ "$MULTI_PLATFORM" = true ]; then
    build_multi_platform
    log_message "🎉 Multi-platform build completed successfully!"

elif [ "$DEPLOY" = true ]; then
    log_message "Starting zero-downtime deployment..."

    deploy_service app "$APP_IMAGE" true
    deploy_service worker "$WORKER_IMAGE" true

elif [ "$BUILD" = true ]; then
    docker compose -f "$COMPOSE_FILE" down --remove-orphans

    log_message "Building containers..."
    docker compose -f "$COMPOSE_FILE" build --no-cache
    docker compose -f "$COMPOSE_FILE" up -d

    log_message "Waiting for database to be ready..."
    sleep 10

    run_migration_and_clear_cache
    
    cleanup
    log_message "🎉 Build completed successfully!"
    log_message "Application is available at: https://dev.nmrxiv.org"

elif [ "$BACKUP" = true ]; then
    backup_database
else
    log_message "Skipping build and deploy step — please pass at least one argument (--build, --deploy, or --multi-platform)..."
fi

# === End of script ===
log_message "✅ === END of deployment script ==="
