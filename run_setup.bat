@echo off
echo ========================================
echo Foxes Rental Management System Setup
echo ========================================
echo.

echo Step 1: Installing PHP dependencies with Composer...
composer install --no-interaction --prefer-dist
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    echo Please make sure Composer is installed and accessible.
    pause
    exit /b 1
)
echo Composer install completed successfully!
echo.

echo Step 2: Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Failed to generate application key!
    echo Make sure .env file exists and is properly configured.
    pause
    exit /b 1
)
echo Application key generated successfully!
echo.

echo Step 3: Installing Node.js dependencies...
npm install
if %errorlevel% neq 0 (
    echo ERROR: npm install failed!
    echo Please make sure Node.js and npm are installed.
    pause
    exit /b 1
)
echo npm install completed successfully!
echo.

echo Step 4: Running database migrations...
echo Please ensure MySQL is running and database 'foxes_rentals' exists.
echo Press any key to continue with migrations...
pause
php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Database migration failed!
    echo Please check your database configuration in .env file.
    pause
    exit /b 1
)
echo Database migrations completed successfully!
echo.

echo Step 5: Seeding database...
php artisan db:seed
if %errorlevel% neq 0 (
    echo WARNING: Database seeding failed, but continuing...
    echo You may need to create admin user manually.
)
echo Database seeding completed!
echo.

echo Step 6: Creating storage link...
php artisan storage:link
if %errorlevel% neq 0 (
    echo WARNING: Storage link creation failed, but continuing...
)
echo Storage link created!
echo.

echo Step 7: Building frontend assets...
npm run dev
if %errorlevel% neq 0 (
    echo ERROR: Asset building failed!
    echo Please check your Node.js installation.
    pause
    exit /b 1
)
echo Frontend assets built successfully!
echo.

echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Starting Laravel development server...
echo Application will be available at: http://localhost:8000
echo.
echo Default admin credentials:
echo Email: admin@admin.com
echo Password: demo123#
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php artisan serve
