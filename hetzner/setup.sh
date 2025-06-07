#!/bin/bash

echo "🔗 URL Shortener Setup Script"
echo "=============================="

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    echo "Visit: https://getcomposer.org/download/"
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.1 or higher."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP version: $PHP_VERSION"

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Copy environment file
if [ ! -f .env ]; then
    echo "📝 Creating environment file..."
    cp .env.example .env
    echo "✅ Environment file created"
else
    echo "⚠️  Environment file already exists"
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "Next steps:"
echo "1. Configure your database settings in .env file"
echo "2. Run: php artisan migrate"
echo "3. (Optional) Run: php artisan db:seed"
echo "4. Start the server: php artisan serve"
echo ""
echo "Default admin credentials (after seeding):"
echo "Email: admin@example.com"
echo "Password: password"
echo ""
echo "🚀 Happy URL shortening!" 