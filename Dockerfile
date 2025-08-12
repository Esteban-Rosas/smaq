# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Copia el contenido de tu proyecto al directorio del servidor web
COPY . /var/www/html/

# Habilita mod_rewrite si lo necesitas
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y php-pgsql
