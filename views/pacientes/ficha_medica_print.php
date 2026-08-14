<?php
// Helper to print value or '--'
function getValPrint($array, $key, $default = '--') {
    return isset($array[$key]) && trim($array[$key]) !== '' ? htmlspecialchars($array[$key]) : $default;
}

$edad = '--';
if (isset($paciente['Fecha_Nacimiento'])) {
    $birth = new DateTime($paciente['Fecha_Nacimiento']);
    $now = new DateTime();
    $edad = $now->diff($birth)->y;
}
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Médica - <?= htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']) ?></title>
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 12%, white);
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: #f4f6f9;
        }
        .page {
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid var(--primary-green);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .section-title {
            background: var(--light-green);
            color: var(--dark-green);
            padding: 8px 12px;
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0 15px;
            border-left: 4px solid var(--primary-green);
            border-radius: 4px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 15px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 15px;
        }
        .field {
            margin-bottom: 8px;
        }
        .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #95a5a6;
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .value {
            font-size: 14px;
            font-weight: 500;
        }
        .text-block {
            font-size: 14px;
            background: #fdfefe;
            border: 1px solid #ecf0f1;
            padding: 10px;
            border-radius: 4px;
            white-space: pre-wrap;
            min-height: 40px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #bdc3c7;
            border-top: 1px solid #ecf0f1;
            padding-top: 10px;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
        .btn-print {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            opacity: 0.95;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print no-print">Imprimir Ficha</button>

    <div class="page">
        <div class="header">
            <h1>Ficha Médica Nutricional</h1>
            <p>Reporte Confidencial del Paciente</p>
        </div>

        <!-- 1. Datos Personales -->
        <div class="section-title">Datos Personales</div>
        <div class="grid-3">
            <div class="field">
                <span class="label">Paciente</span>
                <span class="value"><?= htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']) ?></span>
            </div>
            <div class="field">
                <span class="label">DNI</span>
                <span class="value"><?= getValPrint($paciente, 'DNI') ?></span>
            </div>
            <div class="field">
                <span class="label">Edad / Sexo</span>
                <span class="value"><?= $edad ?> años / <?= htmlspecialchars($paciente['Sexo'] ?? 'N/A') ?></span>
            </div>
            <div class="field">
                <span class="label">Teléfono</span>
                <span class="value"><?= getValPrint($paciente, 'Telefono') ?></span>
            </div>
            <div class="field">
                <span class="label">Email</span>
                <span class="value"><?= getValPrint($paciente, 'Email') ?></span>
            </div>
            <div class="field">
                <span class="label">Obra Social</span>
                <span class="value"><?= getValPrint($paciente, 'Obra_Social', 'Particular') ?></span>
            </div>
        </div>

        <!-- 2. Motivo de Consulta -->
        <div class="section-title">Motivo de Consulta</div>
        <div class="text-block"><?= getValPrint($datosHistoria, 'dat_motivo') ?></div>

        <!-- 3. Antecedentes -->
        <div class="section-title">Antecedentes Médicos</div>
        <div class="grid-2">
            <div class="field">
                <span class="label">Patologías Previas</span>
                <span class="value"><?= getValPrint($datosHistoria, 'ant_patologias') ?></span>
            </div>
            <div class="field">
                <span class="label">Alergias / Intolerancias</span>
                <span class="value"><?= getValPrint($datosHistoria, 'ant_alergias') ?></span>
            </div>
            <div class="field">
                <span class="label">Medicamentos</span>
                <span class="value"><?= getValPrint($datosHistoria, 'ant_medicamentos') ?></span>
            </div>
            <div class="field">
                <span class="label">Antecedentes Familiares</span>
                <span class="value"><?= getValPrint($datosHistoria, 'ant_familiares') ?></span>
            </div>
        </div>

        <!-- 4. Antropometría -->
        <div class="section-title">Antropometría y Composición Corporal</div>
        <div class="grid-3">
            <div class="field">
                <span class="label">Peso Actual</span>
                <span class="value"><?= getValPrint($datosHistoria, 'peso_actual', getValPrint($datosHistoria, 'antrop_peso_actual')) ?> kg</span>
            </div>
            <div class="field">
                <span class="label">Talla</span>
                <span class="value"><?= getValPrint($datosHistoria, 'talla', getValPrint($datosHistoria, 'antrop_talla')) ?> m</span>
            </div>
            <div class="field">
                <span class="label">IMC / Diagnóstico</span>
                <span class="value"><?= getValPrint($datosHistoria, 'imc') ?> (<?= getValPrint($datosHistoria, 'imc_diagnostico', 'N/A') ?>)</span>
            </div>
            <div class="field">
                <span class="label">Circ. Cintura</span>
                <span class="value"><?= getValPrint($datosHistoria, 'circ_cintura') ?> cm</span>
            </div>
            <div class="field">
                <span class="label">Circ. Cadera</span>
                <span class="value"><?= getValPrint($datosHistoria, 'circ_cadera') ?> cm</span>
            </div>
            <div class="field">
                <span class="label">Relación Cintura/Cadera (ICC)</span>
                <span class="value"><?= getValPrint($datosHistoria, 'relacion_cc') ?> (<?= getValPrint($datosHistoria, 'relacion_cc_diagnostico', 'N/A') ?>)</span>
            </div>
        </div>

        <!-- 5. Hábitos -->
        <div class="section-title">Hábitos (Digestión y Actividad)</div>
        <div class="grid-2">
            <div class="field">
                <span class="label">Ritmo Evacuatorio</span>
                <span class="value"><?= getValPrint($datosHistoria, 'hab_evacuatorio') ?></span>
            </div>
            <div class="field">
                <span class="label">Actividad Física</span>
                <span class="value"><?= getValPrint($datosHistoria, 'hab_actividad') ?></span>
            </div>
        </div>

        <!-- 6. Observaciones Generales -->
        <div class="section-title">Observaciones Clínicas (Seguimiento)</div>
        <div class="text-block"><?= getValPrint($datosHistoria, 'seg_observaciones') ?></div>

        <div class="footer">
            Generado por NutriSalud - <?= date('d/m/Y H:i') ?>
        </div>
    </div>

</body>
</html>
