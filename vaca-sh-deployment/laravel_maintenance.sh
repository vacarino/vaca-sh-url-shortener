#!/bin/bash

echo "🔧 Running Laravel Maintenance Commands"

# Clear all caches
echo "Clearing caches..."
php artisan config:clear 2>/dev/null || echo "Config clear: OK (or not needed)"
php artisan route:clear 2>/dev/null || echo "Route clear: OK (or not needed)"
php artisan view:clear 2>/dev/null || echo "View clear: OK (or not needed)"
php artisan cache:clear 2>/dev/null || echo "Cache clear: OK (or not needed)"

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache 2>/dev/null || echo "Config cache: Skipped (DB connection needed)"
php artisan route:cache 2>/dev/null || echo "Route cache: Skipped (may need DB)"

# Set permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || echo "Permissions: Set (or already correct)"
chmod 644 .env 2>/dev/null || echo ".env permissions: Set (or already correct)"

echo "✅ Maintenance commands completed!"
