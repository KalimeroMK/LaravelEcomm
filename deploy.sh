#!/bin/bash
# =============================================================================
# DEPLOY SCRIPT FOR PRODUCTION
# =============================================================================
# Употреба: ./deploy.sh
#
# Напомена: Cloudflare тунелот е централизиран во /root/cloudflare-tunnel/
# и не е дел од овој compose.
# =============================================================================

set -e

echo "🚀 Starting deployment to production..."
echo ""

# Провери дали сме на продукција
if [ -f ".env.prod" ]; then
    echo "✓ Production environment detected (.env.prod found)"
    ENV_FILE=".env.prod"
else
    echo "⚠️  Warning: .env.prod not found, using .env"
    ENV_FILE=".env"
fi

# Копирај го prod env во .env за Docker
cp $ENV_FILE .env
echo "✓ Environment file copied"

echo ""
echo "📦 Pulling latest changes from git..."
git pull origin main 2>/dev/null || echo "⚠️  No git remote or already up to date"

echo ""
echo "🐳 Building and starting containers..."
docker compose -f docker-compose.prod.yml down --remove-orphans
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d --build

echo ""
echo "⏳ Waiting for services to start..."
sleep 10

echo ""
echo "🔧 Running Laravel optimizations..."

# Миграции
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force 2>/dev/null || echo "⚠️  Migration skipped (may already be up to date)"

# Кеширање на конфигурација, рути и views
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan event:cache

# Чистење на стари кешови
docker compose -f docker-compose.prod.yml exec -T app php artisan cache:clear 2>/dev/null || true
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear 2>/dev/null || true

echo ""
echo "🔒 Setting permissions..."
docker compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
docker compose -f docker-compose.prod.yml exec -T app chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📊 Service status:"
docker compose -f docker-compose.prod.yml ps

echo ""
echo "🌐 Website: https://e-comm.mk"
echo "📋 Logs: docker compose -f docker-compose.prod.yml logs -f"
