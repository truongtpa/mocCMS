#!/usr/bin/env bash

set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

APP_SLUG="${APP_SLUG:-$(basename "$ROOT" | sed -E 's/\..*$//' | tr '[:upper:]' '[:lower:]' \
    | sed -E 's/[^a-z0-9]+/-/g; s/^-+//; s/-+$//')}"
[ -n "$APP_SLUG" ] || { echo "✗ Không suy ra được tên từ thư mục — chạy lại với APP_SLUG=ten-project" >&2; exit 1; }

export IMAGE="${IMAGE:-$APP_SLUG}"
export TAG="${TAG:-latest}"
export PROJECT="${PROJECT:-${APP_SLUG//-/_}}"
export ENV_FILE="${ENV_FILE:-.env}"
export APP_PORT="${APP_PORT:-3005}"
COMPOSE_FILE="docker/docker-compose.yml"

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

DIM=$'\033[2m'; BOLD=$'\033[1m'; GREEN=$'\033[32m'; YELLOW=$'\033[33m'; OFF=$'\033[0m'

# In lệnh rồi chạy — để copy lại dùng ngoài menu
run() { printf '%s$ %s%s\n' "$DIM" "$*" "$OFF"; "$@"; }
dc()  { run docker compose -f "$COMPOSE_FILE" -p "$PROJECT" "$@"; }
kc()  { run kubectl -n "$K8S_NS" "$@"; }

need() { command -v "$1" >/dev/null 2>&1 || { echo "✗ Chưa cài $1" >&2; return 1; }; }

has_service() {
    docker compose -f "$COMPOSE_FILE" -p "$PROJECT" config --services 2>/dev/null | grep -qx "$1"
}

# Chọn 1 deployment trong namespace (không hardcode tên deploy)
pick_deploy() {
    local list n i=1 choice
    list="$(kubectl -n "$K8S_NS" get deploy -o jsonpath='{range .items[*]}{.metadata.name}{"\n"}{end}' 2>/dev/null)"
    [ -n "$list" ] || { echo "✗ Namespace '$K8S_NS' không có deployment nào" >&2; return 1; }
    n="$(printf '%s\n' "$list" | wc -l | tr -d ' ')"
    if [ "$n" = "1" ]; then printf '%s' "$list"; return 0; fi
    {
        echo "Chọn deployment:"
        while IFS= read -r d; do echo "  $i) $d"; i=$((i+1)); done <<< "$list"
        printf 'Số: '
    } >&2
    read -r choice
    printf '%s\n' "$list" | sed -n "${choice}p"
}

menu() {
    cat <<EOF

${BOLD}${APP_SLUG}${OFF}  —  image ${GREEN}${IMAGE}:${TAG}${OFF}   compose project ${GREEN}${PROJECT}${OFF}

${BOLD}Local (docker compose)${OFF}
   1) Build image rồi chạy          ./build.sh + compose up -d
   2) Chạy / khởi động lại          compose up -d
   3) Xem log app (follow)
   4) Xem log worker (follow)
   5) Trạng thái container          compose ps
   6) Vào shell trong container
   7) Dừng & xoá container          compose down

${BOLD}Kubernetes${OFF}  (namespace: ${YELLOW}${K8S_NS}${OFF})
   8) Apply manifest                kubectl apply -f k8s/
   9) Rollout restart               (kéo lại image mới nhất)
  10) Trạng thái pod / deployment
  11) Xem log pod (follow)
  12) Vào shell trong pod
  13) Mô tả pod lỗi                 kubectl describe

   0) Thoát
EOF
}

action() {
    case "$1" in
        1)  run ./build.sh && dc up -d --remove-orphans && dc ps
            echo; echo "✓ Web: http://localhost:$APP_PORT" ;;
        2)  dc up -d --remove-orphans && dc ps
            echo; echo "✓ Web: http://localhost:$APP_PORT" ;;
        3)  echo "${DIM}Ctrl-C để thoát theo dõi log${OFF}"; dc logs -f --tail=100 app ;;
        4)  has_service worker || { echo "✗ Service 'worker' đang bị comment trong $COMPOSE_FILE" >&2; return 1; }
            echo "${DIM}Ctrl-C để thoát theo dõi log${OFF}"; dc logs -f --tail=100 worker ;;
        5)  dc ps ;;
        6)  dc exec app sh ;;
        7)  dc down ;;

        8)  need kubectl || return 1
            if ls k8s/*.yaml >/dev/null 2>&1; then
                run kubectl apply -f k8s/app-config.yaml -f k8s/deployment.yaml
            else
                echo "✗ Chưa có k8s/*.yaml — copy từ file .example rồi sửa lại" >&2
            fi ;;
        9)  need kubectl || return 1
            local d; d="$(pick_deploy)" || return 1
            kc rollout restart "deploy/$d"
            kc rollout status "deploy/$d" ;;
        10) need kubectl || return 1
            kc get deploy,pod -o wide ;;
        11) need kubectl || return 1
            local d; d="$(pick_deploy)" || return 1
            echo "${DIM}Ctrl-C để thoát theo dõi log${OFF}"
            kc logs -f --tail=100 "deploy/$d" ;;
        12) need kubectl || return 1
            local d; d="$(pick_deploy)" || return 1
            kc exec -it "deploy/$d" -- sh ;;
        13) need kubectl || return 1
            local p
            p="$(kubectl -n "$K8S_NS" get pod -o jsonpath='{.items[0].metadata.name}' 2>/dev/null)"
            [ -n "$p" ] || { echo "✗ Không có pod nào trong '$K8S_NS'" >&2; return 1; }
            kc describe "pod/$p" ;;

        0|q|quit|exit) return 9 ;;
        *)  echo "✗ Không có mục '$1'" >&2; return 1 ;;
    esac
}

# Chạy thẳng 1 mục: ./run.sh 3
if [ $# -gt 0 ]; then
    action "$1"; exit $?
fi

while true; do
    menu
    printf 'Chọn: '
    read -r choice || break
    [ -n "${choice// /}" ] || continue
    action "$choice"
    [ $? -eq 9 ] && break
    printf '\n%s— Enter để về menu —%s' "$DIM" "$OFF"; read -r _
done
