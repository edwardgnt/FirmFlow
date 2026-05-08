FROM php:8.4-cli

# Install system dependencies and PHP extensions needed by Laravel/PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer from the official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Allow Composer to run as root inside the container
ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 8000

# Keep the container alive if vendor is missing; otherwise run Laravel dev server
CMD ["sh", "-c", "if [ ! -f vendor/autoload.php ]; then echo 'vendor/autoload.php not found. Run: docker compose exec app composer install, then restart the app container.'; tail -f /dev/null; fi; php artisan serve --host=0.0.0.0 --port=8000"]