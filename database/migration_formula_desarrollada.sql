-- ========================================================================
-- NUTRISALUD SaaS - MIGRACIÓN: MÓDULO DE FÓRMULA DESARROLLADA
-- ========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABLA: alimentos_composicion (Composición Química por 100g comestibles)
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

-- 2. TABLA: formula_desarrollada_cabecera
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

-- 3. TABLA: formula_desarrollada_detalle
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
-- SEEDS: Catálogo Nutricional Base de Composición Química por 100g
-- ========================================================================

INSERT INTO `alimentos_composicion` (`id`, `nombre`, `categoria`, `hidratos_100g`, `proteinas_100g`, `grasas_100g`, `fibra_100g`, `hierro_100g`, `calcio_100g`, `sodio_100g`, `potasio_100g`, `fosforo_100g`, `colesterol_100g`, `ag_sat_100g`, `ag_mon_100g`, `ag_pol_100g`, `vit_a_100g`, `vit_b1_100g`, `vit_b2_100g`, `vit_c_100g`, `niacina_100g`, `es_avb`, `es_protector`) VALUES
-- 1. LÁCTEOS (es_avb = 1, es_protector = 1)
(1, 'Leche descremada fluida', 'Lácteos', 4.90, 3.40, 0.50, 0.00, 0.10, 120.00, 50.00, 150.00, 95.00, 2.00, 0.30, 0.10, 0.00, 28.00, 0.04, 0.18, 1.00, 0.10, 1, 1),
(2, 'Leche entera fluida', 'Lácteos', 4.70, 3.10, 3.00, 0.00, 0.10, 115.00, 48.00, 140.00, 90.00, 10.00, 1.90, 0.80, 0.10, 30.00, 0.03, 0.16, 1.00, 0.10, 1, 1),
(3, 'Yogur natural descremado', 'Lácteos', 5.00, 4.00, 0.50, 0.00, 0.10, 140.00, 60.00, 180.00, 110.00, 2.00, 0.30, 0.10, 0.00, 25.00, 0.04, 0.20, 0.80, 0.12, 1, 1),
(4, 'Queso blanco magro / Untable light', 'Lácteos', 3.50, 11.00, 2.00, 0.00, 0.20, 130.00, 320.00, 110.00, 140.00, 8.00, 1.20, 0.50, 0.10, 40.00, 0.03, 0.15, 0.00, 0.10, 1, 1),
(5, 'Queso semi-duro (Port Salut / Cuartirolo)', 'Lácteos', 1.00, 22.00, 20.00, 0.00, 0.40, 700.00, 550.00, 90.00, 450.00, 65.00, 12.50, 5.50, 0.60, 220.00, 0.02, 0.35, 0.00, 0.10, 1, 1),

-- 2. CARNES Y HUEVOS (es_avb = 1, es_protector = 1)
(6, 'Carne vacuna magra (Lomo/Peceto/Nalga)', 'Carnes', 0.00, 21.50, 4.50, 0.00, 3.20, 12.00, 65.00, 340.00, 210.00, 60.00, 1.80, 1.90, 0.20, 0.00, 0.08, 0.22, 0.00, 5.50, 1, 1),
(7, 'Pechuga de pollo sin piel grillada', 'Carnes', 0.00, 23.00, 2.00, 0.00, 1.10, 15.00, 70.00, 330.00, 220.00, 55.00, 0.60, 0.80, 0.40, 15.00, 0.07, 0.15, 0.00, 11.00, 1, 1),
(8, 'Filet de Merluza / Pescado blanco', 'Carnes', 0.00, 18.00, 1.20, 0.00, 0.80, 30.00, 85.00, 300.00, 190.00, 50.00, 0.30, 0.20, 0.40, 20.00, 0.05, 0.10, 0.00, 2.50, 1, 1),
(9, 'Huevo entero de gallina', 'Carnes', 0.80, 12.50, 11.00, 0.00, 2.20, 55.00, 140.00, 130.00, 180.00, 370.00, 3.30, 4.10, 1.40, 190.00, 0.09, 0.35, 0.00, 0.10, 1, 1),
(10, 'Clara de huevo', 'Carnes', 0.70, 11.00, 0.20, 0.00, 0.10, 8.00, 160.00, 150.00, 15.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.01, 0.40, 0.00, 0.10, 1, 1),

