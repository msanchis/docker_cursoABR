# Use an official PHP image with Apache as the base image
FROM php:8.2-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html/backend

# Install system dependencies (e.g., for database connection)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libpng-dev \
    libjpeg-dev \
    vim \
    && docker-php-ext-install pdo_mysql mysqli zip gd

# Enable Apache rewrite module (for clean URLs if needed)
RUN a2enmod rewrite

# Set file permissions (adjust as needed for your application)
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Switch to the www-data user for security
USER www-data

# Command to start Apache (CMD is optional, as the base image likely has it)
CMD ["apache2-foreground"]
