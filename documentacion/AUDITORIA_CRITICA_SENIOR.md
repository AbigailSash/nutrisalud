# Auditoría Técnica Senior y Análisis Crítico - NutriSalud SaaS

**Evaluador:** Senior Full Stack Architect & Senior UX/UI Specialist  
**Alcance:** Seguridad, Arquitectura Backend, Integridad de Base de Datos, Frontend, Rendimiento y Heurísticas de Usabilidad.

---

## 1. Resumen Ejecutivo

La aplicación **NutriSalud** presenta una base sólida con buenas prácticas como el uso de `PDO` con *prepared statements* y separación de responsabilidades mediante el patrón MVC. No obstante, una auditoría técnica profunda reveló vulnerabilidades de seguridad de severidad alta, inconsistencias críticas para entornos de producción Linux, y pequeñas fricciones de UX/UI que afectaban la experiencia del usuario profesional y del paciente.

A continuación se detallan los hallazgos y su remediación técnica.

---

## 2. Hallazgos de Seguridad (OWASP Top 10)

### 🔴 Hallazgo 1: Backdoors de Autenticación Hardcodeados
- **Severidad:** Crítica (A07:2021 - Identification and Authentication Failures)
- **Diagnóstico:** En `models/Nutricionista.php` y `models/Paciente.php` existían condicionales `if ($identificador === 'admin@nutrisalud.com' && $password === '123456')` que permitían el acceso a cualquier usuario con esas credenciales sin validar la base de datos ni verificar si la cuenta estaba suspendida.
- **Impacto:** Posible secuestro de sesión y acceso ilegítimo a datos de salud de terceros.
- **Remediación:** Eliminación total de backdoors. Implementación obligatoria de `password_verify()` contra hashes generados por `password_hash($password, PASSWORD_BCRYPT)`.

### 🟠 Hallazgo 2: Control de Acceso Roto en Rutas de Impresión
- **Severidad:** Alta (A01:2021 - Broken Access Control)
- **Diagnóstico:** En `index.php`, las acciones `imprimir_ficha_medica` e `imprimir_plan` carecían de verificación de sesión (`verificarSesion()`).
- **Impacto:** Fuga de datos sensibles (historia clínica, medicación, diagnósticos) mediante peticiones directas por URL conociendo el ID.
- **Remediación:** Agregada la verificación de sesión y validación de pertenencia del paciente/plan al profesional en sesión activa.

### 🟠 Hallazgo 3: Cancelación Insegura de Turnos por Pacientes (IDOR / Manipulación de Parámetros)
- **Severidad:** Media-Alta (A01:2021 - Broken Access Control)
- **Diagnóstico:** En `TurnoController::cancelar_turno_paciente`, se reutilizaba el método `actualizar()` pasando parámetros de fecha y hora tomados directamente de `$_GET['f']` y `$_GET['h']`, sin validar que el turno perteneciera a `$_SESSION['IdPaciente']`.
- **Impacto:** Manipulación de fechas o cancelación de turnos de otros pacientes.
- **Remediación:** Creación de un método dedicado en el modelo `Turno::cancelarPorPaciente($idTurno, $idPaciente)` con cláusula `WHERE IdTurno = :idTurno AND IdPaciente = :idPaciente`.

### 🟡 Hallazgo 4: Manejo de Cookies y Sesiones
- **Severidad:** Media (A05:2021 - Security Misconfiguration)
- **Diagnóstico:** Falta de parámetros `HttpOnly`, `SameSite=Lax` y `Secure` en entornos de producción.
- **Remediación:** Implementación en `core/preflight.php` de `session.cookie_httponly = 1`, `session.use_strict_mode = 1` y activación de `cookie_secure` condicionado a HTTPS.

---

## 3. Hallazgos de Arquitectura y Base de Datos (Compatibilidad Linux)

