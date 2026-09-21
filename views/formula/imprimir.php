<?php
// views/formula/imprimir.php
// Plantilla de impresión institucional A4 miembro para Fórmula Desarrollada SARA 2 / ENNyS 2.

$colorTema = $nutri['Color_Tema'] ?? ($_SESSION['ColorTema'] ?? '#2ecc71');

// Helper para cálculo de medida casera proporcional
function calcularMedidaCaseraImpresion($medidaRef, $gramos) {
    if (empty($medidaRef)) return '';
    if (preg_match('/\((\d+(?:\.\d+)?)\s*(?:g|ml)\)/i', $medidaRef, $matches)) {
        $refGramos = (float)$matches[1];
        if ($refGramos > 0) {
            $ratio = $gramos / $refGramos;
            $nombreMedida = trim(preg_replace('/\s*\(\d+(?:\.\d+)?\s*(?:g|ml)\)/i', '', $medidaRef));
            if (abs($ratio - 1.0) < 0.05) {
                return $nombreMedida;
            } elseif (abs($ratio - 0.5) < 0.05) {
                return '1/2 ' . $nombreMedida;
            } elseif (abs($ratio - 0.25) < 0.05) {
                return '1/4 ' . $nombreMedida;
            } elseif (abs($ratio - 0.75) < 0.05) {
                return '3/4 ' . $nombreMedida;
            } elseif (abs($ratio - 1.5) < 0.05) {
                return '1 1/2 ' . $nombreMedida;
            } elseif (abs($ratio - 2.0) < 0.05) {
                return '2 ' . $nombreMedida;
            } else {
                return number_format($ratio, 1) . ' x ' . $nombreMedida;
            }
        }
    }
    return $medidaRef;
}

// Calcular edad del paciente
$edad = '--';
if (!empty($formula['Fecha_Nacimiento'])) {
    $birth = new DateTime($formula['Fecha_Nacimiento']);
    $now = new DateTime();
    $edad = $now->diff($birth)->y . ' años';
}

$pesoPaciente = !empty($formula['PacientePeso']) ? (float)$formula['PacientePeso'] : 0;
$kcalObjetivo = !empty($formula['kcal_objetivo']) ? (float)$formula['kcal_objetivo'] : 0;

// Variables acumuladoras
$totalGramos = 0;
$totalKcal = 0;
$totalAgua = 0;
$totalProt = 0;
$totalLip = 0;
$totalColest = 0;
$totalAgSat = 0;
$totalAgMono = 0;
$totalAgPoli = 0;
$totalHcDisp = 0;
$totalHcTot = 0;
$totalAzucarAgr = 0;
$totalFibra = 0;
$totalAlc = 0;
$totalNa = 0;
$totalK = 0;
$totalCa = 0;
$totalP = 0;
$totalFe = 0;
$totalMg = 0;
$totalZn = 0;
$totalVitARae = 0;
$totalVitC = 0;

$sumHcComplejos = 0;
$sumKcalProtectores = 0;
$sumProtAVB = 0;
$sumKcalLeche = 0;

$detalles = $formula['detalles'] ?? [];

