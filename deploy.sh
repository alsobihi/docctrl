#!/bin/bash

# Laravel Deployment Script
echo "Starting deployment process..."

# 1. Pull the latest changes from the repository
echo "Pulling latest changes..."
git pull origin main

# 2. Install/update Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Install/update NPM dependencies
echo "Installing NPM dependencies..."
npm ci

# 4. Build frontend assets
echo "Building frontend assets..."
npm run build

# 5. Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# 6. Clear and cache routes, config, and views
echo "Optimizing Laravel..."
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set proper permissions
echo "Setting file permissions..."
chmod -R 775 storage bootstrap/cache

# 8. Restart queue workers (if applicable)
echo "Restarting queue workers..."
php artisan queue:restart

# 9. Restart the web server (if needed)
# echo "Restarting web server..."
# sudo systemctl restart nginx

echo "Deployment completed successfully!"