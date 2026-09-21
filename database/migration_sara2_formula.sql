-- ========================================================================
-- NUTRISALUD SaaS - MIGRACIÓN: NORMA OFICIAL ARGENTINA SARA 2 (ENNyS 2)
-- ========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABLA: sara2_alimentos (39 Componentes Nutricionales en base a 100g de porción comestible)
CREATE TABLE IF NOT EXISTS `sara2_alimentos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(200) NOT NULL,
  `grupo_id` TINYINT(3) UNSIGNED NOT NULL COMMENT '1 a 26 según catálogo oficial SARA 2',
  `grupo_nombre` VARCHAR(120) NOT NULL,
  
  -- Macronutrientes y Generales (Tabla A)
  `energia_kcal` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `agua_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `proteina_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `lipidos_totales_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `colesterol_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  
  -- Perfil Lipídico Detallado (Tabla A)
  `ag_sat_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `ag_mono_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `ag_poli_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `ag_trans_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `ac_linoleico_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000 COMMENT '18:2 cis',
  `ac_linolenico_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000 COMMENT '18:3 cis ALA',
  `ac_araquidonico_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000 COMMENT '20:4',
  `ac_epa_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000 COMMENT '20:5 n-3',
  `ac_dha_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000 COMMENT '22:6 n-3',
  
  -- Carbohidratos y Alcohol (Tabla A)
  `hc_disponibles_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `hc_totales_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `azucar_total_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `azucar_agregado_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `fibra_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `alcohol_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  
  -- Minerales y Cenizas (Tabla B)
  `cenizas_g` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `sodio_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `potasio_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `calcio_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `cobre_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `fosforo_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `hierro_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `magnesio_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `zinc_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  
  -- Vitaminas (Tabla B)
  `niacina_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `folato_efd_ug` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `ac_folico_sint_ug` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `vit_a_rae_ug` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `retinol_ug` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `tiamina_b1_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `riboflavina_b2_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `vit_b12_ug` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `vit_c_mg` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  `vit_d_ug` DECIMAL(8,3) NOT NULL DEFAULT 0.000,
  
  -- Flags Clínicos Diagnósticos
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

-- 2. TABLAS DE CABECERA Y DETALLE DE FÓRMULA DESARROLLADA
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
-- SEEDS: Catálogo Nutricional Oficial SARA 2 (Muestras Representativas)
-- ========================================================================

INSERT INTO `sara2_alimentos` 
(`id`, `nombre`, `grupo_id`, `grupo_nombre`, `energia_kcal`, `agua_g`, `proteina_g`, `lipidos_totales_g`, `colesterol_mg`, `ag_sat_g`, `ag_mono_g`, `ag_poli_g`, `ag_trans_g`, `ac_linoleico_g`, `ac_linolenico_g`, `ac_araquidonico_g`, `ac_epa_g`, `ac_dha_g`, `hc_disponibles_g`, `hc_totales_g`, `azucar_total_g`, `azucar_agregado_g`, `fibra_g`, `alcohol_g`, `cenizas_g`, `sodio_mg`, `potasio_mg`, `calcio_mg`, `cobre_mg`, `fosforo_mg`, `hierro_mg`, `magnesio_mg`, `zinc_mg`, `niacina_mg`, `folato_efd_ug`, `ac_folico_sint_ug`, `vit_a_rae_ug`, `retinol_ug`, `tiamina_b1_mg`, `riboflavina_b2_mg`, `vit_b12_ug`, `vit_c_mg`, `vit_d_ug`, `es_avb`, `es_protector`, `es_leche`, `es_hc_complejo`) VALUES

-- Grupo 1: Verduras
(1, 'Acelga cruda', 1, 'Verduras', 19.000, 92.700, 1.800, 0.200, 0.000, 0.030, 0.010, 0.080, 0.000, 0.050, 0.030, 0.000, 0.000, 0.000, 2.100, 3.700, 1.100, 0.000, 1.600, 0.000, 1.600, 213.000, 379.000, 51.000, 0.180, 46.000, 1.800, 81.000, 0.360, 0.400, 14.000, 0.000, 306.000, 0.000, 0.040, 0.090, 0.000, 30.000, 0.000, 0, 1, 0, 1),
(2, 'Acelga hervida escurrida', 1, 'Verduras', 20.000, 92.600, 1.900, 0.100, 0.000, 0.020, 0.010, 0.050, 0.000, 0.030, 0.020, 0.000, 0.000, 0.000, 2.100, 4.200, 0.600, 0.000, 2.100, 0.000, 1.200, 179.000, 549.000, 58.000, 0.150, 33.000, 2.300, 86.000, 0.330, 0.360, 9.000, 0.000, 340.000, 0.000, 0.030, 0.090, 0.000, 18.000, 0.000, 0, 1, 0, 1),
(3, 'Tomate redondo fresco', 1, 'Verduras', 18.000, 94.500, 0.900, 0.200, 0.000, 0.030, 0.030, 0.080, 0.000, 0.080, 0.000, 0.000, 0.000, 0.000, 2.700, 3.900, 2.600, 0.000, 1.200, 0.000, 0.500, 5.000, 237.000, 10.000, 0.060, 24.000, 0.270, 11.000, 0.170, 0.590, 15.000, 0.000, 42.000, 0.000, 0.040, 0.020, 0.000, 14.000, 0.000, 0, 1, 0, 1),
(4, 'Zanahoria fresca', 1, 'Verduras', 41.000, 88.300, 0.900, 0.200, 0.000, 0.040, 0.010, 0.120, 0.000, 0.110, 0.010, 0.000, 0.000, 0.000, 6.800, 9.600, 4.700, 0.000, 2.800, 0.000, 1.000, 69.000, 320.000, 33.000, 0.040, 35.000, 0.300, 12.000, 0.240, 0.980, 19.000, 0.000, 835.000, 0.000, 0.070, 0.060, 0.000, 6.000, 0.000, 0, 1, 0, 1),
(5, 'Calabaza / Zapallo anco cocido', 1, 'Verduras', 26.000, 93.000, 0.900, 0.100, 0.000, 0.020, 0.010, 0.040, 0.000, 0.030, 0.010, 0.000, 0.000, 0.000, 5.400, 6.500, 2.200, 0.000, 1.100, 0.000, 0.800, 3.000, 230.000, 21.000, 0.070, 19.000, 0.600, 14.000, 0.150, 0.500, 16.000, 0.000, 426.000, 0.000, 0.050, 0.080, 0.000, 9.000, 0.000, 0, 1, 0, 1),

-- Grupo 2: Frutas
(6, 'Banana madura fresca', 2, 'Frutas', 89.000, 74.900, 1.100, 0.300, 0.000, 0.110, 0.030, 0.070, 0.000, 0.050, 0.030, 0.000, 0.000, 0.000, 20.200, 22.800, 12.200, 0.000, 2.600, 0.000, 0.800, 1.000, 358.000, 5.000, 0.080, 22.000, 0.260, 27.000, 0.150, 0.660, 20.000, 0.000, 3.000, 0.000, 0.030, 0.070, 0.000, 9.000, 0.000, 0, 1, 0, 1),
(7, 'Manzana con cáscara', 2, 'Frutas', 52.000, 85.600, 0.300, 0.200, 0.000, 0.030, 0.010, 0.050, 0.000, 0.040, 0.010, 0.000, 0.000, 0.000, 11.400, 13.800, 10.400, 0.000, 2.400, 0.000, 0.300, 1.000, 107.000, 6.000, 0.030, 11.000, 0.120, 5.000, 0.040, 0.090, 3.000, 0.000, 3.000, 0.000, 0.020, 0.030, 0.000, 5.000, 0.000, 0, 1, 0, 1),
(8, 'Naranja fresca', 2, 'Frutas', 47.000, 86.800, 0.900, 0.100, 0.000, 0.020, 0.020, 0.030, 0.000, 0.020, 0.010, 0.000, 0.000, 0.000, 9.400, 11.800, 9.400, 0.000, 2.400, 0.000, 0.400, 0.000, 181.000, 40.000, 0.050, 14.000, 0.100, 10.000, 0.070, 0.280, 30.000, 0.000, 11.000, 0.000, 0.090, 0.040, 0.000, 53.000, 0.000, 0, 1, 0, 1),

-- Grupo 3: Legumbres, cereales, papa, choclo, batata, pan y pastas
(9, 'Arroz blanco cocido', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 130.000, 68.400, 2.700, 0.300, 0.000, 0.080, 0.090, 0.080, 0.000, 0.070, 0.010, 0.000, 0.000, 0.000, 28.200, 28.600, 0.100, 0.000, 0.400, 0.000, 0.400, 1.000, 35.000, 10.000, 0.070, 43.000, 0.200, 12.000, 0.490, 1.600, 3.000, 0.000, 0.000, 0.000, 0.020, 0.010, 0.000, 0.000, 0.000, 0, 0, 0, 1),
(10, 'Avena arrollada / en copos', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 389.000, 8.200, 16.900, 6.900, 0.000, 1.200, 2.200, 2.500, 0.000, 2.400, 0.110, 0.000, 0.000, 0.000, 55.700, 66.300, 1.000, 0.000, 10.600, 0.000, 1.700, 2.000, 429.000, 54.000, 0.620, 523.000, 4.700, 177.000, 3.970, 0.960, 56.000, 0.000, 0.000, 0.000, 0.760, 0.140, 0.000, 0.000, 0.000, 0, 0, 0, 1),
(11, 'Papa hervida sin piel', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 87.000, 77.000, 1.900, 0.100, 0.000, 0.030, 0.000, 0.040, 0.000, 0.040, 0.010, 0.000, 0.000, 0.000, 18.300, 20.100, 0.900, 0.000, 1.800, 0.000, 0.900, 5.000, 379.000, 8.000, 0.170, 44.000, 0.310, 20.000, 0.300, 1.310, 10.000, 0.000, 0.000, 0.000, 0.110, 0.020, 0.000, 7.000, 0.000, 0, 1, 0, 1),
(12, 'Lentejas secas cocidas', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 116.000, 69.600, 9.000, 0.400, 0.000, 0.050, 0.070, 0.180, 0.000, 0.150, 0.030, 0.000, 0.000, 0.000, 12.200, 20.100, 1.800, 0.000, 7.900, 0.000, 0.900, 2.000, 369.000, 19.000, 0.250, 180.000, 3.330, 36.000, 1.270, 1.060, 181.000, 0.000, 2.000, 0.000, 0.170, 0.070, 0.000, 1.500, 0.000, 0, 1, 0, 1),
(13, 'Pan francés / común', 3, 'Legumbres, cereales, papa, choclo, batata, pan y pastas', 265.000, 35.700, 9.000, 3.200, 0.000, 0.700, 0.600, 1.400, 0.000, 1.200, 0.070, 0.000, 0.000, 0.000, 46.400, 49.100, 3.600, 0.000, 2.700, 0.000, 2.000, 540.000, 115.000, 26.000, 0.150, 99.000, 3.600, 25.000, 0.740, 4.300, 111.000, 54.000, 0.000, 0.000, 0.470, 0.310, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 4: Leche y postres de leche
(14, 'Leche fluida descremada fortificada', 4, 'Leche y postres de leche', 36.000, 90.800, 3.400, 0.200, 2.000, 0.120, 0.050, 0.010, 0.010, 0.010, 0.000, 0.000, 0.000, 0.000, 4.900, 4.900, 4.900, 0.000, 0.000, 0.000, 0.700, 52.000, 156.000, 120.000, 0.010, 101.000, 0.050, 11.000, 0.420, 0.090, 5.000, 0.000, 60.000, 60.000, 0.040, 0.180, 0.400, 1.000, 1.000, 1, 1, 1, 0),
(15, 'Leche fluida entera', 4, 'Leche y postres de leche', 61.000, 88.100, 3.200, 3.300, 10.000, 1.900, 0.800, 0.120, 0.100, 0.100, 0.020, 0.000, 0.000, 0.000, 4.800, 4.800, 4.800, 0.000, 0.000, 0.000, 0.700, 43.000, 132.000, 113.000, 0.010, 84.000, 0.030, 10.000, 0.370, 0.080, 5.000, 0.000, 46.000, 46.000, 0.050, 0.170, 0.450, 0.000, 1.000, 1, 1, 1, 0),

-- Grupo 5: Yogures
(16, 'Yogur descremado natural/vainilla', 5, 'Yogures', 45.000, 88.000, 4.000, 0.200, 2.000, 0.100, 0.040, 0.010, 0.000, 0.010, 0.000, 0.000, 0.000, 0.000, 6.800, 6.800, 6.800, 0.000, 0.000, 0.000, 0.800, 65.000, 210.000, 140.000, 0.010, 120.000, 0.080, 15.000, 0.600, 0.120, 7.000, 0.000, 50.000, 50.000, 0.050, 0.210, 0.500, 0.500, 0.500, 1, 1, 1, 0),

-- Grupo 6: Quesos
(17, 'Queso Port Salut / Cremoso magro', 6, 'Quesos', 220.000, 55.000, 22.000, 14.000, 45.000, 8.500, 3.800, 0.500, 0.400, 0.400, 0.100, 0.000, 0.000, 0.000, 1.500, 1.500, 1.000, 0.000, 0.000, 0.000, 3.500, 480.000, 95.000, 650.000, 0.030, 420.000, 0.300, 20.000, 3.100, 0.100, 12.000, 0.000, 180.000, 180.000, 0.030, 0.320, 1.400, 0.000, 0.300, 1, 1, 0, 0),
(18, 'Queso untable descremado / blanco', 6, 'Quesos', 85.000, 78.000, 11.000, 2.500, 8.000, 1.500, 0.700, 0.100, 0.050, 0.080, 0.020, 0.000, 0.000, 0.000, 4.000, 4.000, 3.500, 0.000, 0.000, 0.000, 1.500, 350.000, 120.000, 120.000, 0.020, 140.000, 0.100, 11.000, 0.800, 0.100, 8.000, 0.000, 35.000, 35.000, 0.030, 0.180, 0.500, 0.000, 0.100, 1, 1, 0, 0),

-- Grupo 7: Carnes
(19, 'Carne vacuna magra (Lomo/Nalga/Peceto) cocida', 7, 'Carnes', 185.000, 62.000, 28.000, 7.500, 75.000, 2.800, 3.200, 0.400, 0.300, 0.350, 0.050, 0.040, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.200, 60.000, 360.000, 12.000, 0.080, 220.000, 3.400, 22.000, 5.800, 6.200, 8.000, 0.000, 0.000, 0.000, 0.080, 0.220, 2.200, 0.000, 0.100, 1, 1, 0, 0),
(20, 'Pechuga de pollo sin piel cocida/grillada', 7, 'Carnes', 165.000, 65.000, 31.000, 3.600, 85.000, 1.000, 1.300, 0.800, 0.000, 0.700, 0.060, 0.080, 0.010, 0.020, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.100, 74.000, 256.000, 15.000, 0.050, 228.000, 1.040, 29.000, 1.000, 13.700, 4.000, 0.000, 6.000, 6.000, 0.070, 0.120, 0.340, 0.000, 0.100, 1, 1, 0, 0),

-- Grupo 8: Huevos
(21, 'Huevo entero de gallina hervido', 8, 'Huevos', 155.000, 74.600, 12.600, 10.600, 373.000, 3.300, 4.100, 1.400, 0.040, 1.200, 0.040, 0.140, 0.000, 0.030, 0.800, 1.100, 1.100, 0.000, 0.000, 0.000, 0.900, 124.000, 126.000, 50.000, 0.070, 172.000, 1.190, 10.000, 1.050, 0.060, 44.000, 0.000, 140.000, 140.000, 0.070, 0.510, 1.110, 0.000, 2.000, 1, 1, 0, 0),

-- Grupo 9: Pescados y mariscos
(22, 'Merluza filet cocido al horno/vapor', 9, 'Pescados y mariscos', 90.000, 79.500, 19.500, 1.200, 60.000, 0.250, 0.200, 0.450, 0.000, 0.050, 0.040, 0.020, 0.150, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.200, 95.000, 340.000, 35.000, 0.040, 210.000, 0.500, 30.000, 0.650, 2.500, 10.000, 0.000, 25.000, 25.000, 0.060, 0.120, 1.800, 0.000, 1.500, 1, 1, 0, 0),
(23, 'Atún al natural enlatado', 9, 'Pescados y mariscos', 116.000, 73.000, 25.500, 0.800, 35.000, 0.200, 0.150, 0.300, 0.000, 0.030, 0.020, 0.020, 0.080, 0.150, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 1.400, 320.000, 240.000, 12.000, 0.050, 190.000, 1.300, 28.000, 0.800, 10.500, 5.000, 0.000, 18.000, 18.000, 0.030, 0.080, 2.500, 0.000, 1.200, 1, 1, 0, 0),

-- Grupo 10: Aceites
(24, 'Aceite de oliva extra virgen', 10, 'Aceites', 884.000, 0.000, 0.000, 100.000, 0.000, 14.000, 73.000, 10.000, 0.000, 9.200, 0.700, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 2.000, 1.000, 1.000, 0.000, 0.000, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(25, 'Aceite de girasol', 10, 'Aceites', 884.000, 0.000, 0.000, 100.000, 0.000, 11.000, 28.000, 58.000, 0.000, 57.500, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 11: Frutas secas y semillas
(26, 'Nueces peladas', 11, 'Frutas secas y semillas', 654.000, 4.100, 15.200, 65.200, 0.000, 6.100, 8.900, 47.200, 0.000, 38.100, 9.100, 0.000, 0.000, 0.000, 6.700, 13.700, 2.600, 0.000, 6.700, 0.000, 1.800, 2.000, 441.000, 98.000, 1.580, 346.000, 2.910, 158.000, 3.090, 1.120, 98.000, 0.000, 2.000, 0.000, 0.340, 0.150, 0.000, 1.300, 0.000, 0, 0, 0, 0),
(27, 'Semillas de chía', 11, 'Frutas secas y semillas', 486.000, 5.800, 16.500, 30.700, 0.000, 3.300, 2.300, 23.700, 0.000, 5.800, 17.800, 0.000, 0.000, 0.000, 7.700, 42.100, 0.800, 0.000, 34.400, 0.000, 4.900, 16.000, 407.000, 631.000, 0.920, 860.000, 7.720, 335.000, 4.580, 8.830, 49.000, 0.000, 5.000, 0.000, 0.620, 0.170, 0.000, 1.600, 0.000, 0, 0, 0, 0),

-- Grupo 12: Azúcares, mermeladas y dulces
(28, 'Azúcar blanco común', 12, 'Azúcares, mermeladas y dulces', 399.000, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 99.800, 99.800, 99.800, 99.800, 0.000, 0.000, 0.000, 1.000, 2.000, 1.000, 0.000, 0.000, 0.050, 0.000, 0.010, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(29, 'Dulce de leche común', 12, 'Azúcares, mermeladas y dulces', 315.000, 29.000, 6.500, 6.000, 22.000, 3.600, 1.600, 0.200, 0.100, 0.150, 0.020, 0.000, 0.000, 0.000, 58.000, 58.000, 55.000, 40.000, 0.000, 0.000, 1.500, 130.000, 280.000, 230.000, 0.020, 190.000, 0.200, 20.000, 0.800, 0.200, 10.000, 0.000, 55.000, 55.000, 0.050, 0.250, 0.600, 0.000, 0.100, 0, 0, 0, 0),

-- Grupo 13: Golosinas y chocolates
(30, 'Chocolate semiamargo (60% cacao)', 13, 'Golosinas y chocolates', 546.000, 1.200, 5.500, 31.000, 3.000, 18.500, 10.000, 1.100, 0.000, 0.900, 0.050, 0.000, 0.000, 0.000, 54.000, 61.000, 47.000, 46.000, 7.000, 0.000, 1.500, 20.000, 500.000, 56.000, 1.000, 180.000, 8.000, 140.000, 2.300, 1.050, 10.000, 0.000, 2.000, 0.000, 0.030, 0.080, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 14: Grasas
(31, 'Manteca vacuna', 14, 'Grasas', 717.000, 16.000, 0.800, 81.100, 215.000, 51.400, 21.000, 3.000, 3.200, 2.200, 0.300, 0.000, 0.000, 0.000, 0.100, 0.100, 0.100, 0.000, 0.000, 0.000, 2.100, 580.000, 24.000, 24.000, 0.010, 24.000, 0.020, 2.000, 0.090, 0.040, 3.000, 0.000, 684.000, 671.000, 0.010, 0.030, 0.170, 0.000, 1.500, 0, 0, 0, 0),

-- Grupo 15: Snacks salados
(32, 'Papas fritas en paquete / snack', 15, 'Snacks salados', 536.000, 2.000, 7.000, 35.000, 0.000, 4.500, 16.500, 12.000, 0.200, 11.200, 0.500, 0.000, 0.000, 0.000, 49.000, 53.000, 0.500, 0.000, 4.000, 0.000, 3.000, 520.000, 1200.000, 25.000, 0.250, 160.000, 1.600, 65.000, 1.100, 4.200, 45.000, 0.000, 0.000, 0.000, 0.200, 0.100, 0.000, 18.000, 0.000, 0, 0, 0, 0),

-- Grupo 16: Aderezos
(33, 'Mayonesa común industrial', 16, 'Aderezos', 680.000, 21.000, 1.000, 75.000, 42.000, 11.500, 18.000, 42.000, 0.300, 38.000, 3.500, 0.050, 0.000, 0.000, 2.500, 2.500, 1.500, 1.000, 0.000, 0.000, 1.500, 590.000, 30.000, 12.000, 0.020, 28.000, 0.200, 4.000, 0.200, 0.100, 5.000, 0.000, 25.000, 25.000, 0.010, 0.020, 0.100, 0.000, 0.200, 0, 0, 0, 0),

-- Grupo 17: Caldos y sopas industriales
(34, 'Caldo de verduras en cubo', 17, 'Caldos y sopas industriales', 180.000, 3.000, 8.000, 5.000, 1.000, 2.500, 1.500, 0.500, 0.100, 0.400, 0.050, 0.000, 0.000, 0.000, 25.000, 25.000, 5.000, 4.000, 1.000, 0.000, 58.000, 22000.000, 350.000, 50.000, 0.100, 120.000, 1.200, 30.000, 0.800, 1.500, 15.000, 0.000, 10.000, 0.000, 0.100, 0.100, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 18: Postres industriales y helados
(35, 'Helado de crema y dulce de leche', 18, 'Postres industriales y helados', 222.000, 60.000, 3.800, 11.500, 35.000, 7.000, 3.200, 0.500, 0.300, 0.400, 0.050, 0.000, 0.000, 0.000, 26.000, 26.000, 24.000, 20.000, 0.200, 0.000, 0.900, 80.000, 180.000, 130.000, 0.030, 110.000, 0.200, 15.000, 0.700, 0.150, 10.000, 0.000, 85.000, 85.000, 0.040, 0.200, 0.400, 0.500, 0.200, 0, 0, 0, 0),

-- Grupo 19: Sales
(36, 'Sal de mesa fina yodada', 19, 'Sales', 0.000, 0.200, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 99.800, 38758.000, 8.000, 24.000, 0.030, 0.000, 0.330, 1.000, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 20: Bebidas con azúcar
(37, 'Gaseosa cola regular', 20, 'Bebidas con azúcar', 42.000, 89.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 10.600, 10.600, 10.600, 10.600, 0.000, 0.000, 0.100, 11.000, 2.000, 2.000, 0.000, 13.000, 0.050, 1.000, 0.050, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 21: Bebidas sin azúcar
(38, 'Gaseosa cola light / zero', 21, 'Bebidas sin azúcar', 0.500, 99.400, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.100, 15.000, 5.000, 3.000, 0.000, 15.000, 0.020, 1.000, 0.020, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 22: Bebidas alcohólicas y energizantes
(39, 'Vino tinto común', 22, 'Bebidas alcohólicas y energizantes', 85.000, 86.500, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 2.600, 2.600, 0.600, 0.000, 0.000, 10.500, 0.300, 4.000, 127.000, 8.000, 0.010, 23.000, 0.460, 12.000, 0.140, 0.220, 1.000, 0.000, 0.000, 0.000, 0.000, 0.030, 0.000, 0.000, 0.000, 0, 0, 0, 0),
(40, 'Cerveza rubia común', 22, 'Bebidas alcohólicas y energizantes', 43.000, 92.000, 0.500, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 3.600, 3.600, 0.000, 0.000, 0.000, 3.900, 0.200, 4.000, 27.000, 4.000, 0.010, 14.000, 0.020, 6.000, 0.010, 0.510, 6.000, 0.000, 0.000, 0.000, 0.010, 0.030, 0.020, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 23: Bebidas de frutas naturales o mínimamente procesadas sin azúcar agregada
(41, 'Jugo de naranja exprimido natural', 23, 'Bebidas de frutas naturales o mínimamente procesadas sin azúcar agregada', 45.000, 88.300, 0.700, 0.200, 0.000, 0.030, 0.040, 0.050, 0.000, 0.040, 0.010, 0.000, 0.000, 0.000, 10.200, 10.400, 8.400, 0.000, 0.200, 0.000, 0.400, 1.000, 200.000, 11.000, 0.040, 17.000, 0.200, 11.000, 0.050, 0.400, 30.000, 0.000, 10.000, 0.000, 0.090, 0.030, 0.000, 50.000, 0.000, 0, 1, 0, 1),

-- Grupo 24: Infusiones
(42, 'Yerba mate cebada / mate cocido infusión', 24, 'Infusiones', 2.000, 99.200, 0.300, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.300, 0.300, 0.000, 0.000, 0.000, 0.000, 0.200, 3.000, 55.000, 12.000, 0.020, 8.000, 0.600, 15.000, 0.100, 0.800, 5.000, 0.000, 0.000, 0.000, 0.020, 0.040, 0.000, 1.500, 0.000, 0, 0, 0, 0),
(43, 'Café infusión sin azúcar', 24, 'Infusiones', 1.000, 99.400, 0.100, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 0.200, 0.200, 0.000, 0.000, 0.000, 0.000, 0.200, 2.000, 49.000, 2.000, 0.010, 3.000, 0.010, 3.000, 0.020, 0.190, 2.000, 0.000, 0.000, 0.000, 0.010, 0.080, 0.000, 0.000, 0.000, 0, 0, 0, 0),

-- Grupo 25: Alimentos de locales de comidas rápidas
(44, 'Hamburguesa completa con queso en pan', 25, 'Alimentos de locales de comidas rápidas', 275.000, 48.000, 14.500, 13.000, 40.000, 5.200, 5.100, 1.800, 0.500, 1.600, 0.200, 0.040, 0.000, 0.000, 25.000, 26.500, 4.500, 3.000, 1.500, 0.000, 2.200, 620.000, 230.000, 110.000, 0.120, 180.000, 2.200, 22.000, 2.400, 3.800, 38.000, 12.000, 45.000, 45.000, 0.180, 0.200, 0.900, 1.000, 0.200, 0, 0, 0, 0),

-- Grupo 26: Suplementos nutricionales
(45, 'Suplemento Proteico Whey Protein 80%', 26, 'Suplementos nutricionales', 390.000, 5.000, 80.000, 4.000, 50.000, 2.200, 1.100, 0.400, 0.000, 0.300, 0.050, 0.000, 0.000, 0.000, 6.000, 6.000, 3.000, 0.000, 0.000, 0.000, 4.000, 180.000, 520.000, 450.000, 0.080, 380.000, 0.900, 65.000, 1.200, 1.500, 25.000, 0.000, 40.000, 40.000, 0.200, 0.650, 2.000, 2.000, 0.500, 1, 1, 0, 0)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

SET FOREIGN_KEY_CHECKS = 1;
