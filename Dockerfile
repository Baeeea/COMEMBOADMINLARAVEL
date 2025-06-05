# Use PHP 8.2 with Apache on Debian Buster (stable, includes PHP source needed for extensions)
FROM php:8.2-apache-buster

# Install system dependencies and PHP extensions required by Laravel 12
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    build-essential \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql zip mbstring bcmath xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for Laravel routes
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install Composer (latest stable)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies without dev packages, optimize autoloader
RUN composer install --no-dev --optimize-autoloader

# Set permissions (optional but recommended for Laravel)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 to the outside world
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
