#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

# "My Shop.example.com" -> "my-shop"  (cắt domain, hạ chữ thường, ký tự lạ -> '-')
APP_SLUG="${APP_SLUG:-$(basename "$ROOT" | sed -E 's/\..*$//' | tr '[:upper:]' '[:lower:]' \
    | sed -E 's/[^a-z0-9]+/-/g; s/^-+//; s/-+$//')}"
[ -n "$APP_SLUG" ] || { echo "✗ Không suy ra được tên từ thư mục — chạy lại với APP_SLUG=ten-project" >&2; exit 1; }

IMAGE="${IMAGE:-$APP_SLUG}"
TAG="${TAG:-latest}"
ENV_FILE="${ENV_FILE:-.env}"
PLATFORM="${PLATFORM:-linux/amd64}"
ASSETS_IMAGE="$APP_SLUG-assets:latest"
OUT_DIR="public/asset/admin/build"

[ -f "$ENV_FILE" ] || { echo "✗ Không tìm thấy $ENV_FILE" >&2; exit 1; }

# --- 1. Build JS (Vue 3 + Vite) trong container, copy kết quả ra repo --------
if [ "${SKIP_ASSETS:-0}" = "1" ]; then
    echo "==> [1/2] Bỏ qua build JS (SKIP_ASSETS=1)"
else
    echo "==> [1/2] Build JS ($ASSETS_IMAGE) — ENV_FILE=$ENV_FILE"
    docker build --build-arg "ENV_FILE=$ENV_FILE" -f docker/Dockerfile.assets -t "$ASSETS_IMAGE" .

    CID="$(docker create "$ASSETS_IMAGE")"
    trap 'docker rm -f "$CID" >/dev/null 2>&1 || true' EXIT
    rm -rf "$OUT_DIR"
    mkdir -p "$(dirname "$OUT_DIR")"
    docker cp "$CID:/app/$OUT_DIR" "$OUT_DIR"
    docker rm -f "$CID" >/dev/null
    trap - EXIT
    echo "    -> $OUT_DIR ($(du -sh "$OUT_DIR" | cut -f1))"
fi

[ -f "$OUT_DIR/manifest.json" ] || {
    echo "✗ Chưa có $OUT_DIR/manifest.json — chạy lại không kèm SKIP_ASSETS, hoặc kiểm tra vite.config.js" >&2
    exit 1
}

# --- 2. Build image app -----------------------------------------------------
echo "==> [2/2] Build image $IMAGE:$TAG ($PLATFORM) — ENV_FILE=$ENV_FILE"
docker buildx build \
    --platform "$PLATFORM" \
    --build-arg "ENV_FILE=$ENV_FILE" \
    -f docker/Dockerfile \
    -t "$IMAGE:$TAG" \
    --load \
    .

echo
echo "✓ Image sẵn sàng: $IMAGE:$TAG"
echo "  Chạy thử:  ./run.sh"
echo "  Đẩy lên registry:  ./push.sh"
