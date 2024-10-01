# Use the official PHP image
FROM php:8.1-cli

# Install PDO and MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy all files
COPY . .

# Install Composer dependencies
RUN apt-get update && apt-get install -y git unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
RUN chmod 644 .env
# Expose port 80 for the application
EXPOSE 80

# Run PHP server
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]