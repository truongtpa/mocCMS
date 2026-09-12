#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Deploy target: KUBERNETES.
# Build image base (./build-image.sh) rồi tag + push lên registry.
# Manifest nằm ở k8s/ — apply bằng kubectl sau khi push xong.
#
#   ./build-k8s.sh
#   ENV_FILE=.env.production TAG=v1.2.0 ./build-k8s.sh
#   SKIP_ASSETS=1 ./build-k8s.sh        # JS đã build sẵn rồi
#   PUSH=0 ./build-k8s.sh               # chỉ build, không push
# ---------------------------------------------------------------------------
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

REGISTRY="${REGISTRY:-172.20.6.10:32000}"
IMAGE="${IMAGE:-hoso-sinhvien}"
TAG="${TAG:-latest}"
PUSH="${PUSH:-1}"
# k8s cluster chạy amd64
export PLATFORM="${PLATFORM:-linux/amd64}"
export IMAGE TAG

./build-image.sh

docker tag "$IMAGE:$TAG" "$REGISTRY/$IMAGE:$TAG"

if [ "$PUSH" = "1" ]; then
    docker push "$REGISTRY/$IMAGE:$TAG"
    echo "✓ Pushed: $REGISTRY/$IMAGE:$TAG"
    echo
    echo "  Deploy:  kubectl apply -f k8s/app-config.yaml -f k8s/deployment.yaml"
    echo "  Rollout: kubectl -n vlute-hoso-sinhvien rollout restart deploy/hoso-sinhvien deploy/hoso-sinhvien-worker"
else
    echo "✓ Built (chưa push): $REGISTRY/$IMAGE:$TAG"
fi
