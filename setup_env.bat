@echo off
echo Setting up environment file...

echo APP_NAME="Foxes Rentals" > .env
echo APP_ENV=local >> .env
echo APP_KEY= >> .env
echo APP_DEBUG=true >> .env
echo APP_URL=http://localhost:8000 >> .env
echo. >> .env
echo LOG_CHANNEL=stack >> .env
echo LOG_DEPRECATIONS_CHANNEL=null >> .env
echo LOG_LEVEL=debug >> .env
echo. >> .env
echo DB_CONNECTION=mysql >> .env
echo DB_HOST=127.0.0.1 >> .env
echo DB_PORT=3306 >> .env
echo DB_DATABASE=foxes_rentals >> .env
echo DB_USERNAME=root >> .env
echo DB_PASSWORD= >> .env
echo. >> .env
echo BROADCAST_DRIVER=log >> .env
echo CACHE_DRIVER=file >> .env
echo FILESYSTEM_DISK=local >> .env
echo QUEUE_CONNECTION=sync >> .env
echo SESSION_DRIVER=file >> .env
echo SESSION_LIFETIME=120 >> .env
echo. >> .env
echo MEMCACHED_HOST=127.0.0.1 >> .env
echo. >> .env
echo REDIS_HOST=127.0.0.1 >> .env
echo REDIS_PASSWORD=null >> .env
echo REDIS_PORT=6379 >> .env
echo. >> .env
echo MAIL_MAILER=smtp >> .env
echo MAIL_HOST=mailpit >> .env
echo MAIL_PORT=1025 >> .env
echo MAIL_USERNAME=null >> .env
echo MAIL_PASSWORD=null >> .env
echo MAIL_ENCRYPTION=null >> .env
echo MAIL_FROM_ADDRESS="hello@example.com" >> .env
echo MAIL_FROM_NAME="${APP_NAME}" >> .env

echo Environment file created!
echo Generating application key...
php artisan key:generate

echo Setup complete!
pause

