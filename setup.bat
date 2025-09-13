@echo off
echo Setting up Foxes Rental Management System...

echo.
echo Step 1: Installing PHP dependencies...
composer install

echo.
echo Step 2: Installing Node.js dependencies...
npm install

echo.
echo Step 3: Generating application key...
php artisan key:generate

echo.
echo Step 4: Please create database 'foxes_rentals' in MySQL before continuing...
echo Press any key when database is ready...
pause

echo.
echo Step 5: Running migrations...
php artisan migrate

echo.
echo Step 6: Seeding database...
php artisan db:seed

echo.
echo Step 7: Creating storage link...
php artisan storage:link

echo.
echo Step 8: Building assets...
npm run dev

echo.
echo Step 9: Starting server...
echo Application will be available at http://localhost:8000
echo Default admin credentials:
echo Email: admin@admin.com
echo Password: demo123#
echo.
php artisan serve
