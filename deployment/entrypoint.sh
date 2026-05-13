#!/bin/sh
set -eu

: "${PORT:?PORT is required (Railway dynamic port not set).}"

# Substitute Railway dynamic port into Nginx config template.
# We replace the ${PORT} token before starting nginx.
sed -i "s/\${PORT}/$PORT/g" /etc/nginx/http.d/default.conf

# Ensure nginx picks up the new listen port.
nginx -t

# Optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Force PHP-FPM to listen on the address nginx expects.
# The nginx config uses: fastcgi_pass 127.0.0.1:9000;
# On Alpine images the default listen address may differ, so we override it.
mkdir -p /usr/local/etc/php-fpm.d
cat > /usr/local/etc/php-fpm.d/zz-railway-listen.conf <<'EOF'
[www]
listen = 127.0.0.1:9000
listen.allowed_clients = 127.0.0.1
EOF

# Validate php-fpm config and start PHP-FPM in background
php-fpm -t
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"

