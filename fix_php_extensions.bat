@echo off
echo ========================================
echo Fixing PHP Extensions for XAMPP
echo ========================================
echo.

echo Step 1: Backing up current php.ini...
copy "C:\xampp\php\php.ini" "C:\xampp\php\php.ini.backup"

echo Step 2: Enabling required extensions...
echo.
echo Please manually edit C:\xampp\php\php.ini and:
echo 1. Find the line: ;extension=gd
echo 2. Remove the semicolon to make it: extension=gd
echo 3. Find the line: ;extension=zip  
echo 4. Remove the semicolon to make it: extension=zip
echo 5. Save the file
echo.
echo After editing, restart XAMPP and run: composer install
echo.
pause

