FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app

# Create .env file before composer install
RUN cp .env.example .env || echo "APP_KEY=" > .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Generate app key
RUN php artisan key:generate --force

# Create SQLite database
RUN touch /app/database/database.sqlite

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database
RUN chmod -R 775 /app/storage /app/bootstrap/cache /app/database

# Expose port
EXPOSE 8080

# Start server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
