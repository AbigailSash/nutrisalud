# 🚀 Guía Maestra de Despliegue a Producción - NutriSalud SaaS
**Plataforma:** NutriSalud SaaS (nutrisaludintegra.com)  
**Stack Tecnológico:** PHP 8.0+ (Nativo/PDO MVC), MySQL 8.0+ / MariaDB 10.5+, Vanilla JS, Chart.js, FullCalendar v6  
**Hosting Objetivo:** Hostinger (hPanel / VPS / Cloud) o cualquier servidor Apache/Linux

---

## 📋 1. Requisitos del Servidor
Asegúrate de que tu servidor cumpla con los siguientes requisitos:
- **PHP Version:** 8.0, 8.1, 8.2 o 8.3.
- **Extensiones PHP Requeridas:** `pdo_mysql`, `openssl`, `mbstring`, `json`, `session`, `curl`, `fileinfo`.
- **Servidor Web:** Apache 2.4+ con módulos activos:
  - `mod_rewrite` (imprescindible para el Front Controller y URLs amigables).
  - `mod_headers` (para cabeceras de seguridad HTTP).
- **Base de Datos:** MySQL 8.0+ o MariaDB 10.5+ con cotejamiento `utf8mb4_unicode_ci` y zona horaria `-03:00` (Argentina).
- **Certificado SSL:** Certificado Let's Encrypt / SSL activo para protocolo seguro HTTPS.

---

## 📂 2. Archivos del Proyecto a Desplegar

### Carpetas y Archivos a Subir al Servidor (`public_html/`):
```text
public_html/
├── app/                      # Lógica de aplicación adicional
├── config/                   # Configuración y Conexión PDO
│   ├── Conexion.php          # Driver de conexión blindado
│   ├── config.php            # Detección de entorno y credenciales
│   └── email.php             # Configuración de servidor SMTP
├── controllers/              # Controladores MVC
├── core/                     # Preflight, enrutador y calculadores clínicos
├── database/                 # Esquemas SQL y script de migración
│   ├── schema_produccion.sql # DDL maestro consolidado con datos base
│   └── check_and_migrate_prod.php # Verificador/migrador idempotente
├── documentacion/            # Documentación interna del sistema
├── logs/                     # Registro de errores y notificaciones (.htaccess protegido)
├── models/                   # Modelos de datos multi-tenant PDO
├── public/                   # Recursos estáticos (CSS, JS, imágenes, uploads)
├── services/                 # Servicios de Correo (SMTP), Notificaciones
├── views/                    # Vistas HTML5 / PHP y plantillas clínicas
├── .htaccess                 # Reglas de seguridad, headers y enrutamiento
├── index.php                 # Front Controller principal
└── router.php                # Router secundario para servidores embebidos
```

> **Nota:** Puedes omitir del despliegue carpetas de desarrollo local como `.git/`, `.gemini/` y `scratch/`.

---

## 🛠️ 3. Paso a Paso de Instalación en Hostinger (hPanel)

### Paso 1: Crear la Base de Datos MySQL en Hostinger
1. Inicia sesión en tu **hPanel de Hostinger**.
2. Ve a la sección **Bases de Datos** > **Bases de datos MySQL**.
3. Crea una nueva base de datos asignando:
   - **Nombre de la Base de Datos:** (ej: `u362815695_nutrisalud`)
   - **Nombre de Usuario:** (ej: `u362815695_admin`)
   - **Contraseña Segura:** (ej: Generar una clave alfanumérica fuerte)
4. Guarda estos datos exactos.

---

### Paso 2: Importar la Estructura y Catálogos de Base de Datos
1. En hPanel, haz clic en **phpMyAdmin** al lado de tu base de datos creada.
2. Selecciona la pestaña **Importar**.
3. Haz clic en **Seleccionar archivo** y elige el archivo:
   `database/schema_produccion.sql`
4. Presiona **Continuar / Importar**.
5. phpMyAdmin creará todas las 18 tablas, la vista de menú, los catálogos oficiales SARA 2 (26 grupos de alimentos y 39 nutrientes) y el usuario administrador inicial.

> **💡 Verificación Automática Opcional:**  
> También puedes abrir en tu navegador `https://tudominio.com/database/check_and_migrate_prod.php` para verificar que todas las tablas, columnas e índices estén 100% operativos.

---

### Paso 3: Subir los Archivos de la Aplicación
1. Ve a **hPanel** > **Administrador de Archivos** (o conéctate por FTP / FileZilla).
2. Entra a la carpeta raíz pública: `public_html/`.
3. Sube todos los archivos y carpetas del proyecto.
4. Asegúrate de que el archivo `.htaccess` esté en la raíz de `public_html/`.

---

### Paso 4: Configurar `config/config.php`
Abre el archivo `config/config.php` en el Administrador de Archivos de Hostinger y verifica o coloca las credenciales creadas en el Paso 1:

```php
// config/config.php (Sección Producción)
} else {
    // Entorno de Producción (Hostinger)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u362815695_db_yRSksOSf'); // Tu base de datos
    define('DB_USER', 'u362815695_usr_yRSksOSf'); // Tu usuario
    define('DB_PASS', 'TuContraseñaSegura');       // Tu contraseña
    define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
}
```

---

### Paso 5: Configurar el Servicio de Correo SMTP (`config/email.php`)
Para que los nutricionistas y pacientes reciban confirmaciones de turno, recordatorios y botones de Google Calendar / WhatsApp:

