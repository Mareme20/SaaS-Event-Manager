# TODO

## Healthcheck/deployment fix (Railway)
- [ ] Update `deployment/entrypoint.sh` so Nginx + PHP-FPM start immediately before running `php artisan migrate`.
- [ ] Add a small readiness check loop hitting `http://127.0.0.1:$PORT/health` (with fallback to port 80 if needed).
- [ ] Run `php artisan migrate --force` after the service is reachable (preferably backgrounded or after readiness).
- [ ] Ensure `php artisan config:cache route:cache view:cache` does not block healthcheck too long (either after readiness or keep but after Nginx start).
- [ ] Redeploy and confirm Railway healthcheck passes.

