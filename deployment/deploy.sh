#!/bin/bash

set -e

COMPOSE_FILE="/mnt/docker/nmrxiv/deployment/docker-compose.prod.yml"
APP_IMAGE="nfdi4chem/nmrxiv:app-dev-latest"
WORKER_IMAGE="nfdi4chem/nmrxiv:worker-dev-latest"
NEW_CONTAINER_ID=""
BUILD=false
DEPLOY=false

# === Parse arguments ===
while [[ $# -gt 0 ]]; do
    case $1 in
        --build) BUILD=true; shift ;;
        --deploy) DEPLOY=true; shift ;;
        *) echo "Unknown option: $1"; exit 1 ;;
    esac
done

# === Load environment ===
PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")
cd "$PROJECT_ROOT"
echo "Project root: $PROJECT_ROOT"

set -a
source .env
set +a

export HTTP_PROXY="${HTTP_PROXY:-$http_proxy}"
export HTTPS_PROXY="${HTTPS_PROXY:-$https_proxy}"
export NO_PROXY="${NO_PROXY:-$no_proxy}"
export COMPOSE_PROJECT_NAME=nmrxiv-dev

set -x

# === Functions ===
check_health() {
    HEALTH=$(docker inspect --format='{{json .State.Health.Status}}' "$NEW_CONTAINER_ID")
    [[ "$HEALTH" == *"healthy"* ]]
}

wait_for_health() {
    echo "⏳ Waiting for new container to pass health check (up to 10 retries)..."
    for i in {1..10}; do
        if check_health; then
            echo "✅ Container is healthy."
            return 0
        else
            echo "Retry $i/10: Waiting 60s..."
            sleep 60
        fi
    done
    return 1
}

replace_old_container() {
    local name_prefix=$1
    echo "🧼 Replacing old ${name_prefix} container(s)..."

    container_ids=$(docker ps -a --filter "name=${name_prefix}" --format "{{.ID}}")
    sorted_container_ids=$(echo "$container_ids" | xargs docker inspect --format='{{.Created}} {{.ID}}' | sort | awk '{print $2}')
    oldest_container_id=$(echo "$sorted_container_ids" | head -n 1)

    if [ -z "$oldest_container_id" ]; then
        echo "❌ No containers found with name prefix: ${name_prefix}"
        exit 1
    fi

    docker stop "$oldest_container_id"
    docker rm "$oldest_container_id"
    docker image prune -af

    echo "✅ Deleted old container ID: $oldest_container_id"
}

deploy_service() {
    local service=$1
    local image=$2
    local run_healthcheck=$3

    if [ "$(docker pull "$image" | grep -c "Status: Image is up to date")" -eq 0 ]; then
        echo "📦 New ${service^^} image available."

        docker compose -f "$COMPOSE_FILE" up -d "$service" --scale "$service"=2 --no-deps --no-recreate
        NEW_CONTAINER_ID=$(docker ps -q -l)
        echo "🔍 New container ID: $NEW_CONTAINER_ID"

        if [ "$run_healthcheck" = true ]; then
            if wait_for_health; then
                replace_old_container "$service"
                echo "✅ Deployment of $service done successfully.."
            else
                echo "❌ Deployment aborted: new $service container is unhealthy."
                docker stop "$NEW_CONTAINER_ID"
                docker rm "$NEW_CONTAINER_ID"
            fi
        else
            replace_old_container "$service"
            echo "✅ Deployment of $service done successfully.."
        fi
    else
        echo "✅ No update for $service Skipping deployment."
    fi
}

# === Deployment Flow ===
if [ "$DEPLOY" = true ]; then
    echo "Deployment in progress..."
    deploy_service app "$APP_IMAGE" true
    deploy_service worker "$WORKER_IMAGE" false
    
elif [ "$BUILD" = true ]; then
    docker compose -f "$COMPOSE_FILE" down --remove-orphans
    echo "Building containers..."
    docker compose -f "$COMPOSE_FILE" build --no-cache
    docker compose -f "$COMPOSE_FILE" up -d

    echo "Waiting for database to be ready..."
    sleep 10

    docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan optimize:clear
    docker compose -f "$COMPOSE_FILE" exec -T app php artisan optimize
    docker compose -f "$COMPOSE_FILE" ps

    echo "🎉 Build completed successfully!"
else 
    echo "Skipping build and deploy step — please pass at least one argument (--build (if you want to build everything for the first time) or --deploy(for zero downtime deployment))..."
fi
