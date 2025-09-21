# 🚀 Foxes Rentals Deployment Guide

## 📋 Overview

This guide provides comprehensive instructions for deploying the Foxes Rentals property management system to production environments.

## 🔧 Prerequisites

### System Requirements
- **PHP**: 8.1 or higher
- **MySQL**: 8.0 or higher
- **Redis**: 6.0 or higher (recommended)
- **Web Server**: Nginx or Apache
- **SSL Certificate**: Required for production
- **Domain**: Configured with DNS

### Server Specifications
- **CPU**: 2+ cores
- **RAM**: 4GB+ (8GB recommended)
- **Storage**: 50GB+ SSD
- **Bandwidth**: 1TB+ monthly

## 🛠️ Installation Steps

### 1. Server Setup

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server redis-server php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-bcmath php8.1-gd php8.1-redis php8.1-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
mysql -u root -p
```

```sql
CREATE DATABASE foxes_rentals CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'foxes_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON foxes_rentals.* TO 'foxes_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Clone repository
git clone https://github.com/your-org/foxes-rentals.git /var/www/foxes-rentals
cd /var/www/foxes-rentals

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/foxes-rentals
sudo chmod -R 755 /var/www/foxes-rentals
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure environment variables
nano .env
```

```env
APP_NAME="Foxes Rentals"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foxes_rentals
DB_USERNAME=foxes_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Foxes Rentals"

# Payment Gateways
STRIPE_KEY=pk_live_your_stripe_key
STRIPE_SECRET=sk_live_your_stripe_secret
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_secret
PAYPAL_MODE=live

# M-Pesa Configuration
MPESA_CONSUMER_KEY=your_mpesa_consumer_key
MPESA_CONSUMER_SECRET=your_mpesa_consumer_secret
MPESA_SHORTCODE=your_mpesa_shortcode
MPESA_PASSKEY=your_mpesa_passkey
MPESA_ENVIRONMENT=production

# Third-party Services
GOOGLE_MAPS_API_KEY=your_google_maps_key
TWILIO_ACCOUNT_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_token
TWILIO_FROM_NUMBER=your_twilio_number
SENDGRID_API_KEY=your_sendgrid_key
```

### 5. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Optimize application
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Web Server Configuration

#### Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/foxes-rentals
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/foxes-rentals/public;

    # SSL Configuration
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;

    # Main location block
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /(storage|bootstrap/cache) {
        deny all;
    }

    # File upload size
    client_max_body_size 100M;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/foxes-rentals /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL Certificate Setup

#### Using Let's Encrypt (Certbot)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
```

Add this line:
```
0 12 * * * /usr/bin/certbot renew --quiet
```

### 8. Queue Worker Setup

```bash
# Create systemd service
sudo nano /etc/systemd/system/foxes-rentals-worker.service
```

```ini
[Unit]
Description=Foxes Rentals Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/foxes-rentals/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
WorkingDirectory=/var/www/foxes-rentals

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start service
sudo systemctl enable foxes-rentals-worker
sudo systemctl start foxes-rentals-worker
```

### 9. Cron Jobs Setup

```bash
# Edit crontab
sudo crontab -e
```

Add these lines:
```
* * * * * cd /var/www/foxes-rentals && php artisan schedule:run >> /dev/null 2>&1
0 0 * * * cd /var/www/foxes-rentals && php artisan foxes:run-automation >> /var/log/foxes-automation.log 2>&1
0 2 * * * cd /var/www/foxes-rentals && php artisan foxes:optimize-performance >> /var/log/foxes-optimization.log 2>&1
```

### 10. Monitoring Setup

#### Install monitoring tools

```bash
# Install htop for system monitoring
sudo apt install htop

# Install logrotate for log management
sudo apt install logrotate

# Configure log rotation
sudo nano /etc/logrotate.d/foxes-rentals
```

```
/var/www/foxes-rentals/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        /bin/kill -USR1 `cat /var/run/php8.1-fpm.pid 2> /dev/null` 2> /dev/null || true
    endscript
}
```

## 🔒 Security Hardening

### 1. Firewall Configuration

```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 2. PHP Security

```bash
# Edit PHP configuration
sudo nano /etc/php/8.1/fpm/php.ini
```

Key settings:
```ini
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
display_errors = Off
log_errors = On
max_execution_time = 30
max_input_time = 60
memory_limit = 256M
post_max_size = 100M
upload_max_filesize = 100M
max_file_uploads = 20
```

### 3. MySQL Security

```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Key settings:
```ini
bind-address = 127.0.0.1
skip-networking
local-infile = 0
```

## 📊 Performance Optimization

### 1. OPcache Configuration

```bash
sudo nano /etc/php/8.1/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

### 2. Redis Configuration

```bash
sudo nano /etc/redis/redis.conf
```

```ini
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## 🚀 Deployment Checklist

- [ ] Server setup completed
- [ ] Database configured and migrated
- [ ] Application deployed and optimized
- [ ] SSL certificate installed
- [ ] Web server configured
- [ ] Queue workers running
- [ ] Cron jobs scheduled
- [ ] Monitoring configured
- [ ] Security hardening applied
- [ ] Performance optimization applied
- [ ] Backup strategy implemented
- [ ] Documentation updated

## 🔄 Backup Strategy

### Database Backup

```bash
# Create backup script
sudo nano /usr/local/bin/backup-database.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/foxes-rentals"
mkdir -p $BACKUP_DIR

mysqldump -u foxes_user -p foxes_rentals > $BACKUP_DIR/database_$DATE.sql
gzip $BACKUP_DIR/database_$DATE.sql

# Keep only last 7 days of backups
find $BACKUP_DIR -name "database_*.sql.gz" -mtime +7 -delete
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-database.sh

# Schedule daily backups
echo "0 2 * * * /usr/local/bin/backup-database.sh" | sudo crontab -
```

### Application Backup

```bash
# Create application backup script
sudo nano /usr/local/bin/backup-application.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/foxes-rentals"
APP_DIR="/var/www/foxes-rentals"

tar -czf $BACKUP_DIR/application_$DATE.tar.gz -C $APP_DIR --exclude=node_modules --exclude=vendor --exclude=.git .

# Keep only last 7 days of backups
find $BACKUP_DIR -name "application_*.tar.gz" -mtime +7 -delete
```

## 📞 Support

For deployment support:
- **Email**: support@foxesrentals.com
- **Documentation**: https://docs.foxesrentals.com
- **Status Page**: https://status.foxesrentals.com

---

**Last Updated**: January 15, 2024  
**Version**: 1.0.0
