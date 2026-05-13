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
nginx -t 2>&1 >&2 || true

# Show whether nginx has already bound (useful if previous process still running)
(if pgrep -x nginx >/dev/null 2>&1; then echo "[entrypoint] nginx already running" >&2; else echo "[entrypoint] nginx not running yet" >&2; fi)

# Start PHP-FPM first so /health can be served as soon as Nginx starts.

# Force PHP-FPM to listen on the address nginx expects.
# The nginx config uses: fastcgi_pass 127.0.0.1:9000;
mkdir -p /usr/local/etc/php-fpm.d
cat > /usr/local/etc/php-fpm.d/zz-railway-listen.conf <<'EOF'
[www]
listen = 127.0.0.1:9000
listen.allowed_clients = 127.0.0.1
EOF

# Validate php-fpm config and start PHP-FPM in background
php-fpm -t
php-fpm -D

# Start Nginx once, then wait until the app responds to /health.
# This prevents starting multiple Nginx instances (which can cause "Address already in use").
nginx -g "daemon off;" &

for i in $(seq 1 30); do
  if wget -qO- "http://127.0.0.1:${PORT}/health" >/dev/null 2>&1; then
    break
  fi
  sleep 1
done

# If Nginx failed to bind (e.g. PORT already in use), stop early.
if ! pgrep -x nginx >/dev/null 2>&1; then
  echo "Nginx failed to start (port bind error likely)" >&2
  exit 1
fi



# Optimizations (safe to do after the service is reachable)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (after startup readiness)
php artisan migrate --force

# Keep container alive (nginx is already running in background)
# Wait on nginx process.
wait


