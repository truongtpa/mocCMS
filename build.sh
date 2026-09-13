#!/usr/bin/env bash

git reset --hard
git pull

set -euo pipefail
cd "$(dirname "$0")"

ENV_FILE=${ENV_FILE:-.env}
OUT=public/asset/admin/build
DC="docker compose -f docker/docker-compose.yml"

echo "==> Build JS"
docker build --build-arg ENV_FILE="$ENV_FILE" -f docker/Dockerfile.assets -t prop_assets .
CID=$(docker create prop_assets)
rm -rf "$OUT"
mkdir -p "$(dirname "$OUT")"
docker cp "$CID:/app/$OUT" "$OUT"
docker rm -f "$CID" >/dev/null

echo "==> Build & run (tên image/project lấy từ docker/docker-compose.yml)"
$DC up -d --build

echo
echo "✓ done"
echo "  Trạng thái: $DC ps"
echo "  Log:        $DC logs -f --tail=100"
echo "  Log file:   tail -f storage/logs/laravel.log"
echo "  Dừng:       $DC down"