foreach ($detalles as $item) {
    $gramos = (float)$item['gramos'];
    $factor = $gramos / 100;

    $hcDisp = (float)($item['hc_disponibles_g'] ?? 0) * $factor;
    $prot = (float)($item['proteina_g'] ?? 0) * $factor;
    $lip = (float)($item['lipidos_totales_g'] ?? 0) * $factor;
    $alc = (float)($item['alcohol_g'] ?? 0) * $factor;

    // Fórmula Atwater SARA 2
    $kcal = ($hcDisp * 4) + ($prot * 4) + ($lip * 9) + ($alc * 7);

    $totalGramos += $gramos;
    $totalKcal += $kcal;
    $totalAgua += (float)($item['agua_g'] ?? 0) * $factor;
    $totalProt += $prot;
    $totalLip += $lip;
    $totalColest += (float)($item['colesterol_mg'] ?? 0) * $factor;
    $totalAgSat += (float)($item['ag_sat_g'] ?? 0) * $factor;
    $totalAgMono += (float)($item['ag_mono_g'] ?? 0) * $factor;
    $totalAgPoli += (float)($item['ag_poli_g'] ?? 0) * $factor;
    $totalHcDisp += $hcDisp;
    $totalHcTot += (float)($item['hc_totales_g'] ?? 0) * $factor;
    $totalAzucarAgr += (float)($item['azucar_agregado_g'] ?? 0) * $factor;
    $totalFibra += (float)($item['fibra_g'] ?? 0) * $factor;
    $totalAlc += $alc;
    $totalNa += (float)($item['sodio_mg'] ?? 0) * $factor;
    $totalK += (float)($item['potasio_mg'] ?? 0) * $factor;
    $totalCa += (float)($item['calcio_mg'] ?? 0) * $factor;
    $totalP += (float)($item['fosforo_mg'] ?? 0) * $factor;
    $totalFe += (float)($item['hierro_mg'] ?? 0) * $factor;
    $totalMg += (float)($item['magnesio_mg'] ?? 0) * $factor;
    $totalZn += (float)($item['zinc_mg'] ?? 0) * $factor;
    $totalVitARae += (float)($item['vit_a_rae_ug'] ?? 0) * $factor;
    $totalVitC += (float)($item['vit_c_mg'] ?? 0) * $factor;

    if (!empty($item['es_hc_complejo'])) {
        $sumHcComplejos += $hcDisp;
    }
    if (!empty($item['es_protector'])) {
        $sumKcalProtectores += $kcal;
    }
    if (!empty($item['es_avb'])) {
        $sumProtAVB += $prot;
    }
    if (!empty($item['es_leche'])) {
        $sumKcalLeche += $kcal;
    }
}

// 1. Densidad Calórica
$densCal = $totalGramos > 0 ? ($totalKcal / $totalGramos) : 0;

// 2. Cociente g/kcal
$cocienteGC = $totalKcal > 0 ? ($totalGramos / $totalKcal) : 0;

// 3. % HC Complejos
$porcHCComp = $totalHcDisp > 0 ? (($sumHcComplejos / $totalHcDisp) * 100) : 0;

// 4. Woodyatt
$numWoodyatt = ($totalLip * 0.90) + ($totalProt * 0.42);
$denWoodyatt = ($totalLip * 0.10) + $totalHcDisp + ($totalProt * 0.58);
$woodyatt = $denWoodyatt > 0 ? ($numWoodyatt / $denWoodyatt) : 0;

// 5. % Protectores
$porcProtectores = $totalKcal > 0 ? (($sumKcalProtectores / $totalKcal) * 100) : 0;

// 6. % AVB
$porcAVB = $totalProt > 0 ? (($sumProtAVB / $totalProt) * 100) : 0;

// 7. % Leche
$porcLeche = $totalKcal > 0 ? (($sumKcalLeche / $totalKcal) * 100) : 0;

// Distribución Calórica Macronutrientes
$kcalHc = $totalHcDisp * 4;
$kcalProt = $totalProt * 4;
$kcalLip = $totalLip * 9;
$porcKcalHc = $totalKcal > 0 ? (($kcalHc / $totalKcal) * 100) : 0;
$porcKcalProt = $totalKcal > 0 ? (($kcalProt / $totalKcal) * 100) : 0;
$porcKcalLip = $totalKcal > 0 ? (($kcalLip / $totalKcal) * 100) : 0;

// Semáforos Patológicos
$kcalAzucarAgr = $totalAzucarAgr * 4;
$porcKcalAzucarAgr = $totalKcal > 0 ? (($kcalAzucarAgr / $totalKcal) * 100) : 0;
$protPorKg = ($pesoPaciente > 0 && $totalProt > 0) ? ($totalProt / $pesoPaciente) : 0;

