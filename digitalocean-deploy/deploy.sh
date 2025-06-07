#!/bin/bash

# =============================================================================
# Vaca.Sh Laravel URL Shortener - Deployment Update Script
# Run this script to deploy updates from GitHub
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
APP_DIR="/var/www/vaca.sh"
PHP_VERSION="8.2"

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

# Check if we're in the right directory
if [ ! -f "$APP_DIR/artisan" ]; then
    error "Laravel application not found at $APP_DIR"
fi

log "=========================================="
log "Starting Vaca.Sh Deployment Update"
log "=========================================="

# Change to application directory
cd $APP_DIR

# =============================================================================
# STEP 1: Backup Current Version
# =============================================================================
log "Step 1: Creating backup..."

BACKUP_DIR="/tmp/vaca-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

# Backup current application
cp -r $APP_DIR $BACKUP_DIR/app
info "✅ Application backed up to: $BACKUP_DIR"

# =============================================================================
# STEP 2: Put Application in Maintenance Mode
# =============================================================================
log "Step 2: Enabling maintenance mode..."
php artisan down --message="Updating Vaca.Sh - Back in a moment!" --retry=60

# =============================================================================
# STEP 3: Pull Latest Changes from GitHub
# =============================================================================
log "Step 3: Pulling latest changes from GitHub..."

# Stash any local changes (just in case)
git stash push -m "Pre-deployment stash $(date)"

# Pull latest changes
git pull origin main

# =============================================================================
# STEP 4: Update Dependencies
# =============================================================================
log "Step 4: Updating dependencies..."

# Update Composer dependencies
log "Updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Update Node dependencies and rebuild assets
log "Updating Node dependencies and rebuilding assets..."
npm ci
npm run build

# =============================================================================
# STEP 5: Clear Caches and Optimize
# =============================================================================
log "Step 5: Clearing caches and optimizing..."

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Clear compiled services
php artisan clear-compiled

# =============================================================================
# STEP 6: Run Database Migrations
# =============================================================================
log "Step 6: Running database migrations..."

# Run migrations (with backup confirmation)
warning "⚠️  About to run database migrations. Ensure you have a database backup!"
read -p "Continue with migrations? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    error "Deployment cancelled by user"
fi

php artisan migrate --force

# =============================================================================
# STEP 7: Set Proper Permissions
# =============================================================================
log "Step 7: Setting proper file permissions..."

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
# STEP 8: Recreate Storage Link
# =============================================================================
log "Step 8: Recreating storage link..."

# Remove existing link and recreate
if [ -L "$APP_DIR/public/storage" ]; then
    rm $APP_DIR/public/storage
fi

php artisan storage:link

# =============================================================================
# STEP 9: Optimize for Production
# =============================================================================
log "Step 9: Optimizing for production..."

# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --no-dev

# =============================================================================
# STEP 10: Restart Services
# =============================================================================
log "Step 10: Restarting services..."

# Restart PHP-FPM
sudo systemctl reload php${PHP_VERSION}-fpm

# Restart Nginx
sudo systemctl reload nginx

# =============================================================================
# STEP 11: Disable Maintenance Mode
# =============================================================================
log "Step 11: Disabling maintenance mode..."

php artisan up

# =============================================================================
# STEP 12: Run Health Checks
# =============================================================================
log "Step 12: Running health checks..."

# Check if application responds
DOMAIN=$(php artisan tinker --execute="echo config('app.url');" | tail -1)
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" $DOMAIN || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    info "✅ Application is responding correctly (HTTP $HTTP_STATUS)"
else
    warning "⚠️  Application may have issues (HTTP $HTTP_STATUS)"
fi

# Check database connection
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" > /dev/null 2>&1; then
    info "✅ Database connection is working"
else
    warning "⚠️  Database connection may have issues"
fi

# Check storage permissions
if [ -w "$APP_DIR/storage/logs" ]; then
    info "✅ Storage permissions are correct"
else
    warning "⚠️  Storage permissions may have issues"
fi

# =============================================================================
# DEPLOYMENT COMPLETE
# =============================================================================
log "=========================================="
log "🎉 Deployment Update Complete!"
log "=========================================="

info "✅ Application updated successfully"
info "🔗 Website: $DOMAIN"
info "📁 Backup location: $BACKUP_DIR"
info ""
info "🛠️ Useful commands:"
info "   • View logs: tail -f $APP_DIR/storage/logs/laravel.log"
info "   • Check status: systemctl status nginx php${PHP_VERSION}-fpm"
info "   • Manual backup: sudo /usr/local/bin/vaca-backup.sh"
info ""

# Check for any pending updates
COMMITS_BEHIND=$(git rev-list --count HEAD..origin/main 2>/dev/null || echo "0")
if [ "$COMMITS_BEHIND" = "0" ]; then
    info "✅ Application is up to date"
else
    warning "⚠️  There are $COMMITS_BEHIND commit(s) available. Run deployment again to get latest changes."
fi

log "🚀 Deployment completed at $(date)"

# Optional: Send notification (uncomment if you want email notifications)
# echo "Vaca.Sh deployment completed successfully at $(date)" | mail -s "Deployment Success" admin@vaca.sh

info "💡 Pro tip: Schedule regular deployments with: 0 3 * * 1 cd $APP_DIR && ./digitalocean-deploy/deploy.sh" 