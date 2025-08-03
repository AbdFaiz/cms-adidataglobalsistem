FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose Laravel port
EXPOSE 9000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=9000
