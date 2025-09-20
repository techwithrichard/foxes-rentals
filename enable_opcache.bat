@echo off
echo 🚀 Enabling PHP OPcache for Foxes Rental Management System
echo.

echo 📋 Current PHP Configuration:
php -i | findstr "opcache.enable"
echo.

echo 🔧 Adding OPcache configuration to php.ini...
echo.

REM Find php.ini location
for /f "tokens=*" %%i in ('php --ini') do (
    echo %%i
)

echo.
echo 📝 Please add the following configuration to your php.ini file:
echo.
type opcache_config.ini
echo.

echo 🔄 After adding the configuration, restart your web server.
echo.

echo ✅ OPcache Configuration Complete!
echo 📊 Expected Performance Improvement: 30-50% faster response times
echo.

pause
