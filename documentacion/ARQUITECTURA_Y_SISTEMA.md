# Arquitectura y Contexto Completo del Sistema: NutriSalud SaaS

**Versión:** 2.0 (Producción Ready)  
**Autor:** Equipo de Ingeniería Senior & UX/UI  
**Stack Principal:** PHP 8.x (Vanilla MVC), MySQL/MariaDB (PDO), JavaScript ES6+ (Vanilla / Fetch API), HTML5 Semántico, CSS3 / Bootstrap 5, FontAwesome 6, SweetAlert2.

---

## 1. Visión General del Producto (SaaS)

**NutriSalud** es una solución de software como servicio (SaaS) diseñada para la práctica de nutrición clínica y ambulatoria. El sistema centraliza la gestión del consultorio, historia clínica digitalizada, formulación dietoterápica reactiva con educación nutricional integrada y el portal interactivo del paciente.

```
                  ┌──────────────────────────────────────────────┐
                  │                 CLIENTES                     │
                  │   (Navegadores Web / Móviles de Pacientes)   │
                  └──────────────────────┬───────────────────────┘
                                         │ HTTPS (TLS 1.3)
                                         ▼
                  ┌──────────────────────────────────────────────┐
                  │          SERVIDOR WEB (Apache/Nginx)         │
                  │       .htaccess / URL Rewriting Router       │
                  └──────────────────────┬───────────────────────┘
                                         │
                                         ▼
                  ┌──────────────────────────────────────────────┐
                  │      FRONT CONTROLLER (index.php)            │
                  │    - Preflight Security                      │
                  │    - Middleware de Sesión y Roles            │
                  │    - Enrutamiento por acción                 │
                  └───────┬──────────────┬──────────────┬────────┘
                          │              │              │
        ┌─────────────────▼───┐   ┌──────▼──────┐   ┌───▼────────────────┐
        │   AUTH & USUARIOS   │   │  CLÍNICO    │   │  ADMINISTRACIÓN    │
        │ - AuthController    │   │ - Paciente  │   │ - AdminController  │
        │ - PerfilController  │   │ - Plan &    │   │                    │
        │                     │   │   Educación │   │                    │
        │                     │   │ - Turno     │   │                    │
        └─────────────────┬───┘   └──────┬──────┘   └───┬────────────────┘
                          │              │              │
                          └──────────────┼──────────────┘
                                         │ Lógica de Negocio / Servicios
                                         ▼
                  ┌──────────────────────────────────────────────┐
                  │           CAPA DE SERVICIOS Y CORE           │
                  │ - NutriCalculoService (Antropometría & VCT)  │
                  │ - NutriReglasClinicasService (NFPE/Alertas)  │
                  │ - NutriCalculator (Fórmulas Bioquímicas)     │
                  └──────────────────────┬───────────────────────┘
                                         │ Modelos PDO (Transacciones & WHERE IdNutri)
                                         ▼
                  ┌──────────────────────────────────────────────┐
                  │           BASE DE DATOS RELACIONAL           │
                  │              MySQL / MariaDB                 │
                  └──────────────────────────────────────────────┘
```

---

## 2. Decisiones de Diseño Clínico & UX (Unificación Plan + Educación)

Desde la perspectiva de la práctica clínica nutricional moderna, **la educación nutricional es inseparable de la prescripción dietética**. Fragmentar el plan alimentario y la educación en módulos independientes genera fricción para el profesional y confusión para el paciente.

Por ello, NutriSalud implementa un **Flujo Clínico Unificado**:
1. **En el Gestor de Planes (`gestionar_detalles_plan`)**: El nutricionista tiene dos espacios integrados:
   - **Pestaña 1 (Menú Semanal)**: Asignación ágil de comidas por momentos del día (Desayuno, Almuerzo, Merienda, Cena, Colaciones) con búsqueda predictiva y porciones libres.
   - **Pestaña 2 (Educación Nutricional & Pautas)**: Editor enriquecido con **píldoras de plantillas clínicas en 1 clic** (Hidratación, Métodos de Cocción, Manejo de Saciedad, Lectura de Etiquetas, Método del Plato y Crononutrición).
2. **En la Entrega al Paciente (PDF e Impresión)**: Se emite una **Guía Nutricional Integral** membretada con los datos del profesional, la grilla del menú semanal, el informe de educación nutricional y el espacio de firma/matrícula.
3. **En el Portal del Paciente (`mi_plan`)**: El paciente accede en un solo lugar a su menú diario/semanal y a sus pautas educativas sin tener que cambiar de pantalla.

---

## 3. Aislamiento Multi-Tenant (Seguridad entre Profesionales)

