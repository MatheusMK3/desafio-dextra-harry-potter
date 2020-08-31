# Let's use PHP 7.4 FPM, matching our Vagrant's version
FROM php:7.4-fpm

# Clears APT caches
USER root
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install server dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Install PHP extensions for Laravel
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Install Ccomposer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cleanup old files
RUN rm -rf /var/www

# Copy existing application directory contents
COPY ./src /var/www

# Copy example .env file as basis, we won't be using much of it, as the environment settings are under the docker-compose.yaml file
COPY ./src/.env.example /var/www/.env

# Setup user permissions
RUN chown -R www-data:www-data /var/www

# Change current user to www-data
USER www-data

# Now let's work under /var/www/
WORKDIR /var/www

# Run Laravel setup scripts: Install Composer deps, generate application key, run migrations
RUN composer install

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]