-- 3. VEGETALES A (es_avb = 0, es_protector = 1, hidratos < 5%)
(11, 'Tomate fresco redondo', 'Vegetales A', 3.50, 1.00, 0.20, 1.20, 0.60, 10.00, 10.00, 240.00, 25.00, 0.00, 0.03, 0.02, 0.08, 42.00, 0.06, 0.04, 25.00, 0.70, 0, 1),
(12, 'Lechuga criolla / capuchina', 'Vegetales A', 2.00, 1.30, 0.20, 1.30, 1.00, 35.00, 25.00, 200.00, 30.00, 0.00, 0.02, 0.01, 0.09, 130.00, 0.06, 0.08, 10.00, 0.40, 0, 1),
(13, 'Espinaca fresca cruda', 'Vegetales A', 2.50, 2.80, 0.40, 2.20, 3.00, 95.00, 70.00, 550.00, 45.00, 0.00, 0.06, 0.02, 0.18, 470.00, 0.08, 0.19, 30.00, 0.60, 0, 1),
(14, 'Zapallito verde / Calabacín', 'Vegetales A', 3.00, 1.20, 0.20, 1.10, 0.70, 20.00, 3.00, 220.00, 30.00, 0.00, 0.04, 0.01, 0.08, 25.00, 0.05, 0.04, 15.00, 0.50, 0, 1),

-- 4. VEGETALES B (es_avb = 0, es_protector = 1, hidratos 5% - 10%)
(15, 'Zanahoria fresca', 'Vegetales B', 7.50, 1.00, 0.20, 2.80, 0.50, 30.00, 60.00, 320.00, 35.00, 0.00, 0.03, 0.01, 0.10, 835.00, 0.07, 0.05, 7.00, 0.90, 0, 1),
(16, 'Calabaza / Zapallo anco', 'Vegetales B', 6.50, 1.10, 0.10, 1.50, 0.80, 25.00, 4.00, 300.00, 30.00, 0.00, 0.03, 0.01, 0.05, 400.00, 0.06, 0.06, 12.00, 0.60, 0, 1),
(17, 'Cebolla blanca/morada', 'Vegetales B', 7.00, 1.20, 0.10, 1.70, 0.30, 23.00, 5.00, 160.00, 30.00, 0.00, 0.02, 0.01, 0.04, 2.00, 0.04, 0.02, 8.00, 0.20, 0, 1),
(18, 'Remolacha cruda', 'Vegetales B', 8.50, 1.60, 0.10, 2.50, 0.90, 20.00, 75.00, 330.00, 40.00, 0.00, 0.02, 0.01, 0.04, 2.00, 0.03, 0.04, 5.00, 0.30, 0, 1),

-- 5. VEGETALES C / FECULENTOS (es_avb = 0, es_protector = 1, hidratos > 10%)
(19, 'Papa cocida / hervida', 'Vegetales C', 18.00, 2.00, 0.10, 1.80, 0.80, 10.00, 6.00, 400.00, 50.00, 0.00, 0.03, 0.00, 0.05, 0.00, 0.10, 0.03, 13.00, 1.40, 0, 1),
(20, 'Batata / Camote cocido', 'Vegetales C', 21.00, 1.60, 0.10, 3.00, 0.70, 30.00, 35.00, 350.00, 45.00, 0.00, 0.02, 0.00, 0.04, 700.00, 0.08, 0.06, 15.00, 0.80, 0, 1),
(21, 'Choclo amarillo / Maíz en grano', 'Vegetales C', 19.00, 3.20, 1.20, 2.70, 0.60, 5.00, 15.00, 270.00, 90.00, 0.00, 0.20, 0.40, 0.50, 10.00, 0.15, 0.06, 7.00, 1.70, 0, 1),

-- 6. FRUTAS (es_avb = 0, es_protector = 1)
(22, 'Manzana con cáscara', 'Frutas', 14.00, 0.30, 0.20, 2.40, 0.15, 6.00, 1.00, 110.00, 11.00, 0.00, 0.03, 0.01, 0.05, 3.00, 0.02, 0.03, 5.00, 0.10, 0, 1),
(23, 'Banana / Plátano maduro', 'Frutas', 22.00, 1.10, 0.30, 2.60, 0.30, 5.00, 1.00, 360.00, 22.00, 0.00, 0.10, 0.03, 0.07, 4.00, 0.04, 0.07, 9.00, 0.70, 0, 1),
(24, 'Naranja / Cítrico fresco', 'Frutas', 10.00, 0.90, 0.10, 2.20, 0.10, 40.00, 0.00, 180.00, 14.00, 0.00, 0.02, 0.02, 0.03, 11.00, 0.09, 0.04, 53.00, 0.30, 0, 1),
(25, 'Frutillas / Fresas frescas', 'Frutas', 7.50, 0.70, 0.30, 2.00, 0.40, 16.00, 1.00, 150.00, 24.00, 0.00, 0.02, 0.04, 0.15, 1.00, 0.02, 0.02, 60.00, 0.40, 0, 1),

