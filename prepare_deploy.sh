#!/bin/bash

# Crear directorio de despliegue
mkdir -p deploy

# Copiar archivos y directorios necesarios
cp -r app deploy/
cp -r public deploy/
cp -r system deploy/
cp -r vendor deploy/
cp -r writable deploy/
cp spark deploy/
cp env deploy/.env
cp .htaccess deploy/

# Configurar .env para producción
sed -i '' 's/CI_ENVIRONMENT = development/CI_ENVIRONMENT = production/' deploy/.env

# Ajustar permisos
chmod -R 755 deploy
chmod -R 777 deploy/writable

# Crear archivo de instrucciones
cat > deploy/README.txt << EOL
Instrucciones de despliegue:

1. Configurar la base de datos en el archivo .env:
   - database.default.hostname = tu_host
   - database.default.database = tu_base_de_datos
   - database.default.username = tu_usuario
   - database.default.password = tu_contraseña

2. Ejecutar las migraciones:
   php spark migrate

3. Ejecutar el seeder para datos iniciales:
   php spark db:seed InitialData

4. Configurar el cron job para las tareas programadas:
   * * * * * cd /ruta/a/tu/proyecto && php spark tasks:run >> /dev/null 2>&1

Credenciales por defecto:
- Admin: admin@system.com / admin123
- Usuario: usuario@demo.com / usuario123
EOL

# Crear archivo zip
cd deploy
zip -r ../control-acceso-deploy.zip ./*
cd ..

# Limpiar
rm -rf deploy

echo "Archivo de despliegue creado: control-acceso-deploy.zip"
