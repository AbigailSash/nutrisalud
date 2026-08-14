<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Plan Alimentario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: white; color: black; }
        .plan-header { border-bottom: 2px solid #2ecc71; margin-bottom: 2rem; padding-bottom: 1rem; }
        .card-menu { border: 1px solid #ddd; margin-bottom: 1.5rem; page-break-inside: avoid; }
        .card-header-day { font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd; }
        .momento-title { font-weight: bold; margin-top: 10px; text-transform: uppercase; font-size: 0.9rem; }
        .food-item { margin-bottom: 5px; padding: 5px 10px; border-left: 3px solid #2ecc71; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container py-4">
        <div class="d-flex justify-content-between mb-3 no-print">
            <button onclick="window.close()" class="btn btn-outline-secondary">Cerrar</button>
            <button onclick="window.print()" class="btn btn-dark"><i class="fa-solid fa-print me-2"></i> Imprimir de nuevo</button>
        </div>

        <div class="plan-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><?= htmlspecialchars($planActivo['Nombre_Plan']) ?></h2>
                <p class="mb-0 fs-5">Paciente: <?= htmlspecialchars($planActivo['Nombre'] . ' ' . $planActivo['Apellido']) ?></p>
                <p class="mb-0 fs-6 text-muted">Objetivo: <?= htmlspecialchars($planActivo['Objetivo']) ?></p>
            </div>
            <div class="text-end">
                <small class="d-block text-uppercase fw-bold">Fecha de Inicio</small>
                <span class="fs-4 fw-bold"><?= date('d M, Y', strtotime($planActivo['Fecha_Inicio'])) ?></span>
            </div>
        </div>

        <?php if(empty($planAgrupado)): ?>
            <div class="text-center py-5">
                <h4 class="text-muted">El plan aún no tiene comidas diseñadas.</h4>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($planAgrupado as $nombreDia => $momentosAgrupados): ?>
                    <div class="col-12 mb-4">
                        <div class="card card-menu">
                            <div class="card-header card-header-day bg-light text-dark">
                                <h5 class="mb-0"><i class="fa-regular fa-calendar-check me-2"></i> <?= htmlspecialchars($nombreDia) ?></h5>
                            </div>
                            <div class="card-body p-3">
                                <?php foreach($momentosAgrupados as $momento => $items): ?>
                                    <div class="momento-title">
                                        <?= htmlspecialchars($momento) ?>
                                    </div>
                                    <?php foreach($items as $item): ?>
                                        <div class="food-item d-flex justify-content-between">
                                            <div>
                                                <strong><?= htmlspecialchars($item['Alimento']) ?></strong>
                                                <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($item['Indicaciones_Especiales']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <span><?= htmlspecialchars($item['Cantidad']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Recomendaciones del Plan -->
        <?php if(!empty($planActivo['Recomendaciones'])): ?>
            <div class="mt-5" style="page-break-before: auto;">
                <h3 class="fw-bold mb-3 border-bottom pb-2">Recomendaciones / Educación</h3>
                <p style="white-space: pre-wrap; font-size: 1.05rem;"><?= htmlspecialchars($planActivo['Recomendaciones']) ?></p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
