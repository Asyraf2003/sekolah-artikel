# ==============================================================
# 🌐 School with Article — Laravel 12
# ==============================================================
# A modern school & article management system built with Laravel 12,
# TailwindCSS, Vite, and MySQL. This guide shows how to install,
# configure, and run the project from GitHub to local server.
# ==============================================================
# 🧩 Tech Stack
# --------------------------------------------------------------
# - PHP 8.2+ & Laravel 12
# - MySQL / MariaDB
# - TailwindCSS + Vite
# - Composer & npm
# ==============================================================
# 📦 Quick Setup
# --------------------------------------------------------------
git clone https://github.com/Asyraf2003/school-with-article.git
cd school-with-article
composer install
cp .env.example .env
php artisan key:generate

# --- Configure Database (.env) ---
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_db
# DB_USERNAME=root
# DB_PASSWORD=

# Create database:
# mysql -u root -p -e "CREATE DATABASE school_db;"

# --- (Optional) Mail Setup (.env) ---
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=email@example.com
# MAIL_PASSWORD=your_app_password
# MAIL_ENCRYPTION=tls
# MAIL_FROM_ADDRESS="email@example.com"
# MAIL_FROM_NAME="${APP_NAME}"

php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve

# ✅ Access: http://127.0.0.1:8000
# ==============================================================
# ⚙️ Common Commands
# --------------------------------------------------------------
# php artisan serve           → Run dev server
# npm run dev                 → Hot reload assets
# php artisan migrate:fresh   → Reset DB
# php artisan optimize:clear  → Clear cache
# ==============================================================
# 🩵 Troubleshooting
# --------------------------------------------------------------
# ❗ Blank Page → php artisan optimize:clear
# ❗ DB Error → Check .env & run MySQL service
# ❗ CSS/JS Missing → npm run dev or npm run build
# ❗ Mail Error → Use Gmail App Password (2FA)
# ==============================================================
# 👨‍💻 Author: Asyraf (https://github.com/Asyraf2003)
# ⭐ Repo: https://github.com/Asyraf2003/school-with-article
# 🖼️ Preview: public/img/preview.png
# License: MIT
# ==============================================================
