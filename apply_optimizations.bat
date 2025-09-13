@echo off
echo 🚀 Applying Performance Optimizations for Foxes Rental Management System
echo ========================================================================

echo.
echo 1. Running Laravel Optimizations...
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo.
echo 2. Building Optimized Assets...
yarn build

echo.
echo 3. Running Performance Analysis...
php optimize_performance.php

echo.
echo 4. Database Optimization (if MySQL is available)...
echo    Run: mysql -u root -p foxes_rentals < database_optimization.sql

echo.
echo 5. Checking Redis Installation...
redis-cli ping 2>nul
if %errorlevel% equ 0 (
    echo    ✅ Redis is running
    echo    💡 Update CACHE_DRIVER=redis in your .env file
) else (
    echo    ⚠️  Redis not found - Install Redis for better caching
    echo    💡 Download from: https://github.com/microsoftarchive/redis/releases
)

echo.
echo 6. PHP OPcache Check...
php -m | findstr opcache
if %errorlevel% equ 0 (
    echo    ✅ OPcache extension is loaded
    echo    💡 Enable opcache.enable=1 in php.ini for better performance
) else (
    echo    ⚠️  OPcache not loaded - Enable it in php.ini
)

echo.
echo ✅ Performance optimizations applied!
echo.
echo 📊 Next Steps:
echo    1. Test your application performance
echo    2. Run 'php performance_test.php' to measure improvements
echo    3. Monitor slow queries in MySQL
echo    4. Consider implementing CDN for static assets
echo.
pause

