# Documentación del Sistema: NutriSalud

**Versión:** 1.0  
**Arquitectura:** Patrón MVC (Modelo-Vista-Controlador) en PHP Nativo (Vanilla).  
**Frontend:** HTML5, JavaScript (Vanilla), CSS3, Bootstrap 5.  
**Base de Datos:** MySQL / MariaDB (Relacional).  

---

## 1. Descripción General
**NutriSalud** es una plataforma integral de gestión clínica y nutricional diseñada para optimizar el flujo de trabajo de los profesionales de la nutrición. Permite la administración de pacientes, control de agendas, diseño de planes alimentarios calculados algorítmicamente y el seguimiento detallado del estado clínico a través de historias clínicas electrónicas.

---

## 2. Roles de Usuario y Permisos

El sistema está diseñado con una arquitectura multi-rol basada en niveles de acceso:

1. **SuperAdmin (Administrador del Sistema)**
   - **Propósito:** Gestión de la plataforma a nivel de infraestructura técnica y licencias.
   - **Funciones:** Alta, baja, modificación y auditoría de cuentas de Nutricionistas. Bloqueo de cuentas y blanqueo de contraseñas. Acceso a métricas globales de uso.

2. **Nutricionista (Profesional Clínico)**
   - **Propósito:** Usuario principal del sistema.
   - **Funciones:** Gestión total de su cartera de pacientes, agenda de turnos, creación de historias clínicas, cálculos antropométricos, prescripción de planes alimentarios y emisión de informes educativos.

---

## 3. Módulos Principales (Funcionalidades)

### 3.1. Gestión de Pacientes (Directorio)
- **CRUD Completo:** Alta, lectura, actualización y eliminación de pacientes vinculados de forma exclusiva al ID del nutricionista logueado (privacidad de datos).
- **Ficha Médica Imprimible:** Generación de un reporte clínico consolidado (formato A4) listo para imprimir o exportar a PDF, resumiendo los datos, antecedentes y antropometría del paciente.

### 3.2. Historia Clínica Electrónica (HCE)
Interfaz dividida en pestañas para una recolección de datos ordenada:
- **Datos y Motivo:** Información personal y motivo principal de consulta.
- **Antecedentes:** Patologías, alergias, intolerancias, medicación y carga genética.
- **Antropometría y Laboratorio (Reactiva):** Ingreso de medidas (Peso, Talla, Circunferencia de muñeca) con **cálculo automático y en tiempo real (JS)** del IMC, Peso Ideal, % de Peso Ideal, Complexión Ósea y requerimientos calóricos básicos.
- **Hábitos y 24hs:** Ritmo evacuatorio, actividad física, horas de sueño y recordatorio de ingesta.
- **Seguimiento Clínico:** Textos libres para evolución de consultas periódicas.

### 3.3. Módulo de Planes Alimentarios
El núcleo técnico-nutricional del sistema:
- **Fórmula Desarrollada:** Motor de cálculo que evalúa automáticamente la distribución de Macronutrientes (Carbohidratos, Proteínas, Grasas) en gramos, porcentajes y calorías totales (VCT).
- **Búsqueda Dinámica (Debounce API):** Buscador de alimentos asíncrono optimizado con *debounce* para evitar saturación del servidor. Extrae datos del catálogo base del sistema (Calorías, Proteínas, HC, Grasas por 100g).
- **Matriz de Distribución:** Asignación de alimentos por *Día de la semana* (Lunes a Domingo) y *Momento del Día* (Desayuno, Almuerzo, Cena, etc.).

### 3.4. Agenda y Turnos
- Calendario interno para gestión de citas.
- Estados de turnos: Pendiente, Confirmado, Cancelado, Atendido.
- Visualización de turnos del día directamente en el *Dashboard* principal.

### 3.5. Informes Educativos
- Módulo cualitativo. Creación de material didáctico, guías de reemplazo, tips de hidratación o manejo de patologías específicas (ej: Hipertensión) para entregar como complemento al Plan Alimentario.

### 3.6. Suite Clínica (Calculadora Rápida)
Herramienta flotante (Modal Bootstrap) accesible desde la barra lateral en todo momento para cálculos urgentes en plena consulta. Incluye:
- **TMB y Gasto Energético:** Algoritmo FAO/OMS interactivo según Sexo, Edad, Peso y Nivel de Actividad Física.
- **Conversor de Minerales:** Transformación automática de mEq a mg (Sodio, Potasio, Fósforo).
- **Catabolismo (NUU):** Cálculo del Nitrógeno Ureico Urinario y el Índice Catabólico (IC) para pacientes críticos o deportistas de alto rendimiento, incluyendo insignias visuales (badges) de severidad.
- **Exportación Automática:** Botones para enviar el resultado calculado directamente a la caja de texto de "Observaciones de Seguimiento" de la Historia Clínica abierta.

---

## 4. Tecnologías y Seguridad Implementada
- **Protección contra Inyecciones SQL:** Uso exclusivo de consultas preparadas (Prepared Statements) con `PDO` en PHP.
- **Validación de Entradas:** Sanitización y validación estricta de variables por método POST/GET para prevenir ataques XSS.
- **Optimización UI/UX:** Uso intensivo de AJAX y manipulación del DOM nativa para proveer retroalimentación visual instantánea (insignias de colores, cálculos sin recargar página).
- **Desacoplamiento:** Separación estricta entre la lógica de negocio (Controllers/Models) y las interfaces de usuario (Views).
