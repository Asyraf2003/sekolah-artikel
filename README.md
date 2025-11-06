# =============================================================================
#  School with Article — Laravel 12
# =============================================================================
# A modern school website with article management built using Laravel 12,
# TailwindCSS, Vite, and MySQL/MariaDB.
#
# This README is intentionally wrapped in a single Bash code block so you can
# copy–paste it directly into your GitHub README.md. All prose is written as
# Bash comments (# ...) and all commands are ready to run on Linux/macOS/WSL.
#
# Repository:
#   https://github.com/Asyraf2003/school-with-article
#
# Preview image:
#   Place your screenshot at: public/img/preview.png
#   (GitHub will display it when someone browses the repo)
#
# =============================================================================
#  🖼️ Project Preview
# =============================================================================
# To show a preview image on GitHub, commit a file at:
#   public/img/preview.png
# and mention it in your README text (outside this code block) like:
#   <img src="public/img/preview.png" width="800" alt="School with Article Preview" />
#
# =============================================================================
#  ⚙️ Tech Stack
# =============================================================================
# - Framework:  Laravel 12  (PHP 8.2+)
# - Frontend:   TailwindCSS, Vite
# - Database:   MySQL / MariaDB
# - Tooling:    Composer, npm
# - Runtime:    PHP Artisan (built-in dev server)
#
# =============================================================================
#  ✅ Requirements (install these first)
# =============================================================================
# You need the following available in your shell:
#   git, php (8.2+), composer, node (18+) & npm, mysql or mariadb
#
# Ubuntu/Debian (hint):
#   sudo apt update && sudo apt install -y php php-cli php-mbstring php-xml php-curl php-mysql \
#     unzip git nodejs npm mysql-server
#
# Arch (hint):
#   sudo pacman -S --needed php composer nodejs npm mariadb git
#
# Start MySQL/MariaDB and set a root password if needed.
#
# =============================================================================
#  🚀 Quick Installation — Clone → Configure → Migrate → Build → Serve
# =============================================================================
# Copy and run these commands one-by-one.
#
# 1) Clone the project
git clone https://github.com/Asyraf2003/school-with-article.git
cd school-with-article

# 2) Install PHP dependencies
composer install

# 3) Create .env and generate app key
cp -n .env.example .env 2>/dev/null || cp .env.example .env
php artisan key:generate

# 4) Configure database in .env (edit with your values)
# -----------------------------------------------------------------------------
# Open .env and set:
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=school_db
#   DB_USERNAME=root
#   DB_PASSWORD=your_password
#
# Create the database (example):
#   mysql -u root -p -e "CREATE DATABASE school_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
#
# 5) (Optional but recommended) Configure Mail in .env
# -----------------------------------------------------------------------------
# If you want email features (password reset, notifications), set:
#   MAIL_MAILER=smtp
#   MAIL_HOST=smtp.gmail.com
#   MAIL_PORT=587
#   MAIL_USERNAME=emailkamu@example.com
#   MAIL_PASSWORD=app_password_or_your_password
#   MAIL_ENCRYPTION=tls
#   MAIL_FROM_ADDRESS="emailkamu@example.com"
#   MAIL_FROM_NAME="${APP_NAME}"
#
# NOTE (Gmail users):
# - If your Google account has 2FA, create an "App password" and use that in
#   MAIL_PASSWORD (recommended).
# - Less-secure app access is deprecated by Google; use App Passwords.
#
# 6) Run migrations (and seeders if available)
php artisan migrate --force || php artisan migrate
php artisan db:seed --force || true

# 7) Link storage (for public file access)
php artisan storage:link || true

# 8) Install frontend dependencies and build assets
npm install
npm run build   # (or `npm run dev` for HMR during development)

# 9) Serve the application (dev server)
php artisan serve --host=127.0.0.1 --port=8000