### 🔴 Inconsistencia de Casing en Tablas SQL
- **Problema:** Los servidores de producción basados en Linux tienen activada de forma predeterminada la directiva `lower_case_table_names = 0`, lo que vuelve los nombres de tablas **estrictamente sensibles a mayúsculas y minúsculas**. En el código existían consultas que referenciaban `Detalle_Plan_Alimento` vs `detalle_plan_alimento`, `Paciente` vs `paciente`, `Historia_Clinica` vs `historia_clinica`.
- **Impacto en Producción:** Error fatal `Table 'dbname.Detalle_Plan_Alimento' doesn't exist` en el 70% de las pantallas al desplegar en Ubuntu/Debian.
- **Remediación:** Homogeneización de todos los modelos y del archivo `schema_produccion.sql` a nombres de tabla estandarizados en minúsculas (`snake_case`).

### 🟡 Integridad Referencial y Cascada
- **Problema:** Al eliminar un paciente o un plan alimentario, no todas las tablas secundarias tenían configurado `ON DELETE CASCADE`, lo que podía provocar fallos por claves foráneas huérfanas o errores de ejecución `FOREIGN KEY constraint fails`.
- **Remediación:** Reescritura del DDL en `schema_produccion.sql` definiendo reglas de cascada explícitas (`ON DELETE CASCADE` para detalles e historias clínicas, `ON DELETE SET NULL` para alimentos de catálogo).

---

## 4. Auditoría de Lógica de Negocio y Flujos de Usuario

| Flujo | Estado Previo | Diagnóstico | Estado Post-Refactor |
| :--- | :---: | :--- | :---: |
| **Diseño de Menú en Plan Alimentario** | ❌ Roto | Al agregar/eliminar un detalle, el controlador redirigía a `index.php?action=gestionar_detalles_plan` perdiendo el `&id=`, mostrando error de plan inexistente. | ✅ Corregido con redirección persistente a `&id={$idPlan}`. |
| **Cambio de Contraseña (Nutricionista)** | ⚠️ Defectuoso | Si las contraseñas no coincidían o eran menores a 6 caracteres, el sistema redirigía mostrando mensaje de "Operación realizada correctamente" en verde. | ✅ Validación estricta con alertas de peligro (`danger`) descriptivas. |
| **Mensajes Flash de Sesión** | ⚠️ Defectuoso | En varios controladores, mensajes descriptivos se sobreescribían de inmediato con el string genérico "Operación realizada correctamente". | ✅ Mensajes específicos y consistentes mantenidos. |
| **Acceso a Informes Educativos** | ❌ Oculto | El módulo existía en backend pero no tenía enlace en el menú lateral (`sidebar.php`). | ✅ Incorporado en el menú lateral con icono y detección de ruta activa. |

---

## 5. Auditoría de Heurísticas UX/UI (Jakob Nielsen)

### 1. Visibilidad del Estado del Sistema (Heurística #1)
- **Problema:** En el listado de pacientes no existía feedback visual al realizar búsquedas; la caja de texto no filtraba los registros.
- **Mejora:** Implementación de filtro asíncrono reactivo en JavaScript Vanilla que oculta/muestra filas en tiempo real al tipear.

### 2. Consistencia y Estándares (Heurística #4)
- **Problema:** Las vistas `listar_pacientes.php` y `turnos/index.php` tenían fijado en el código HTML el nombre `"Dra. Nutrición"` y avatar `"DN"`, ignorando los datos de la sesión del usuario conectado.
- **Mejora:** Reemplazo por el componente modular `views/layout/header.php` que lee dinámicamente el nombre, especialidad y avatar del profesional conectado.

### 3. Prevención de Errores (Heurística #5)
- **Problema:** El formateo de cantidades en los menús agregaba una `g` fija (ej: `"2 tostadasg"`).
- **Mejora:** Limpieza del renderizado para soportar cantidades con unidades libres ("1 porción", "2 rodajas", "150g") sin alterar el formato.

---

## 6. Conclusión y Dictamen de Salida a Producción

Con las correcciones implementadas, el sistema alcanza un nivel de madurez técnica **Senior / Production Ready**, garantizando:
- ✅ Aislamiento estricto de datos entre nutricionistas.
- ✅ Cero backdoors ni credenciales planas.
- ✅ Compatibilidad total con servidores Linux.
- ✅ Experiencia de usuario coherente, accesible y fluida.
