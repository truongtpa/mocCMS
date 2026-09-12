#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Deploy target: DOCKER thuần (máy dev / server đơn lẻ).
# Build image base (./build-image.sh) rồi chạy docker/docker-compose.yml:
# 2 container cùng image — app (Octane :8000) + worker (queue + scheduler).
#
#   ./build-docker.sh
#   ENV_FILE=.env.production APP_PORT=8080 ./build-docker.sh
#   SKIP_ASSETS=1 ./build-docker.sh     # JS đã build sẵn rồi
#   UP=0 ./build-docker.sh              # chỉ build image, không chạy
#   FRESH=1 ./build-docker.sh           # down -v trước khi up (xoá volume)
# ---------------------------------------------------------------------------
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

IMAGE="${IMAGE:-hoso-sinhvien}"
TAG="${TAG:-latest}"
ENV_FILE="${ENV_FILE:-.env}"
APP_PORT="${APP_PORT:-3005}"
PROJECT="${PROJECT:-hoso_sinhvien}"
COMPOSE_FILE="docker/docker-compose.yml"
UP="${UP:-1}"

export PLATFORM="${PLATFORM:-$(docker version -f '{{.Server.Os}}/{{.Server.Arch}}')}"
export IMAGE TAG ENV_FILE APP_PORT

./build-image.sh

if [ "$UP" != "1" ]; then
    echo "✓ Đã build image $IMAGE:$TAG (UP=0 nên không chạy container)"
    exit 0
fi

if [ "${FRESH:-0}" = "1" ]; then
    echo "==> FRESH=1 -> docker compose down -v"
    docker compose -f "$COMPOSE_FILE" -p "$PROJECT" down -v
fi

echo "==> Khởi động container"
docker compose -f "$COMPOSE_FILE" -p "$PROJECT" up -d --remove-orphans

echo
docker compose -f "$COMPOSE_FILE" -p "$PROJECT" ps
echo
echo "✓ Web:    http://localhost:$APP_PORT"
echo "  Logs:   docker compose -f $COMPOSE_FILE -p $PROJECT logs -f"
echo "  Worker: docker compose -f $COMPOSE_FILE -p $PROJECT logs -f hoso_sinhvien_worker"
echo "  Stop:   docker compose -f $COMPOSE_FILE -p $PROJECT down"
