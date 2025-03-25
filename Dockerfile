FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    wget \
    gnupg2 \
    libxrender1 \
    libfontconfig \
    libxext6 \
    supervisor \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Expose port
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]

RUN apt-get install wkhtmltopdf -y
