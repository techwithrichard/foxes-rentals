@echo off
echo ========================================
echo Complete Fix for Foxes Rental System
echo ========================================
echo.

echo CRITICAL: You need to enable PHP extensions first!
echo.
echo 1. Open C:\xampp\php\php.ini in a text editor
echo 2. Find these lines and remove the semicolon (;):
echo    ;extension=gd
echo    ;extension=zip
echo 3. Change them to:
echo    extension=gd
echo    extension=zip
echo 4. Save the file and restart XAMPP
echo.
echo Press any key after you've enabled the extensions...
pause

echo.
echo Step 1: Clearing composer cache...
composer clear-cache

echo.
echo Step 2: Installing dependencies with timeout fix...
composer install --prefer-dist --no-interaction --timeout=600

echo.
echo Step 3: Generating application key...
php artisan key:generate

echo.
echo Step 4: Installing npm dependencies...
npm install

echo.
echo Step 5: Building assets...
npm run dev

echo.
echo ========================================
echo Starting Laravel server...
echo Application will be available at: http://localhost:8000
echo ========================================
echo.

php artisan serve

