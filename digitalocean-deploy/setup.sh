#!/bin/bash

# =============================================================================
# Vaca.Sh Laravel URL Shortener - DigitalOcean Production Setup
# Ubuntu 22.04 LTS - Complete Automated Deployment Script
# =============================================================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration Variables
DOMAIN="vaca.sh"
APP_DIR="/var/www/vaca.sh"
GITHUB_REPO="https://github.com/YOUR_GITHUB_USERNAME/vaca-sh-url-shortener.git"
DB_NAME="vaca_sh"
DB_USER="vacauser"
DB_PASS="Durimi,.123"
PHP_VERSION="8.2"
NODE_VERSION="18"

# Functions
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

# Get server IP
SERVER_IP=$(curl -s ifconfig.me || curl -s icanhazip.com || echo "Unable to detect")

log "=========================================="
log "Vaca.Sh Laravel Production Setup Starting"
log "=========================================="
info "Server IP: $SERVER_IP"
info "Domain: $DOMAIN"
info "App Directory: $APP_DIR"
log "=========================================="

# =============================================================================
# STEP 1: System Update and Basic Packages
# =============================================================================
log "Step 1: Updating system and installing basic packages..."

sudo apt update -y
sudo apt upgrade -y

# Install essential packages
sudo apt install -y \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
    curl \
    wget \
    gnupg \
    unzip \
    git \
    nano \
    htop \
    fail2ban \
    ufw

# =============================================================================
# STEP 2: Install PHP 8.2 and Extensions
# =============================================================================
log "Step 2: Installing PHP $PHP_VERSION and required extensions..."

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update -y

# Install PHP and required extensions for Laravel
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
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-readline

# =============================================================================
# STEP 3: Install and Configure MySQL
# =============================================================================
log "Step 3: Installing and configuring MySQL..."

sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_PASS}';"
sudo mysql -u root -p${DB_PASS} -e "DELETE FROM mysql.user WHERE User='';"
sudo mysql -u root -p${DB_PASS} -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
sudo mysql -u root -p${DB_PASS} -e "DROP DATABASE IF EXISTS test;"
sudo mysql -u root -p${DB_PASS} -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"

# Create application database and user
log "Creating database and user..."
sudo mysql -u root -p${DB_PASS} -e "CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -u root -p${DB_PASS} -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -u root -p${DB_PASS} -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
sudo mysql -u root -p${DB_PASS} -e "FLUSH PRIVILEGES;"

# =============================================================================
# STEP 4: Install Nginx
# =============================================================================
log "Step 4: Installing and configuring Nginx..."

sudo apt install -y nginx

# Remove default site
sudo rm -f /etc/nginx/sites-enabled/default

# =============================================================================
# STEP 5: Install Composer
# =============================================================================
log "Step 5: Installing Composer..."

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify Composer installation
composer --version

# =============================================================================
# STEP 6: Install Node.js and NPM
# =============================================================================
log "Step 6: Installing Node.js and NPM..."

curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installations
node --version
npm --version

# =============================================================================
# STEP 7: Clone and Setup Laravel Application
# =============================================================================
log "Step 7: Cloning and setting up Laravel application..."

# Create application directory
sudo mkdir -p /var/www

# Clone the repository (user needs to update GITHUB_REPO variable)
if [ "$GITHUB_REPO" = "https://github.com/YOUR_GITHUB_USERNAME/vaca-sh-url-shortener.git" ]; then
    error "Please update the GITHUB_REPO variable in this script with your actual GitHub repository URL"
fi

log "Cloning repository from: $GITHUB_REPO"
sudo git clone $GITHUB_REPO $APP_DIR

# Change to application directory
cd $APP_DIR

# Set proper ownership
sudo chown -R $USER:$USER $APP_DIR

# Install Composer dependencies
log "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
log "Installing Node dependencies and building assets..."
npm install
npm run build

# =============================================================================
# STEP 8: Configure Laravel Environment
# =============================================================================
log "Step 8: Configuring Laravel environment..."

# Copy .env.example to .env if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# Configure .env file with production settings
log "Configuring .env file..."
cat > .env << EOF
APP_NAME="Vaca.Sh"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://${DOMAIN}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

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
EOF

# Generate application key
log "Generating application key..."
php artisan key:generate --force

# =============================================================================
# STEP 9: Set File Permissions
# =============================================================================
log "Step 9: Setting proper file permissions..."

