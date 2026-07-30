#!/bin/bash

set -e

COMPOSE_FILE="/mnt/docker/nmrxiv/deployment/docker-compose.prod.yml"
APP_IMAGE="nfdi4chem/nmrxiv:app-latest"
WORKER_IMAGE="nfdi4chem/nmrxiv:worker-latest"
NMRKIT_IMAGE="nfdi4chem/nmrkit:latest"
NMR_CLI_IMAGE="nfdi4chem/nmr-cli:latest"
NMR_RESPREDICT_IMAGE="nfdi4chem/nmr-respredict:latest"
NEW_CONTAINER_ID=""
BACKUP_DIR="/mnt/docker/nmrxiv-db-backups"
BUILD=false
DEPLOY=false
MULTI_PLATFORM=false
RESTART=false
BACKUP=false
HELP=false
RELEASE_NUMBER=""
SKIP_BACKUP=false
CEPH_REMOTE="${CEPH_REMOTE:-ceph}"
CEPH_BUCKET="${CEPH_BUCKET:-nmrxiv}"
CEPH_BACKUP_PREFIX="${CEPH_BACKUP_PREFIX:-production/database/release-backup}"

LOG_FILE="/mnt/docker/nmrxiv-deploy.log"

# Ensure backup directory exists and is secure
if [ ! -d "$BACKUP_DIR" ]; then
    mkdir -p "$BACKUP_DIR"
    chmod 700 "$BACKUP_DIR"
fi

# Create log file if it doesn't exist and ensure permissions
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
export COMPOSE_PROJECT_NAME=nmrxiv

