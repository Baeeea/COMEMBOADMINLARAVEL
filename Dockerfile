FROM php:8.2-apache

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
    && docker-php-ext-install pdo_mysql zip mbstring bcmath xml tokenizer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for Laravel routes
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies without dev packages, optimize autoloader
RUN composer install --no-dev --optimize-autoloader

# Set permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
