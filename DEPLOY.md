# Build & Deploy — hoso-sinhvien.vlute.edu.vn

**Image là base chung.** Docker thuần hay k8s đều chạy đúng một image
(FrankenPHP + Laravel Octane), chỉ khác ở script deploy.

```
                    ./build-assets.sh      JS (Vue+Vite) trong docker node
                            │
                    ./build-image.sh       image base: JS + vendor + .env
                            │
              ┌─────────────┴─────────────┐
      ./build-docker.sh            ./build-k8s.sh
   docker compose up -d          tag + push registry
   (docker/docker-compose.yml)   (kubectl apply -f k8s/)
```

## Cấu trúc

```
docker/                     # BASE — build & chạy image, không phụ thuộc nơi deploy
├── Dockerfile              #   FrankenPHP + Octane (multi-stage)
├── Dockerfile.assets       #   node build JS
├── php.prod.ini            #   opcache + JIT, upload 100M, memory 512M
├── entrypoint.sh           #   chọn role: app | worker | scheduler
├── supervisor.d/
│   ├── app.conf            #   octane
│   └── worker.conf         #   queue:work x2 + schedule:work
├── docker-compose.yml      # TARGET docker thuần: app + worker
└── minio.yml               #   S3 local cho dev (tuỳ chọn)

k8s/                        # TARGET kubernetes — chỉ manifest
├── app-config.yaml.example #   ConfigMap
└── deployment.yaml.example #   Deployment web + worker + Service

build-assets.sh  build-image.sh  build-docker.sh  build-k8s.sh
```

## Script

| Script | Vai trò |
|---|---|
| `./build-assets.sh` | Build JS bằng docker node → `public/asset/admin/build` |
| `./build-image.sh`  | build-assets + `docker buildx` → image base |
| `./build-docker.sh` | build-image + `docker compose up -d` (app + worker) |
| `./build-k8s.sh`    | build-image + tag + **push registry** |

Hai script deploy tự gọi `build-image.sh`, script này tự gọi `build-assets.sh`.
Bình thường chỉ chạy script deploy.

### Biến dùng chung

| Biến | Mặc định | Ý nghĩa |
|---|---|---|
| `ENV_FILE` | `.env` | File env cho Vite **và** nướng vào image |
| `IMAGE` | `hoso-sinhvien` | Tên image |
| `TAG` | `latest` | Tag image |
| `PLATFORM` | k8s: `linux/amd64`, docker: arch máy | Kiến trúc build |
| `SKIP_ASSETS` | `0` | `1` = bỏ qua build JS |

Riêng `build-k8s.sh`: `REGISTRY` (`172.20.6.10:32000`), `PUSH` (`1`).
Riêng `build-docker.sh`: `APP_PORT` (`3005`), `PROJECT` (`hoso_sinhvien`), `UP` (`1`), `FRESH` (`0`).

## Chạy docker thuần

```bash
./build-docker.sh
ENV_FILE=.env.production APP_PORT=8080 ./build-docker.sh
UP=0 ./build-docker.sh            # chỉ build image
FRESH=1 ./build-docker.sh         # down -v trước khi up
```

## Deploy k8s

```bash
ENV_FILE=.env.production TAG=v1.0.0 ./build-k8s.sh

cp k8s/app-config.yaml.example k8s/app-config.yaml   # sửa CHANGE_ME
cp k8s/deployment.yaml.example k8s/deployment.yaml
kubectl apply -f k8s/app-config.yaml -f k8s/deployment.yaml
```

Một image, hai Deployment khác nhau ở `CONTAINER_ROLE`:

- `app` → Octane/FrankenPHP trên `:8000`, probe `/up`
- `worker` → 2 process `queue:work` + `schedule:work` (giữ **1 replica** để
  scheduler không chạy trùng job)

## Môi trường dev

Dev chạy thẳng trên máy, không qua docker:

```bash
php artisan serve
npm run dev
```

Cần S3 local thì bật minio riêng:

```bash
docker compose -f docker/minio.yml up -d
```

## Chạy tay 1 container

```bash
docker run --rm -p 8000:8000 --env-file .env hoso-sinhvien:latest                        # web
docker run --rm -e CONTAINER_ROLE=worker --env-file .env hoso-sinhvien:latest             # queue
docker run --rm -e CONTAINER_ROLE=cli --env-file .env hoso-sinhvien:latest php artisan migrate
```

## Lưu ý

- **`.env` được nướng vào image** theo yêu cầu. Trên k8s, key trong ConfigMap
  (`envFrom`) sẽ override biến trong `.env` lúc chạy. Không commit
  `.env.production` chứa secret.
- **`config:cache` mặc định tắt.** `bootstrap/app.php` gọi `env('APP_ENV')` bên
  ngoài file config; khi config bị cache Laravel không nạp `.env` nữa nên `env()`
  trả `null`. Bật bằng `CACHE_CONFIG=true` chỉ sau khi `APP_ENV` được set qua biến
  môi trường thật, hoặc sau khi sửa `bootstrap/app.php` dùng
  `app()->environment('production')`.
- **`route:cache` đang fail** — `routes/web.php:79` và `routes/api.php:26` trùng
  tên route `StudentPortalController.getDashboardStats`. Entrypoint có `|| true`
  nên app vẫn chạy, nhưng routes không được cache (khá phí với Octane).
- **Octane giữ ứng dụng trong RAM giữa các request.** Tránh static/singleton giữ
  state theo user. `--max-requests=1000` reload worker định kỳ để giảm rủi ro.
- **Sourcemap đang bật** (`vite.config.js` → `build.sourcemap: true`) nên
  `public/asset/admin/build` ~26MB. Tắt nếu không cần Sentry unminify.
