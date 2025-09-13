@echo off
echo Setting up Foxes Rentals Database...
echo.

echo Please provide your MySQL root password:
set /p MYSQL_PASSWORD=

echo Creating database...
mysql -u root -p%MYSQL_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS foxes_rentals;"

if %ERRORLEVEL% EQU 0 (
    echo Database created successfully!
    echo.
    echo Updating .env file with MySQL password...
    powershell -command "(Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=%MYSQL_PASSWORD%' | Set-Content .env"
    echo.
    echo Running migrations...
    php artisan migrate
    echo.
    echo Seeding database...
    php artisan db:seed
    echo.
    echo Database setup complete!
) else (
    echo Failed to create database. Please check your MySQL credentials.
)

pause
