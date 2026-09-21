-- ========================================================================
-- NUTRISALUD SaaS - MIGRACIÓN: EVALUACIÓN DE RIESGO CARDIOVASCULAR A 10 AÑOS (HEARTS / OMS)
-- Compatibilidad: MySQL 8.0+ / MariaDB 10.5+
-- ========================================================================

CREATE TABLE IF NOT EXISTS `evaluacion_riesgo_cv` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_paciente` INT NOT NULL,
  `id_nutri` INT NOT NULL,
  `fecha_evaluacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `antecedente_ecv` TINYINT(1) DEFAULT 0,
  `antecedente_erc` TINYINT(1) DEFAULT 0,
  `diabetes` TINYINT(1) DEFAULT 0,
  `tabaquismo` TINYINT(1) DEFAULT 0,
  `edad` INT NOT NULL,
  `sexo` ENUM('M', 'F') NOT NULL,
  `presion_sistolica` INT NOT NULL,
  `con_colesterol` TINYINT(1) DEFAULT 0,
  `colesterol_total` DECIMAL(5,2) NULL,
  `peso` DECIMAL(5,2) NULL,
  `altura` DECIMAL(5,2) NULL,
  `imc` DECIMAL(4,1) NULL,
  `porcentaje_riesgo` VARCHAR(10) NOT NULL,
  `categoria_riesgo` ENUM('Bajo', 'Moderado', 'Alto', 'Muy Alto', 'Critico') NOT NULL,
  `recomendacion_terapeutica` TEXT NULL,
  INDEX (`id_paciente`),
  INDEX (`id_nutri`),
  CONSTRAINT `fk_eval_riesgocv_paciente` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`IdPaciente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_eval_riesgocv_nutri` FOREIGN KEY (`id_nutri`) REFERENCES `nutricionista` (`IdNutri`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
