#!/bin/bash
set -e

echo "[*] Starting project setup..."

########################################
# 1. Pull latest project
########################################
echo "[*] Pulling latest code from Git..."
git pull origin main || true


########################################
# 2. Ensure .env exists
########################################
if [ ! -f .env ]; then
    echo "[*] Creating .env from .env.example..."
    cp .env.example .env
fi

chmod 664 .env


########################################
# 3. Start Docker stack FIRST
########################################
echo "[*] Starting Docker containers..."
docker compose up -d --build

sleep 2


########################################
# 4. Fix permissions BEFORE artisan/compile
########################################
echo "[*] Setting storage permissions..."
sudo chmod -R 777 storage bootstrap/cache || true


########################################
# 4.5 Wait containers ready
########################################
echo "[*] Waiting for containers to be fully ready..."

until docker exec school-app sh -c "php -v" >/dev/null 2>&1; do
    echo "    - Waiting for school-app..."
    sleep 2
done

until docker exec school-node sh -c "node -v" >/dev/null 2>&1; do
    echo "    - Waiting for school-node..."
    sleep 2
done

echo "[*] All containers ready."


########################################
# 5. Composer install
########################################
echo "[*] Installing Composer dependencies..."
docker exec -it school-app composer install --no-interaction --prefer-dist


########################################
# 6. Generate Laravel APP_KEY
########################################
echo "[*] Generating Laravel APP_KEY..."
docker exec -it school-app php artisan key:generate --force


########################################
# 7. Migrate DB
########################################
echo "[*] Running database migrations..."
docker exec -it school-app php artisan migrate --force || true


########################################
# 7.5 Seeders
########################################
echo "[*] Running database seeders..."
docker exec -it school-app php artisan db:seed --force || true


########################################
# 7.7 Storage Symlink (PENTING)
########################################
echo "[*] Creating Laravel storage symlink..."
docker exec -it school-app php artisan storage:link || true

sudo chmod -R 777 storage public/storage || true


########################################
# 8. Frontend build
########################################
echo "[*] Installing Node dependencies..."
docker exec -it school-node npm install

echo "[*] Building front-end assets..."
docker exec -it school-node npm run build

rm -f storage/vite.hot


########################################
# DONE
########################################
echo ""
echo "[*] Project restored successfully!"
echo "[*] Visit: http://school.local"
