# Usa la imagen base oficial de PHP 8.0.30 con Apache
FROM php:8.0.30-apache

# Instala extensiones de PHP necesarias para PostgreSQL y otras funcionalidades
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd

# Opcional: Instala Composer si tu proyecto lo utiliza para manejar dependencias
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copia todos los archivos de tu aplicación al directorio raíz del servidor web de Apache.
COPY . /var/www/html/

# Configura los permisos para el usuario del servidor web (www-data)
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Expone el puerto 80, que es el puerto HTTP estándar.
EXPOSE 80
