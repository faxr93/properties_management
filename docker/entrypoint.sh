#!/bin/sh
set -e

cd /app

echo "──────────────────────────────────────────────────────────────"
echo " Properties POC · Render boot"
echo "──────────────────────────────────────────────────────────────"

# ----------------------------------------------------------------------------
# Map Render's DATABASE_URL onto Laravel's DB_URL so config/database.php picks
# it up via env('DB_URL'). Render injects DATABASE_URL as
#   postgres://user:pass@host:port/dbname
# which is the format Laravel's URL parser understands.
# ----------------------------------------------------------------------------
if [ -n "${DATABASE_URL}" ] && [ -z "${DB_URL}" ]; then
    export DB_URL="${DATABASE_URL}"
fi

# ----------------------------------------------------------------------------
# APP_KEY is required. Fail loudly rather than starting unauthenticated.
# Generate one with: docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
# Then set it as a Render env var.
# ----------------------------------------------------------------------------
if [ -z "${APP_KEY}" ]; then
    echo "❌ APP_KEY environment variable is not set."
    echo "   Generate one and add it to your Render service env vars:"
    echo "     APP_KEY=base64:$(php -r "echo base64_encode(random_bytes(32));")"
    exit 1
fi

# ----------------------------------------------------------------------------
# Clear any stale caches left over from previous builds in the same image
# layers, then re-cache for the current environment.
# ----------------------------------------------------------------------------
php artisan config:clear >/dev/null 2>&1 || true
php artisan route:clear  >/dev/null 2>&1 || true
php artisan view:clear   >/dev/null 2>&1 || true

# ----------------------------------------------------------------------------
# Run database migrations. --force skips the interactive prompt that would
# otherwise halt the container in production.
# ----------------------------------------------------------------------------
echo "🗄️   Running database migrations…"
if ! php artisan migrate --force --no-interaction; then
    echo "❌ Migration failed. Aborting boot so Render marks this deploy as failed."
    exit 1
fi

# ----------------------------------------------------------------------------
# Prime caches for production. config:cache MUST come after migrations
# because some config providers can introspect the DB.
# ----------------------------------------------------------------------------
echo "⚡  Caching config, routes, and views…"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ----------------------------------------------------------------------------
# Symlink storage/app/public → public/storage so uploaded files are reachable.
# Idempotent; fails silently if the link already exists.
# ----------------------------------------------------------------------------
php artisan storage:link >/dev/null 2>&1 || true

echo "🚀  Starting FrankenPHP on :${PORT:-8080}"
echo "──────────────────────────────────────────────────────────────"

exec "$@"
