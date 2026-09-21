-- Migración para el Calendario Multivista y Gestión Avanzada de Turnos
-- NutriSalud SaaS

ALTER TABLE `turno`
    ADD COLUMN IF NOT EXISTS `Modalidad` ENUM('Presencial', 'Online') NOT NULL DEFAULT 'Presencial' AFTER `Estado_Turno`,
    ADD COLUMN IF NOT EXISTS `Motivo_Consulta` VARCHAR(255) DEFAULT 'Consulta Nutricional' AFTER `Modalidad`,
    ADD COLUMN IF NOT EXISTS `Link_Reunion` VARCHAR(255) DEFAULT NULL AFTER `Motivo_Consulta`,
    ADD COLUMN IF NOT EXISTS `Direccion` VARCHAR(255) DEFAULT NULL AFTER `Link_Reunion`,
    ADD COLUMN IF NOT EXISTS `Notas` TEXT DEFAULT NULL AFTER `Direccion`;

-- Índices para optimizar la carga del calendario por rango de fechas y estado
CREATE INDEX IF NOT EXISTS `idx_turno_calendario` ON `turno` (`IdNutri`, `Fecha`, `Hora`, `Estado_Turno`);
