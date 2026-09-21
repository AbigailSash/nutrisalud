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
  `Modalidad` ENUM('Presencial', 'Online') NOT NULL DEFAULT 'Presencial',
  `Motivo_Consulta` VARCHAR(255) DEFAULT 'Consulta Nutricional',
  `Link_Reunion` VARCHAR(255) DEFAULT NULL,
  `Direccion` VARCHAR(255) DEFAULT NULL,
  `Notas` TEXT DEFAULT NULL,
  `IdPaciente` INT(11) NOT NULL,
  `IdNutri` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdTurno`),
  KEY `fk_turno_paciente` (`IdPaciente`),
  KEY `fk_turno_nutri` (`IdNutri`),
  KEY `idx_turno_fecha_nutri` (`Fecha`, `IdNutri`, `Estado_Turno`),
  KEY `idx_turno_calendario` (`IdNutri`, `Fecha`, `Hora`, `Estado_Turno`),
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

-- 16. TABLA: alimentos_composicion (Composición Química por 100g comestibles)
CREATE TABLE IF NOT EXISTS `alimentos_composicion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `categoria` VARCHAR(50) NOT NULL,
  `hidratos_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `proteinas_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `grasas_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `fibra_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `hierro_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `calcio_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `sodio_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `potasio_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `fosforo_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `colesterol_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `ag_sat_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `ag_mon_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `ag_pol_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `vit_a_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `vit_b1_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `vit_b2_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `vit_c_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `niacina_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `es_avb` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Proteína de Alto Valor Biológico',
  `es_protector` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Alimento Protector',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_alimentos_nombre` (`nombre`),
  KEY `idx_alimentos_cat` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. TABLA: formula_desarrollada_cabecera
