# Stage 1: Build frontend assets
FROM node:18 AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP runtime
FROM php:8.2-cli

# Install system dependencies and PHP extensions required by Laravel & PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql pgsql zip bcmath xml

# Set up non-root user 1000 (Hugging Face default)
RUN useradd -m -u 1000 user
USER user
ENV HOME=/home/user \
    PATH=/home/user/.local/bin:$PATH

WORKDIR /app

# Copy application files (with ownership set to user 1000)
COPY --chown=user:user . /app

# Copy compiled assets from Stage 1 builder
COPY --from=assets-builder --chown=user:user /app/public/build /app/public/build

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts --ignore-platform-reqs

# Expose port 7860
EXPOSE 7860

# Run migrations, seeders, optimizations, and start Laravel built-in server
CMD php artisan package:discover && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=7860