El modelo de aislamiento es **Multi-Tenant Lógico mediante Discriminador de Clave Primaria (`IdNutri`)**:
1. Todo paciente, turno, plan alimentario e informe clínico pertenece de forma estricta a un `IdNutri`.
2. Las consultas `SELECT`, `UPDATE` y `DELETE` en la capa de modelos bindean obligatoriamente el parámetro `:id_nutri` obtenido de la sesión activa en el servidor (`$_SESSION['IdNutri']`).
3. Esto garantiza que ningún profesional pueda ver, modificar o eliminar registros de otro colega, aun cuando intente manipular los IDs en peticiones URL o payloads POST.

---

## 4. Matriz de Roles y Permisos

| Módulo / Acción | SuperAdmin (`admin`) | Nutricionista (`nutricionista`) | Paciente (`paciente`) | Público / Anónimo |
| :--- | :---: | :---: | :---: | :---: |
| **Landing Page** (`landing`) | ✅ | ✅ | ✅ | ✅ |
| **Login Nutricionista** (`login_nutri`, `procesar_login_nutri`) | ✅ | ✅ | ❌ | ✅ |
| **Login Paciente** (`login_paciente`, `procesar_login_paciente`) | ❌ | ❌ | ✅ | ✅ |
| **Dashboard Principal** (`dashboard`) | ❌ | ✅ | ❌ | ❌ |
| **Dashboard Admin** (`admin_dashboard`, `admin_nutricionistas`) | ✅ | ❌ | ❌ | ❌ |
| **Portal Paciente** (`dashboard_paciente`, `mi_plan`, `mis_turnos`) | ❌ | ❌ | ✅ | ❌ |
| **CRUD Pacientes** (`listar_pacientes`, `crear_paciente`, etc.) | ❌ | ✅ (Propios) | ❌ | ❌ |
| **Historia Clínica Electrónica** (`ver_historia_clinica`, etc.) | ❌ | ✅ (Propios) | ❌ | ❌ |
| **Planificador Integral + Educación** (`listar_planes`, `crear_plan`, `gestionar_detalles_plan`) | ❌ | ✅ (Propios) | ❌ | ❌ |
| **Gestión de Agenda / Turnos** (`listar_turnos`, `agendar_turno`) | ❌ | ✅ (Propios) | ✅ (Solicitar/Cancelar) | ❌ |
| **Perfil & Ajustes** (`mi_perfil`, `cambiar_password`) | ❌ | ✅ (Propio) | ✅ (`mi_perfil_paciente`) | ❌ |

---

## 5. Algoritmos y Motor de Cálculos Biomédicos

El sistema cuenta con un motor matemático (`NutriCalculoService` y `NutriCalculator`) que implementa consensos internacionales:

### 5.1. Antropometría
- **Índice de Masa Corporal (IMC)**:
  $$IMC = \frac{Peso\,(kg)}{Estatura^2\,(m)}$$
- **Peso Ideal (Fórmula de Robinson / Hamwi adaptada)**:
  - Masculino ($Talla > 150\,cm$): $Peso\,Ideal = 47.7 + \left(\frac{Talla_{cm} - 150}{2.5}\right) \times 2.72$
  - Femenino ($Talla > 150\,cm$): $Peso\,Ideal = 45.5 + \left(\frac{Talla_{cm} - 150}{2.5}\right) \times 2.27$
- **Peso Ideal Corregido (para Sobrepeso u Obesidad)**:
  $$Peso\,Corregido = Peso\,Ideal + 0.25 \times (Peso\,Actual - Peso\,Ideal)$$
- **Complexión Ósea ($r$)**:
  $$r = \frac{Estatura\,(cm)}{Circunferencia\,Muñeca\,(cm)}$$

### 5.2. Requerimientos Energéticos
- **Gasto Energético Basal (Mifflin-St. Jeor)**:
  - Hombres: $GEB = (10 \times Peso) + (6.25 \times Talla_{cm}) - (5 \times Edad) + 5$
  - Mujeres: $GEB = (10 \times Peso) + (6.25 \times Talla_{cm}) - (5 \times Edad) - 161$
- **Valor Calórico Total (VCT)**:
  $$VCT = GEB \times Nivel\,Actividad\,Física\,(NAF)$$

### 5.3. Suite Clínica de Cuidados Críticos
- **Nitrógeno Ureico Urinario (NUU)**:
  $$NUU = Urea_{orina}\,(g/L) \times 0.467 \times Diuresis\,(L/24h)$$
- **Índice Catabólico (IC)**:
  $$IC = NUU - \left(0.5 \times \frac{Proteínas_{exógenas}}{6.25} + 3\right)$$
- **Cociente Ceto/Anticetogénico**:
  $$Cociente = \frac{0.46 \times Prot + 0.9 \times Grasas}{1.0 \times HC + 0.58 \times Prot + 0.1 \times Grasas}$$