# App URL:
#   http://127.0.0.1:8000
#
# If you want live-reload during development, open a second terminal and run:
#   npm run dev
#
# =============================================================================
#  🧪 One-Paste Auto-Setup Script (MySQL + Mail) — Optional
# =============================================================================
# Power users can run this block to apply common .env settings automatically.
# Adjust variables (DB_NAME, DB_USER, DB_PASS, MAIL settings) before running.
#
#   bash <(cat <<'EOS'
#   set -euo pipefail
#   DB_NAME="school_db"
#   DB_USER="root"
#   DB_PASS=""                # fill if you have one
#
#   MAIL_MAILER="smtp"
#   MAIL_HOST="smtp.gmail.com"
#   MAIL_PORT="587"
#   MAIL_USERNAME="emailkamu@example.com"
#   MAIL_PASSWORD="app_password_here"
#   MAIL_ENCRYPTION="tls"
#   MAIL_FROM_ADDRESS="emailkamu@example.com"
#
#   sed -i.bak \
#     -e "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" \
#     -e "s/^DB_HOST=.*/DB_HOST=127.0.0.1/" \
#     -e "s/^DB_PORT=.*/DB_PORT=3306/" \
#     -e "s/^DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" \
#     -e "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" \
#     -e "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" \
#     -e "s/^MAIL_MAILER=.*/MAIL_MAILER=${MAIL_MAILER}/" \
#     -e "s/^MAIL_HOST=.*/MAIL_HOST=${MAIL_HOST}/" \
#     -e "s/^MAIL_PORT=.*/MAIL_PORT=${MAIL_PORT}/" \
#     -e "s/^MAIL_USERNAME=.*/MAIL_USERNAME=${MAIL_USERNAME}/" \
#     -e "s/^MAIL_PASSWORD=.*/MAIL_PASSWORD=${MAIL_PASSWORD}/" \
#     -e "s/^MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=${MAIL_ENCRYPTION}/" \
#     -e "s/^MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"${MAIL_FROM_ADDRESS}\"/" \
#     -e "s/^MAIL_FROM_NAME=.*/MAIL_FROM_NAME=\"${APP_NAME:-School with Article}\"/" \
#     .env
#   echo "Updated .env for DB and Mail."
#   EOS
#   )
#
# =============================================================================
#  🧰 Useful Commands
# =============================================================================
# Run dev server
#   php artisan serve
#
# Vite dev server (HMR)
#   npm run dev
#
# Build production assets
#   npm run build
#
# Fresh DB + seed
#   php artisan migrate:fresh --seed
#
# Clear caches
#   php artisan optimize:clear
#
# =============================================================================
#  🩵 Troubleshooting
# =============================================================================
# .env not found
#   cp .env.example .env && php artisan key:generate
#
# Database connection errors
#   Ensure MySQL is running and credentials in .env are correct.
#   Try: mysql -u root -p -e "SHOW DATABASES;"
#
# 500 error / blank page
#   php artisan optimize:clear && tail -n 200 storage/logs/laravel.log
#
# CSS/JS not updating
#   npm run dev (for HMR) or npm run build, then hard refresh the browser.
#
# Permissions (Linux)
#   chmod -R ug+rwx storage bootstrap/cache
#
# Mail not sending (Gmail)
#   Use an App Password for MAIL_PASSWORD if 2FA enabled.
#   Confirm your provider doesn’t block SMTP.
#
# =============================================================================
#  📜 License
# =============================================================================
# This project is open-sourced under the MIT License.
#
# =============================================================================
#  👨‍💻 Author
# =============================================================================
# Asyraf — https://github.com/Asyraf2003
# Project — https://github.com/Asyraf2003/school-with-article
#
# =============================================================================
#  ⭐ Star the Repo
# =============================================================================
# If this project helped you, please star it on GitHub!
#
# =============================================================================
#  Saran terbaik (from maintainer)
# =============================================================================
# 1) Keep the Quick Installation minimal and on top.
# 2) Provide both manual steps and an optional auto-setup block.
# 3) Add a clean preview banner at public/img/preview.png to look professional.
# 4) If you later add Docker, create a "Profiles" section (Local MySQL vs Docker).
# =============================================================================
