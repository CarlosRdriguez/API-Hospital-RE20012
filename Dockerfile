FROM php:8.2-apache

RUN a2enmod rewrite

# Instalar las extensiones para conectarse a la base datos
RUN docker-php-ext-install pdo pdo_mysql

# Redirigir el servidor
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copia el codigo
COPY . /var/www/html/

# Instala Composer y las dependencias necesarias de Slim
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader