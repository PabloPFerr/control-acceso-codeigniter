# Sistema de Control de Acceso - CodeIgniter 4

Este proyecto es un sistema de registro horario laboral desarrollado con CodeIgniter 4. El sistema permite la autenticación de usuarios, gestión de roles y permisos, y registro de accesos.

## Características Principales

- Autenticación de usuarios
- Gestión de roles y permisos
- Registro y monitoreo de accesos
- Panel de administración
- Reportes y estadísticas

## Estructura del Proyecto

```
app/
├── Controllers/     # Controladores de la aplicación
├── Models/         # Modelos para la gestión de datos
├── Views/          # Vistas y templates
├── Config/         # Archivos de configuración
└── Database/
    └── Migrations/ # Migraciones de la base de datos
```

## Requisitos del Sistema

- PHP 8.1 o superior
- Extensiones PHP requeridas:
  - intl
  - mbstring
  - json
  - mysqlnd (para MySQL)
  - libcurl
- MySQL 5.7 o superior
- Composer
- Docker (opcional)

## Instalación con Docker

1. Clonar el repositorio:
```bash
git clone [URL_DEL_REPOSITORIO]
cd control-acceso-codeigniter
```

2. Construir y levantar los contenedores:
```bash
docker-compose up -d
```

3. Instalar dependencias:
```bash
docker-compose exec app composer install
```

4. Configurar el entorno:
```bash
cp env .env
```
Editar el archivo .env con las credenciales de la base de datos y demás configuraciones necesarias.

5. Ejecutar las migraciones:
```bash
docker-compose exec app php spark migrate
```

6. Acceder a la aplicación:
```
http://localhost:8080
```

## Instalación en Servidor Propio

1. Requisitos del servidor:
   - Servidor web Apache/Nginx
   - PHP 8.1 o superior
   - MySQL 5.7 o superior
   - Composer

2. Pasos de instalación:

   a. Clonar el repositorio en el directorio web:
   ```bash
   git clone [URL_DEL_REPOSITORIO] /var/www/html/control-acceso
   ```

   b. Instalar dependencias:
   ```bash
   cd /var/www/html/control-acceso
   composer install
   ```

   c. Configurar el entorno:
   ```bash
   cp env .env
   ```
   Editar el archivo .env con las configuraciones apropiadas.

   d. Configurar el servidor web:
   - Asegurarse de que el DocumentRoot apunte a la carpeta `public/`
   - Habilitar el módulo rewrite de Apache
   - Configurar los permisos adecuados:
   ```bash
   chmod -R 755 writable/
   chown -R www-data:www-data *
   ```

   e. Ejecutar las migraciones:
   ```bash
   php spark migrate
   ```

## Configuración del Servidor Web

### Apache
Asegúrate de tener un archivo .htaccess en la carpeta public/ con la siguiente configuración:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

### Nginx
Ejemplo de configuración para Nginx:

```nginx
server {
    listen 80;
    server_name tudominio.com;
    root /var/www/html/control-acceso/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Tareas Programadas

El sistema incluye un comando para cerrar automáticamente los registros que quedaron abiertos del día anterior. Este comando necesita ser configurado como una tarea programada (cron job) para su ejecución automática.

### Comando CerrarRegistros

Este comando cierra automáticamente los registros que no tienen hora de salida registrada del día anterior, estableciendo la hora de salida a las 23:59:59 del mismo día.

Para ejecutar el comando manualmente:
```bash
php spark registros:cerrar
```

### Configuración del Cron Job

Para automatizar la ejecución del comando, configura un cron job en el servidor:

1. Abrir el editor de cron:
```bash
crontab -e
```

2. Añadir la siguiente línea (ajustar la ruta según tu instalación):
```bash
1 0 * * * cd /ruta/a/tu/proyecto && php spark registros:cerrar >> /ruta/logs/cron.log 2>&1
```

Esta configuración ejecutará el comando todos los días a las 00:01 y guardará un log de la ejecución.

## Seguridad

- Asegúrate de que solo la carpeta `public/` sea accesible desde la web
- Configura correctamente los permisos de archivos y carpetas
- Mantén actualizado PHP y todas las dependencias
- Utiliza HTTPS en producción
- Realiza copias de seguridad regulares de la base de datos
