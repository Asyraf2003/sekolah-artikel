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

# Fix permission so container can edit it
chmod 664 .env


########################################
# 3. Start Docker stack FIRST (important)
########################################
echo "[*] Starting Docker containers..."
docker compose up -d --build

# Wait for containers to settle
sleep 2


########################################
# 4. Fix permissions BEFORE artisan/compile
########################################
echo "[*] Setting storage permissions..."
sudo chmod -R 777 storage bootstrap/cache || true


########################################
# 5. Install Composer dependencies
########################################
echo "[*] Installing Composer dependencies..."
docker exec -it school-app composer install --no-interaction --prefer-dist


########################################
# 6. Generate Laravel APP_KEY (safe)
########################################
echo "[*] Generating Laravel APP_KEY..."
docker exec -it school-app php artisan key:generate --force


########################################
# 7. Run DB migrations
########################################
echo "[*] Running database migrations..."
docker exec -it school-app php artisan migrate --force || true


########################################
# 8. Install & Build frontend assets
########################################
echo "[*] Installing Node dependencies..."
docker exec -it school-node npm install

echo "[*] Building front-end assets..."
docker exec -it school-node npm run build

# Remove hot file (prevent nginx confusion)
rm -f storage/vite.hot


########################################
# DONE
########################################
echo ""
echo "[*] Project restored successfully!"
echo "[*] Visit: http://school.local"
