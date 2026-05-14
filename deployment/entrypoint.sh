#!/bin/sh
set -eu

: "${PORT:?PORT is required (Railway dynamic port not set).}"

# Substitute Railway dynamic port into Nginx config template.
# We replace the ${PORT} token before starting nginx.
echo "[entrypoint] PORT=${PORT}" >&2

# Substitute Railway dynamic port into Nginx config template.
# (The file is copied from deployment/nginx.conf at build time.)
sed -i "s/\${PORT}/$PORT/g" /etc/nginx/http.d/default.conf

echo "[entrypoint] nginx listen line:" >&2
sed -n '1,200p' /etc/nginx/http.d/default.conf | grep -E "^\s*listen\b" -n >&2 || true

echo "[entrypoint] nginx -t output:" >&2
nginx -t 2>&1 >&2 || {
  echo "[entrypoint] nginx -t failed" >&2
  exit 1
}


# Show whether nginx has already bound (useful if previous process still running)
(if pgrep -x nginx >/dev/null 2>&1; then echo "[entrypoint] nginx already running" >&2; else echo "[entrypoint] nginx not running yet" >&2; fi)

# Start PHP-FPM first so /health can be served as soon as Nginx starts.

# Force PHP-FPM to listen on the address nginx expects with optimized settings for Railway memory.
mkdir -p /usr/local/etc/php-fpm.d
cat > /usr/local/etc/php-fpm.d/zz-railway-listen.conf <<'EOF'
[www]
listen = 127.0.0.1:9000
listen.allowed_clients = 127.0.0.1
pm = ondemand
pm.max_children = 5
pm.process_idle_timeout = 10s
pm.max_requests = 200
request_terminate_timeout = 60s
php_admin_flag[log_errors] = on
php_admin_value[error_log] = /proc/self/fd/2
EOF

# Validate php-fpm config and start PHP-FPM in background
php-fpm -t
php-fpm -D

# Start Nginx once, then wait until the app responds to /health.
# IMPORTANT: start nginx as the main process (no extra wait), to avoid
# double-process/port conflicts on Railway.

echo "[entrypoint] starting nginx..." >&2
# Start nginx and capture its startup failure logs.
# We do NOT silence output here so Railway logs show the real bind error.
nginx -g "daemon off;" &
NGINX_PID=$!


for i in $(seq 1 30); do
  if wget -qO- "http://127.0.0.1:${PORT}/health" >/dev/null 2>&1; then
    break
  fi
  sleep 1
done

# Vérification finale de la disponibilité du service HTTP
if ! wget -qO- "http://127.0.0.1:${PORT}/health" >/dev/null 2>&1; then
  echo "[entrypoint] Erreur fatale : Nginx ne répond pas sur la route /health après 30 secondes." >&2
  kill "$NGINX_PID"
  exit 1
fi

echo "[entrypoint] Nginx et PHP-FPM sont opérationnels !" >&2


# Nettoyer impérativement les anciens caches obsolètes pour forcer la lecture de l'environnement Railway
php artisan config:clear
php artisan route:clear

# Wait for DB readiness BEFORE any Laravel DB interaction (migrations, cached config, etc.)
# Prevents: SQLSTATE[HY000] [2002] Connection refused

echo "[entrypoint] Checking database connectivity (PDO/mysql) ..." >&2

# Give Railway DB time to be reachable (default 60s)
DB_WAIT_SECONDS="${DB_WAIT_SECONDS:-60}"

for i in $(seq 1 "$DB_WAIT_SECONDS"); do
  php -r '
    $host = getenv("DB_HOST") ?: "127.0.0.1";
    $port = getenv("DB_PORT") ?: "3306";
    $db   = getenv("DB_DATABASE") ?: "forge";
    $user = getenv("DB_USERNAME") ?: "forge";
    $pass = getenv("DB_PASSWORD") ?: "";

    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 2]);
    $pdo->query("select 1");
    exit(0);
  ' >/dev/null 2>&1 && {
    echo "[entrypoint] Database reachable." >&2
    break
  }

  if [ "$i" -eq "$DB_WAIT_SECONDS" ]; then
    echo "[entrypoint] Fatal: database not reachable after ${DB_WAIT_SECONDS}s." >&2
    exit 1
  fi

  if [ "$((i % 5))" -eq 0 ]; then
    echo "[entrypoint] waiting for DB... (${i}/${DB_WAIT_SECONDS})" >&2
  fi

  sleep 1
done

# Optimizations (safe to do after the service is reachable)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (after DB readiness)
php artisan migrate --force

# Keep container alive.
# Wait for nginx PID.
wait "$NGINX_PID"
