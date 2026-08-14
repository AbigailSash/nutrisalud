# Manual de Despliegue en Producción - NutriSalud SaaS

**Guía completa de aprovisionamiento, configuración de servidor, despliegue y mantenimiento continuo.**

---

## 1. Requisitos del Servidor de Producción

### Infraestructura Recomendada
- **Sistema Operativo:** Ubuntu 22.04 LTS o 24.04 LTS (o Debian 11/12).
- **Servidor Web:** Apache 2.4 con `mod_rewrite` y `mod_ssl` activados (o Nginx con PHP-FPM).
- **PHP:** Versión 8.1, 8.2 o 8.3 con las siguientes extensiones obligatorias:
  - `php-pdo`
  - `php-mysql`
  - `php-mbstring`
  - `php-json`
  - `php-gd` (para procesamiento de avatares/logos)
  - `php-curl`
  - `php-zip`
- **Base de Datos:** MySQL 8.0+ o MariaDB 10.6+.
- **Certificado SSL:** Let's Encrypt (Certbot) o SSL comercial (HTTPS obligatorio).

---

## 2. Paso a Paso: Aprovisionamiento del Servidor (Ubuntu/Debian)

### Paso 2.1. Actualizar el Sistema Operativo
```bash
sudo apt update && sudo apt upgrade -y
```

### Paso 2.2. Instalar Apache, PHP y MySQL
```bash
sudo apt install -y apache2 mysql-server \
    php libapache2-mod-php php-mysql php-mbstring \
    php-json php-gd php-curl php-zip certbot python3-certbot-apache
```

### Paso 2.3. Habilitar Módulos de Apache
```bash
sudo a2enmod rewrite ssl headers
sudo systemctl restart apache2
```

---

## 3. Configuración de la Base de Datos

### Paso 3.1. Crear Base de Datos y Usuario Seguro en MySQL
Acceder a la consola de MySQL:
```bash
sudo mysql
```

Ejecutar las siguientes sentencias SQL (sustituir contraseñas por valores seguros):
```sql
CREATE DATABASE nutrisalud_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'nutri_user'@'localhost' IDENTIFIED BY 'TuPasswordUltraSegura2026!';
GRANT ALL PRIVILEGES ON nutrisalud_prod.* TO 'nutri_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Paso 3.2. Importar el Esquema DDL y Catálogos
Desde la raíz del proyecto en el servidor:
```bash
mysql -u nutri_user -p nutrisalud_prod < database/schema_produccion.sql
```

---

## 4. Despliegue del Código Fuente

### Paso 4.1. Clonar o Subir el Repositorio
Copiar los archivos al directorio web (por ejemplo `/var/www/nutrisalud`):
```bash
sudo mkdir -p /var/www/nutrisalud
sudo cp -r /ruta_de_origen/* /var/www/nutrisalud/
```

### Paso 4.2. Configuración de Permisos de Archivos y Carpetas
```bash
# Asignar propietario al usuario del servidor web
sudo chown -R www-data:www-data /var/www/nutrisalud

# Permisos de carpetas (755) y archivos (644)
sudo find /var/www/nutrisalud -type d -exec chmod 755 {} \;
sudo find /var/www/nutrisalud -type f -exec chmod 644 {} \;

# Permisos especiales de escritura en carpetas de uploads y logs
sudo chmod -R 775 /var/www/nutrisalud/public/uploads
sudo chmod -R 775 /var/www/nutrisalud/public/assets/uploads
sudo chmod -R 775 /var/www/nutrisalud/logs
```

---

## 5. Configuración de Entorno (`config/config.php`)

Editar el archivo de configuración en el servidor (`/var/www/nutrisalud/config/config.php`):
```php
<?php
// config/config.php

$is_cli = php_sapi_name() === 'cli';
$is_local = $is_cli || (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']));

if ($is_local) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'nutrisalud');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('BASE_URL', 'http://localhost/nutrisalud/');
} else {
    // ENTORNO DE PRODUCCIÓN REAL
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'nutrisalud_prod');
    define('DB_USER', 'nutri_user');
    define('DB_PASS', 'TuPasswordUltraSegura2026!');
    define('BASE_URL', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'tudominio.com') . '/');
}
```

---

## 6. Configuración del VirtualHost en Apache

Crear el archivo de configuración del sitio:
```bash
sudo nano /etc/apache2/sites-available/nutrisalud.conf
```

Añadir el siguiente bloque de configuración:
```apache
<VirtualHost *:80>
    ServerName tudominio.com
    ServerAlias www.tudominio.com
    ServerAdmin soporte@tudominio.com
    DocumentRoot /var/www/nutrisalud

    <Directory /var/www/nutrisalud>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/nutrisalud_error.log
    CustomLog ${APACHE_LOG_DIR}/nutrisalud_access.log combined
</VirtualHost>
```

Activar el sitio y reiniciar Apache:
```bash
sudo a2ensite nutrisalud.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

---

## 7. Instalación del Certificado SSL (HTTPS Obligatorio)

Ejecutar Certbot para obtener e instalar automáticamente el certificado Let's Encrypt:
```bash
sudo certbot --apache -d tudominio.com -d www.tudominio.com
```
*Seleccionar la opción de forzar redirección automática de HTTP a HTTPS.*

---

## 8. Seguridad y Endurecimiento en Servidor

### 8.1. Protección de Archivos Sensibles en `.htaccess`
El `.htaccess` de la raíz del proyecto ya contiene las reglas para bloquear accesos directos a carpetas críticas (`config/`, `logs/`, `core/`) y extensiones sensibles (`.sql`, `.log`, `.env`, `.md`).

### 8.2. Prohibición de Ejecución de PHP en Carpetas de Subidas (`uploads/`)
Crear un archivo `/var/www/nutrisalud/public/uploads/.htaccess`:
```apache
# Desactivar ejecución de scripts en carpeta de uploads
<FilesMatch "\.(php|phtml|php3|php4|php5|php7|php8|phps|cgi|pl|py)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

---

## 9. Respaldos Automatizados (Cronjobs)

Para asegurar la continuidad del negocio y la integridad de los datos de salud, programar un respaldo nocturno diario:

Editar el cron del usuario root:
```bash
sudo crontab -e
```

Añadir la siguiente línea para respaldo diario a las 02:00 AM:
```bash
0 2 * * * mysqldump -u nutri_user -p'TuPasswordUltraSegura2026!' nutrisalud_prod | gzip > /var/backups/nutrisalud_$(date +\%F).sql.gz
```

---

## 10. Checklist Final Previo al Lanzamiento

- [ ] Base de datos poblada con `schema_produccion.sql` sin errores.
- [ ] Usuario Administrador configurado y contraseña cambiada.
- [ ] Credenciales de producción en `config/config.php`.
- [ ] `display_errors` apagado en producción (verificado en `core/preflight.php`).
- [ ] Certificado SSL activo (icono de candado verde en navegador).
- [ ] Permisos de escritura correctos en `public/uploads` y `logs/`.
- [ ] Flujo de login probado en Nutricionista y Paciente.
- [ ] Creación de un plan y prueba de impresión en PDF verificada.