// Cobertura IDR Micronutrientes
$idrCa = 1000;
$idrFe = 14;
$idrVitC = 75;
$idrVitA = 800;
$idrMg = 350;
$idrZn = 10;
$idrK = 3500;

$cobCa = min(200, ($totalCa / $idrCa) * 100);
$cobFe = min(200, ($totalFe / $idrFe) * 100);
$cobVitC = min(200, ($totalVitC / $idrVitC) * 100);
$cobVitA = min(200, ($totalVitARae / $idrVitA) * 100);
$cobMg = min(200, ($totalMg / $idrMg) * 100);
$cobZn = min(200, ($totalZn / $idrZn) * 100);
$cobK = min(200, ($totalK / $idrK) * 100);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fórmula Desarrollada SARA 2 - <?= htmlspecialchars($formula['PacienteNombre'] . ' ' . $formula['PacienteApellido']) ?></title>
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 12%, white);
        }
        @page {
            size: A4 landscape;
            margin: 6mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            line-height: 1.25;
            margin: 0;
            padding: 10px;
            background: #f8fafc;
            font-size: 10px;
        }
        .page {
            background: #fff;
            max-width: 100%;
            margin: 0 auto;
            padding: 14px 18px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary-green);
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header-title h1 {
            color: #0f172a;
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 2px 0 0;
            color: #64748b;
            font-size: 9.5px;
        }
        .header-nutri {
            text-align: right;
            font-size: 10px;
        }
        .header-nutri strong {
            font-size: 12px;
            color: var(--dark-green);
            display: block;
        }
        .paciente-box {
            background: var(--light-green);
            border-left: 4px solid var(--primary-green);
            padding: 6px 10px;
            border-radius: 4px;
            margin-bottom: 8px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 6px;
            font-size: 9.5px;
        }
        .paciente-box strong {
            color: #0f172a;
        }
        .target-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            padding: 4px 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 9.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 3px 4px;
            text-align: right;
        }
        th {
            background-color: #0f172a;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 8.5px;
        }
        th.th-main, td.td-main {
            text-align: left;
        }
        tfoot td {
            background: #f1f5f9;
            font-weight: bold;
        }
        .badge-momento {
            background: #e2e8f0;
            color: #334155;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            display: inline-block;
        }
        .indicators-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-bottom: 8px;
        }
        .indicator-item {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 4px 5px;
            background: #ffffff;
            text-align: center;
        }
        .indicator-item small {
            display: block;
            font-size: 7.5px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .indicator-item .val {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .indicator-item .diag {
            font-size: 7px;
            font-weight: bold;
            margin-top: 1px;
            display: inline-block;
            padding: 1px 3px;
            border-radius: 3px;
            background: #e2e8f0;
        }
        .diag-ok { background: #dcfce7 !important; color: #15803d !important; }
        .diag-warn { background: #fef9c3 !important; color: #854d0e !important; }
        .diag-bad { background: #fee2e2 !important; color: #b91c1c !important; }

        .clinical-summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 6px 8px;
            background: #ffffff;
            font-size: 8.5px;
        }
        .summary-card h4 {
            margin: 0 0 4px;
            font-size: 9px;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 2px;
            text-transform: uppercase;
        }
        .macro-bar {
            height: 6px;
            border-radius: 3px;
            display: flex;
            overflow: hidden;
            margin: 4px 0;
            background: #e2e8f0;
        }
        .macro-cho { background: #3b82f6; }
        .macro-prot { background: #ef4444; }
        .macro-lip { background: #f59e0b; }

        .obs-box {
            border: 1px dashed #94a3b8;
            padding: 5px 8px;
            border-radius: 5px;
            background: #fafafa;
            margin-bottom: 8px;
            font-size: 8.5px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
        }
        .firma-box {
            text-align: center;
            width: 170px;
            border-top: 1px solid #334155;
            padding-top: 2px;
            color: #334155;
            font-size: 8.5px;
        }
        .no-print {
            text-align: center;
            margin-bottom: 8px;
        }
        .btn-print {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            body { background: transparent; padding: 0; }
            .page { box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Imprimir Reporte / Guardar PDF (A4 Paisaje)</button>
    </div>

    <div class="page">
        <!-- Membrete -->
        <div class="header">
            <div class="header-title">
                <h1>Fórmula Desarrollada & Balance Nutricional (Norma SARA 2 / ENNyS 2)</h1>
                <p><?= htmlspecialchars($formula['nombre_formula']) ?> | Fecha: <?= date('d/m/Y', strtotime($formula['fecha_creacion'])) ?></p>
            </div>
            <div class="header-nutri">
                <strong><?= htmlspecialchars(($nutri['Nombre'] ?? '') . ' ' . ($nutri['Apellido'] ?? '')) ?></strong>
                <span><?= htmlspecialchars($nutri['Especialidad'] ?? 'Lic. en Nutrición') ?></span>
                <?php if(!empty($nutri['Matricula'])): ?>
                    <br><span>M.P. <?= htmlspecialchars($nutri['Matricula']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ficha Paciente -->
        <div class="paciente-box">
            <div><strong>Paciente:</strong> <?= htmlspecialchars($formula['PacienteNombre'] . ' ' . $formula['PacienteApellido']) ?></div>
            <div><strong>DNI:</strong> <?= htmlspecialchars($formula['PacienteDNI']) ?></div>
            <div><strong>Edad:</strong> <?= $edad ?></div>
            <div><strong>Peso:</strong> <?= $pesoPaciente > 0 ? $pesoPaciente . ' kg' : '--' ?></div>
            <div><strong>Talla:</strong> <?= !empty($formula['PacienteEstatura']) ? $formula['PacienteEstatura'] . ' cm' : '--' ?></div>
            <div><strong>Sexo:</strong> <?= ($formula['PacienteSexo'] ?? 'M') === 'F' ? 'Femenino' : 'Masculino' ?></div>
        </div>

        <!-- Meta Calórica -->
        <?php if ($kcalObjetivo > 0): 
            $deltaKcal = $totalKcal - $kcalObjetivo;
            $pctKcal = ($totalKcal / $kcalObjetivo) * 100;
        ?>
        <div class="target-box">
            <div><strong>Kcal Objetivo:</strong> <?= number_format($kcalObjetivo, 0) ?> kcal</div>
            <div><strong>Kcal Calculadas (Atwater):</strong> <?= number_format($totalKcal, 1) ?> kcal</div>
            <div><strong>Diferencia (Δ):</strong> <span style="font-weight:bold; color:<?= abs($deltaKcal) <= 50 ? '#15803d' : '#b91c1c' ?>;"><?= ($deltaKcal >= 0 ? '+' : '') . number_format($deltaKcal, 1) ?> kcal</span></div>
            <div><strong>Cumplimiento:</strong> <span style="font-weight:bold; color:<?= ($pctKcal >= 95 && $pctKcal <= 105) ? '#15803d' : '#854d0e' ?>;"><?= number_format($pctKcal, 1) ?>%</span></div>
        </div>
        <?php endif; ?>

        <!-- Tabla de Aportes SARA 2 -->
        <table>
            <thead>
                <tr>
                    <th class="th-main">Alimento SARA 2 / Medida Casera</th>
                    <th>Momento</th>
                    <th>Grupo SARA 2</th>
                    <th>Gramos</th>
                    <th style="background:#15803d;">Kcal (Atwater)</th>
                    <th>HC Disp (g)</th>
                    <th>Prot (g)</th>
                    <th>Líp (g)</th>
                    <th>Fibra (g)</th>
                    <th>AG Sat (g)</th>
                    <th>Colest (mg)</th>
                    <th>Sodio (mg)</th>
                    <th>Potasio (mg)</th>
                    <th>Calcio (mg)</th>
                    <th>Fósforo (mg)</th>
                    <th>Hierro (mg)</th>
                    <th>Magnesio (mg)</th>
                    <th>Zinc (mg)</th>
                    <th>Vit A (µg)</th>
                    <th>Vit C (mg)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $d): 
                    $g = (float)$d['gramos'];
                    $f = $g / 100;
                    $hc = (float)($d['hc_disponibles_g'] ?? 0) * $f;
                    $pr = (float)($d['proteina_g'] ?? 0) * $f;
                    $gr = (float)($d['lipidos_totales_g'] ?? 0) * $f;
                    $alc = (float)($d['alcohol_g'] ?? 0) * $f;
                    $kc = ($hc * 4) + ($pr * 4) + ($gr * 9) + ($alc * 7);
                    $medidaEstimada = calcularMedidaCaseraImpresion($d['medida_casera_ref'] ?? '', $g);
                ?>
                <tr>
                    <td class="td-main">
                        <strong><?= htmlspecialchars($d['nombre']) ?></strong>
                        <?php if(!empty($medidaEstimada)): ?>
                            <span style="display:block; font-size:7.5px; color:#64748b;">🥄 <?= htmlspecialchars($medidaEstimada) ?></span>
                        <?php endif; ?>
                        <div style="margin-top:1px;">
                            <?php if(!empty($d['es_avb'])): ?><span style="font-size:7px; color:#0284c7; font-weight:bold;">[AVB]</span><?php endif; ?>
                            <?php if(!empty($d['es_protector'])): ?><span style="font-size:7px; color:#16a34a; font-weight:bold;">[PROT]</span><?php endif; ?>
                            <?php if(!empty($d['es_leche'])): ?><span style="font-size:7px; color:#2563eb; font-weight:bold;">[LÁCTEO]</span><?php endif; ?>
                            <?php if(!empty($d['es_hc_complejo'])): ?><span style="font-size:7px; color:#d97706; font-weight:bold;">[HC COMP]</span><?php endif; ?>
                        </div>
                    </td>
                    <td style="text-align:center;"><span class="badge-momento"><?= htmlspecialchars($d['momento_dia'] ?? 'Almuerzo') ?></span></td>
                    <td style="text-align:center; font-size:7.5px;"><?= htmlspecialchars($d['grupo_nombre'] ?? '') ?></td>
                    <td><?= number_format($g, 0) ?> g</td>
                    <td style="font-weight:bold; color:#15803d;"><?= number_format($kc, 1) ?></td>
                    <td><?= number_format($hc, 2) ?></td>
                    <td><?= number_format($pr, 2) ?></td>
                    <td><?= number_format($gr, 2) ?></td>
                    <td><?= number_format((float)($d['fibra_g'] ?? 0) * $f, 2) ?></td>
                    <td><?= number_format((float)($d['ag_sat_g'] ?? 0) * $f, 2) ?></td>
                    <td><?= number_format((float)($d['colesterol_mg'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['sodio_mg'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['potasio_mg'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['calcio_mg'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['fosforo_mg'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['hierro_mg'] ?? 0) * $f, 2) ?></td>
                    <td><?= number_format((float)($d['magnesio_mg'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['zinc_mg'] ?? 0) * $f, 2) ?></td>
                    <td><?= number_format((float)($d['vit_a_rae_ug'] ?? 0) * $f, 1) ?></td>
                    <td><?= number_format((float)($d['vit_c_mg'] ?? 0) * $f, 1) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="td-main">TOTALES GENERALES</td>
                    <td>-</td>
                    <td>-</td>
                    <td><?= number_format($totalGramos, 0) ?> g</td>
                    <td style="color:#15803d; font-size:9.5px;"><?= number_format($totalKcal, 1) ?></td>
                    <td><?= number_format($totalHcDisp, 2) ?></td>
                    <td><?= number_format($totalProt, 2) ?></td>
                    <td><?= number_format($totalLip, 2) ?></td>
                    <td><?= number_format($totalFibra, 2) ?></td>
                    <td><?= number_format($totalAgSat, 2) ?></td>
                    <td><?= number_format($totalColest, 1) ?></td>
                    <td><?= number_format($totalNa, 1) ?></td>
                    <td><?= number_format($totalK, 1) ?></td>
                    <td><?= number_format($totalCa, 1) ?></td>
                    <td><?= number_format($totalP, 1) ?></td>
                    <td><?= number_format($totalFe, 2) ?></td>
                    <td><?= number_format($totalMg, 1) ?></td>
                    <td><?= number_format($totalZn, 2) ?></td>
                    <td><?= number_format($totalVitARae, 1) ?></td>
                    <td><?= number_format($totalVitC, 1) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- 7 Indicadores Diagnósticos SARA 2 -->
        <div class="indicators-grid">
            <div class="indicator-item">
                <small>1. Densidad Calórica</small>
                <div class="val"><?= number_format($densCal, 2) ?></div>
                <div class="diag <?= $densCal <= 1.2 ? 'diag-ok' : 'diag-warn' ?>"><?= $densCal < 0.9 ? 'Baja (<0.90)' : ($densCal <= 1.2 ? 'Normal (0.9-1.2)' : 'Alta (>1.20)') ?></div>
            </div>
            <div class="indicator-item">
                <small>2. Cociente g / Kcal</small>
                <div class="val"><?= number_format($cocienteGC, 2) ?></div>
                <div class="diag diag-ok">g / kcal</div>
            </div>
            <div class="indicator-item">
                <small>3. % HC Complejos</small>
                <div class="val"><?= number_format($porcHCComp, 1) ?>%</div>
                <div class="diag <?= $porcHCComp >= 50 ? 'diag-ok' : 'diag-bad' ?>"><?= $porcHCComp >= 50 ? 'NORMAL (≥50%)' : 'BAJO (<50%)' ?></div>
            </div>
            <div class="indicator-item">
                <small>4. Woodyatt (Ceto/Anticeto)</small>
                <div class="val"><?= number_format($woodyatt, 2) ?></div>
                <div class="diag <?= ($woodyatt >= 0.25 && $woodyatt <= 0.35) ? 'diag-ok' : ($woodyatt < 0.25 ? 'diag-warn' : 'diag-bad') ?>">
                    <?= ($woodyatt >= 0.25 && $woodyatt <= 0.35) ? 'NORMAL' : ($woodyatt < 0.25 ? 'BAJO (<0.25)' : 'ALTO (>0.35)') ?>
                </div>
            </div>
            <div class="indicator-item">
                <small>5. % Protectores</small>
                <div class="val"><?= number_format($porcProtectores, 1) ?>%</div>
                <div class="diag <?= $porcProtectores > 50 ? 'diag-ok' : ($porcProtectores >= 30 ? 'diag-warn' : 'diag-bad') ?>">
                    <?= $porcProtectores > 50 ? 'ÓPTIMO (>50%)' : ($porcProtectores >= 30 ? 'ACEPTABLE' : 'BAJO') ?>
                </div>
            </div>
            <div class="indicator-item">
                <small>6. % Prot. AVB</small>
                <div class="val"><?= number_format($porcAVB, 1) ?>%</div>
                <div class="diag <?= $porcAVB > 55 ? 'diag-ok' : ($porcAVB >= 30 ? 'diag-warn' : 'diag-bad') ?>">
                    <?= $porcAVB > 55 ? 'ÓPTIMO (>55%)' : ($porcAVB >= 30 ? 'ACEPTABLE' : 'BAJO') ?>
                </div>
            </div>
            <div class="indicator-item">
                <small>7. % Cobertura Leche</small>
                <div class="val"><?= number_format($porcLeche, 1) ?>%</div>
                <div class="diag <?= ($porcLeche >= 6 && $porcLeche <= 10) ? 'diag-ok' : 'diag-warn' ?>">
                    <?= ($porcLeche >= 6 && $porcLeche <= 10) ? 'NORMAL (6-10%)' : ($porcLeche < 6 ? 'BAJO' : 'ELEVADO') ?>
                </div>
            </div>
        </div>

        <!-- Analítica Visual y Semáforos Clínicos -->
        <div class="clinical-summary-grid">
            <!-- Macronutrientes -->
            <div class="summary-card">
                <h4>Distribución Calórica</h4>
                <div class="macro-bar">
                    <div class="macro-cho" style="width: <?= $porcKcalHc ?>%;"></div>
                    <div class="macro-prot" style="width: <?= $porcKcalProt ?>%;"></div>
                    <div class="macro-lip" style="width: <?= $porcKcalLip ?>%;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:3px;">
                    <span style="color:#2563eb;"><b>HC:</b> <?= number_format($porcKcalHc, 1) ?>% (<?= number_format($totalHcDisp, 1) ?>g)</span>
                    <span style="color:#dc2626;"><b>P:</b> <?= number_format($porcKcalProt, 1) ?>% (<?= number_format($totalProt, 1) ?>g)</span>
                    <span style="color:#d97706;"><b>G:</b> <?= number_format($porcKcalLip, 1) ?>% (<?= number_format($totalLip, 1) ?>g)</span>
                </div>
                <?php if ($protPorKg > 0): ?>
                    <div style="margin-top:3px; color:#475569;">
                        <b>Prot / kg:</b> <?= number_format($protPorKg, 2) ?> g/kg/día
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cobertura IDR Micronutrientes Clave -->
            <div class="summary-card">
                <h4>Cobertura Micronutrientes (vs IDR)</h4>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 2px;">
                    <div>Ca (1000mg): <b><?= number_format($cobCa, 0) ?>%</b></div>
                    <div>Fe (14mg): <b><?= number_format($cobFe, 0) ?>%</b></div>
                    <div>Vit C (75mg): <b><?= number_format($cobVitC, 0) ?>%</b></div>
                    <div>Vit A (800µg): <b><?= number_format($cobVitA, 0) ?>%</b></div>
                    <div>Mg (350mg): <b><?= number_format($cobMg, 0) ?>%</b></div>
                    <div>K (3500mg): <b><?= number_format($cobK, 0) ?>%</b></div>
                </div>
            </div>

            <!-- Semáforos Patológicos -->
            <div class="summary-card">
                <h4>Semáforos Patológicos</h4>
                <div style="margin-bottom:2px;">
                    <b>Sodio:</b> <?= number_format($totalNa, 0) ?> mg 
                    <span class="diag <?= $totalNa <= 2000 ? 'diag-ok' : 'diag-bad' ?>"><?= $totalNa <= 2000 ? 'Normal (≤2000mg)' : 'ALERTA HIPERTENSIÓN' ?></span>
                </div>
                <div style="margin-bottom:2px;">
                    <b>Fibra:</b> <?= number_format($totalFibra, 1) ?> g 
                    <span class="diag <?= $totalFibra >= 25 ? 'diag-ok' : 'diag-bad' ?>"><?= $totalFibra >= 25 ? 'Adecuada (≥25g)' : 'BAJA EN FIBRA' ?></span>
                </div>
                <div>
                    <b>Azúcar Agregado:</b> <?= number_format($porcKcalAzucarAgr, 1) ?>% kcal 
                    <span class="diag <?= $porcKcalAzucarAgr <= 10 ? 'diag-ok' : 'diag-bad' ?>"><?= $porcKcalAzucarAgr <= 10 ? 'OMS (≤10%)' : 'EXCESO AZÚCARES' ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($formula['observaciones'])): ?>
        <div class="obs-box">
            <strong>Observaciones y Pautas Dietoterápicas:</strong><br>
            <?= nl2br(htmlspecialchars($formula['observaciones'])) ?>
        </div>
        <?php endif; ?>

        <!-- Firma y Pie -->
        <div class="footer">
            <div>
                Documento clínico emitido por <strong>NutriSalud SaaS</strong> según Norma SARA 2 / ENNyS 2 | <?= date('d/m/Y H:i') ?>
            </div>
            <div class="firma-box">
                Firma y Sello del Profesional
            </div>
        </div>
    </div>

</body>
</html>
