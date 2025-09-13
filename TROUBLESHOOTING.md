# Foxes Rental Management System - Troubleshooting Guide

## Quick Setup Commands

### 1. Create .env File
Copy the content from `env_content.txt` and create a `.env` file in your project root.

### 2. Run Setup Script
```bash
run_setup.bat
```

## Common Issues and Solutions

### Issue 1: Composer Not Found
**Error:** `'composer' is not recognized as an internal or external command`

**Solutions:**
1. Install Composer from https://getcomposer.org/download/
2. Add Composer to your system PATH
3. Use full path: `C:\ProgramData\ComposerSetup\bin\composer.bat install`

### Issue 2: PHP Not Found
**Error:** `'php' is not recognized as an internal or external command`

**Solutions:**
1. Install PHP or use XAMPP
2. Add PHP to your system PATH
3. Use XAMPP PHP: `C:\xampp\php\php.exe artisan key:generate`

### Issue 3: Node.js/npm Not Found
**Error:** `'npm' is not recognized as an internal or external command`

**Solutions:**
1. Install Node.js from https://nodejs.org/
2. Restart your terminal after installation

### Issue 4: Database Connection Failed
**Error:** `SQLSTATE[HY000] [2002] No connection could be made`

**Solutions:**
1. Start MySQL service
2. Create database: `CREATE DATABASE foxes_rentals;`
3. Check .env database credentials
4. Ensure MySQL is running on port 3306

### Issue 5: Missing vendor/autoload.php
**Error:** `Failed to open stream: No such file or directory`

**Solution:** Run `composer install` first

## Manual Command Sequence

If the automated script fails, run these commands manually:

```bash
# 1. Install PHP dependencies
composer install

# 2. Generate application key
php artisan key:generate

# 3. Install Node.js dependencies
npm install

# 4. Create database (in MySQL)
CREATE DATABASE foxes_rentals;

# 5. Run migrations
php artisan migrate

# 6. Seed database
php artisan db:seed

# 7. Create storage link
php artisan storage:link

# 8. Build assets
npm run dev

# 9. Start server
php artisan serve
```

## Environment Configuration

### Required .env Settings:
```env
APP_NAME="Foxes Rental Management System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foxes_rentals
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

MPESA_CONSUMER_KEY=YOUR_CONSUMER_KEY
MPESA_CONSUMER_SECRET=YOUR_CONSUMER_SECRET
MPESA_PASSKEY=YOUR_PASSKEY
```

## Access Information

- **Application URL:** http://localhost:8000
- **Admin Email:** admin@admin.com
- **Admin Password:** demo123#

## Support

If you encounter issues not covered here:
1. Check Laravel logs in `storage/logs/laravel.log`
2. Ensure all required PHP extensions are installed
3. Verify file permissions on storage and bootstrap/cache directories
