FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y git unzip libzip-dev

# Instalar las extensiones para conectarse a la base datos
RUN docker-php-ext-install pdo pdo_mysql zip

# Redirigir el servidor
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Copia el codigo
COPY . /var/www/html/

# Instala Composer y las dependencias necesarias de Slim
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer update --ignore-platform-reqs --no-interaction