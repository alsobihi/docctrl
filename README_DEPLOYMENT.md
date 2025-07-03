# DocCtrl Deployment Guide

This document provides instructions for deploying the DocCtrl application to various environments.

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL database
- Web server (Apache or Nginx)

## Deployment Options

### 1. Traditional Server Deployment

#### Server Requirements

- PHP 8.1+
- MySQL 5.7+ or PostgreSQL 10+
- Composer
- Node.js 16+
- Web server (Apache or Nginx)
- SSL certificate

#### Deployment Steps

1. Clone the repository to your server:
   ```bash
   git clone https://github.com/yourusername/docctrl.git
   cd docctrl
   ```

2. Install PHP dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. Install and build frontend assets:
   ```bash
   npm ci
   npm run build
   ```

4. Set up environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Edit the `.env` file with your production settings:
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Configure database connection
   - Set proper mail settings

5. Run database migrations:
   ```bash
   php artisan migrate --force
   ```

6. Set proper permissions:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

7. Set up the web server:
   - For Apache, ensure the `.htaccess` file is properly configured
   - For Nginx, use the provided `nginx.conf` as a template

8. Set up SSL certificate (Let's Encrypt recommended)

9. Set up cron job for Laravel's scheduler:
   ```
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

### 2. Shared Hosting Deployment

1. Create a ZIP archive of your project (excluding vendor, node_modules, etc.)

2. Upload the ZIP to your hosting and extract it

3. Create a database and import the schema

4. Configure the `.env` file with your hosting details

5. Run the deployment commands via SSH or hosting panel:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan storage:link
   ```

6. Ensure the document storage directory is writable

### 3. Docker Deployment

If using Docker:

1. Build the Docker image:
   ```bash
   docker build -t docctrl .
   ```

2. Run the container:
   ```bash
   docker run -p 8080:80 -e DB_HOST=your-db-host -e DB_DATABASE=docctrl -e DB_USERNAME=root -e DB_PASSWORD=secret docctrl
   ```

### 4. Platform as a Service (PaaS)

#### Heroku Deployment

1. Create a Heroku app:
   ```bash
   heroku create your-app-name
   ```

2. Add a database:
   ```bash
   heroku addons:create heroku-postgresql:hobby-dev
   ```

3. Configure environment variables:
   ```bash
   heroku config:set APP_KEY=$(php artisan key:generate --show)
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   ```

4. Deploy the application:
   ```bash
   git push heroku main
   ```

5. Run migrations:
   ```bash
   heroku run php artisan migrate --force
   ```

#### Laravel Forge

1. Create a new server in Laravel Forge
2. Connect your repository
3. Configure deployment settings
4. Set up environment variables
5. Deploy the site

## Post-Deployment Tasks

1. Verify the application is working correctly
2. Set up SSL certificate if not already done
3. Configure backup strategy
4. Set up monitoring
5. Ensure proper file permissions for document uploads

## Troubleshooting

### Common Issues

1. **500 Server Error**
   - Check storage and bootstrap/cache permissions
   - Verify .env file exists and is properly configured
   - Check PHP error logs

2. **Database Connection Issues**
   - Verify database credentials in .env
   - Check if database server is accessible

3. **File Upload Problems**
   - Check PHP upload limits in php.ini
   - Verify storage directory permissions

4. **Missing Assets**
   - Run `npm run build` again
   - Check if the public directory is properly served

## Maintenance Mode

To put the application in maintenance mode during updates:

```bash
php artisan down --message="Upgrading Database" --retry=60
```

To bring it back online:

```bash
php artisan up
```

## Automatic Deployment

For automatic deployment, you can use the provided `deploy.sh` script:

```bash
chmod +x deploy.sh
./deploy.sh
```

This script will:
1. Pull the latest changes
2. Install dependencies
3. Build assets
4. Run migrations
5. Optimize Laravel
6. Set proper permissions
7. Restart queue workers

## Security Considerations

1. Ensure `.env` file is not publicly accessible
2. Set proper file permissions
3. Keep all software updated
4. Use HTTPS for all connections
5. Implement proper backup strategy
6. Set up monitoring and alerts