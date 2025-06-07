#!/bin/bash

# =============================================================================
# VacaSh Laravel Deployment Script for Hetzner VPS (Ubuntu 22.04)
# Production-Ready Automated Deployment
# =============================================================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="vaca.sh"
APP_DIR="/var/www/vaca.sh"
DB_NAME="vaca_sh"
DB_USER="vacauser"
DB_PASS="Durimi,.123"
PHP_VERSION="8.2"

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   error "This script should not be run as root for security reasons. Please run as a regular user with sudo privileges."
fi

# Check if sudo is available
if ! command -v sudo &> /dev/null; then
    error "sudo is required but not installed. Please install sudo first."
fi

log "Starting VacaSh Laravel deployment on Hetzner VPS..."

# =============================================================================
# STEP 1: System Update and Upgrade
# =============================================================================
log "Step 1: Updating and upgrading the system..."
sudo apt update -y
sudo apt upgrade -y

# =============================================================================
# STEP 2: Install Required Packages
# =============================================================================
log "Step 2: Installing required packages..."

# Install basic packages
sudo apt install -y software-properties-common ca-certificates lsb-release apt-transport-https

# Add PHP repository for latest versions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update -y

# Install Nginx
log "Installing Nginx..."
sudo apt install -y nginx

# Install PHP and required extensions
log "Installing PHP ${PHP_VERSION} and required extensions..."
sudo apt install -y \
    php${PHP_VERSION} \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-soap \
    php${PHP_VERSION}-xmlrpc \
    php${PHP_VERSION}-opcache

# Install MySQL
log "Installing MySQL Server..."
sudo apt install -y mysql-server

# Install additional tools
log "Installing additional tools..."
sudo apt install -y unzip git curl wget nano htop

# Install Composer
log "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js and NPM (for Laravel Mix/Vite)
log "Installing Node.js and NPM..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# =============================================================================
# STEP 3: Configure MySQL
# =============================================================================
log "Step 3: Configuring MySQL..."

# Secure MySQL installation
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_PASS}';"
sudo mysql -u root -p${DB_PASS} -e "DELETE FROM mysql.user WHERE User='';"
sudo mysql -u root -p${DB_PASS} -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
sudo mysql -u root -p${DB_PASS} -e "DROP DATABASE IF EXISTS test;"
sudo mysql -u root -p${DB_PASS} -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"

# Create database and user
log "Creating database and user..."
sudo mysql -u root -p${DB_PASS} -e "CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -u root -p${DB_PASS} -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -u root -p${DB_PASS} -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
sudo mysql -u root -p${DB_PASS} -e "FLUSH PRIVILEGES;"

# =============================================================================
# STEP 4: Setup Laravel Project
# =============================================================================
log "Step 4: Setting up Laravel project..."

# Create application directory
sudo mkdir -p ${APP_DIR}

# Check if Laravel project exists in current directory
if [ -d "./app" ] && [ -f "./artisan" ]; then
    log "Laravel project found in current directory. Copying to ${APP_DIR}..."
    sudo cp -r . ${APP_DIR}/
else
    error "Laravel project not found. Please ensure the Laravel application is in the current directory or adjust the script."
fi

# Navigate to application directory
cd ${APP_DIR}

# Install Composer dependencies
log "Installing Composer dependencies..."
sudo composer install --no-dev --optimize-autoloader --no-interaction

# Setup Laravel environment
log "Setting up Laravel environment..."

# Copy .env.example to .env if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        sudo cp .env.example .env
    else
        sudo touch .env
    fi
fi

# Configure .env file
log "Configuring .env file..."
sudo tee .env > /dev/null <<EOL
APP_NAME=VacaSh
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://${DOMAIN}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@${DOMAIN}"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOL

# Generate application key
log "Generating application key..."
sudo php artisan key:generate --force

# Clear and cache configuration
log "Optimizing Laravel..."
sudo php artisan config:clear
sudo php artisan cache:clear
sudo php artisan view:clear
sudo php artisan route:clear

# Run database migrations
log "Running database migrations..."
sudo php artisan migrate --force

# Create storage link
log "Creating storage link..."
sudo php artisan storage:link

# Optimize for production
log "Optimizing for production..."
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# Install Node dependencies and build assets if package.json exists
if [ -f package.json ]; then
    log "Installing Node dependencies and building assets..."
    sudo npm install
    sudo npm run build
fi

# =============================================================================
# STEP 5: Set File Permissions
# =============================================================================
log "Step 5: Setting file permissions..."

# Change ownership to www-data
sudo chown -R www-data:www-data ${APP_DIR}

# Set directory permissions
sudo find ${APP_DIR} -type d -exec chmod 755 {} \;

# Set file permissions
sudo find ${APP_DIR} -type f -exec chmod 644 {} \;

# Set special permissions for storage and cache
sudo chmod -R 775 ${APP_DIR}/storage
sudo chmod -R 775 ${APP_DIR}/bootstrap/cache

# Make artisan executable
sudo chmod +x ${APP_DIR}/artisan

# =============================================================================
# STEP 6: Configure Nginx
# =============================================================================
log "Step 6: Configuring Nginx..."

# Create Nginx site configuration
sudo tee /etc/nginx/sites-available/${DOMAIN} > /dev/null <<EOL
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt|tar|woff|svg|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
EOL

# Enable the site
sudo ln -sf /etc/nginx/sites-available/${DOMAIN} /etc/nginx/sites-enabled/

