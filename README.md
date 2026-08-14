# NutriSalud SaaS - Plataforma de Gestión Nutricional y Clínica

[![PHP Version](https://img.shields.io/badge/PHP-8.1%20|%208.2%20|%208.3-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%208%20|%20MariaDB%2010.5+-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![Architecture](https://img.shields.io/badge/Architecture-Clean%20MVC%20Vanilla-2ecc71?style=flat)](#)
[![Security](https://img.shields.io/badge/Security-Multi--Tenant%20Isolated-e74c3c?style=flat)](#)

Sistema SaaS profesional para la práctica de nutrición clínica y ambulatoria. Diseñado para optimizar la atención de pacientes, registro digital de historias clínicas, formulación dietoterapéutica reactiva, agendamiento de citas y portal interactivo del paciente.

---

## 📚 Índice de Documentación Técnica

Toda la documentación técnica se encuentra en la carpeta [`documentacion/`](documentacion/):

- 🏛️ [**Arquitectura y Contexto Completo del Sistema**](documentacion/ARQUITECTURA_Y_SISTEMA.md): Diagramas de flujo, diseño MVC, diccionario de datos, fórmulas biomédicas (IMC, Mifflin-St Jeor, Lorentz, NUU, IC) y aislamiento multi-tenant.
- 🔍 [**Auditoría Técnica Senior**](documentacion/AUDITORIA_CRITICA_SENIOR.md): Matriz de riesgos de seguridad OWASP, heurísticas de usabilidad Nielsen y deuda técnica remediada.
- 🚀 [**Manual de Despliegue en Producción**](documentacion/MANUAL_DESPLIEGUE_PRODUCCION.md): Guía paso a paso para desplegar en VPS (Ubuntu/Debian, Apache/Nginx, SSL Let's Encrypt, PHP 8.x, MySQL, permisos y cronjobs).
- 🗄️ [**Esquema DDL de Producción**](database/schema_produccion.sql): Script SQL estandarizado con restricciones de clave foránea, vistas unificadas con `COALESCE` y datos semilla (*seeds*).

---

## ⚡ Inicio Rápido en Desarrollo Local (XAMPP / Laragon / Docker)

### 1. Requisitos
- PHP 8.1 o superior con extensiones `pdo`, `pdo_mysql`, `mbstring`, `gd`, `json`.
- MySQL 8.0 o MariaDB 10.5+.
- Servidor web Apache con módulo `mod_rewrite` habilitado.

### 2. Base de Datos
Crear la base de datos e importar el esquema de producción:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS nutrisalud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p nutrisalud < database/schema_produccion.sql
```

### 3. Configuración del Entorno
Editar [`config/config.php`](config/config.php) para ajustar las credenciales locales o de producción.

### 4. Ejecutar el Suite de Pruebas de Integración
Para verificar que todos los flujos de base de datos, aislamiento multi-tenant y motores biomédicos funcionan al 100%:
```bash
php scratch_test_flows.php
```

---

## 🔐 Credenciales de Prueba por Defecto

| Rol | Usuario / Identificador | Contraseña Inicial |
| :--- | :--- | :--- |
| **Super Administrador** | `admin@nutrisalud.com` | `admin123` |
| **Nutricionista Demo** | `abigail.sash@nutrisalud.com` | `nutri123` |

---

## 📂 Estructura del Proyecto

```
nutrisalud/
├── config/             # Configuración de entorno y conexión PDO Singleton
├── controllers/        # Controladores del patrón MVC (Auth, Paciente, Plan, Turno, etc.)
├── core/               # Motores matemáticos y preflight de seguridad de sesión
├── database/           # Scripts DDL y semillas SQL de producción
├── documentacion/      # Manuales de arquitectura, auditoría y despliegue
├── logs/               # Registro rotativo de errores de PHP
├── models/             # Capa de acceso a datos con consultas seguras PDO
├── public/             # Archivos estáticos (CSS, JS, imágenes, uploads de avatares)
├── services/           # Lógica clínica, fórmulas antropométricas y reglas NFPE
├── views/              # Plantillas y vistas divididas por módulos y layout
├── .htaccess           # Reglas de enrutamiento, forzado SSL y blindaje de carpetas
├── index.php           # Front Controller y Router principal
└── README.md           # Este archivo
```

---

## 🛡️ Seguridad y Buenas Prácticas
- **Aislamiento Multi-Tenant:** Cada consulta a pacientes, turnos o dietas filtra estrictamente por `$_SESSION['IdNutri']`.
- **Prepared Statements (PDO):** Protección contra inyección SQL en todos los modelos.
- **Protección de Archivos:** Las carpetas `config/`, `logs/`, `core/` y `database/` están bloqueadas ante peticiones HTTP directas vía `.htaccess`.
- **Uploads Sanitizados:** La carpeta de subidas bloquea la ejecución de cualquier script `.php`, `.cgi` o binarios.
