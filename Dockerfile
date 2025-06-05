# Use PHP 8.2 with Apache (Debian Buster)
FROM php:8.2-apache-buster

# Install system dependencies and PHP extensions
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

# Enable mod_rewrite for Laravel pretty URLs
RUN a2enmod rewrite

# Change Apache DocumentRoot to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update Apache site config to use public/ as root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/../!g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy app source
COPY . /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose HTTP port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
