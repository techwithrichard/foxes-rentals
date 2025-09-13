@echo off
echo ========================================
echo Foxes Rental Management System
echo Performance Testing Suite
echo ========================================
echo.

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and add it to your system PATH
    pause
    exit /b 1
)

REM Check if Laravel is available
if not exist "artisan" (
    echo ERROR: This doesn't appear to be a Laravel project
    echo Please run this script from the root directory of your Laravel project
    pause
    exit /b 1
)

echo Starting performance tests...
echo.

REM Test 1: Basic PHP Performance Test
echo [1/5] Running Basic PHP Performance Test...
php performance_test.php
echo.

REM Test 2: Database Performance Test
echo [2/5] Running Database Performance Test...
php database_performance_test.php
echo.

REM Test 3: Laravel Artisan Performance Test
echo [3/5] Running Laravel Performance Test...
php artisan performance:test --all
echo.

REM Test 4: Check Laravel Configuration
echo [4/5] Checking Laravel Configuration...
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo Configuration cached successfully
echo.

REM Test 5: Generate Performance Report
echo [5/5] Generating Performance Report...
echo.
echo ========================================
echo PERFORMANCE TEST SUMMARY
echo ========================================
echo Test Date: %date% %time%
echo PHP Version: 
php --version | findstr "PHP"
echo.
echo Laravel Version:
php artisan --version
echo.
echo Environment:
php artisan env
echo.
echo ========================================
echo RECOMMENDATIONS
echo ========================================
echo 1. Enable OPcache for better PHP performance
echo 2. Use Redis for caching instead of file-based cache
echo 3. Optimize database queries and add proper indexes
echo 4. Enable gzip compression for static assets
echo 5. Use CDN for static assets (CSS, JS, images)
echo 6. Implement database query result caching
echo 7. Use Laravel's built-in caching mechanisms
echo 8. Optimize images and use WebP format
echo 9. Minify CSS and JavaScript files
echo 10. Use HTTP/2 for better performance
echo.
echo ========================================
echo Performance testing completed!
echo ========================================
echo.
echo You can also access the web-based performance test at:
echo http://localhost:8000/performance-test.html
echo.
pause

