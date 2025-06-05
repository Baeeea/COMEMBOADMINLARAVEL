# Use the official PHP image with Apache
FROM php:8.1-apache

# Install system dependencies and PHP extensions required by Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql zip mbstring bcmath xml tokenizer

# Enable Apache mod_rewrite for Laravel routes
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow Composer to run as root inside container
ENV COMPOSER_ALLOW_SUPERUSER=1

# Update Composer to latest version
RUN composer self-update

# Install PHP dependencies without dev packages and optimize autoloader
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel storage and cache folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 to the outside world
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]

