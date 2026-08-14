<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Educativo Nutricional</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f1f2f6; padding: 2rem 0; color: #2c3e50; }
        .hoja { max-width: 800px; margin: 0 auto; background: white; padding: 3rem; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .header-box { background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; padding: 2rem; border-radius: 15px; margin-bottom: 2rem; }
        .diagnostico-card { background: #eafaf1; border-left: 4px solid #2ecc71; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 15px; }
        .pilar-box { border: 1px solid #e0e0e0; border-radius: 15px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .pilar-numero { width: 40px; height: 40px; background: #2ecc71; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin-right: 15px; }
        .footer-box { border-top: 2px dashed #e0e0e0; margin-top: 3rem; padding-top: 2rem; text-align: center; }
        ul.checklist { list-style: none; padding-left: 0; }
        ul.checklist li { position: relative; padding-left: 30px; margin-bottom: 10px; }
        ul.checklist li::before { content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; left: 0; color: #2ecc71; }
        
        @media print {
            body { background: white; padding: 0; }
            .hoja { box-shadow: none; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="hoja">
        <!-- 1. HEADER -->
        <div class="header-box d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Informe Educativo Nutricional</h2>
                <p class="mb-0 fs-5 text-white-50"><?= htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']) ?> | <?= date('d/m/Y', strtotime($informeRow['Fecha'])) ?></p>
            </div>
            <div class="text-end">
                <h5 class="fw-bold mb-0"><?= htmlspecialchars($informe['header']['profesional']['nombre_completo']) ?></h5>
                <small class="d-block"><?= htmlspecialchars($informe['header']['profesional']['especialidad']) ?></small>
                <small><?= htmlspecialchars($informe['header']['profesional']['matricula']) ?></small>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-bold text-success">🎯 Objetivo General</h5>
            <p class="fs-5"><?= htmlspecialchars($informe['header']['objetivo_general'] ?? '') ?></p>
            <?php if(!empty($informe['header']['recordatorio_clave'])): ?>
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 fw-bold">
                    <i class="fa-solid fa-bell me-2 text-warning"></i> <?= htmlspecialchars($informe['header']['recordatorio_clave']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. DIAGNOSTICO POSITIVO -->
        <?php if(!empty($informe['diagnostico_positivo'])): ?>
        <div class="mb-5">
            <h4 class="fw-bold mb-3"><i class="fa-solid fa-star text-warning me-2"></i> Lo que estamos haciendo muy bien</h4>
            <div class="row">
                <?php foreach($informe['diagnostico_positivo'] as $diag): ?>
                <div class="col-md-6">
                    <div class="diagnostico-card">
                        <i class="fa-solid fa-check-circle text-success fs-4"></i>
                        <span class="fw-medium"><?= htmlspecialchars($diag['texto']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 3. PILARES / EJES DE TRABAJO -->
        <?php if(!empty($informe['ejes_trabajo'])): ?>
        <div class="mb-5">
            <h4 class="fw-bold mb-4"><i class="fa-solid fa-list-check text-primary me-2"></i> Pilares de Acción</h4>
            <?php foreach($informe['ejes_trabajo'] as $eje): ?>
                <div class="pilar-box">
                    <div class="d-flex align-items-center mb-3">
                        <div class="pilar-numero"><?= $eje['numero'] ?></div>
                        <div>
                            <h5 class="fw-bold mb-0"><?= htmlspecialchars($eje['titulo']) ?></h5>
                            <?php if(!empty($eje['subtitulo'])): ?>
                                <small class="text-muted"><?= htmlspecialchars($eje['subtitulo']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if(!empty($eje['acciones'])): ?>
                    <ul class="checklist mt-3 mb-0">
                        <?php foreach($eje['acciones'] as $accion): ?>
                            <li><?= htmlspecialchars($accion) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- 4. CONOCE A TU NUTRICIONISTA (NUEVO) -->
        <div class="pilar-box" style="background-color: #f8f9fa; border-color: #e2e8f0; margin-top: 3rem;">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-user-doctor text-primary me-2"></i> Conoce a tu Profesional</h5>
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <?php if(!empty($informe['header']['profesional']['logo_url'])): ?>
                        <img src="<?= htmlspecialchars($informe['header']['profesional']['logo_url']) ?>" alt="Logo" class="img-fluid rounded-circle" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                    <?php else: ?>
                        <i class="fa-solid fa-user-circle text-muted" style="font-size: 5rem;"></i>
                    <?php endif; ?>
                </div>
                <div class="col-md-10">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($informe['header']['profesional']['nombre_completo']) ?></h5>
                    <p class="mb-2 text-muted fw-medium"><?= htmlspecialchars($informe['header']['profesional']['especialidad']) ?> | <?= htmlspecialchars($informe['header']['profesional']['matricula']) ?></p>
                    <?php if(!empty($biografia)): ?>
                        <p class="small text-secondary mb-3 fst-italic">"<?= htmlspecialchars($biografia) ?>"</p>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-3">
                        <?php if(!empty($informe['footer']['contacto']['whatsapp'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $informe['footer']['contacto']['whatsapp']) ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                        <?php endif; ?>
                        <?php if(!empty($informe['footer']['contacto']['instagram'])): ?>
                            <a href="https://instagram.com/<?= str_replace('@', '', $informe['footer']['contacto']['instagram']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill"><i class="fa-brands fa-instagram"></i> Instagram</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. FOOTER -->
        <div class="footer-box">
            <h4 class="text-success fw-bold fst-italic mb-4">"<?= htmlspecialchars($informe['footer']['mensaje_motivacional'] ?? '') ?>"</h4>
            
            <div class="d-flex justify-content-center gap-4 text-muted mt-4">
                <?php if(!empty($informe['footer']['contacto']['direccion'])): ?>
                <span><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($informe['footer']['contacto']['direccion']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-5 d-print-none">
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 py-2"><i class="fa-solid fa-print me-2"></i> Imprimir o Guardar PDF</button>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
