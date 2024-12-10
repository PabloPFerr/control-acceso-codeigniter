#!/bin/bash

# Establecer permisos correctos
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable
chmod -R 777 /var/www/html/writable/session

# Iniciar el servicio cron
service cron start

# Iniciar Apache en primer plano
apache2-foreground
