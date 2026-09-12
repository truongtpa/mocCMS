#!/usr/bin/env bash

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

APP_SLUG="${APP_SLUG:-$(basename "$ROOT" | sed -E 's/\..*$//' | tr '[:upper:]' '[:lower:]' \
    | sed -E 's/[^a-z0-9]+/-/g; s/^-+//; s/-+$//')}"
[ -n "$APP_SLUG" ] || { echo "✗ Không suy ra được tên từ thư mục — chạy lại với APP_SLUG=ten-project" >&2; exit 1; }

IMAGE="${IMAGE:-$APP_SLUG}"
TAG="${TAG:-latest}"
REGISTRY="${REGISTRY:-172.20.6.10:32000}"
REMOTE="$REGISTRY/$IMAGE:$TAG"

# Namespace k8s: ưu tiên biến, rồi đọc từ manifest, cuối cùng là tên project
K8S_NS="${K8S_NAMESPACE:-}"
if [ -z "$K8S_NS" ]; then
    # manifest thật bị gitignore -> chưa có thì đọc tạm từ file .example
    for f in k8s/deployment.yaml k8s/deployment.yaml.example; do
        [ -f "$f" ] || continue
        K8S_NS="$(awk '/^[[:space:]]*namespace:/{print $2; exit}' "$f")"
        if [ -n "$K8S_NS" ]; then break; fi
    done
fi
K8S_NS="${K8S_NS:-$APP_SLUG}"

if [ "${SKIP_BUILD:-0}" = "1" ]; then
    docker image inspect "$IMAGE:$TAG" >/dev/null 2>&1 \
        || { echo "✗ Chưa có image $IMAGE:$TAG ở local — bỏ SKIP_BUILD để build trước" >&2; exit 1; }
    echo "==> [1/3] Dùng image có sẵn $IMAGE:$TAG (SKIP_BUILD=1)"
else
    echo "==> [1/3] Build image"
    PLATFORM="${PLATFORM:-linux/amd64}" IMAGE="$IMAGE" TAG="$TAG" ./build.sh
fi

echo "==> [2/3] Tag $IMAGE:$TAG -> $REMOTE"
docker tag "$IMAGE:$TAG" "$REMOTE"

echo "==> [3/3] Push $REMOTE"
docker push "$REMOTE"

echo
echo "✓ Đã push: $REMOTE"
echo
echo "  Deploy lần đầu:"
echo "    kubectl apply -f k8s/app-config.yaml -f k8s/deployment.yaml"
echo
echo "  Cập nhật image mới (rollout):"
echo "    kubectl -n $K8S_NS rollout restart deploy"
echo "    kubectl -n $K8S_NS rollout status deploy"
echo
echo "  Xem log:"
echo "    kubectl -n $K8S_NS get pod -o wide"
echo "    kubectl -n $K8S_NS logs -f --tail=100 deploy/$IMAGE"
echo
echo "  Hoặc dùng menu:  ./run.sh   (mục 9 rollout, 11 xem log)"
