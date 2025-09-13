@echo off
echo ========================================
echo Foxes Rental Management System
echo Performance Optimization Script
echo ========================================
echo.

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    pause
    exit /b 1
)

REM Check if Laravel is available
if not exist "artisan" (
    echo ERROR: This doesn't appear to be a Laravel project
    pause
    exit /b 1
)

echo Starting performance optimization...
echo.

echo [1/8] Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo.

echo [2/8] Optimizing Laravel for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo.

echo [3/8] Optimizing Composer autoloader...
composer dump-autoload --optimize
echo.

echo [4/8] Clearing and optimizing application cache...
php artisan optimize
echo.

echo [5/8] Running database optimizations...
php artisan migrate --force
echo.

echo [6/8] Checking OPcache status...
php -r "if (function_exists('opcache_get_status')) { $status = opcache_get_status(); echo 'OPcache: ' . ($status['opcache_enabled'] ? 'ENABLED' : 'DISABLED') . PHP_EOL; } else { echo 'OPcache: NOT AVAILABLE' . PHP_EOL; }"
echo.

echo [7/8] Generating optimized assets...
if exist "package.json" (
    echo Running npm build...
    npm run build
) else (
    echo No package.json found, skipping asset optimization
)
echo.

echo [8/8] Final optimization check...
php artisan about
echo.

echo ========================================
echo OPTIMIZATION COMPLETE!
echo ========================================
echo.
echo Performance optimizations applied:
echo - Laravel caches optimized
echo - Composer autoloader optimized
echo - Application optimized for production
echo - Database migrations applied
echo.
echo Next steps:
echo 1. Enable OPcache in php.ini if not already enabled
echo 2. Consider implementing Redis for caching
echo 3. Run performance tests again to verify improvements
echo.
echo Run 'run_performance_tests.bat' to test the improvements!
echo.
pause