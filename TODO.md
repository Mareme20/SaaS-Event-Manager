# TODO

## Healthcheck/deployment fix (Railway)
- [x] Update `deployment/entrypoint.sh` so Nginx + PHP-FPM start immediately before running `php artisan migrate`.
- [x] Add a small readiness check loop hitting `http://127.0.0.1:$PORT/health`.
- [x] Run `php artisan migrate --force` after the service is reachable.
- [x] Ensure `php artisan config:cache route:cache view:cache` happens after readiness.
- [ ] Redeploy and confirm Railway healthcheck passes.
  
  
## GitHub Actions CI + Deploy sur Railway
- [x] Créer le workflow GitHub Actions (GHCR -> Railway)
- [ ] Configurer les secrets GitHub : `RAILWAY_TOKEN`, `RAILWAY_PROJECT_ID`
- [ ] Pusher sur `main` pour valider le build + deploy