# Set ownership to www-data
sudo chown -R www-data:www-data $APP_DIR

# Set directory permissions
sudo find $APP_DIR -type d -exec chmod 755 {} \;

# Set file permissions
sudo find $APP_DIR -type f -exec chmod 644 {} \;

# Set special permissions for Laravel
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Make artisan executable
sudo chmod +x $APP_DIR/artisan

# =============================================================================
# STEP 10: Configure PHP-FPM
# =============================================================================
log "Step 10: Configuring PHP-FPM..."

# Optimize PHP-FPM pool configuration
sudo tee /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf > /dev/null <<EOL
[www]
user = www-data
group = www-data
listen = /var/run/php/php${PHP_VERSION}-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
pm.process_idle_timeout = 10s
pm.max_requests = 1000
chdir = /
EOL

# Configure PHP settings for production
sudo tee /etc/php/${PHP_VERSION}/fpm/conf.d/99-laravel.ini > /dev/null <<EOL
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
# STEP 11: Configure Nginx
# =============================================================================
log "Step 11: Configuring Nginx..."

# Copy the nginx.conf from the deployment directory
if [ -f "digitalocean-deploy/nginx.conf" ]; then
    sudo cp digitalocean-deploy/nginx.conf /etc/nginx/sites-available/${DOMAIN}
else
    # Create nginx configuration if file doesn't exist
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
fi

# Enable the site
sudo ln -sf /etc/nginx/sites-available/${DOMAIN} /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# =============================================================================
# STEP 12: Install and Configure SSL with Certbot
# =============================================================================
log "Step 12: Installing SSL certificate with Certbot..."

# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# =============================================================================
# STEP 13: Run Laravel Setup
# =============================================================================
log "Step 13: Running Laravel setup commands..."

cd $APP_DIR

# Clear and cache configuration
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database migrations
log "Running database migrations..."
php artisan migrate --force

# Create storage link
log "Creating storage link..."
php artisan storage:link

# Seed the database with admin user and invite codes
log "Seeding database..."
php artisan db:seed --force

# Optimize for production
log "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# =============================================================================
# STEP 14: Configure Firewall
# =============================================================================
log "Step 14: Configuring firewall..."

sudo ufw --force enable
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'

# =============================================================================
# STEP 15: Start Services
# =============================================================================
log "Step 15: Starting and enabling services..."

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
# STEP 16: Setup Automatic Backups and Maintenance
# =============================================================================
log "Step 16: Setting up automatic backups and maintenance..."

# Create backup script
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

# Setup Laravel scheduler cron job
(sudo crontab -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1") | sudo crontab -

# Setup daily backup cron
(sudo crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/vaca-backup.sh") | sudo crontab -

# Setup log rotation
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

# =============================================================================
# STEP 17: Final System Optimization
# =============================================================================
log "Step 17: Final system optimization..."

# Clean up
sudo apt autoremove -y
sudo apt autoclean

# =============================================================================
# DEPLOYMENT COMPLETE
# =============================================================================
log "=========================================="
log "🎉 Vaca.Sh Deployment Complete!"
log "=========================================="

info "✅ Your application is ready at: http://${DOMAIN}"
info "🔒 To enable HTTPS, run: sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN}"
info ""
info "📊 Application Details:"
info "   • Domain: ${DOMAIN}"
info "   • App Directory: ${APP_DIR}"
info "   • Database: ${DB_NAME}"
info "   • Database User: ${DB_USER}"
info ""
info "🔧 Next Steps:"
info "   1. Point your DNS A record for ${DOMAIN} to: ${SERVER_IP}"
info "   2. Wait for DNS propagation (up to 24 hours)"
info "   3. Run SSL setup: sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN}"
info "   4. Test your application at https://${DOMAIN}"
info ""
info "🎯 Admin Access:"
info "   • URL: https://${DOMAIN}/admin"
info "   • Email: admin@vaca.sh"
info "   • Password: password"
info ""
info "🛠️ Management Commands:"
info "   • Deploy updates: cd ${APP_DIR} && ./digitalocean-deploy/deploy.sh"
info "   • View logs: tail -f ${APP_DIR}/storage/logs/laravel.log"
info "   • Backup: sudo /usr/local/bin/vaca-backup.sh"
info ""
warning "⚠️  Remember to:"
warning "   • Change default admin password"
warning "   • Update DNS records"
warning "   • Configure email settings in .env"
warning "   • Setup SSL certificate"

log "🚀 Deployment completed successfully!"