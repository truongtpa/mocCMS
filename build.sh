#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"

IMAGE=${IMAGE:-$(basename "$PWD" | cut -d. -f1 | tr '[:upper:] _' '[:lower:]--')}
TAG=${TAG:-latest}
ENV_FILE=${ENV_FILE:-.env}
PLATFORM=${PLATFORM:-linux/amd64}
OUT=public/asset/admin/build

echo "==> Build JS"
docker build --build-arg ENV_FILE="$ENV_FILE" -f docker/Dockerfile.assets -t "$IMAGE-assets" .
CID=$(docker create "$IMAGE-assets")
rm -rf "$OUT"
docker cp "$CID:/app/$OUT" "$OUT"
docker rm -f "$CID" >/dev/null

echo "==> Build image $IMAGE:$TAG ($PLATFORM)"
docker buildx build --platform "$PLATFORM" --build-arg ENV_FILE="$ENV_FILE" -f docker/Dockerfile -t "$IMAGE:$TAG" --load .

echo "✓ $IMAGE:$TAG"
