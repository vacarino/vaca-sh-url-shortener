#!/bin/bash

# =============================================================================
# MySQL User Fix Script for Vaca.Sh Deployment
# Run this if you encounter MySQL user creation errors
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
DB_NAME="vaca_sh"
DB_USER="vacauser"
DB_PASS="Durimi,.123"

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

log "=========================================="
log "MySQL User Fix Script for Vaca.Sh"
log "=========================================="

# Check if MySQL is running
if ! systemctl is-active --quiet mysql; then
    log "Starting MySQL service..."
    sudo systemctl start mysql
fi

# Get MySQL root password
echo -n "Enter MySQL root password (default: ${DB_PASS}): "
read -s ROOT_PASS
echo
if [ -z "$ROOT_PASS" ]; then
    ROOT_PASS="$DB_PASS"
fi

# Test root connection
if ! mysql -u root -p"$ROOT_PASS" -e "SELECT 1;" >/dev/null 2>&1; then
    error "Cannot connect to MySQL with provided root password"
fi

info "✅ Connected to MySQL successfully"

# Step 1: Check current users
log "Current MySQL users:"
mysql -u root -p"$ROOT_PASS" -e "SELECT User, Host FROM mysql.user WHERE User IN ('$DB_USER', 'root');"

# Step 2: Force cleanup of existing user
log "Cleaning up existing user '$DB_USER'..."

# Method 1: Try modern MySQL syntax
mysql -u root -p"$ROOT_PASS" -e "DROP USER IF EXISTS '$DB_USER'@'localhost';" 2>/dev/null || {
    warning "Modern DROP USER syntax failed, trying alternative methods..."
    
    # Method 2: Check if user exists and drop manually
    USER_COUNT=$(mysql -u root -p"$ROOT_PASS" -e "SELECT COUNT(*) FROM mysql.user WHERE User='$DB_USER' AND Host='localhost';" -s -N)
    if [ "$USER_COUNT" -gt 0 ]; then
        log "Found existing user, dropping..."
        mysql -u root -p"$ROOT_PASS" -e "DROP USER '$DB_USER'@'localhost';" || {
            warning "Standard DROP USER failed, trying direct deletion..."
            # Method 3: Direct deletion from mysql.user table
            mysql -u root -p"$ROOT_PASS" -e "DELETE FROM mysql.user WHERE User='$DB_USER' AND Host='localhost';"
        }
    fi
}

# Step 3: Clean up database
log "Cleaning up existing database '$DB_NAME'..."
mysql -u root -p"$ROOT_PASS" -e "DROP DATABASE IF EXISTS $DB_NAME;"

# Step 4: Flush privileges and wait
mysql -u root -p"$ROOT_PASS" -e "FLUSH PRIVILEGES;"
sleep 2

# Step 5: Create fresh database
log "Creating database '$DB_NAME'..."
mysql -u root -p"$ROOT_PASS" -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Step 6: Create fresh user
log "Creating user '$DB_USER'..."
mysql -u root -p"$ROOT_PASS" -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"

# Step 7: Grant privileges
log "Granting privileges..."
mysql -u root -p"$ROOT_PASS" -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -u root -p"$ROOT_PASS" -e "FLUSH PRIVILEGES;"

# Step 8: Verify setup
log "Verifying setup..."
if mysql -u root -p"$ROOT_PASS" -e "SELECT User, Host FROM mysql.user WHERE User='$DB_USER';" | grep -q "$DB_USER"; then
    info "✅ User '$DB_USER' created successfully"
else
    error "❌ Failed to create user '$DB_USER'"
fi

# Step 9: Test connection
log "Testing user connection..."
if mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME; SELECT 'Connection test successful' as result;" 2>/dev/null; then
    info "✅ Database connection test successful"
    log "=========================================="
    log "🎉 MySQL setup completed successfully!"
    log "=========================================="
    info "Database: $DB_NAME"
    info "User: $DB_USER"
    info "Password: [HIDDEN]"
    info ""
    info "You can now continue with the main setup script:"
    info "  ./digitalocean-deploy/setup.sh"
else
    error "❌ Database connection test failed"
fi

log "Script completed." 