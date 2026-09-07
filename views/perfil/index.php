<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Perfil & Personalización de Marca</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary-green: <?= htmlspecialchars($_SESSION['ColorTema'] ?? $perfil['Color_Tema'] ?? '#2ecc71') ?>; 
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black); 
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white); 
            --sidebar-bg: #1a252f; 
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .main-content { margin-left: 280px; padding: 2rem 3rem; }
        .dashboard-card { background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 2rem; }
        .btn-gradient { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important; color: white !important; border: none; font-weight: 600; border-radius: 50px; }
        .btn-gradient:hover { color: white !important; opacity: 0.95; transform: translateY(-1px); }
        .avatar-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #ccc; margin-bottom: 1rem; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green) !important;
            box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--primary-green) 25%, transparent) !important;
        }

        /* Swatches de Color */
        .color-swatch-item {
            cursor: pointer;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 3px solid white;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            position: relative;
        }
        .color-swatch-item:hover {
            transform: scale(1.18);
        }
        .color-swatch-item.active {
            border: 3px solid #1a252f;
            transform: scale(1.12);
        }
        .color-swatch-item.active::after {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: white;
            font-size: 0.9rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.6);
        }

        .preview-theme-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 1.25rem;
            transition: all 0.3s;
        }

        .auto-save-pill {
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media (max-width: 991px) {
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <!-- Top Header Modular -->
        <?php include 'views/layout/header.php'; ?>

        <!-- Alertas Flash -->
        <?php include 'views/layout/alertas.php'; ?>

        <div class="row">
            <!-- Columna Izquierda: Datos Profesionales -->
            <div class="col-lg-7">
                <div class="dashboard-card border-top border-4" style="border-top-color: var(--primary-green) !important;">
                    <h4 class="fw-bold mb-4" style="color: var(--primary-green);">
                        <i class="fa-solid fa-address-card me-2"></i> Datos Profesionales
                    </h4>
                    <form action="index.php?action=actualizar_perfil" method="POST" enctype="multipart/form-data">
                        <div class="row mb-4 align-items-center">
                            <div class="col-auto">
                                <?php if(!empty($perfil['Logo_URL'])): ?>
                                    <img src="<?= htmlspecialchars($perfil['Logo_URL']) ?>?v=<?= time() ?>" class="avatar-preview">
                                <?php else: ?>
                                    <div class="avatar-preview"><i class="fa-solid fa-camera"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="col">
                                <label class="form-label fw-bold">Foto de Perfil / Logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <small class="text-muted">Formatos: JPG, PNG, WEBP. Se utilizará en los encabezados y guías impresas.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($perfil['Nombre'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($perfil['Apellido'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Título / Especialidad</label>
                                <input type="text" name="especialidad" class="form-control" value="<?= htmlspecialchars($perfil['Especialidad'] ?? '') ?>" placeholder="Ej: Lic. en Nutrición Clínica">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Matrícula Profesional (M.P.)</label>
                                <input type="text" name="matricula" class="form-control" value="<?= htmlspecialchars($perfil['Matricula'] ?? '') ?>" placeholder="Ej: M.P. 12345">
                            </div>
                        </div>

                        <h5 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-address-book text-muted me-2"></i> Datos de Contacto (Para Informes y Guías)</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-instagram text-danger"></i> Instagram</label>
                                <input type="text" name="instagram" class="form-control" value="<?= htmlspecialchars($perfil['Instagram'] ?? '') ?>" placeholder="@tu.usuario">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-whatsapp text-success"></i> WhatsApp / Teléfono</label>
                                <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($perfil['Whatsapp'] ?? '') ?>" placeholder="+54 9 11...">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-location-dot text-primary"></i> Dirección / Localidad</label>
                            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($perfil['Direccion'] ?? '') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-book-open text-info"></i> Biografía / Presentación</label>
                            <textarea name="biografia" class="form-control" rows="3" placeholder="Ej: Especialista en nutrición deportiva y recomposición corporal..."><?= htmlspecialchars($perfil['Biografia'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-3 fw-bold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Datos de Perfil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Columna Derecha: Personalización de Marca & Seguridad -->
            <div class="col-lg-5">
                
                <!-- Tarjeta: Personalización de Tema de Color con Auto-Guardado -->
                <div class="dashboard-card border-top border-4" style="border-top-color: var(--primary-green) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-palette me-2" style="color: var(--primary-green);"></i> Tema y Color de Marca
                        </h5>
                        <div id="saveStatusIndicator" class="auto-save-pill badge rounded-pill px-3 py-1" style="background-color: var(--light-green); color: var(--dark-green);">
                            <i class="fa-solid fa-check"></i> Listo
                        </div>
                    </div>
                    <p class="text-muted small mb-3">Elige un color para personalizar tu consultorio. <strong>Se guarda y aplica automáticamente en tiempo real.</strong></p>

                    <!-- Paletas Rápidas Pre-configuradas -->
                    <label class="form-label fw-bold text-muted small mb-2">Paletas de Color Rápidas (1 Clic):</label>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <div class="color-swatch-item" style="background-color: #2ecc71;" data-color="#2ecc71" title="Verde Esmeralda Nutri (Predeterminado)"></div>
                        <div class="color-swatch-item" style="background-color: #3498db;" data-color="#3498db" title="Azul Clínico Salud"></div>
                        <div class="color-swatch-item" style="background-color: #1abc9c;" data-color="#1abc9c" title="Verde Menta Aqua"></div>
                        <div class="color-swatch-item" style="background-color: #9b59b6;" data-color="#9b59b6" title="Lavanda & Bienestar"></div>
                        <div class="color-swatch-item" style="background-color: #e67e22;" data-color="#e67e22" title="Coral & Vitalidad"></div>
                        <div class="color-swatch-item" style="background-color: #e91e63;" data-color="#e91e63" title="Rosa Armonía"></div>
                        <div class="color-swatch-item" style="background-color: #34495e;" data-color="#34495e" title="Medianoche Elegante"></div>
                    </div>

                    <!-- Selector Libre de Color (Color Picker + Hex) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Selector Totalmente Libre:</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color p-1" id="colorPickerInput" value="<?= htmlspecialchars($_SESSION['ColorTema'] ?? $perfil['Color_Tema'] ?? '#2ecc71') ?>" style="width: 55px; height: 42px; cursor: pointer;">
                            <input type="text" id="colorHexInput" class="form-control font-monospace fw-bold" value="<?= htmlspecialchars($_SESSION['ColorTema'] ?? $perfil['Color_Tema'] ?? '#2ecc71') ?>" placeholder="#2ecc71" maxlength="7">
                        </div>
                    </div>

                    <!-- Vista Previa en Tiempo Real -->
                    <div class="preview-theme-box mb-3 text-center">
                        <small class="text-muted d-block fw-bold mb-2">Vista Previa de Componentes:</small>
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm rounded-pill px-3 text-white fw-bold shadow-sm" id="previewBtnGradient" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                                <i class="fa-solid fa-check me-1"></i> Botón
                            </button>
                            <span class="badge rounded-pill px-3 py-2 shadow-sm" id="previewBadge" style="background-color: var(--light-green); color: var(--dark-green); font-weight: 600;">
                                Activo
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="resetDefaultColor()">
                            <i class="fa-solid fa-rotate-left me-1"></i> Restablecer a Verde
                        </button>
                    </div>
                </div>

                <!-- Tarjeta: Seguridad de Cuenta (100% Theme Color Responsive) -->
                <div class="dashboard-card border-top border-4" style="border-top-color: var(--primary-green) !important;">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-shield-halved me-2" style="color: var(--primary-green);"></i> Seguridad de Cuenta
                    </h5>
                    <form action="index.php?action=actualizar_password" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Contraseña Actual</label>
                            <div class="input-group">
                                <input type="password" name="password_actual" class="form-control" placeholder="••••••••" style="border-right: none;">
                                <button type="button" class="btn btn-toggle-password input-group-text border" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="nueva_password" class="form-control" placeholder="••••••••" required minlength="6" style="border-right: none;">
                                <button type="button" class="btn btn-toggle-password input-group-text border" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="confirmar_password" class="form-control" placeholder="••••••••" required minlength="6" style="border-right: none;">
                                <button type="button" class="btn btn-toggle-password input-group-text border" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="fa-solid fa-key me-2"></i> Actualizar Contraseña
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colorPicker = document.getElementById('colorPickerInput');
            const colorHex = document.getElementById('colorHexInput');
            const swatches = document.querySelectorAll('.color-swatch-item');
            const previewBtn = document.getElementById('previewBtnGradient');
            const previewBadge = document.getElementById('previewBadge');
            const statusIndicator = document.getElementById('saveStatusIndicator');

            let saveTimeout = null;

            function showSavingState() {
                if (statusIndicator) {
                    statusIndicator.style.backgroundColor = 'rgba(255, 193, 7, 0.2)';
                    statusIndicator.style.color = '#856404';
                    statusIndicator.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Guardando...';
                }
            }

            function showSavedState(hex) {
                if (statusIndicator) {
                    statusIndicator.style.backgroundColor = `color-mix(in srgb, ${hex} 15%, white)`;
                    statusIndicator.style.color = `color-mix(in srgb, ${hex} 75%, black)`;
                    statusIndicator.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Guardado';
                }
            }

            // Función para enviar y persistir el color en la base de datos automáticamente
            function autoSaveColor(hex) {
                showSavingState();
                fetch('index.php?action=actualizar_color_tema', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ color_tema: hex })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showSavedState(hex);
                    }
                })
                .catch(err => {
                    console.error('Error auto-guardando tema:', err);
                    showSavedState(hex);
                });
            }

            function applyColorTheme(hex, shouldSave = true) {
                if (!hex.startsWith('#')) hex = '#' + hex;
                
                // Actualizar inputs
                colorPicker.value = hex;
                colorHex.value = hex.toUpperCase();

                // Actualizar CSS variables en el documento en tiempo real
                document.documentElement.style.setProperty('--primary-green', hex);
                document.documentElement.style.setProperty('--dark-green', `color-mix(in srgb, ${hex} 75%, black)`);
                document.documentElement.style.setProperty('--light-green', `color-mix(in srgb, ${hex} 15%, white)`);

                // Actualizar swatches activos
                swatches.forEach(swatch => {
                    if (swatch.dataset.color.toLowerCase() === hex.toLowerCase()) {
                        swatch.classList.add('active');
                    } else {
                        swatch.classList.remove('active');
                    }
                });

                // Actualizar preview box
                if (previewBtn) {
                    previewBtn.style.background = `linear-gradient(135deg, ${hex}, color-mix(in srgb, ${hex} 75%, black))`;
                }
                if (previewBadge) {
                    previewBadge.style.backgroundColor = `color-mix(in srgb, ${hex} 15%, white)`;
                    previewBadge.style.color = `color-mix(in srgb, ${hex} 75%, black)`;
                }

                // Auto-guardado en backend con debounce
                if (shouldSave) {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        autoSaveColor(hex);
                    }, 200);
                }
            }

            // Click en swatch rápido -> aplica y auto-guarda instantáneamente
            swatches.forEach(swatch => {
                swatch.addEventListener('click', function() {
                    applyColorTheme(this.dataset.color, true);
                });
            });

            // Cambio en color picker
            colorPicker.addEventListener('input', function() {
                applyColorTheme(this.value, false); // Actualizar UI en vivo
            });
            colorPicker.addEventListener('change', function() {
                applyColorTheme(this.value, true); // Auto-guardar al soltar
            });

            // Input manual en hex
            colorHex.addEventListener('input', function() {
                if (this.value.length === 7 && this.value.startsWith('#')) {
                    applyColorTheme(this.value, true);
                }
            });

            window.resetDefaultColor = function() {
                applyColorTheme('#2ecc71', true);
            };

            // Inicializar estado activo sin re-guardar
            applyColorTheme(colorHex.value || '#2ecc71', false);
        });
    </script>
</body>
</html>
