#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"

IMAGE=${IMAGE:-$(basename "$PWD" | cut -d. -f1 | tr '[:upper:] _' '[:lower:]--')}
TAG=${TAG:-latest}
ENV_FILE=${ENV_FILE:-.env}
PLATFORM=${PLATFORM:-linux/amd64}
PROJECT=${PROJECT:-${IMAGE//-/_}}
OUT=public/asset/admin/build
DC="docker compose -f docker/docker-compose.yml -p $PROJECT"

echo "==> Build JS"
docker build --build-arg ENV_FILE="$ENV_FILE" -f docker/Dockerfile.assets -t "$IMAGE-assets" .
CID=$(docker create "$IMAGE-assets")
rm -rf "$OUT"
docker cp "$CID:/app/$OUT" "$OUT"
docker rm -f "$CID" >/dev/null

echo "==> Build image $IMAGE:$TAG ($PLATFORM)"
docker buildx build --platform "$PLATFORM" --build-arg ENV_FILE="$ENV_FILE" -f docker/Dockerfile -t "$IMAGE:$TAG" --load .

echo "✓ $IMAGE:$TAG"
echo
echo "  Chạy:       $DC up -d"
echo "  Trạng thái: $DC ps"
echo "  Log:        $DC logs -f --tail=100"
echo "  Log app:    docker logs -f --tail=100 ${PROJECT}_app"
echo "  Log file:   tail -f storage/logs/laravel.log"
echo "  Dừng:       $DC down"