# === Validate Environment Variables ===
validate_env_vars() {
    local missing_vars=()

    for var in DB_USERNAME DB_DATABASE; do
        if [ -z "${!var}" ]; then
            missing_vars+=("$var")
        fi
    done

    if [ ${#missing_vars[@]} -ne 0 ]; then
        log_message "❌ Missing required environment variables: ${missing_vars[*]}"
        exit 1
    fi
}

# Call the validation function at the start of the script
validate_env_vars

# === Functions ===
check_health() {
    if ! HEALTH=$(docker inspect --format='{{json .State.Health.Status}}' "$NEW_CONTAINER_ID" 2>/dev/null); then
        log_message "❌ Failed to inspect container $NEW_CONTAINER_ID. Ensure the container ID is correct."
        return 1
    fi

    log_message "Health status for $NEW_CONTAINER_ID: $HEALTH"

    if [[ "$HEALTH" == *"healthy"* ]]; then
        return 0
    else
        log_message "❌ Container $NEW_CONTAINER_ID is not healthy."
        return 1
    fi
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
    log_message "Starting zero-downtime deployment..."
    local service=$1
    local image=$2

    log_message "Checking for new image: $image"

    if [ "$(docker pull "$image" | grep -c "Status: Image is up to date")" -eq 0 ]; then
        log_message "📦 New ${service^^} image available."

        # No per-service backup here: backup_and_upload_to_ceph already ran once
        # for the whole deployment before any deploy_service call.

        docker compose -f "$COMPOSE_FILE" up -d "$service" --scale "$service"=2 --no-deps --no-recreate
        NEW_CONTAINER_ID=$(docker ps -q -l)
        log_message "🔍 New container ID: $NEW_CONTAINER_ID"

        sleep 10
        remove_old_containers "$service"
        run_migration_and_clear_cache
        log_message "✅ Deployment of $service done successfully.."
        log_message "nmrXiv application is available at: https://nmrxiv.org"

        # Skipping health check for dev because we want the service to be down if there is an error in the container
        # if wait_for_health; then
        #     remove_old_containers "$service"
        #     log_message "✅ Deployment of $service done successfully.."
        #     run_migration_and_clear_cache
        #     log_message "Application is available at: https://nmrxiv.org"
        # else
        #     log_message "❌ Deployment aborted: new $service container is unhealthy."
        #     docker stop "$NEW_CONTAINER_ID"
        #     docker rm "$NEW_CONTAINER_ID"
        # fi
    else
        log_message "✅ No update for $service Skipping deployment."
    fi
}

deploy_nmrkit_service() {
    local service=$1
    local image=$2

    log_message "Checking for new image: $image"

    if [ "$(docker pull "$image" | grep -c "Status: Image is up to date")" -eq 0 ]; then
        log_message "📦 New ${service^^} image available."

        docker compose -f "$COMPOSE_FILE" up -d "$service" --no-deps

        log_message "✅ Deployment of $service done successfully.."
        log_message "nmrKit application is available at: https://nmrkit.nmrxiv.org/"
    else
        log_message "✅ No update for $service Skipping deployment."
    fi
}

# Prompts for (or uses the --release provided) the CURRENT/OUTGOING release
# number - i.e. the version running right now, before this deployment upgrades
# it - since the backup captures the database state prior to the upgrade.
prompt_release_number() {
    if [[ -n "$RELEASE_NUMBER" ]]; then
        return 0
    fi

    if [[ ! -t 0 ]]; then
        log_message "❌ No release number provided and no interactive terminal available. Re-run with --release=<version> (e.g. --release=1.6.0)."
        exit 1
    fi

    read -r -p "Enter the CURRENT release number being upgraded FROM (i.e. the version still running before this deployment, e.g. 1.6.0): " RELEASE_NUMBER

     if [[ ! "$RELEASE_NUMBER" =~ ^[A-Za-z0-9][A-Za-z0-9._-]*$ ]]; then
         log_message "❌ Release number contains invalid characters. Use only letters/numbers plus . _ - (e.g. 1.6.0). Aborting deployment."
         exit 1
     fi
}

# Creates a fresh database dump, zips it, and copies it to Ceph storage under a
# release-numbered name/path. Runs before every deployment; aborts the
# deployment with a clear error if any step (dump, zip, or upload) fails.
backup_and_upload_to_ceph() {
    log_message "📦 Creating pre-upgrade database backup of outgoing release v${RELEASE_NUMBER}..."

    if ! command -v rclone >/dev/null 2>&1; then
        log_message "❌ rclone is not installed. Cannot copy release backup to Ceph. Aborting deployment."
        exit 1
    fi

    if ! command -v zip >/dev/null 2>&1; then
        log_message "❌ zip is not installed. Cannot create release backup archive. Aborting deployment."
        exit 1
    fi

    mkdir -p "$BACKUP_DIR"
    local backup_file="$BACKUP_DIR/db_backup_$(date +%Y%m%d_%H%M%S).sql"

    if ! docker compose -p "$COMPOSE_PROJECT_NAME" -f "$COMPOSE_FILE" exec -T pgsql \
        pg_dump -h localhost -U "${DB_USERNAME}" "${DB_DATABASE}" > "$backup_file" 2>/dev/null; then
        log_message "❌ Pre-deployment database backup failed. Aborting deployment before Ceph upload."
        rm -f "$backup_file"
        exit 1
    fi

    local zip_name="nmrxiv-data-dump-v${RELEASE_NUMBER}-$(date +%Y-%m-%d_%H%M%S).zip"
    local zip_path="$BACKUP_DIR/$zip_name"

    if ! zip -j "$zip_path" "$backup_file" >/dev/null; then
        log_message "❌ Failed to zip pre-deployment release backup. Aborting deployment."
        rm -f "$zip_path" "$backup_file"
        exit 1
    fi

    rm -f "$backup_file"

    local ceph_target="${CEPH_REMOTE}:${CEPH_BUCKET}/${CEPH_BACKUP_PREFIX}/${zip_name}"
    log_message "☁️  Copying release backup to Ceph: ${ceph_target}"

    if ! rclone copyto "$zip_path" "$ceph_target"; then
        log_message "❌ Failed to copy release backup to Ceph (${ceph_target}). Aborting deployment."
        rm -f "$zip_path"
        exit 1
    fi

    log_message "✅ Release backup v${RELEASE_NUMBER} copied to Ceph: ${zip_name}"
    rm -f "$zip_path"

    if [[ -d "$BACKUP_DIR" ]]; then
        find "$BACKUP_DIR" -name "*.sql" -type f | sort -r | tail -n +6 | xargs -r rm -f
        log_message "Old backups pruned (kept last 5 backups)"
    fi
}

run_migration_and_clear_cache() {
    log_message "Running database migration and clearing cache..."

    docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan optimize
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan optimize:clear
    
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
        --tag nfdi4chem/nmrxiv:app-latest \
        --tag nfdi4chem/nmrxiv:app-latest-$(date +%Y%m%d-%H%M%S) \
        --file deployment/Dockerfile \
        --push \
        .
    
    # Build worker image for multiple platforms
    log_message "Building worker image for multiple platforms..."
    docker buildx build \
        --platform linux/amd64,linux/arm64 \
        --tag nfdi4chem/nmrxiv:worker-latest \
        --tag nfdi4chem/nmrxiv:worker-latest-$(date +%Y%m%d-%H%M%S) \
        --file deployment/Dockerfile.worker \
        --push \
        .
    
    log_message "✅ Multi-platform builds completed successfully!"
}

check_db_readiness() {
    log_message "Waiting for database to be ready..."
    for i in {1..10}; do
        if docker compose -f "$COMPOSE_FILE" exec -T pgsql psql -U "${DB_USERNAME}" -d "${DB_DATABASE}" -c "SELECT 1" >/dev/null 2>&1; then
            log_message "✅ Database is ready."
            return 0
        else
            log_message "Retry $i/10: Waiting 10s for database to be ready..."
            sleep 10
        fi

        if [ $i -eq 10 ]; then
            log_message "❌ Database readiness check failed after 10 retries."
            return 1
        fi
    done
}

# Full from-scratch rebuild: tears down containers/networks (volumes are
# preserved - DB data and uploads are NOT touched), prunes ALL unused
# images/build cache system-wide so no stale layers/cache remain, then builds
# every image with --no-cache --pull and force-recreates the containers.
build_services() {
    if docker compose -f "$COMPOSE_FILE" ps -q | grep -q .; then
        docker compose -f "$COMPOSE_FILE" down --remove-orphans
    fi

    log_message "🧹 Pruning unused images and build cache system-wide (volumes preserved)..."
    docker builder prune -af    

    log_message "🔨 Building app containers from scratch (--no-cache --pull)..."
    docker compose -f "$COMPOSE_FILE" build --no-cache --pull
    docker compose -f "$COMPOSE_FILE" up -d --force-recreate

    if ! check_db_readiness; then
        exit 1
    fi

    run_migration_and_clear_cache

    cleanup
    log_message "✅ Services built and started successfully!"
    log_message " nmrXiv application is available at: https://nmrxiv.org"
    log_message " nmrKit application is available at: https://nmrkit.nmrxiv.org"
}

# Restarts existing containers (force-recreated) without rebuilding images.
# Volumes are preserved.
restart_services() {
    if docker compose -f "$COMPOSE_FILE" ps -q | grep -q .; then
        docker compose -f "$COMPOSE_FILE" down --remove-orphans
    fi

    log_message "🔄 Restarting app containers (force-recreate, no rebuild)..."
    docker compose -f "$COMPOSE_FILE" up -d --force-recreate

    if ! check_db_readiness; then
        exit 1
    fi

    run_migration_and_clear_cache

    cleanup
    log_message "✅ Services restarted successfully!"
    log_message " nmrXiv application is available at: https://nmrxiv.org"
    log_message " nmrKit application is available at: https://nmrkit.nmrxiv.org"
}

# === Display Help ===
display_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --build              Clean rebuild: down, prune unused images/cache (volumes kept), build --no-cache --pull, up --force-recreate"
    echo "  --deploy             Zero-downtime rolling deploy of app/worker/nmrkit images (only changed services); backs up DB to Ceph first"
    echo "  --backup             Dump DB, zip, and upload to Ceph under a release-numbered name (no deploy/restart)"
    echo "  --restart            Force-recreate containers from current images, no rebuild (volumes kept)"
    echo "  --multi-platform     Build and push amd64+arm64 app/worker images via buildx"
    echo "  --release=<version>  CURRENT (outgoing) release number, e.g. 1.6.0 - names the pre-deployment Ceph backup; prompted if omitted"
    echo "  --skip-backup        Skip the pre-deployment Ceph backup during --deploy"
    echo "  --help               Display this help message"
    exit 0
}

# === Parse arguments ===
while [[ $# -gt 0 ]]; do
    case $1 in
        --build) BUILD=true; shift ;;
        --deploy) DEPLOY=true; shift ;;
        --backup) BACKUP=true; shift ;;
        --multi-platform) MULTI_PLATFORM=true; shift ;;
        --restart) RESTART=true; shift ;;
        --release=*) RELEASE_NUMBER="${1#--release=}"; shift ;;
        --skip-backup) SKIP_BACKUP=true; shift ;;
        --help) HELP=true; shift ;;
        *) log_message "Unknown option: $1"; exit 1 ;;
    esac
