-- ========================================================================
-- NUTRISALUD SaaS - SCRIPT DDL DE BASE DE DATOS (PRODUCCIÓN)
-- Compatibilidad: MySQL 8.0+ / MariaDB 10.5+ (Linux & Windows)
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABLA: nutricionista
CREATE TABLE IF NOT EXISTS `nutricionista` (
  `IdNutri` INT(11) NOT NULL AUTO_INCREMENT,
  `DNI` VARCHAR(20) DEFAULT NULL,
  `Matricula` VARCHAR(30) NOT NULL,
  `Nombre` VARCHAR(50) NOT NULL,
  `Apellido` VARCHAR(50) NOT NULL,
  `Email` VARCHAR(100) NOT NULL,
  `Password_Hash` VARCHAR(255) DEFAULT NULL,
  `Rol` ENUM('admin', 'nutricionista') NOT NULL DEFAULT 'nutricionista',
  `Telefono` VARCHAR(30) DEFAULT NULL,
  `Estado_Cuenta` CHAR(1) NOT NULL DEFAULT 'A',
  `Especialidad` VARCHAR(255) DEFAULT 'Lic. en Nutrición',
  `Logo_URL` VARCHAR(255) DEFAULT NULL,
  `Instagram` VARCHAR(255) DEFAULT NULL,
  `Whatsapp` VARCHAR(255) DEFAULT NULL,
  `Direccion` VARCHAR(255) DEFAULT NULL,
  `Biografia` TEXT DEFAULT NULL,
  `Color_Tema` VARCHAR(20) DEFAULT '#2ecc71',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdNutri`),
  UNIQUE KEY `uq_nutri_matricula` (`Matricula`),
  UNIQUE KEY `uq_nutri_email` (`Email`),
  UNIQUE KEY `uq_nutri_dni` (`DNI`),
  KEY `idx_nutri_estado` (`Estado_Cuenta`),
  CONSTRAINT `chk_nutri_estado` CHECK (`Estado_Cuenta` IN ('A', 'I'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLA: paciente
CREATE TABLE IF NOT EXISTS `paciente` (
  `IdPaciente` INT(11) NOT NULL AUTO_INCREMENT,
  `DNI` VARCHAR(20) NOT NULL,
  `Nombre` VARCHAR(50) NOT NULL,
  `Apellido` VARCHAR(50) NOT NULL,
  `Fecha_Nacimiento` DATE NOT NULL,
  `Telefono` VARCHAR(30) DEFAULT NULL,
  `Email` VARCHAR(100) DEFAULT NULL,
  `IdNutri` INT(11) NOT NULL,
  `Password` VARCHAR(255) DEFAULT NULL,
  `FotoPerfil` VARCHAR(255) DEFAULT NULL,
  `Obra_Social` VARCHAR(100) DEFAULT 'Particular',
  `Peso` DECIMAL(5,2) DEFAULT NULL,
  `Estatura` INT(11) DEFAULT NULL,
  `Sexo` VARCHAR(10) DEFAULT 'M',
  `Actividad` DECIMAL(4,3) DEFAULT 1.200,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdPaciente`),
  KEY `fk_paciente_nutri` (`IdNutri`),
  KEY `idx_paciente_dni` (`DNI`),
  KEY `idx_paciente_busqueda` (`IdNutri`, `Apellido`, `Nombre`),
  CONSTRAINT `fk_paciente_nutri` FOREIGN KEY (`IdNutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLA: turno
CREATE TABLE IF NOT EXISTS `turno` (
  `IdTurno` INT(11) NOT NULL AUTO_INCREMENT,
  `Fecha` DATE NOT NULL,
  `Hora` TIME NOT NULL,
  `Estado_Turno` VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
  `IdPaciente` INT(11) NOT NULL,
  `IdNutri` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdTurno`),
  KEY `fk_turno_paciente` (`IdPaciente`),
  KEY `fk_turno_nutri` (`IdNutri`),
  KEY `idx_turno_fecha_nutri` (`Fecha`, `IdNutri`, `Estado_Turno`),
  CONSTRAINT `fk_turno_nutri` FOREIGN KEY (`IdNutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_turno_paciente` FOREIGN KEY (`IdPaciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `chk_turno_estado` CHECK (`Estado_Turno` IN ('Pendiente', 'Confirmado', 'Cancelado', 'Atendido'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABLA: historia_clinica
CREATE TABLE IF NOT EXISTS `historia_clinica` (
  `IdHistoria` INT(11) NOT NULL AUTO_INCREMENT,
  `IdPaciente` INT(11) NOT NULL,
  `IdNutri` INT(11) NOT NULL,
  `FechaUltimaModificacion` DATETIME NOT NULL,
  `Datos_JSON` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`IdHistoria`),
  UNIQUE KEY `uq_historia_paciente` (`IdPaciente`),
  KEY `fk_historia_nutri` (`IdNutri`),
  CONSTRAINT `fk_historia_paciente` FOREIGN KEY (`IdPaciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_historia_nutri` FOREIGN KEY (`IdNutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABLA: historia_clinica_campos_custom
CREATE TABLE IF NOT EXISTS `historia_clinica_campos_custom` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` INT(11) NOT NULL,
  `seccion` VARCHAR(50) NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `contenido` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_custom_paciente` (`paciente_id`),
  CONSTRAINT `fk_custom_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABLA: dia_semana
CREATE TABLE IF NOT EXISTS `dia_semana` (
  `IdDia` INT(11) NOT NULL,
  `Nombre_Dia` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`IdDia`),
  CONSTRAINT `chk_dia_nombre` CHECK (`Nombre_Dia` IN ('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TABLA: momento_dia
CREATE TABLE IF NOT EXISTS `momento_dia` (
  `IdMomento` INT(11) NOT NULL,
  `Nombre_Momento` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`IdMomento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. TABLA: alimento
CREATE TABLE IF NOT EXISTS `alimento` (
  `IdAlimento` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre_Alimento` VARCHAR(150) NOT NULL,
  `Calorias_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `Proteinas_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `Carbohidratos_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `Grasas_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`IdAlimento`),
  KEY `idx_alimento_nombre` (`Nombre_Alimento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. TABLA: plan_alimentario
CREATE TABLE IF NOT EXISTS `plan_alimentario` (
  `IdPlan` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre_Plan` VARCHAR(100) NOT NULL,
  `Fecha_Inicio` DATE NOT NULL,
  `Fecha_Fin` DATE DEFAULT NULL,
  `Objetivo` VARCHAR(255) DEFAULT NULL,
  `Recomendaciones` TEXT DEFAULT NULL,
  `IdPaciente` INT(11) NOT NULL,
  `Estado_Plan` VARCHAR(20) NOT NULL DEFAULT 'Activo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdPlan`),
  KEY `fk_plan_paciente` (`IdPaciente`),
  CONSTRAINT `fk_plan_paciente` FOREIGN KEY (`IdPaciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. TABLA: detalle_plan_alimento
CREATE TABLE IF NOT EXISTS `detalle_plan_alimento` (
  `IdDetalle` INT(11) NOT NULL AUTO_INCREMENT,
  `IdPlan` INT(11) NOT NULL,
  `IdDia` INT(11) NOT NULL,
  `IdMomento` INT(11) NOT NULL,
  `IdAlimento` INT(11) DEFAULT NULL,
  `Cantidad_Gramos` VARCHAR(100) DEFAULT NULL,
  `Indicaciones_Especiales` VARCHAR(255) DEFAULT NULL,
  `Alimento_Personalizado` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`IdDetalle`),
  KEY `fk_detalle_plan` (`IdPlan`),
  KEY `fk_detalle_dia` (`IdDia`),
  KEY `fk_detalle_momento` (`IdMomento`),
  KEY `fk_detalle_alimento` (`IdAlimento`),
  CONSTRAINT `fk_detalle_plan` FOREIGN KEY (`IdPlan`) REFERENCES `plan_alimentario` (`IdPlan`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_dia` FOREIGN KEY (`IdDia`) REFERENCES `dia_semana` (`IdDia`),
  CONSTRAINT `fk_detalle_momento` FOREIGN KEY (`IdMomento`) REFERENCES `momento_dia` (`IdMomento`),
  CONSTRAINT `fk_detalle_alimento` FOREIGN KEY (`IdAlimento`) REFERENCES `alimento` (`IdAlimento`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. TABLA: informe_educativo
CREATE TABLE IF NOT EXISTS `informe_educativo` (
  `IdInforme` INT(11) NOT NULL AUTO_INCREMENT,
  `IdPaciente` INT(11) NOT NULL,
  `IdNutri` INT(11) NOT NULL,
  `Fecha` DATE NOT NULL,
  `Contenido_JSON` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdInforme`),
  KEY `fk_informe_paciente` (`IdPaciente`),
  KEY `fk_informe_nutri` (`IdNutri`),
  CONSTRAINT `fk_informe_paciente` FOREIGN KEY (`IdPaciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_informe_nutri` FOREIGN KEY (`IdNutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. TABLA: plan_informe_educativo
CREATE TABLE IF NOT EXISTS `plan_informe_educativo` (
  `IdPlan` INT(11) NOT NULL,
  `IdInforme` INT(11) NOT NULL,
  PRIMARY KEY (`IdPlan`, `IdInforme`),
  KEY `fk_pie_informe` (`IdInforme`),
  CONSTRAINT `fk_pie_plan` FOREIGN KEY (`IdPlan`) REFERENCES `plan_alimentario` (`IdPlan`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_pie_informe` FOREIGN KEY (`IdInforme`) REFERENCES `informe_educativo` (`IdInforme`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. TABLA: pago (Módulo Financiero / SaaS)
CREATE TABLE IF NOT EXISTS `pago` (
  `IdPago` INT(11) NOT NULL AUTO_INCREMENT,
  `Monto` DECIMAL(10,2) NOT NULL,
  `Fecha_Pago` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Estado_Pago` VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
  `Metodo_Pago` VARCHAR(30) DEFAULT NULL,
  `IdPaciente` INT(11) NOT NULL,
  `IdNutri` INT(11) NOT NULL,
  PRIMARY KEY (`IdPago`),
  KEY `fk_pago_paciente` (`IdPaciente`),
  KEY `fk_pago_nutri` (`IdNutri`),
  CONSTRAINT `fk_pago_paciente` FOREIGN KEY (`IdPaciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_pago_nutri` FOREIGN KEY (`IdNutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `chk_pago_estado` CHECK (`Estado_Pago` IN ('Aprobado','Rechazado','Pendiente'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. TABLA: password_resets (Tokens seguros de recuperación de contraseña)
CREATE TABLE IF NOT EXISTS `password_resets` (
  `IdReset` INT(11) NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(150) NOT NULL,
  `Token` VARCHAR(64) NOT NULL,
  `Tipo_Usuario` ENUM('nutricionista', 'paciente') NOT NULL,
  `Expires_At` DATETIME NOT NULL,
  `Used_At` DATETIME NULL DEFAULT NULL,
  `Created_At` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdReset`),
  UNIQUE KEY `uq_token` (`Token`),
  KEY `idx_email_tipo` (`Email`, `Tipo_Usuario`),
  KEY `idx_token_expires` (`Token`, `Expires_At`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. VISTA: vista_menu_paciente
CREATE OR REPLACE VIEW `vista_menu_paciente` AS
SELECT 
    dp.IdDetalle AS IdDetalle,
    dp.IdPlan AS IdPlan,
    dp.IdDia AS IdDia,
    dp.IdMomento AS IdMomento,
    m.Nombre_Momento AS Momento_Comida,
    COALESCE(dp.Alimento_Personalizado, a.Nombre_Alimento, 'Alimento General') AS Alimento,
    dp.Cantidad_Gramos AS Cantidad,
    dp.Indicaciones_Especiales AS Indicaciones_Especiales
FROM detalle_plan_alimento dp
LEFT JOIN alimento a ON dp.IdAlimento = a.IdAlimento
JOIN momento_dia m ON dp.IdMomento = m.IdMomento;

-- ========================================================================
-- POBLACIÓN DE CATÁLOGOS BASE Y SEMILLAS
-- ========================================================================

-- Catálogo: Días de la Semana
INSERT IGNORE INTO `dia_semana` (`IdDia`, `Nombre_Dia`) VALUES
(1, 'Lunes'),
(2, 'Martes'),
(3, 'Miercoles'),
(4, 'Jueves'),
(5, 'Viernes'),
(6, 'Sabado'),
(7, 'Domingo');

-- Catálogo: Momentos del Día
INSERT IGNORE INTO `momento_dia` (`IdMomento`, `Nombre_Momento`) VALUES
(1, 'Desayuno'),
(2, 'Media Mañana'),
(3, 'Almuerzo'),
(4, 'Merienda'),
(5, 'Cena'),
(6, 'Colación Nocturna');

-- Catálogo Base de Alimentos
INSERT IGNORE INTO `alimento` (`IdAlimento`, `Nombre_Alimento`, `Calorias_100g`, `Proteinas_100g`, `Carbohidratos_100g`, `Grasas_100g`) VALUES
(1, 'Avena tradicional', 389.00, 16.90, 66.30, 6.90),
(2, 'Pechuga de Pollo grillada', 165.00, 31.00, 0.00, 3.60),
(3, 'Manzana roja', 52.00, 0.30, 14.00, 0.20),
(4, 'Huevo entero hervido', 155.00, 13.00, 1.10, 11.00),
(5, 'Arroz integral cocido', 111.00, 2.60, 23.00, 0.90),
(6, 'Palta Hass', 160.00, 2.00, 8.50, 14.70),
(7, 'Yogur natural descremado', 59.00, 10.00, 3.60, 0.40),
(8, 'Espinaca fresca', 23.00, 2.90, 3.60, 0.40),
(9, 'Lentejas cocidas', 116.00, 9.00, 20.00, 0.40),
(10, 'Nueces peladas', 654.00, 15.00, 14.00, 65.00);

-- Usuario Administrador Principal (Email: admin@nutrisalud.com / Password: admin123)
INSERT IGNORE INTO `nutricionista` (`IdNutri`, `DNI`, `Matricula`, `Nombre`, `Apellido`, `Email`, `Password_Hash`, `Rol`, `Telefono`, `Estado_Cuenta`, `Especialidad`, `Direccion`, `Biografia`) VALUES
(1, '00000000', 'MN-ADMIN-01', 'Administrador', 'NutriSalud', 'admin@nutrisalud.com', '$2y$10$K9aKUirpqzSlvEArPTM0fOTrCFVJVRoa.alhmDBG0wp3PwCIhm0OG', 'admin', '1100000000', 'A', 'Administrador de Plataforma', 'Sede Central NutriSalud', 'Cuenta maestra de administración del sistema NutriSalud SaaS');

SET FOREIGN_KEY_CHECKS = 1;
