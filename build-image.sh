#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

IMAGE="${IMAGE:-hoso-sinhvien}"
TAG="${TAG:-latest}"
ENV_FILE="${ENV_FILE:-.env}"
PLATFORM="${PLATFORM:-linux/amd64}"

if [ ! -f "$ENV_FILE" ]; then
    echo "✗ Không tìm thấy $ENV_FILE" >&2
    exit 1
fi

if [ "${SKIP_ASSETS:-0}" != "1" ]; then
    ENV_FILE="$ENV_FILE" ./build-assets.sh
else
    echo "==> Bỏ qua bước build JS (SKIP_ASSETS=1)"
fi

if [ ! -f "public/asset/admin/build/manifest.json" ]; then
    echo "✗ Chưa có public/asset/admin/build/manifest.json — chạy ./build-assets.sh trước" >&2
    exit 1
fi

echo "==> Build image $IMAGE:$TAG ($PLATFORM) — ENV_FILE=$ENV_FILE"
docker buildx build \
    --platform "$PLATFORM" \
    --build-arg "ENV_FILE=$ENV_FILE" \
    -f docker/Dockerfile \
    -t "$IMAGE:$TAG" \
    --load \
    .

echo "✓ Image sẵn sàng: $IMAGE:$TAG"
