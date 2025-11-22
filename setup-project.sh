#!/bin/bash
set -e

echo "[*] Starting project setup..."

# 1. Pull latest project
echo "[*] Pulling latest code from Git..."
git pull origin main

# 2. Copy .env example → .env (jika belum ada)
if [ ! -f .env ]; then
    echo "[*] Creating .env from .env.example..."
    cp .env.example .env
fi

# 3. Generate app key
echo "[*] Generating Laravel APP_KEY..."
docker exec -it school-app php artisan key:generate --force || true

# 4. Build & start all containers
echo "[*] Starting Docker containers..."
docker compose up -d --build

# 5. Install PHP dependencies
echo "[*] Installing Composer dependencies..."
docker exec -it school-app composer install --no-interaction --prefer-dist

# 6. Run Laravel migrations
echo "[*] Running database migrations..."
docker exec -it school-app php artisan migrate --force

# 7. Install JS dependencies
echo "[*] Installing Node dependencies..."
docker exec -it school-node npm install

# 8. Build assets (production build)
echo "[*] Building front-end assets..."
docker exec -it school-node npm run build

# 9. Set permissions
echo "[*] Fixing storage & cache permissions..."
docker exec -it school-app chmod -R 775 storage bootstrap/cache

echo ""
echo "[*] Project restored successfully!"
echo "[*] Visit: http://school.local"