-- 7. CEREALES, LEGUMBRES Y DERIVADOS (es_avb = 0, es_protector = 0)
(26, 'Avena en copos tradicional', 'Cereales', 66.00, 14.00, 7.00, 10.00, 4.50, 52.00, 4.00, 360.00, 410.00, 0.00, 1.30, 2.50, 2.60, 0.00, 0.50, 0.15, 0.00, 1.10, 0, 0),
(27, 'Arroz integral cocido', 'Cereales', 25.00, 2.60, 0.90, 1.80, 0.50, 10.00, 5.00, 80.00, 100.00, 0.00, 0.20, 0.30, 0.30, 0.00, 0.10, 0.02, 0.00, 1.50, 0, 0),
(28, 'Fideos / Pastas secas cocidas', 'Cereales', 28.00, 5.00, 0.80, 1.50, 1.20, 12.00, 3.00, 50.00, 60.00, 0.00, 0.15, 0.10, 0.30, 0.00, 0.12, 0.05, 0.00, 1.20, 0, 0),
(29, 'Pan blanco de mesa', 'Panes', 52.00, 8.50, 1.50, 2.70, 2.50, 30.00, 500.00, 120.00, 90.00, 0.00, 0.40, 0.30, 0.60, 0.00, 0.25, 0.15, 0.00, 3.00, 0, 0),
(30, 'Pan integral con salvado', 'Panes', 45.00, 9.50, 3.00, 6.50, 3.20, 55.00, 450.00, 220.00, 180.00, 0.00, 0.60, 0.70, 1.30, 0.00, 0.35, 0.18, 0.00, 4.00, 0, 0),
(31, 'Lentejas secas cocidas', 'Legumbres', 20.00, 9.00, 0.40, 7.90, 3.30, 20.00, 4.00, 370.00, 180.00, 0.00, 0.06, 0.07, 0.18, 2.00, 0.17, 0.07, 1.50, 1.00, 0, 0),
(32, 'Garbanzos cocidos', 'Legumbres', 27.00, 8.80, 2.60, 7.60, 2.90, 49.00, 7.00, 290.00, 170.00, 0.00, 0.30, 0.60, 1.20, 3.00, 0.12, 0.06, 1.30, 0.60, 0, 0),

-- 8. GRASAS Y ACEITES (es_avb = 0, es_protector = 0)
(33, 'Aceite de Oliva Extra Virgen', 'Grasas/Aceites', 0.00, 0.00, 100.00, 0.00, 0.10, 1.00, 2.00, 1.00, 0.00, 0.00, 14.00, 73.00, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0),
(34, 'Aceite de Girasol', 'Grasas/Aceites', 0.00, 0.00, 100.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 11.00, 28.00, 58.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0),
(35, 'Nueces peladas', 'Frutos Secos', 13.50, 15.00, 65.00, 6.70, 2.90, 98.00, 2.00, 440.00, 350.00, 0.00, 6.10, 9.00, 47.00, 2.00, 0.34, 0.15, 1.30, 1.10, 0, 0),
(36, 'Almendras', 'Frutos Secos', 10.00, 21.00, 52.00, 12.00, 3.70, 260.00, 1.00, 700.00, 480.00, 0.00, 4.00, 33.00, 12.50, 1.00, 0.20, 0.80, 0.00, 3.50, 0, 0),
(37, 'Palta / Aguacate Hass', 'Grasas/Aceites', 8.50, 2.00, 15.00, 6.70, 0.60, 12.00, 7.00, 480.00, 52.00, 0.00, 2.10, 10.00, 1.80, 7.00, 0.07, 0.13, 10.00, 1.70, 0, 0),

-- 9. AZÚCARES Y DULCES (es_avb = 0, es_protector = 0)
(38, 'Azúcar blanco de mesa', 'Azúcares/Dulces', 99.80, 0.00, 0.00, 0.00, 0.05, 1.00, 1.00, 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0),
(39, 'Mermelada de frutas común', 'Azúcares/Dulces', 68.00, 0.40, 0.10, 1.00, 0.50, 15.00, 20.00, 60.00, 10.00, 0.00, 0.02, 0.01, 0.03, 2.00, 0.01, 0.02, 3.00, 0.10, 0, 0),
(40, 'Miel pura de abeja', 'Azúcares/Dulces', 82.00, 0.30, 0.00, 0.20, 0.40, 6.00, 4.00, 52.00, 4.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.04, 0.50, 0.10, 0, 0)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

SET FOREIGN_KEY_CHECKS = 1;