CREATE TABLE IF NOT EXISTS `formula_desarrollada_cabecera` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` INT(11) NOT NULL,
  `id_nutri` INT(11) NOT NULL,
  `nombre_formula` VARCHAR(150) NOT NULL DEFAULT 'Planilla de Fórmula Desarrollada',
  `observaciones` TEXT DEFAULT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_formula_paciente` (`id_paciente`),
  KEY `fk_formula_nutri` (`id_nutri`),
  CONSTRAINT `fk_formula_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_formula_nutri` FOREIGN KEY (`id_nutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. TABLA: formula_desarrollada_detalle
CREATE TABLE IF NOT EXISTS `formula_desarrollada_detalle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_formula` INT(11) NOT NULL,
  `id_alimento` INT(11) NOT NULL,
  `gramos` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `fk_fdetalle_formula` (`id_formula`),
  KEY `fk_fdetalle_alimento` (`id_alimento`),
  CONSTRAINT `fk_fdetalle_formula` FOREIGN KEY (`id_formula`) REFERENCES `formula_desarrollada_cabecera` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_fdetalle_alimento` FOREIGN KEY (`id_alimento`) REFERENCES `alimentos_composicion` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Catálogo Base de Composición Química de Alimentos
INSERT IGNORE INTO `alimentos_composicion` (`id`, `nombre`, `categoria`, `hidratos_100g`, `proteinas_100g`, `grasas_100g`, `fibra_100g`, `hierro_100g`, `calcio_100g`, `sodio_100g`, `potasio_100g`, `fosforo_100g`, `colesterol_100g`, `ag_sat_100g`, `ag_mon_100g`, `ag_pol_100g`, `vit_a_100g`, `vit_b1_100g`, `vit_b2_100g`, `vit_c_100g`, `niacina_100g`, `es_avb`, `es_protector`) VALUES
(1, 'Leche descremada fluida', 'Lácteos', 4.90, 3.40, 0.50, 0.00, 0.10, 120.00, 50.00, 150.00, 95.00, 2.00, 0.30, 0.10, 0.00, 28.00, 0.04, 0.18, 1.00, 0.10, 1, 1),
(2, 'Leche entera fluida', 'Lácteos', 4.70, 3.10, 3.00, 0.00, 0.10, 115.00, 48.00, 140.00, 90.00, 10.00, 1.90, 0.80, 0.10, 30.00, 0.03, 0.16, 1.00, 0.10, 1, 1),
(3, 'Yogur natural descremado', 'Lácteos', 5.00, 4.00, 0.50, 0.00, 0.10, 140.00, 60.00, 180.00, 110.00, 2.00, 0.30, 0.10, 0.00, 25.00, 0.04, 0.20, 0.80, 0.12, 1, 1),
(4, 'Queso blanco magro / Untable light', 'Lácteos', 3.50, 11.00, 2.00, 0.00, 0.20, 130.00, 320.00, 110.00, 140.00, 8.00, 1.20, 0.50, 0.10, 40.00, 0.03, 0.15, 0.00, 0.10, 1, 1),
(5, 'Queso semi-duro (Port Salut / Cuartirolo)', 'Lácteos', 1.00, 22.00, 20.00, 0.00, 0.40, 700.00, 550.00, 90.00, 450.00, 65.00, 12.50, 5.50, 0.60, 220.00, 0.02, 0.35, 0.00, 0.10, 1, 1),
(6, 'Carne vacuna magra (Lomo/Peceto/Nalga)', 'Carnes', 0.00, 21.50, 4.50, 0.00, 3.20, 12.00, 65.00, 340.00, 210.00, 60.00, 1.80, 1.90, 0.20, 0.00, 0.08, 0.22, 0.00, 5.50, 1, 1),
(7, 'Pechuga de pollo sin piel grillada', 'Carnes', 0.00, 23.00, 2.00, 0.00, 1.10, 15.00, 70.00, 330.00, 220.00, 55.00, 0.60, 0.80, 0.40, 15.00, 0.07, 0.15, 0.00, 11.00, 1, 1),
(8, 'Filet de Merluza / Pescado blanco', 'Carnes', 0.00, 18.00, 1.20, 0.00, 0.80, 30.00, 85.00, 300.00, 190.00, 50.00, 0.30, 0.20, 0.40, 20.00, 0.05, 0.10, 0.00, 2.50, 1, 1),
(9, 'Huevo entero de gallina', 'Carnes', 0.80, 12.50, 11.00, 0.00, 2.20, 55.00, 140.00, 130.00, 180.00, 370.00, 3.30, 4.10, 1.40, 190.00, 0.09, 0.35, 0.00, 0.10, 1, 1),
(10, 'Clara de huevo', 'Carnes', 0.70, 11.00, 0.20, 0.00, 0.10, 8.00, 160.00, 150.00, 15.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.01, 0.40, 0.00, 0.10, 1, 1),
(11, 'Tomate fresco redondo', 'Vegetales A', 3.50, 1.00, 0.20, 1.20, 0.60, 10.00, 10.00, 240.00, 25.00, 0.00, 0.03, 0.02, 0.08, 42.00, 0.06, 0.04, 25.00, 0.70, 0, 1),
(12, 'Lechuga criolla / capuchina', 'Vegetales A', 2.00, 1.30, 0.20, 1.30, 1.00, 35.00, 25.00, 200.00, 30.00, 0.00, 0.02, 0.01, 0.09, 130.00, 0.06, 0.08, 10.00, 0.40, 0, 1),
(13, 'Espinaca fresca cruda', 'Vegetales A', 2.50, 2.80, 0.40, 2.20, 3.00, 95.00, 70.00, 550.00, 45.00, 0.00, 0.06, 0.02, 0.18, 470.00, 0.08, 0.19, 30.00, 0.60, 0, 1),
(14, 'Zapallito verde / Calabacín', 'Vegetales A', 3.00, 1.20, 0.20, 1.10, 0.70, 20.00, 3.00, 220.00, 30.00, 0.00, 0.04, 0.01, 0.08, 25.00, 0.05, 0.04, 15.00, 0.50, 0, 1),
(15, 'Zanahoria fresca', 'Vegetales B', 7.50, 1.00, 0.20, 2.80, 0.50, 30.00, 60.00, 320.00, 35.00, 0.00, 0.03, 0.01, 0.10, 835.00, 0.07, 0.05, 7.00, 0.90, 0, 1),
(16, 'Calabaza / Zapallo anco', 'Vegetales B', 6.50, 1.10, 0.10, 1.50, 0.80, 25.00, 4.00, 300.00, 30.00, 0.00, 0.03, 0.01, 0.05, 400.00, 0.06, 0.06, 12.00, 0.60, 0, 1),
(17, 'Cebolla blanca/morada', 'Vegetales B', 7.00, 1.20, 0.10, 1.70, 0.30, 23.00, 5.00, 160.00, 30.00, 0.00, 0.02, 0.01, 0.04, 2.00, 0.04, 0.02, 8.00, 0.20, 0, 1),
(18, 'Remolacha cruda', 'Vegetales B', 8.50, 1.60, 0.10, 2.50, 0.90, 20.00, 75.00, 330.00, 40.00, 0.00, 0.02, 0.01, 0.04, 2.00, 0.03, 0.04, 5.00, 0.30, 0, 1),
(19, 'Papa cocida / hervida', 'Vegetales C', 18.00, 2.00, 0.10, 1.80, 0.80, 10.00, 6.00, 400.00, 50.00, 0.00, 0.03, 0.00, 0.05, 0.00, 0.10, 0.03, 13.00, 1.40, 0, 1),
(20, 'Batata / Camote cocido', 'Vegetales C', 21.00, 1.60, 0.10, 3.00, 0.70, 30.00, 35.00, 350.00, 45.00, 0.00, 0.02, 0.00, 0.04, 700.00, 0.08, 0.06, 15.00, 0.80, 0, 1),
(21, 'Choclo amarillo / Maíz en grano', 'Vegetales C', 19.00, 3.20, 1.20, 2.70, 0.60, 5.00, 15.00, 270.00, 90.00, 0.00, 0.20, 0.40, 0.50, 10.00, 0.15, 0.06, 7.00, 1.70, 0, 1),
(22, 'Manzana con cáscara', 'Frutas', 14.00, 0.30, 0.20, 2.40, 0.15, 6.00, 1.00, 110.00, 11.00, 0.00, 0.03, 0.01, 0.05, 3.00, 0.02, 0.03, 5.00, 0.10, 0, 1),
(23, 'Banana / Plátano maduro', 'Frutas', 22.00, 1.10, 0.30, 2.60, 0.30, 5.00, 1.00, 360.00, 22.00, 0.00, 0.10, 0.03, 0.07, 4.00, 0.04, 0.07, 9.00, 0.70, 0, 1),
(24, 'Naranja / Cítrico fresco', 'Frutas', 10.00, 0.90, 0.10, 2.20, 0.10, 40.00, 0.00, 180.00, 14.00, 0.00, 0.02, 0.02, 0.03, 11.00, 0.09, 0.04, 53.00, 0.30, 0, 1),
(25, 'Frutillas / Fresas frescas', 'Frutas', 7.50, 0.70, 0.30, 2.00, 0.40, 16.00, 1.00, 150.00, 24.00, 0.00, 0.02, 0.04, 0.15, 1.00, 0.02, 0.02, 60.00, 0.40, 0, 1),
(26, 'Avena en copos tradicional', 'Cereales', 66.00, 14.00, 7.00, 10.00, 4.50, 52.00, 4.00, 360.00, 410.00, 0.00, 1.30, 2.50, 2.60, 0.00, 0.50, 0.15, 0.00, 1.10, 0, 0),
(27, 'Arroz integral cocido', 'Cereales', 25.00, 2.60, 0.90, 1.80, 0.50, 10.00, 5.00, 80.00, 100.00, 0.00, 0.20, 0.30, 0.30, 0.00, 0.10, 0.02, 0.00, 1.50, 0, 0),
(28, 'Fideos / Pastas secas cocidas', 'Cereales', 28.00, 5.00, 0.80, 1.50, 1.20, 12.00, 3.00, 50.00, 60.00, 0.00, 0.15, 0.10, 0.30, 0.00, 0.12, 0.05, 0.00, 1.20, 0, 0),
(29, 'Pan blanco de mesa', 'Panes', 52.00, 8.50, 1.50, 2.70, 2.50, 30.00, 500.00, 120.00, 90.00, 0.00, 0.40, 0.30, 0.60, 0.00, 0.25, 0.15, 0.00, 3.00, 0, 0),
(30, 'Pan integral con salvado', 'Panes', 45.00, 9.50, 3.00, 6.50, 3.20, 55.00, 450.00, 220.00, 180.00, 0.00, 0.60, 0.70, 1.30, 0.00, 0.35, 0.18, 0.00, 4.00, 0, 0),
(31, 'Lentejas secas cocidas', 'Legumbres', 20.00, 9.00, 0.40, 7.90, 3.30, 20.00, 4.00, 370.00, 180.00, 0.00, 0.06, 0.07, 0.18, 2.00, 0.17, 0.07, 1.50, 1.00, 0, 0),
(32, 'Garbanzos cocidos', 'Legumbres', 27.00, 8.80, 2.60, 7.60, 2.90, 49.00, 7.00, 290.00, 170.00, 0.00, 0.30, 0.60, 1.20, 3.00, 0.12, 0.06, 1.30, 0.60, 0, 0),
(33, 'Aceite de Oliva Extra Virgen', 'Grasas/Aceites', 0.00, 0.00, 100.00, 0.00, 0.10, 1.00, 2.00, 1.00, 0.00, 0.00, 14.00, 73.00, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0),
(34, 'Aceite de Girasol', 'Grasas/Aceites', 0.00, 0.00, 100.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 11.00, 28.00, 58.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0),
(35, 'Nueces peladas', 'Frutos Secos', 13.50, 15.00, 65.00, 6.70, 2.90, 98.00, 2.00, 440.00, 350.00, 0.00, 6.10, 9.00, 47.00, 2.00, 0.34, 0.15, 1.30, 1.10, 0, 0),
(36, 'Almendras', 'Frutos Secos', 10.00, 21.00, 52.00, 12.00, 3.70, 260.00, 1.00, 700.00, 480.00, 0.00, 4.00, 33.00, 12.50, 1.00, 0.20, 0.80, 0.00, 3.50, 0, 0),
(37, 'Palta / Aguacate Hass', 'Grasas/Aceites', 8.50, 2.00, 15.00, 6.70, 0.60, 12.00, 7.00, 480.00, 52.00, 0.00, 2.10, 10.00, 1.80, 7.00, 0.07, 0.13, 10.00, 1.70, 0, 0),
(38, 'Azúcar blanco de mesa', 'Azúcares/Dulces', 99.80, 0.00, 0.00, 0.00, 0.05, 1.00, 1.00, 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0),
(39, 'Mermelada de frutas común', 'Azúcares/Dulces', 68.00, 0.40, 0.10, 1.00, 0.50, 15.00, 20.00, 60.00, 10.00, 0.00, 0.02, 0.01, 0.03, 2.00, 0.01, 0.02, 3.00, 0.10, 0, 0),
(40, 'Miel pura de abeja', 'Azúcares/Dulces', 82.00, 0.30, 0.00, 0.20, 0.40, 6.00, 4.00, 52.00, 4.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.04, 0.50, 0.10, 0, 0);

-- ========================================================================
-- TABLA: sara2_alimentos (39 Componentes Nutricionales SARA 2 / ENNyS 2)
-- ========================================================================
CREATE TABLE IF NOT EXISTS `sara2_alimentos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `grupo_id` TINYINT(3) UNSIGNED NOT NULL COMMENT '1 a 26 según catálogo oficial SARA 2',
  `grupo_nombre` VARCHAR(150) NOT NULL,
  `medida_casera_ref` VARCHAR(100) NULL COMMENT 'Referencia de porción casera, ej: 1 taza (200g)',
  `energia_kcal` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `agua_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `proteina_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `lipidos_totales_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `colesterol_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `ag_sat_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `ag_mono_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `ag_poli_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `ag_trans_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `ac_linoleico_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000 COMMENT '18:2 cis',
  `ac_linolenico_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000 COMMENT '18:3 cis ALA',
  `ac_araquidonico_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000 COMMENT '20:4',
  `ac_epa_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000 COMMENT '20:5 n-3',
  `ac_dha_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000 COMMENT '22:6 n-3',
  `hc_disponibles_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `hc_totales_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `azucar_total_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `azucar_agregado_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `fibra_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `alcohol_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `cenizas_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `sodio_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `potasio_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `calcio_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `cobre_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `fosforo_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `hierro_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `magnesio_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `zinc_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `niacina_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `folato_efd_ug` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `ac_folico_sint_ug` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `vit_a_rae_ug` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `retinol_ug` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `tiamina_b1_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `riboflavina_b2_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `vit_b12_ug` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `vit_c_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `vit_d_ug` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
  `es_avb` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Proteína de Alto Valor Biológico',
  `es_protector` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Alimento Protector',
  `es_leche` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Leches y yogures (cobertura láctea)',
  `es_hc_complejo` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Carbohidratos Complejos',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sara2_nombre` (`nombre`),
  KEY `idx_sara2_grupo` (`grupo_id`),
  KEY `idx_sara2_flags` (`es_avb`, `es_protector`, `es_leche`, `es_hc_complejo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- TABLA: formula_desarrollada_cabecera
-- ========================================================================
CREATE TABLE IF NOT EXISTS `formula_desarrollada_cabecera` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` INT(11) NOT NULL,
  `id_nutri` INT(11) NOT NULL,
  `nombre_formula` VARCHAR(150) NOT NULL DEFAULT 'Planilla de Fórmula Desarrollada SARA 2',
  `observaciones` TEXT DEFAULT NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_formula_paciente` (`id_paciente`),
  KEY `fk_formula_nutri` (`id_nutri`),
  CONSTRAINT `fk_formula_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_formula_nutri` FOREIGN KEY (`id_nutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- TABLA: formula_desarrollada_detalle
-- ========================================================================
CREATE TABLE IF NOT EXISTS `formula_desarrollada_detalle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_formula` INT(11) NOT NULL,
  `id_alimento` INT(11) NOT NULL,
  `gramos` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `fk_fdetalle_formula` (`id_formula`),
  KEY `fk_fdetalle_sara2` (`id_alimento`),
  CONSTRAINT `fk_fdetalle_formula` FOREIGN KEY (`id_formula`) REFERENCES `formula_desarrollada_cabecera` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_fdetalle_sara2` FOREIGN KEY (`id_alimento`) REFERENCES `sara2_alimentos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- TABLA: evaluacion_riesgo_cv (Iniciativa HEARTS OPS / OMS)
-- ========================================================================
CREATE TABLE IF NOT EXISTS `evaluacion_riesgo_cv` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_paciente` INT(11) NOT NULL,
  `id_nutri` INT(11) NOT NULL,
  `fecha_evaluacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `antecedente_ecv` TINYINT(1) DEFAULT 0,
  `antecedente_erc` TINYINT(1) DEFAULT 0,
  `diabetes` TINYINT(1) DEFAULT 0,
  `tabaquismo` TINYINT(1) DEFAULT 0,
  `edad` INT(11) NOT NULL,
  `sexo` ENUM('M', 'F') NOT NULL,
  `presion_sistolica` INT(11) NOT NULL,
  `con_colesterol` TINYINT(1) DEFAULT 0,
  `colesterol_total` DECIMAL(5,2) NULL,
  `peso` DECIMAL(5,2) NULL,
  `altura` DECIMAL(5,2) NULL,
  `imc` DECIMAL(4,1) NULL,
  `porcentaje_riesgo` VARCHAR(10) NOT NULL,
  `categoria_riesgo` ENUM('Bajo', 'Moderado', 'Alto', 'Muy Alto', 'Critico') NOT NULL,
  `recomendacion_terapeutica` TEXT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_eval_riesgo_paciente` (`id_paciente`),
  KEY `idx_eval_riesgo_nutri` (`id_nutri`),
  CONSTRAINT `fk_eval_riesgocv_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_eval_riesgocv_nutri` FOREIGN KEY (`id_nutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `sara2_alimentos` 
(`id`, `nombre`, `grupo_id`, `grupo_nombre`, `energia_kcal`, `agua_g`, `proteina_g`, `lipidos_totales_g`, `colesterol_mg`, `ag_sat_g`, `ag_mono_g`, `ag_poli_g`, `ag_trans_g`, `ac_linoleico_g`, `ac_linolenico_g`, `ac_araquidonico_g`, `ac_epa_g`, `ac_dha_g`, `hc_disponibles_g`, `hc_totales_g`, `azucar_total_g`, `azucar_agregado_g`, `fibra_g`, `alcohol_g`, `cenizas_g`, `sodio_mg`, `potasio_mg`, `calcio_mg`, `cobre_mg`, `fosforo_mg`, `hierro_mg`, `magnesio_mg`, `zinc_mg`, `niacina_mg`, `folato_efd_ug`, `ac_folico_sint_ug`, `vit_a_rae_ug`, `retinol_ug`, `tiamina_b1_mg`, `riboflavina_b2_mg`, `vit_b12_ug`, `vit_c_mg`, `vit_d_ug`, `es_avb`, `es_protector`, `es_leche`, `es_hc_complejo`) VALUES
(1, 'Acelga cruda', 1, 'Verduras', 19.000, 92.700, 1.800, 0.200, 0.000, 0.030, 0.010, 0.080, 0.000, 0.050, 0.030, 0.000, 0.000, 0.000, 2.100, 3.700, 1.100, 0.000, 1.600, 0.000, 1.600, 213.000, 379.000, 51.000, 0.180, 46.000, 1.800, 81.000, 0.360, 0.400, 14.000, 0.000, 306.000, 0.000, 0.040, 0.090, 0.000, 30.000, 0.000, 0, 1, 0, 1),
(2, 'Acelga hervida escurrida', 1, 'Verduras', 20.000, 92.600, 1.900, 0.100, 0.000, 0.020, 0.010, 0.050, 0.000, 0.030, 0.020, 0.000, 0.000, 0.000, 2.100, 4.200, 0.600, 0.000, 2.100, 0.000, 1.200, 179.000, 549.000, 58.000, 0.150, 33.000, 2.300, 86.000, 0.330, 0.360, 9.000, 0.000, 340.000, 0.000, 0.030, 0.090, 0.000, 18.000, 0.000, 0, 1, 0, 1),
(3, 'Tomate redondo fresco', 1, 'Verduras', 18.000, 94.500, 0.900, 0.200, 0.000, 0.030, 0.030, 0.080, 0.000, 0.080, 0.000, 0.000, 0.000, 0.000, 2.700, 3.900, 2.600, 0.000, 1.200, 0.000, 0.500, 5.000, 237.000, 10.000, 0.060, 24.000, 0.270, 11.000, 0.170, 0.590, 15.000, 0.000, 42.000, 0.000, 0.040, 0.020, 0.000, 14.000, 0.000, 0, 1, 0, 1),
(4, 'Zanahoria fresca', 1, 'Verduras', 41.000, 88.300, 0.900, 0.200, 0.000, 0.040, 0.010, 0.120, 0.000, 0.110, 0.010, 0.000, 0.000, 0.000, 6.800, 9.600, 4.700, 0.000, 2.800, 0.000, 1.000, 69.000, 320.000, 33.000, 0.040, 35.000, 0.300, 12.000, 0.240, 0.980, 19.000, 0.000, 835.000, 0.000, 0.070, 0.060, 0.000, 6.000, 0.000, 0, 1, 0, 1),
(5, 'Calabaza / Zapallo anco cocido', 1, 'Verduras', 26.000, 93.000, 0.900, 0.100, 0.000, 0.020, 0.010, 0.040, 0.000, 0.030, 0.010, 0.000, 0.000, 0.000, 5.400, 6.500, 2.200, 0.000, 1.100, 0.000, 0.800, 3.000, 230.000, 21.000, 0.070, 19.000, 0.600, 14.000, 0.150, 0.500, 16.000, 0.000, 426.000, 0.000, 0.050, 0.080, 0.000, 9.000, 0.000, 0, 1, 0, 1),
(6, 'Banana madura fresca', 2, 'Frutas', 89.000, 74.900, 1.100, 0.300, 0.000, 0.110, 0.030, 0.070, 0.000, 0.050, 0.030, 0.000, 0.000, 0.000, 20.200, 22.800, 12.200, 0.000, 2.600, 0.000, 0.800, 1.000, 358.000, 5.000, 0.080, 22.000, 0.260, 27.000, 0.150, 0.660, 20.000, 0.000, 3.000, 0.000, 0.030, 0.070, 0.000, 9.000, 0.000, 0, 1, 0, 1),
(7, 'Manzana con cáscara', 2, 'Frutas', 52.000, 85.600, 0.300, 0.200, 0.000, 0.030, 0.010, 0.050, 0.000, 0.040, 0.010, 0.000, 0.000, 0.000, 11.400, 13.800, 10.400, 0.000, 2.400, 0.000, 0.300, 1.000, 107.000, 6.000, 0.030, 11.000, 0.120, 5.000, 0.040, 0.090, 3.000, 0.000, 3.000, 0.000, 0.020, 0.030, 0.000, 5.000, 0.000, 0, 1, 0, 1),
(8, 'Naranja fresca', 2, 'Frutas', 47.000, 86.800, 0.900, 0.100, 0.000, 0.020, 0.020, 0.030, 0.000, 0.020, 0.010, 0.000, 0.000, 0.000, 9.400, 11.800, 9.400, 0.000, 2.400, 0.000, 0.400, 0.000, 181.000, 40.000, 0.050, 14.000, 0.100, 10.000, 0.070, 0.280, 30.000, 0.000, 11.000, 0.000, 0.090, 0.040, 0.000, 53.000, 0.000, 0, 1, 0, 1),
(9, 'Arroz blanco cocido', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 130.000, 68.400, 2.700, 0.300, 0.000, 0.080, 0.090, 0.080, 0.000, 0.070, 0.010, 0.000, 0.000, 0.000, 28.200, 28.600, 0.100, 0.000, 0.400, 0.000, 0.400, 1.000, 35.000, 10.000, 0.070, 43.000, 0.200, 12.000, 0.490, 1.600, 3.000, 0.000, 0.000, 0.000, 0.020, 0.010, 0.000, 0.000, 0.000, 0, 0, 0, 1),
(10, 'Avena arrollada / en copos', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 389.000, 8.200, 16.900, 6.900, 0.000, 1.200, 2.200, 2.500, 0.000, 2.400, 0.110, 0.000, 0.000, 0.000, 55.700, 66.300, 1.000, 0.000, 10.600, 0.000, 1.700, 2.000, 429.000, 54.000, 0.620, 523.000, 4.700, 177.000, 3.970, 0.960, 56.000, 0.000, 0.000, 0.000, 0.760, 0.140, 0.000, 0.000, 0.000, 0, 0, 0, 1),
(11, 'Papa hervida sin piel', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 87.000, 77.000, 1.900, 0.100, 0.000, 0.030, 0.000, 0.040, 0.000, 0.040, 0.010, 0.000, 0.000, 0.000, 18.300, 20.100, 0.900, 0.000, 1.800, 0.000, 0.900, 5.000, 379.000, 8.000, 0.170, 44.000, 0.310, 20.000, 0.300, 1.310, 10.000, 0.000, 0.000, 0.000, 0.110, 0.020, 0.000, 7.000, 0.000, 0, 1, 0, 1),
(12, 'Lentejas secas cocidas', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 116.000, 69.600, 9.000, 0.400, 0.000, 0.050, 0.070, 0.180, 0.000, 0.150, 0.030, 0.000, 0.000, 0.000, 12.200, 20.100, 1.800, 0.000, 7.900, 0.000, 0.900, 2.000, 369.000, 19.000, 0.250, 180.000, 3.330, 36.000, 1.270, 1.060, 181.000, 0.000, 2.000, 0.000, 0.170, 0.070, 0.000, 1.500, 0.000, 0, 1, 0, 1),
(13, 'Pan francés / común', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 265.000, 35.700, 9.000, 3.200, 0.000, 0.700, 0.600, 1.400, 0.000, 1.200, 0.070, 0.000, 0.000, 0.000, 46.400, 49.100, 3.600, 0.000, 2.700, 0.000, 2.000, 540.000, 115.000, 26.000, 0.150, 99.000, 3.600, 25.000, 0.740, 4.300, 111.000, 54.000, 0.000, 0.000, 0.470, 0.310, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(14, 'Leche fluida descremada fortificada', 4, 'Leche y postres de leche', 36.000, 90.800, 3.400, 0.200, 2.000, 0.120, 0.050, 0.010, 0.010, 0.010, 0.000, 0.000, 0.000, 0.000, 4.900, 4.900, 4.900, 0.000, 0.000, 0.000, 0.700, 52.000, 156.000, 120.000, 0.010, 101.000, 0.050, 11.000, 0.420, 0.090, 5.000, 0.000, 60.000, 60.000, 0.040, 0.180, 0.400, 1.000, 1.000, 1, 1, 1, 0),
(15, 'Leche fluida entera', 4, 'Leche y postres de leche', 61.000, 88.100, 3.200, 3.300, 10.000, 1.900, 0.800, 0.120, 0.100, 0.100, 0.020, 0.000, 0.000, 0.000, 4.800, 4.800, 4.800, 0.000, 0.000, 0.000, 0.700, 43.000, 132.000, 113.000, 0.010, 84.000, 0.030, 10.000, 0.370, 0.080, 5.000, 0.000, 46.000, 46.000, 0.050, 0.170, 0.450, 0.000, 1.000, 1, 1, 1, 0),
(16, 'Yogur descremado natural/vainilla', 5, 'Yogures', 45.000, 88.000, 4.000, 0.200, 2.000, 0.100, 0.040, 0.010, 0.000, 0.010, 0.000, 0.000, 0.000, 0.000, 6.800, 6.800, 6.800, 0.000, 0.000, 0.000, 0.800, 65.000, 210.000, 140.000, 0.010, 120.000, 0.080, 15.000, 0.600, 0.120, 7.000, 0.000, 50.000, 50.000, 0.050, 0.210, 0.500, 0.500, 0.500, 1, 1, 1, 0),
(17, 'Queso Port Salut / Cremoso magro', 6, 'Quesos', 220.000, 55.000, 22.000, 14.000, 45.000, 8.500, 3.800, 0.500, 0.400, 0.400, 0.100, 0.000, 0.000, 0.000, 1.500, 1.500, 1.000, 0.000, 0.000, 0.000, 3.500, 480.000, 95.000, 650.000, 0.030, 420.000, 0.300, 20.000, 3.100, 0.100, 12.000, 0.000, 180.000, 180.000, 0.030, 0.320, 1.400, 0.000, 0.300, 1, 1, 0, 0),
(18, 'Queso untable descremado / blanco', 6, 'Quesos', 85.000, 78.000, 11.000, 2.500, 8.000, 1.500, 0.700, 0.100, 0.050, 0.080, 0.020, 0.000, 0.000, 0.000, 4.000, 4.000, 3.500, 0.000, 0.000, 0.000, 1.500, 350.000, 120.000, 120.000, 0.020, 140.000, 0.100, 11.000, 0.800, 0.100, 8.000, 0.000, 35.000, 35.000, 0.030, 0.180, 0.500, 0.000, 0.100, 1, 1, 0, 0),
(19, 'Carne vacuna magra (Lomo/Nalga/Peceto) cocida', 7, 'Carnes', 185.000, 62.000, 28.000, 7.500, 75.000, 2.800, 3.200, 0.400, 0.300, 0.350, 0.050, 0.040, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.200, 60.000, 360.000, 12.000, 0.080, 220.000, 3.400, 22.000, 5.800, 6.200, 8.000, 0.000, 0.000, 0.000, 0.080, 0.220, 2.200, 0.000, 0.100, 1, 1, 0, 0),
(20, 'Pechuga de pollo sin piel cocida/grillada', 7, 'Carnes', 165.000, 65.000, 31.000, 3.600, 85.000, 1.000, 1.300, 0.800, 0.000, 0.700, 0.060, 0.080, 0.010, 0.020, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.100, 74.000, 256.000, 15.000, 0.050, 228.000, 1.040, 29.000, 1.000, 13.700, 4.000, 0.000, 6.000, 6.000, 0.070, 0.120, 0.340, 0.000, 0.100, 1, 1, 0, 0),
(21, 'Huevo entero de gallina hervido', 8, 'Huevos', 155.000, 74.600, 12.600, 10.600, 373.000, 3.300, 4.100, 1.400, 0.040, 1.200, 0.040, 0.140, 0.000, 0.030, 0.800, 1.100, 1.100, 0.000, 0.000, 0.000, 0.900, 124.000, 126.000, 50.000, 0.070, 172.000, 1.190, 10.000, 1.050, 0.060, 44.000, 0.000, 140.000, 140.000, 0.070, 0.510, 1.110, 0.000, 2.000, 1, 1, 0, 0),
(22, 'Merluza filet cocido al horno/vapor', 9, 'Pescados y mariscos', 90.000, 79.500, 19.500, 1.200, 60.000, 0.250, 0.200, 0.450, 0.000, 0.050, 0.040, 0.020, 0.150, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.200, 95.000, 340.000, 35.000, 0.040, 210.000, 0.500, 30.000, 0.650, 2.500, 10.000, 0.000, 25.000, 25.000, 0.060, 0.120, 1.800, 0.000, 1.500, 1, 1, 0, 0),
(23, 'Atún al natural enlatado', 9, 'Pescados y mariscos', 116.000, 73.000, 25.500, 0.800, 35.000, 0.200, 0.150, 0.300, 0.000, 0.030, 0.020, 0.020, 0.080, 0.150, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.400, 320.000, 240.000, 12.000, 0.050, 190.000, 1.300, 28.000, 0.800, 10.500, 5.000, 0.000, 18.000, 18.000, 0.030, 0.080, 2.500, 0.000, 1.200, 1, 1, 0, 0),
(24, 'Aceite de oliva extra virgen', 10, 'Aceites', 884.000, 0.000, 0.000, 100.000, 0.000, 14.000, 73.000, 10.000, 0.000, 9.200, 0.700, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 2.000, 1.000, 1.000, 0.000, 0.000, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(25, 'Aceite de girasol', 10, 'Aceites', 884.000, 0.000, 0.000, 100.000, 0.000, 11.000, 28.000, 58.000, 0.000, 57.500, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(26, 'Nueces peladas', 11, 'Frutas secas y semillas', 654.000, 4.100, 15.200, 65.200, 0.000, 6.100, 8.900, 47.200, 0.000, 38.100, 9.100, 0.000, 0.000, 0.000, 6.700, 13.700, 2.600, 0.000, 6.700, 0.000, 1.800, 2.000, 441.000, 98.000, 1.580, 346.000, 2.910, 158.000, 3.090, 1.120, 98.000, 0.000, 2.000, 0.000, 0.340, 0.150, 0.000, 1.300, 0.000, 0, 0, 0, 0),
(27, 'Semillas de chía', 11, 'Frutas secas y semillas', 486.000, 5.800, 16.500, 30.700, 0.000, 3.300, 2.300, 23.700, 0.000, 5.800, 17.800, 0.000, 0.000, 0.000, 7.700, 42.100, 0.800, 0.000, 34.400, 0.000, 4.900, 16.000, 407.000, 631.000, 0.920, 860.000, 7.720, 335.000, 4.580, 8.830, 49.000, 0.000, 5.000, 0.000, 0.620, 0.170, 0.000, 1.600, 0.000, 0, 0, 0, 0),
(28, 'Azúcar blanco común', 12, 'Azúcares, mermeladas y dulces', 399.000, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 99.800, 99.800, 99.800, 99.800, 0.000, 0.000, 0.000, 1.000, 2.000, 1.000, 0.000, 0.000, 0.050, 0.000, 0.010, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(29, 'Dulce de leche común', 12, 'Azúcares, mermeladas y dulces', 315.000, 29.000, 6.500, 6.000, 22.000, 3.600, 1.600, 0.200, 0.100, 0.150, 0.020, 0.000, 0.000, 0.000, 58.000, 58.000, 55.000, 40.000, 0.000, 0.000, 1.500, 130.000, 280.000, 230.000, 0.020, 190.000, 0.200, 20.000, 0.800, 0.200, 10.000, 0.000, 55.000, 55.000, 0.050, 0.250, 0.600, 0.000, 0.100, 0, 0, 0, 0),
(30, 'Chocolate semiamargo (60% cacao)', 13, 'Golosinas y chocolates', 546.000, 1.200, 5.500, 31.000, 3.000, 18.500, 10.000, 1.100, 0.000, 0.900, 0.050, 0.000, 0.000, 0.000, 54.000, 61.000, 47.000, 46.000, 7.000, 0.000, 1.500, 20.000, 500.000, 56.000, 1.000, 180.000, 8.000, 140.000, 2.300, 1.050, 10.000, 0.000, 2.000, 0.000, 0.030, 0.080, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(31, 'Manteca vacuna', 14, 'Grasas', 717.000, 16.000, 0.800, 81.100, 215.000, 51.400, 21.000, 3.000, 3.200, 2.200, 0.300, 0.000, 0.000, 0.000, 0.100, 0.100, 0.100, 0.000, 0.000, 0.000, 2.100, 580.000, 24.000, 24.000, 0.010, 24.000, 0.020, 2.000, 0.090, 0.040, 3.000, 0.000, 684.000, 671.000, 0.010, 0.030, 0.170, 0.000, 1.500, 0, 0, 0, 0),
(32, 'Papas fritas en paquete / snack', 15, 'Snacks salados', 536.000, 2.000, 7.000, 35.000, 0.000, 4.500, 16.500, 12.000, 0.200, 11.200, 0.500, 0.000, 0.000, 0.000, 49.000, 53.000, 0.500, 0.000, 4.000, 0.000, 3.000, 520.000, 1200.000, 25.000, 0.250, 160.000, 1.600, 65.000, 1.100, 4.200, 45.000, 0.000, 0.000, 0.000, 0.200, 0.100, 0.000, 18.000, 0.000, 0, 0, 0, 0),
(33, 'Mayonesa común industrial', 16, 'Aderezos', 680.000, 21.000, 1.000, 75.000, 42.000, 11.500, 18.000, 42.000, 0.300, 38.000, 3.500, 0.050, 0.000, 0.000, 2.500, 2.500, 1.500, 1.000, 0.000, 0.000, 1.500, 590.000, 30.000, 12.000, 0.020, 28.000, 0.200, 4.000, 0.200, 0.100, 5.000, 0.000, 25.000, 25.000, 0.010, 0.020, 0.100, 0.000, 0.200, 0, 0, 0, 0),
(34, 'Caldo de verduras en cubo', 17, 'Caldos y sopas industriales', 180.000, 3.000, 8.000, 5.000, 1.000, 2.500, 1.500, 0.500, 0.100, 0.400, 0.050, 0.000, 0.000, 0.000, 25.000, 25.000, 5.000, 4.000, 1.000, 0.000, 58.000, 22000.000, 350.000, 50.000, 0.100, 120.000, 1.200, 30.000, 0.800, 1.500, 15.000, 0.000, 10.000, 0.000, 0.100, 0.100, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(35, 'Helado de crema y dulce de leche', 18, 'Postres industriales y helados', 222.000, 60.000, 3.800, 11.500, 35.000, 7.000, 3.200, 0.500, 0.300, 0.400, 0.050, 0.000, 0.000, 0.000, 26.000, 26.000, 24.000, 20.000, 0.200, 0.000, 0.900, 80.000, 180.000, 130.000, 0.030, 110.000, 0.200, 15.000, 0.700, 0.150, 10.000, 0.000, 85.000, 85.000, 0.040, 0.200, 0.400, 0.500, 0.200, 0, 0, 0, 0),
(36, 'Sal de mesa fina yodada', 19, 'Sales', 0.000, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 99.800, 38758.000, 8.000, 24.000, 0.030, 0.000, 0.330, 1.000, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(37, 'Gaseosa cola regular', 20, 'Bebidas con azúcar', 42.000, 89.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 10.600, 10.600, 10.600, 10.600, 0.000, 0.000, 0.100, 11.000, 2.000, 2.000, 0.000, 13.000, 0.050, 1.000, 0.050, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(38, 'Gaseosa cola light / zero', 21, 'Bebidas sin azúcar', 0.500, 99.400, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.100, 15.000, 5.000, 3.000, 0.000, 15.000, 0.020, 1.000, 0.020, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(39, 'Vino tinto común', 22, 'Bebidas alcohólicas y energizantes', 85.000, 86.500, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 2.600, 2.600, 0.600, 0.000, 0.000, 10.500, 0.300, 4.000, 127.000, 8.000, 0.010, 23.000, 0.460, 12.000, 0.140, 0.220, 1.000, 0.000, 0.000, 0.000, 0.000, 0.030, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(40, 'Cerveza rubia común', 22, 'Bebidas alcohólicas y energizantes', 43.000, 92.000, 0.500, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 3.600, 3.600, 0.000, 0.000, 0.000, 3.900, 0.200, 4.000, 27.000, 4.000, 0.010, 14.000, 0.020, 6.000, 0.010, 0.510, 6.000, 0.000, 0.000, 0.000, 0.010, 0.030, 0.020, 0.000, 0.000, 0, 0, 0, 0),
(41, 'Jugo de naranja exprimido natural', 23, 'Bebidas de frutas naturales o mínimamente procesadas sin azúcar agregada', 45.000, 88.300, 0.700, 0.200, 0.000, 0.030, 0.040, 0.050, 0.000, 0.040, 0.010, 0.000, 0.000, 0.000, 10.200, 10.400, 8.400, 0.000, 0.200, 0.000, 0.400, 1.000, 200.000, 11.000, 0.040, 17.000, 0.200, 11.000, 0.050, 0.400, 30.000, 0.000, 10.000, 0.000, 0.090, 0.030, 0.000, 50.000, 0.000, 0, 1, 0, 1),
(42, 'Yerba mate cebada / mate cocido infusión', 24, 'Infusiones', 2.000, 99.200, 0.300, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.300, 0.300, 0.000, 0.000, 0.000, 0.000, 0.200, 3.000, 55.000, 12.000, 0.020, 8.000, 0.600, 15.000, 0.100, 0.800, 5.000, 0.000, 0.000, 0.000, 0.020, 0.040, 0.000, 1.500, 0.000, 0, 0, 0, 0),
(43, 'Café infusión sin azúcar', 24, 'Infusiones', 1.000, 99.400, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.200, 0.200, 0.000, 0.000, 0.000, 0.000, 0.200, 2.000, 49.000, 2.000, 0.010, 3.000, 0.010, 3.000, 0.020, 0.190, 2.000, 0.000, 0.000, 0.000, 0.010, 0.080, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(44, 'Hamburguesa completa con queso en pan', 25, 'Alimentos de locales de comidas rápidas', 275.000, 48.000, 14.500, 13.000, 40.000, 5.200, 5.100, 1.800, 0.500, 1.600, 0.200, 0.040, 0.000, 0.000, 25.000, 26.500, 4.500, 3.000, 1.500, 0.000, 2.200, 620.000, 230.000, 110.000, 0.120, 180.000, 2.200, 22.000, 2.400, 3.800, 38.000, 12.000, 45.000, 45.000, 0.180, 0.200, 0.900, 1.000, 0.200, 0, 0, 0, 0),
(45, 'Suplemento Proteico Whey Protein 80%', 26, 'Suplementos nutricionales', 390.000, 5.000, 80.000, 4.000, 50.000, 2.200, 1.100, 0.400, 0.000, 0.300, 0.050, 0.000, 0.000, 0.000, 6.000, 6.000, 3.000, 0.000, 0.000, 0.000, 4.000, 180.000, 520.000, 450.000, 0.080, 380.000, 0.900, 65.000, 1.200, 1.500, 25.000, 0.000, 40.000, 40.000, 0.200, 0.650, 2.000, 2.000, 0.500, 1, 1, 0, 0)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Usuario Administrador Principal (Email: admin@nutrisalud.com / Password: admin123)
INSERT IGNORE INTO `nutricionista` (`IdNutri`, `DNI`, `Matricula`, `Nombre`, `Apellido`, `Email`, `Password_Hash`, `Rol`, `Telefono`, `Estado_Cuenta`, `Especialidad`, `Direccion`, `Biografia`) VALUES
(1, '00000000', 'MN-ADMIN-01', 'Administrador', 'NutriSalud', 'admin@nutrisalud.com', '$2y$10$K9aKUirpqzSlvEArPTM0fOTrCFVJVRoa.alhmDBG0wp3PwCIhm0OG', 'admin', '1100000000', 'A', 'Administrador de Plataforma', 'Sede Central NutriSalud', 'Cuenta maestra de administración del sistema NutriSalud SaaS');

SET FOREIGN_KEY_CHECKS = 1;

