FROM php:8.1-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install mysqli pdo_mysql mbstring exif pcntl bcmath gd intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Configurar cron job para las tareas programadas
RUN echo "* * * * * cd /var/www/html && php spark tasks:run >> /dev/null 2>&1" > /etc/cron.d/codeigniter
RUN chmod 0644 /etc/cron.d/codeigniter
RUN crontab /etc/cron.d/codeigniter

# Crear directorios necesarios y establecer permisos
RUN mkdir -p /var/www/html/writable/cache \
    && mkdir -p /var/www/html/writable/logs \
    && mkdir -p /var/www/html/writable/session \
    && mkdir -p /var/www/html/writable/uploads \
    && mkdir -p /var/www/html/writable/debugbar \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/writable \
    && chmod -R 777 /var/www/html/writable/session

# Iniciar cron y Apache
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set final permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/writable \
    && find /var/www/html/writable -type d -exec chmod 775 {} \; \
    && find /var/www/html/writable -type f -exec chmod 664 {} \;

USER www-data
