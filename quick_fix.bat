@echo off
echo ========================================
echo Quick Fix for Foxes Rental System
echo ========================================
echo.

echo Installing dependencies with extension workaround...
composer install --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip

echo.
echo Generating application key...
php artisan key:generate

echo.
echo Installing npm dependencies...
npm install

echo.
echo Building assets...
npm run dev

echo.
echo ========================================
echo Starting Laravel server...
echo Application will be available at: http://localhost:8000
echo ========================================
echo.

php artisan serve

