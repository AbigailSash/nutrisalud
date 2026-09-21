<?php
// database/check_and_migrate_prod.php
// Herramienta de Comprobación y Migración Automatizada Idempotente para Producción
// Puede ejecutarse desde CLI: php database/check_and_migrate_prod.php
// O mediante navegador con protección de clave o modo local

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Conexion.php';

$is_cli = php_sapi_name() === 'cli';
$output_lines = [];

function emit($msg, $type = 'info') {
    global $is_cli, $output_lines;
    $prefix = $type === 'success' ? '✅ ' : ($type === 'error' ? '❌ ' : ($type === 'warning' ? '⚠️ ' : 'ℹ️ '));
    $line = $prefix . $msg;
    $output_lines[] = ['text' => $msg, 'type' => $type];
    if ($is_cli) {
        echo $line . PHP_EOL;
    }
}

try {
    $db = Conexion::conectar();
    emit("Conexión a base de datos establecida con éxito (" . DB_HOST . " / " . DB_NAME . ")", 'success');
} catch (Exception $e) {
    emit("Fallo crítico de conexión: " . $e->getMessage(), 'error');
    if ($is_cli) exit(1);
}

// 1. Tablas Requeridas y Creación Automática si no existen
$tablasRequeridas = [
    'nutricionista' => "CREATE TABLE IF NOT EXISTS `nutricionista` (
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
        KEY `idx_nutri_estado` (`Estado_Cuenta`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'paciente' => "CREATE TABLE IF NOT EXISTS `paciente` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'turno' => "CREATE TABLE IF NOT EXISTS `turno` (
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
        CONSTRAINT `fk_turno_paciente` FOREIGN KEY (`IdPaciente`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'historia_clinica' => "CREATE TABLE IF NOT EXISTS `historia_clinica` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'historia_clinica_campos_custom' => "CREATE TABLE IF NOT EXISTS `historia_clinica_campos_custom` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `paciente_id` INT(11) NOT NULL,
        `seccion` VARCHAR(50) NOT NULL,
        `titulo` VARCHAR(255) NOT NULL,
        `contenido` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_custom_paciente` (`paciente_id`),
        CONSTRAINT `fk_custom_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`IdPaciente`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'dia_semana' => "CREATE TABLE IF NOT EXISTS `dia_semana` (
        `IdDia` INT(11) NOT NULL,
        `Nombre_Dia` VARCHAR(10) NOT NULL,
        PRIMARY KEY (`IdDia`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'momento_dia' => "CREATE TABLE IF NOT EXISTS `momento_dia` (
        `IdMomento` INT(11) NOT NULL,
        `Nombre_Momento` VARCHAR(30) NOT NULL,
        PRIMARY KEY (`IdMomento`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'alimento' => "CREATE TABLE IF NOT EXISTS `alimento` (
        `IdAlimento` INT(11) NOT NULL AUTO_INCREMENT,
        `Nombre_Alimento` VARCHAR(150) NOT NULL,
        `Calorias_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
        `Proteinas_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
        `Carbohidratos_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
        `Grasas_100g` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
        PRIMARY KEY (`IdAlimento`),
        KEY `idx_alimento_nombre` (`Nombre_Alimento`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'plan_alimentario' => "CREATE TABLE IF NOT EXISTS `plan_alimentario` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'detalle_plan_alimento' => "CREATE TABLE IF NOT EXISTS `detalle_plan_alimento` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'informe_educativo' => "CREATE TABLE IF NOT EXISTS `informe_educativo` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'plan_informe_educativo' => "CREATE TABLE IF NOT EXISTS `plan_informe_educativo` (
        `IdPlan` INT(11) NOT NULL,
        `IdInforme` INT(11) NOT NULL,
        PRIMARY KEY (`IdPlan`, `IdInforme`),
        KEY `fk_pie_informe` (`IdInforme`),
        CONSTRAINT `fk_pie_plan` FOREIGN KEY (`IdPlan`) REFERENCES `plan_alimentario` (`IdPlan`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `fk_pie_informe` FOREIGN KEY (`IdInforme`) REFERENCES `informe_educativo` (`IdInforme`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'pago' => "CREATE TABLE IF NOT EXISTS `pago` (
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
        CONSTRAINT `fk_pago_nutri` FOREIGN KEY (`IdNutri`) REFERENCES `nutricionista` (`IdNutri`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'password_resets' => "CREATE TABLE IF NOT EXISTS `password_resets` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'sara2_alimentos' => "CREATE TABLE IF NOT EXISTS `sara2_alimentos` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nombre` VARCHAR(255) NOT NULL,
        `grupo_id` TINYINT(3) UNSIGNED NOT NULL,
        `grupo_nombre` VARCHAR(150) NOT NULL,
        `medida_casera_ref` VARCHAR(100) NULL,
        `energia_kcal` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `agua_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `proteina_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `lipidos_totales_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `colesterol_mg` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ag_sat_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ag_mono_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ag_poli_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ag_trans_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ac_linoleico_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ac_linolenico_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ac_araquidonico_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ac_epa_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
        `ac_dha_g` DECIMAL(9,3) NOT NULL DEFAULT 0.000,
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
        `es_avb` TINYINT(1) NOT NULL DEFAULT 0,
        `es_protector` TINYINT(1) NOT NULL DEFAULT 0,
        `es_leche` TINYINT(1) NOT NULL DEFAULT 0,
        `es_hc_complejo` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_sara2_nombre` (`nombre`),
        KEY `idx_sara2_grupo` (`grupo_id`),
        KEY `idx_sara2_flags` (`es_avb`, `es_protector`, `es_leche`, `es_hc_complejo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'formula_desarrollada_cabecera' => "CREATE TABLE IF NOT EXISTS `formula_desarrollada_cabecera` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'formula_desarrollada_detalle' => "CREATE TABLE IF NOT EXISTS `formula_desarrollada_detalle` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `id_formula` INT(11) NOT NULL,
        `id_alimento` INT(11) NOT NULL,
        `gramos` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
        PRIMARY KEY (`id`),
        KEY `fk_fdetalle_formula` (`id_formula`),
        KEY `fk_fdetalle_sara2` (`id_alimento`),
        CONSTRAINT `fk_fdetalle_formula` FOREIGN KEY (`id_formula`) REFERENCES `formula_desarrollada_cabecera` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `fk_fdetalle_sara2` FOREIGN KEY (`id_alimento`) REFERENCES `sara2_alimentos` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    'evaluacion_riesgo_cv' => "CREATE TABLE IF NOT EXISTS `evaluacion_riesgo_cv` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($tablasRequeridas as $nombreTabla => $ddl) {
    try {
        $db->exec($ddl);
        emit("Tabla `$nombreTabla`: verificada / lista", 'success');
    } catch (Exception $e) {
        emit("Error al verificar tabla `$nombreTabla`: " . $e->getMessage(), 'error');
    }
}

// 2. Comprobar columnas adicionales en `turno`
$columnasTurno = [
    'Modalidad'       => "ALTER TABLE `turno` ADD COLUMN `Modalidad` ENUM('Presencial', 'Online') NOT NULL DEFAULT 'Presencial' AFTER `Estado_Turno`",
    'Motivo_Consulta' => "ALTER TABLE `turno` ADD COLUMN `Motivo_Consulta` VARCHAR(255) DEFAULT 'Consulta Nutricional' AFTER `Modalidad`",
    'Link_Reunion'    => "ALTER TABLE `turno` ADD COLUMN `Link_Reunion` VARCHAR(255) DEFAULT NULL AFTER `Motivo_Consulta`",
    'Direccion'       => "ALTER TABLE `turno` ADD COLUMN `Direccion` VARCHAR(255) DEFAULT NULL AFTER `Link_Reunion`",
    'Notas'           => "ALTER TABLE `turno` ADD COLUMN `Notas` TEXT DEFAULT NULL AFTER `Direccion`"
];

$stmtCols = $db->query("SHOW COLUMNS FROM `turno`");
$colsExistentes = $stmtCols->fetchAll(PDO::FETCH_COLUMN, 0);

foreach ($columnasTurno as $col => $alterSql) {
    if (!in_array($col, $colsExistentes)) {
        try {
            $db->exec($alterSql);
            emit("Columna `turno`.`$col` añadida exitosamente", 'success');
        } catch (Exception $e) {
            emit("Error al añadir columna `turno`.`$col`: " . $e->getMessage(), 'error');
        }
    } else {
        emit("Columna `turno`.`$col`: presente", 'success');
    }
}

// 3. Comprobar índice idx_turno_calendario
try {
    $stmtIdx = $db->query("SHOW INDEX FROM `turno` WHERE Key_name = 'idx_turno_calendario'");
    if ($stmtIdx->rowCount() === 0) {
        $db->exec("CREATE INDEX `idx_turno_calendario` ON `turno` (`IdNutri`, `Fecha`, `Hora`, `Estado_Turno`)");
        emit("Índice `idx_turno_calendario` creado en tabla `turno`", 'success');
    } else {
        emit("Índice `idx_turno_calendario`: activo", 'success');
    }
} catch (Exception $e) {
    emit("Aviso índice `idx_turno_calendario`: " . $e->getMessage(), 'warning');
}

// 4. Catálogos base: dia_semana y momento_dia
try {
    $countDias = $db->query("SELECT COUNT(*) FROM dia_semana")->fetchColumn();
    if ($countDias == 0) {
        $db->exec("INSERT INTO `dia_semana` (`IdDia`, `Nombre_Dia`) VALUES
            (1, 'Lunes'), (2, 'Martes'), (3, 'Miercoles'), (4, 'Jueves'), (5, 'Viernes'), (6, 'Sabado'), (7, 'Domingo')");
        emit("Semillas de `dia_semana` insertadas", 'success');
    }
    
    $countMomentos = $db->query("SELECT COUNT(*) FROM momento_dia")->fetchColumn();
    if ($countMomentos == 0) {
        $db->exec("INSERT INTO `momento_dia` (`IdMomento`, `Nombre_Momento`) VALUES
            (1, 'Desayuno'), (2, 'Media Mañana'), (3, 'Almuerzo'), (4, 'Merienda'), (5, 'Cena'), (6, 'Colación Nocturna')");
        emit("Semillas de `momento_dia` insertadas", 'success');
    }
} catch (Exception $e) {
    emit("Aviso catálogos base: " . $e->getMessage(), 'warning');
}

// 5. Catálogo SARA 2: Verificar si contiene alimentos cargados
try {
    $countSara = $db->query("SELECT COUNT(*) FROM sara2_alimentos")->fetchColumn();
    emit("Catálogo SARA 2 cuenta con $countSara alimentos cargados", $countSara > 0 ? 'success' : 'warning');
} catch (Exception $e) {
    emit("Error al verificar SARA 2: " . $e->getMessage(), 'error');
}

// 6. Vista del Menú del Paciente
try {
    $db->exec("CREATE OR REPLACE VIEW `vista_menu_paciente` AS
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
        JOIN momento_dia m ON dp.IdMomento = m.IdMomento;");
    emit("Vista `vista_menu_paciente`: actualizada y lista", 'success');
} catch (Exception $e) {
    emit("Error en vista `vista_menu_paciente`: " . $e->getMessage(), 'error');
}

// 7. Verificar y Asegurar Usuario SuperAdmin Maestro
try {
    $stmtAdmin = $db->query("SELECT * FROM `nutricionista` WHERE `Rol` = 'admin' OR `Email` = 'admin@nutrisalud.com' LIMIT 1");
    $adminUser = $stmtAdmin->fetch();
    $adminHash = '$2y$10$K9aKUirpqzSlvEArPTM0fOTrCFVJVRoa.alhmDBG0wp3PwCIhm0OG'; // admin123
    
    if (!$adminUser) {
        $stmtInsert = $db->prepare("INSERT INTO `nutricionista` 
            (`DNI`, `Matricula`, `Nombre`, `Apellido`, `Email`, `Password_Hash`, `Rol`, `Telefono`, `Estado_Cuenta`, `Especialidad`, `Direccion`, `Biografia`)
            VALUES ('00000000', 'MN-ADMIN-01', 'Administrador', 'NutriSalud', 'admin@nutrisalud.com', :hash, 'admin', '1100000000', 'A', 'Administrador de Plataforma', 'Sede Central NutriSalud', 'Cuenta maestra de administración del sistema NutriSalud SaaS')");
        $stmtInsert->execute([':hash' => $adminHash]);
        emit("Usuario SuperAdmin creado con éxito (Usuario: admin@nutrisalud.com o 'admin' | Clave: admin123)", 'success');
    } else {
        // Asegurar que tenga Rol='admin' y Estado_Cuenta='A'
        if ($adminUser['Rol'] !== 'admin' || $adminUser['Estado_Cuenta'] !== 'A') {
            $db->exec("UPDATE `nutricionista` SET `Rol` = 'admin', `Estado_Cuenta` = 'A' WHERE `IdNutri` = " . intval($adminUser['IdNutri']));
            emit("Cuenta de Administrador actualizada a Rol='admin' y Estado='A'", 'success');
        }
        emit("Usuario SuperAdmin activo: " . htmlspecialchars($adminUser['Email']) . " (Rol: " . htmlspecialchars($adminUser['Rol']) . ")", 'success');
    }
} catch (Exception $e) {
    emit("Aviso usuario SuperAdmin: " . $e->getMessage(), 'warning');
}

if (!$is_cli): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico y Migración de Base de Datos - NutriSalud</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #0f172a; padding: 40px 20px; }
        .container { max-width: 750px; margin: 0 auto; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 30px 35px; border-top: 5px solid #2ecc71; }
        h1 { color: #1e293b; margin-top: 0; font-size: 22px; }
        .log-item { padding: 8px 12px; margin-bottom: 6px; border-radius: 8px; font-size: 14px; font-family: monospace; }
        .log-success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .log-error { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }
        .log-warning { background: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b; }
        .log-info { background: #f0f9ff; color: #075985; border-left: 4px solid #0ea5e9; }
        .footer { margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌱 Diagnóstico y Migración de Base de Datos NutriSalud</h1>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Resultado de la verificación de tablas, columnas, índices y catálogos en el entorno actual:</p>
        <?php foreach ($output_lines as $l): ?>
            <div class="log-item log-<?= $l['type'] ?>">
                <?= htmlspecialchars($l['text']) ?>
            </div>
        <?php endforeach; ?>
        <div class="footer">
            Entorno: <strong><?= defined('DB_HOST') ? DB_HOST : 'N/D' ?></strong> | Base de Datos: <strong><?= defined('DB_NAME') ? DB_NAME : 'N/D' ?></strong>
        </div>
    </div>
</body>
</html>
<?php endif; ?>
