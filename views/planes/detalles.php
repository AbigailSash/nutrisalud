<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Diseñar Menú</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .plan-header { background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card-menu { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .card-header-day { background-color: #1a252f; color: white; border-radius: 15px 15px 0 0 !important; font-weight: 600; }
        .momento-title { color: #27ae60; font-weight: 700; border-bottom: 2px solid #eafaf1; padding-bottom: 0.5rem; margin-top: 1rem; }
        .food-item { background: #fdfdfd; border-left: 3px solid #2ecc71; padding: 10px 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold">Gestor de Menú (Plan Alimentario)</h2>
            <div>
                <a href="index.php?action=imprimir_plan&id_plan=<?= $plan['IdPlan'] ?>" target="_blank" class="btn btn-dark rounded-pill me-2"><i class="fa-solid fa-print me-1"></i> Imprimir / PDF</a>
                <a href="index.php?action=listar_planes" class="btn btn-outline-secondary rounded-pill">Finalizar y Volver</a>
            </div>
        </div>
        
        <div class="plan-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($plan['Nombre_Plan']) ?></h3>
                <p class="mb-0 text-white-50">Paciente: <?= htmlspecialchars($plan['Nombre'] . ' ' . $plan['Apellido']) ?></p>
            </div>
            <div class="text-end">
                <small class="d-block">Inicio: <?= date('d/m/Y', strtotime($plan['Fecha_Inicio'])) ?></small>
                <small>Objetivo: <?= htmlspecialchars($plan['Objetivo']) ?></small>
            </div>
        </div>

        <?php include 'views/layout/alertas.php'; ?>

        <!-- Formulario para agregar alimento -->
        <div class="card card-menu mb-5 border border-success">
            <div class="card-body p-4">
                <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-plus-circle me-2"></i> Agregar Indicación / Alimento</h5>
                <form action="index.php?action=agregar_detalle_plan" method="POST">
                    <input type="hidden" name="id_plan" value="<?= $plan['IdPlan'] ?>">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Día</label>
                            <select name="id_dia" class="form-select form-select-sm" required>
                                <?php foreach($dias as $d): ?>
                                    <option value="<?= $d['IdDia'] ?>"><?= htmlspecialchars($d['Nombre_Dia']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Momento</label>
                            <select name="id_momento" class="form-select form-select-sm" required>
                                <?php foreach($momentos as $m): ?>
                                    <option value="<?= $m['IdMomento'] ?>"><?= htmlspecialchars($m['Nombre_Momento']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 position-relative">
                            <label class="form-label fw-bold small">Alimento</label>
                            <input type="text" name="alimento" id="search-alimento" class="form-control form-control-sm" placeholder="Ej: Tostadas integrales con palta" required autocomplete="off">
                            <div id="sugerencias-alimentos" class="list-group position-absolute w-100 d-none shadow-sm mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Cantidad / Porción</label>
                            <input type="text" name="cantidad" class="form-control form-control-sm" placeholder="Ej: 2 tostadas" required>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label fw-bold small">Indicaciones Especiales (Ej: Cocido, al vapor, sin sal)</label>
                            <input type="text" name="indicaciones" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">Agregar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vista del menú actual -->
        <h4 class="fw-bold mb-4">Menú Diseñado</h4>
        <?php if(empty($planAgrupado)): ?>
            <div class="alert alert-light text-center py-5 border">El plan está vacío. Comienza agregando alimentos en el formulario de arriba.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach($planAgrupado as $nombreDia => $momentosAgrupados): ?>
                    <div class="col-md-6">
                        <div class="card card-menu">
                            <div class="card-header card-header-day p-3">
                                <i class="fa-regular fa-calendar me-2"></i> <?= htmlspecialchars($nombreDia) ?>
                            </div>
                            <div class="card-body">
                                <?php foreach($momentosAgrupados as $nombreMomento => $listaAlimentos): ?>
                                    <h6 class="momento-title"><i class="fa-solid fa-clock text-muted me-1"></i> <?= htmlspecialchars($nombreMomento) ?></h6>
                                    <?php foreach($listaAlimentos as $item): ?>
                                        <div class="food-item">
                                            <div>
                                                <strong><?= htmlspecialchars($item['Alimento']) ?></strong> (<?= $item['Cantidad'] ?>g)
                                                <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                                    <br><small class="text-muted"><em><?= htmlspecialchars($item['Indicaciones_Especiales']) ?></em></small>
                                                <?php endif; ?>
                                            </div>
                                            <a href="index.php?action=eliminar_detalle_plan&id_detalle=<?= $item['IdDetalle'] ?>&id_plan=<?= $plan['IdPlan'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="true" data-mensaje="¿Quitar del menú?"><i class="fa-solid fa-times"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- SECCIÓN DE RECOMENDACIONES / INFORME EDUCATIVO -->
    <div class="container pb-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white p-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-file-medical me-2"></i> Recomendaciones / Informe Educativo</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <form action="index.php?action=guardar_recomendaciones_plan" method="POST">
                    <input type="hidden" name="id_plan" value="<?= $plan['IdPlan'] ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Redacta aquí las recomendaciones, pautas o educación nutricional para este plan:</label>
                        <textarea class="form-control" name="recomendaciones" rows="10" placeholder="Ej. Tomar 2 litros de agua por día. Evitar los procesados..." style="border-radius: 10px;"><?= htmlspecialchars($plan['Recomendaciones'] ?? '') ?></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="fa-solid fa-save me-2"></i> Guardar Recomendaciones</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'views/layout/global_scripts.php'; ?>
    <script src="public/js/plan_alimentario.js"></script>
</body>
</html>
