# TODO

## DB readiness fix (Railway)
- [ ] Inspect current startup/migration flow (entrypoint) and confirm DB env assumptions
- [ ] Add DB connectivity wait loop in `deployment/entrypoint.sh` (using `php artisan tinker` or mysql ping via PHP/PDO)
- [ ] Ensure app uses correct Railway DB host (prefer `DATABASE_URL` / env mapping)
- [ ] Run `php artisan migrate --force` only after DB is reachable
- [ ] Rebuild + redeploy on Railway
- [ ] Verify logs: no more `SQLSTATE[HY000] [2002] Connection refused`