# Remove default site
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# =============================================================================
# STEP 7: Configure PHP-FPM
# =============================================================================
log "Step 7: Configuring PHP-FPM..."

# Optimize PHP-FPM configuration
sudo tee /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf > /dev/null <<EOL
[www]
user = www-data
group = www-data
listen = /var/run/php/php${PHP_VERSION}-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.process_idle_timeout = 10s
pm.max_requests = 1000
chdir = /
php_admin_value[disable_functions] = exec,passthru,shell_exec,system
php_admin_flag[allow_url_fopen] = off
EOL

# Configure PHP settings for production
sudo tee -a /etc/php/${PHP_VERSION}/fpm/conf.d/99-laravel.ini > /dev/null <<EOL
; Laravel optimizations
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
max_input_vars = 3000
date.timezone = UTC

; Security
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
EOL

# =============================================================================
# STEP 8: Start Services
# =============================================================================
log "Step 8: Starting services..."

# Start and enable services
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php${PHP_VERSION}-fpm
sudo systemctl enable php${PHP_VERSION}-fpm
sudo systemctl start mysql
sudo systemctl enable mysql

# Reload services
sudo systemctl reload nginx
sudo systemctl reload php${PHP_VERSION}-fpm

# =============================================================================
# STEP 9: Install and Configure SSL with Let's Encrypt
# =============================================================================
log "Step 9: Installing SSL certificate with Let's Encrypt..."

# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
log "Obtaining SSL certificate for ${DOMAIN} and www.${DOMAIN}..."
sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@${DOMAIN} --redirect

# Setup auto-renewal
log "Setting up SSL certificate auto-renewal..."
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# =============================================================================
# STEP 10: Final Optimizations and Security
# =============================================================================
log "Step 10: Applying final optimizations and security measures..."

# Configure firewall
log "Configuring UFW firewall..."
sudo ufw --force enable
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow mysql

# Create Laravel scheduler cron job
log "Setting up Laravel scheduler..."
(sudo crontab -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1") | sudo crontab -

# Setup log rotation for Laravel logs
sudo tee /etc/logrotate.d/laravel > /dev/null <<EOL
${APP_DIR}/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
}
EOL

# Create backup script
log "Creating backup script..."
sudo tee /usr/local/bin/vaca-backup.sh > /dev/null <<EOL
#!/bin/bash
BACKUP_DIR="/backups"
DATE=\$(date +%Y%m%d_%H%M%S)
mkdir -p \$BACKUP_DIR

# Backup database
mysqldump -u ${DB_USER} -p${DB_PASS} ${DB_NAME} > \$BACKUP_DIR/database_\$DATE.sql

# Backup application files
tar -czf \$BACKUP_DIR/app_\$DATE.tar.gz -C /var/www vaca.sh

# Keep only last 7 days of backups
find \$BACKUP_DIR -name "*.sql" -mtime +7 -delete
find \$BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
EOL

sudo chmod +x /usr/local/bin/vaca-backup.sh

# Setup daily backup cron
(sudo crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/vaca-backup.sh") | sudo crontab -

# =============================================================================
# STEP 11: Final Checks and Cleanup
# =============================================================================
log "Step 11: Performing final checks..."

# Check if all services are running
if sudo systemctl is-active --quiet nginx; then
    log "✓ Nginx is running"
else
    error "✗ Nginx is not running"
fi

if sudo systemctl is-active --quiet php${PHP_VERSION}-fpm; then
    log "✓ PHP-FPM is running"
else
    error "✗ PHP-FPM is not running"
fi

if sudo systemctl is-active --quiet mysql; then
    log "✓ MySQL is running"
else
    error "✗ MySQL is not running"
fi

# Check if website is accessible
log "Testing website accessibility..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200\|301\|302"; then
    log "✓ Website is accessible"
else
    warning "⚠ Website might not be fully accessible yet"
fi

# Clean up
sudo apt autoremove -y
sudo apt autoclean

# =============================================================================
# DEPLOYMENT COMPLETE
# =============================================================================
log "=========================================="
log "VacaSh Laravel Deployment Complete!"
log "=========================================="
info "Your application is now live at: https://${DOMAIN}"
info "Admin panel: https://${DOMAIN}/admin"
info "Database: ${DB_NAME}"
info "DB User: ${DB_USER}"
info ""
info "Important files:"
info "- Application: ${APP_DIR}"
info "- Nginx config: /etc/nginx/sites-available/${DOMAIN}"
info "- PHP config: /etc/php/${PHP_VERSION}/fpm/"
info "- SSL certificate: /etc/letsencrypt/live/${DOMAIN}/"
info ""
info "Services:"
info "- Nginx: sudo systemctl [start|stop|restart|status] nginx"
info "- PHP-FPM: sudo systemctl [start|stop|restart|status] php${PHP_VERSION}-fpm"
info "- MySQL: sudo systemctl [start|stop|restart|status] mysql"
info ""
info "Maintenance:"
info "- Backup script: /usr/local/bin/vaca-backup.sh"
info "- Logs: ${APP_DIR}/storage/logs/"
info "- SSL renewal: sudo certbot renew"
info ""
warning "Please update your DNS records to point ${DOMAIN} and www.${DOMAIN} to this server's IP address."
log "=========================================="

# Show server IP
SERVER_IP=$(curl -s ifconfig.me || curl -s icanhazip.com || echo "Unable to detect")
info "Server IP Address: ${SERVER_IP}"

log "Deployment script completed successfully!" 