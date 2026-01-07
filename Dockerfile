FROM php:8.2-apache

# Extensiones comunes
RUN apt-get update && apt-get install -y \
    libzip-dev unzip \
 && docker-php-ext-install pdo pdo_mysql \
 && a2enmod rewrite

# DocumentRoot a /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
