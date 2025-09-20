@echo off
REM Database Backup Script for Foxes Rentals - Windows Batch File
REM Creates a complete backup of the database before Phase 1 implementation

echo.
echo ========================================
echo   Foxes Rentals Database Backup
echo   Phase 1 Pre-Implementation Backup
echo ========================================
echo.

REM Set backup directory
set BACKUP_DIR=%~dp0backups
set TIMESTAMP=%date:~-4,4%-%date:~-10,2%-%date:~-7,2%_%time:~0,2%-%time:~3,2%-%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

REM Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
    echo ✅ Created backup directory: %BACKUP_DIR%
)

REM Set backup filename
set BACKUP_FILE=%BACKUP_DIR%\foxes_rentals_backup_%TIMESTAMP%.sql

echo 🚀 Starting database backup...
echo 📊 Database: foxes_rentals
echo 📁 Backup file: %BACKUP_FILE%
echo ⏰ Timestamp: %TIMESTAMP%
echo.

REM Check if MySQL is available
where mysqldump >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ mysqldump not found in PATH
    echo 💡 Please ensure MySQL is installed and mysqldump is available
    echo 💡 You can also run: php backup_database.php
    pause
    exit /b 1
)

REM Read database credentials from .env file
echo 🔍 Reading database configuration from .env file...

REM Try to read from .env file (basic parsing)
for /f "tokens=2 delims==" %%a in ('findstr "DB_HOST" .env 2^>nul') do set DB_HOST=%%a
for /f "tokens=2 delims==" %%a in ('findstr "DB_USERNAME" .env 2^>nul') do set DB_USERNAME=%%a
for /f "tokens=2 delims==" %%a in ('findstr "DB_PASSWORD" .env 2^>nul') do set DB_PASSWORD=%%a
for /f "tokens=2 delims==" %%a in ('findstr "DB_DATABASE" .env 2^>nul') do set DB_DATABASE=%%a
for /f "tokens=2 delims==" %%a in ('findstr "DB_PORT" .env 2^>nul') do set DB_PORT=%%a

REM Set defaults if not found
if not defined DB_HOST set DB_HOST=localhost
if not defined DB_USERNAME set DB_USERNAME=root
if not defined DB_PASSWORD set DB_PASSWORD=
if not defined DB_DATABASE set DB_DATABASE=foxes_rentals
if not defined DB_PORT set DB_PORT=3306

echo 📊 Database: %DB_DATABASE%
echo 🏠 Host: %DB_HOST%
echo 👤 User: %DB_USERNAME%
echo 🔌 Port: %DB_PORT%
echo.

REM Create mysqldump command
echo 🔄 Executing backup command...
mysqldump --host=%DB_HOST% --port=%DB_PORT% --user=%DB_USERNAME% --password=%DB_PASSWORD% --single-transaction --routines --triggers --add-drop-table --add-locks --create-options --disable-keys --extended-insert --quick --set-charset %DB_DATABASE% > "%BACKUP_FILE%"

if %errorlevel% equ 0 (
    if exist "%BACKUP_FILE%" (
        if %~z1 gtr 0 (
            echo ✅ Database backup completed successfully!
            echo 📁 Backup file: %BACKUP_FILE%
            echo 📊 File size: %~z1 bytes
            echo 📅 Created: %date% %time%
            echo.
            
            REM Create a copy as latest_backup.sql
            copy "%BACKUP_FILE%" "%BACKUP_DIR%\latest_backup.sql" >nul
            echo 🔗 Created latest backup reference: %BACKUP_DIR%\latest_backup.sql
            echo.
            
            echo 📋 BACKUP SUMMARY
            echo ================
            echo 📊 Database: %DB_DATABASE%
            echo 📁 Backup location: %BACKUP_DIR%
            echo 🔒 Backup type: Complete database dump
            echo 📅 Backup date: %date% %time%
            echo ✅ Status: Ready for Phase 1 implementation
            echo.
            
            echo 🚀 NEXT STEPS:
            echo ==============
            echo 1. ✅ Database backup completed
            echo 2. 🔄 Proceed with Phase 1: Critical Fixes
            echo 3. 📝 Complete empty controller methods
            echo 4. 🔒 Implement input validation
            echo 5. 🛡️ Fix security vulnerabilities
            echo 6. 📊 Add database indexes
            echo.
            
            echo 💡 To restore this backup later, use:
            echo    mysql -u %DB_USERNAME% -p %DB_DATABASE% ^< "%BACKUP_FILE%"
            echo.
            
        ) else (
            echo ❌ Backup file is empty
            echo 💡 Check database credentials and connection
        )
    ) else (
        echo ❌ Backup file was not created
        echo 💡 Check permissions and disk space
    )
) else (
    echo ❌ Backup failed with error code: %errorlevel%
    echo 💡 Check database credentials and MySQL connection
    echo 💡 Make sure MySQL is running
)

echo.
echo 🎉 Backup process completed!
echo 🚀 Ready to proceed with Phase 1 implementation!
echo.
pause
