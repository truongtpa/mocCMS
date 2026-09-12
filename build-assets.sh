#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Build JS (Vue 3 + Vite) bằng Docker local, rồi copy kết quả ra
# public/asset/admin/build của repo. Được ./build-image.sh gọi tự động.
#
#   ./build-assets.sh                  # dùng .env
#   ENV_FILE=.env.production ./build-assets.sh
# ---------------------------------------------------------------------------
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

ENV_FILE="${ENV_FILE:-.env}"
ASSETS_IMAGE="${ASSETS_IMAGE:-hoso-sinhvien-assets:latest}"
OUT_DIR="public/asset/admin/build"

if [ ! -f "$ENV_FILE" ]; then
    echo "✗ Không tìm thấy $ENV_FILE (Vite cần các biến VITE_*)" >&2
    exit 1
fi

echo "==> [1/3] Build image assets ($ASSETS_IMAGE) — ENV_FILE=$ENV_FILE"
docker build \
    --build-arg "ENV_FILE=$ENV_FILE" \
    -f docker/Dockerfile.assets \
    -t "$ASSETS_IMAGE" \
    .

echo "==> [2/3] Copy JS đã build ra $OUT_DIR"
CID="$(docker create "$ASSETS_IMAGE")"
trap 'docker rm -f "$CID" >/dev/null 2>&1 || true' EXIT

rm -rf "$OUT_DIR"
mkdir -p "$(dirname "$OUT_DIR")"
docker cp "$CID:/app/$OUT_DIR" "$OUT_DIR"

echo "==> [3/3] Xong"
if [ -f "$OUT_DIR/manifest.json" ]; then
    echo "    manifest: $OUT_DIR/manifest.json"
else
    echo "    không thấy manifest.json trong $OUT_DIR — kiểm tra lại vite.config.js" >&2
fi
du -sh "$OUT_DIR"
