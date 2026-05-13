#!/bin/sh
set -eu

: "${PORT:?PORT is required (Railway dynamic port not set).}"

# Substitute Railway dynamic port into Nginx config template.
# We replace the ${PORT} token before starting nginx.
sed -i "s/\${PORT}/$PORT/g" /etc/nginx/http.d/default.conf

# Ensure nginx picks up the new listen port.
nginx -t

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

# Wait until the HTTP server responds to /health
# (Railway healthcheck runs against the container network).
# We start Nginx right after PHP-FPM; this loop is a short buffer.
for i in $(seq 1 30); do
  # Start Nginx on first iteration (so Nginx becomes available for the healthcheck loop)
  if ! pgrep -x nginx >/dev/null 2>&1; then
    nginx -g "daemon off;" &
  fi

  # If nginx failed to start, don't loop forever
  if [ "$i" -eq 30 ]; then
    echo "Nginx did not start successfully" >&2
    exit 1
  fi

  if wget -qO- "http://127.0.0.1:${PORT}/health" >/dev/null 2>&1; then
    break
  fi
  sleep 1
done

# Optimizations (safe to do after the service is reachable)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (after startup readiness)
php artisan migrate --force

# Keep nginx in foreground (the container entrypoint must not exit)
# If nginx is already running in background, bring it to foreground by exec-ing.
exec nginx -g "daemon off;"