1. En hPanel, ve a **Emails** y crea una cuenta de correo (ej: `notificaciones@nutrisaludintegra.com`).
2. Edita el archivo `config/email.php`:

```php
return [
    'smtp_enabled'  => true,
    'smtp_host'     => 'smtp.hostinger.com', // o smtp.gmail.com si usas Google Workspace
    'smtp_port'     => 465,                  // 465 para SSL o 587 para TLS
    'smtp_secure'   => 'ssl',                // 'ssl' o 'tls'
    'smtp_username' => 'notificaciones@nutrisaludintegra.com',
    'smtp_password' => 'ContraseñaDeTuCorreo',
    'from_email'    => 'notificaciones@nutrisaludintegra.com',
    'from_name'     => 'NutriSalud',
    'debug_log'     => true
];
```

---

### Paso 6: Configurar Permisos de Archivos y Carpetas (CHMOD)
En el Administrador de Archivos o por consola SSH:
- **Directorios generales:** Permiso `755` (`drwxr-xr-x`).
- **Archivos PHP, CSS, JS:** Permiso `644` (`-rw-r--r--`).
- **Directorio de logs (`logs/`):** Permiso `775` o `755` con permiso de escritura para el proceso de PHP.

---

### Paso 7: Activar Forzado de SSL / HTTPS
1. En hPanel > **Seguridad** > **SSL**.
2. Verifica que el certificado SSL esté instalado y activo en `nutrisaludintegra.com`.
3. Activa la opción **Forzar HTTPS** (el `.htaccess` del sistema ya incluye redirección 301 automática para mayor seguridad).

---

## 👑 4. Acceso del Administrador Maestro (SuperAdmin)

Para acceder al panel de control de la plataforma como Administrador:
- **URL de Acceso:** `https://tudominio.com/index.php?action=login_nutri`
- **Identificador (Usuario):** `admin@nutrisalud.com` o simplemente `admin`
- **Contraseña:** `admin123`
- **Destino tras login:** Redirección automática a `index.php?action=admin_dashboard` (Gestión global de nutricionistas, altas, bajas y estadísticas).

> **💡 ¿No puedes entrar o olvidaste la clave?**  
> Puedes ejecutar directamente en tu navegador o por consola SSH:  
> `https://tudominio.com/database/reset_admin.php`  
> Este script crea o restablece automáticamente la cuenta SuperAdmin con usuario `admin@nutrisalud.com` y clave `admin123` asegurando que esté en estado activo.

---

## 🧪 5. Checklist de Verificación Post-Despliegue

| # | Módulo / Función | URL de Prueba | Estado Esperado |
|---|-------------------|---------------|-----------------|
| 1 | **Landing Page** | `https://tudominio.com/` | Carga limpia, responsive, selector Nutri/Paciente |
| 2 | **Login Nutricionista / Admin** | `index.php?action=login_nutri` | Inicio de sesión seguro con sesión blindada |
| 3 | **Login Paciente** | `index.php?action=login_paciente` | Ingreso con DNI y contraseña |
| 4 | **Fórmula Desarrollada SARA 2** | `index.php?action=formula_desarrollada&id_paciente=X` | Búsqueda por 26 grupos, 39 nutrientes, Atwater y Chart.js |
| 5 | **Calculadora Riesgo CV (HEARTS OMS)** | `index.php?action=evaluador_riesgo_cv&id_paciente=X` | Vía A y B, semáforos, simulador What-If, guardado en BD |
| 6 | **Calendario Multivista v6** | `index.php?action=listar_turnos` | Vistas Mes/Semana/Día/Agenda, Drag & Drop, modales |
| 7 | **Notificaciones por Email** | Agendar o reprogramar un turno | Envío de confirmación HTML con botón Google Calendar |
| 8 | **Descarga de Reportes A4** | Imprimir Fórmula o Plan | Estilos `@media print` optimizados sin barras laterales |

---

## 🔐 5. Características de Seguridad en Producción
- **Aislamiento Multi-Tenant:** Todas las consultas SQL (`SELECT`, `UPDATE`, `DELETE`) están estrictamente filtradas por `IdNutri = :id_nutri` o `id_nutri = :id_nutri` en sentencias preparadas PDO.
- **Manejo de Errores Silencioso:** En producción, `display_errors` está desactivado. Cualquier fallo interno devuelve una pantalla HTTP 503 sobria y registra la traza exclusivamente en `logs/php_errors.log`.
- **Protección de Sesión:** Cookies configuradas con `HttpOnly`, `Secure` (en HTTPS), `SameSite=Lax` y `session.use_strict_mode=1`.
- **Cabeceras HTTP de Seguridad:** Activadas en `.htaccess` (`X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection`, `Referrer-Policy`).

---

## 🆘 6. Soporte y Solución de Problemas

- **Error de Conexión a Base de Datos (503):** Verifica en hPanel que el nombre de la BD, usuario y contraseña en `config/config.php` coincidan exactamente.
- **Error 404 en rutas internas:** Asegúrate de que el módulo `mod_rewrite` esté activo y que el archivo `.htaccess` se haya subido a la raíz `public_html/`.
- **Los correos llegan a Spam:** En hPanel > **DNS**, asegúrate de que los registros **SPF**, **DKIM** y **DMARC** estén configurados y validados para tu dominio.