done

# === Deployment Flow ===
case true in
    "$MULTI_PLATFORM")
        build_multi_platform
        ;;
    "$DEPLOY")
        if [[ "$SKIP_BACKUP" == true ]]; then
            log_message "⚠️  Skipping pre-deployment database backup/Ceph upload (--skip-backup passed)."
        else
            prompt_release_number
            backup_and_upload_to_ceph
        fi
        deploy_service app "$APP_IMAGE"
        deploy_service worker "$WORKER_IMAGE"
        deploy_nmrkit_service nmrkit "$NMRKIT_IMAGE"
        deploy_nmrkit_service nmr-load-save "$NMR_CLI_IMAGE"
        deploy_nmrkit_service nmr-respredict "$NMR_RESPREDICT_IMAGE"
        ;;
    "$BUILD")
        build_services
        ;;
    "$RESTART")
        restart_services
        ;;
    "$BACKUP")
        prompt_release_number
        backup_and_upload_to_ceph
        ;;
    "$HELP")
        display_help
        ;;
    *)
        log_message "Skipping build and deploy step — please pass at least one argument: \n--build: Build and deploy the application \n--deploy: Perform zero-downtime deployment \n--backup: Create a database backup \n--restart: Restart services \n--multi-platform: Build multi-platform Docker images. If you are unsure, use the --help flag for guidance."
        ;;
esac

# === End of script ===
log_message "✅ === END of deployment script ==="
