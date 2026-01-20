FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    nginx \
    gettext-base \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxrender1 \
    libfontconfig1 \
    fontconfig \
    fonts-dejavu-core

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Make scripts executable
RUN chmod +x /app/build.sh /app/start.sh

# Run build script
RUN /app/build.sh

EXPOSE 8080

CMD ["/app/start.sh"]