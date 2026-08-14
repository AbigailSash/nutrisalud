<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Editar Paciente</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
            --primary-blue: #3498db;
            --dark-blue: #2980b9;
            --text-dark: #2c3e50;
            --text-gray: #7f8c8d;
            --bg-light: #f4f7f6;
            --sidebar-bg: #1a252f;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light); 
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            padding-top: 2rem;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand i { color: var(--primary-green); margin-right: 10px; }

        .nav-sidebar .nav-link {
            color: #b8c7ce;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 5px;
            border-left: 4px solid transparent;
        }

        .nav-sidebar .nav-link:hover, .nav-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.05);
            border-left-color: var(--primary-green);
        }

        .nav-sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-title {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 30px;
            text-align: center;
        }

        .form-title i {
            color: var(--primary-blue);
        }

        /* Form Inputs Modernization */
        .form-label {
            font-weight: 500;
            color: var(--text-gray);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
            font-size: 0.95rem;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
            background-color: white;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            width: 100%;
            font-size: 1.1rem;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            color: white;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--primary-blue);
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'views/layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex align-items-center mb-4">
            <a href="index.php?action=listar_pacientes" class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-3">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="form-card">
            <h2 class="form-title"><i class="fa-solid fa-pen-to-square"></i> Editar Datos del Paciente</h2>
            
            <form action="index.php?action=actualizar_paciente" method="POST">
                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente['IdPaciente']) ?>">
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">DNI / Documento de Identidad</label>
                        <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($paciente['DNI'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($paciente['Nombre'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($paciente['Apellido'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($paciente['Fecha_Nacimiento'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Obra Social</label>
                        <input type="text" name="obra_social" class="form-control" value="<?= htmlspecialchars($paciente['Obra_Social'] ?? 'Particular') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Teléfono de Contacto</label>
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($paciente['Telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($paciente['Email'] ?? '') ?>">
                    </div>
                </div>

                <hr class="my-4">
                <h4 class="mb-3 text-secondary"><i class="fa-solid fa-scale-balanced"></i> Datos Antropométricos y Metabólicos</h4>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sexo Biológico</label>
                        <select name="sexo" class="form-control" id="calc_sexo">
                            <option value="M" <?= (isset($paciente['Sexo']) && $paciente['Sexo'] == 'M') ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= (isset($paciente['Sexo']) && $paciente['Sexo'] == 'F') ? 'selected' : '' ?>>Femenino</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Peso Actual (kg)</label>
                        <input type="number" step="0.1" name="peso" id="calc_peso" class="form-control" value="<?= htmlspecialchars($paciente['Peso'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estatura (cm)</label>
                        <input type="number" step="1" name="estatura" id="calc_estatura" class="form-control" value="<?= htmlspecialchars($paciente['Estatura'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nivel Actividad</label>
                        <select name="actividad" class="form-control" id="calc_naf">
                            <option value="1.2" <?= (isset($paciente['Actividad']) && $paciente['Actividad'] == 1.2) ? 'selected' : '' ?>>Sedentario (1.2)</option>
                            <option value="1.375" <?= (isset($paciente['Actividad']) && $paciente['Actividad'] == 1.375) ? 'selected' : '' ?>>Ligero (1.375)</option>
                            <option value="1.55" <?= (isset($paciente['Actividad']) && $paciente['Actividad'] == 1.55) ? 'selected' : '' ?>>Moderado (1.55)</option>
                            <option value="1.725" <?= (isset($paciente['Actividad']) && $paciente['Actividad'] == 1.725) ? 'selected' : '' ?>>Intenso (1.725)</option>
                        </select>
                    </div>
                </div>

                <!-- Botón para abrir el Modal de la Calculadora -->
                <div class="mb-4">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#calculadoraModal" onclick="actualizarCalculadoraInteractiva()">
                        <i class="fa-solid fa-calculator"></i> Ver Calculadora Nutricional
                    </button>
                </div>
                
                <div class="mt-2">
                    <button type="submit" class="btn btn-gradient">Actualizar Paciente <i class="fa-solid fa-arrows-rotate ms-2"></i></button>
                    <a href="index.php?action=listar_pacientes" class="back-link">Cancelar cambios</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Calculadora Nutricional -->
    <div class="modal fade" id="calculadoraModal" tabindex="-1" aria-labelledby="calculadoraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="calculadoraModalLabel"><i class="fa-solid fa-calculator"></i> Calculadora Nutricional del Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <!-- Valores Iniciales de PHP pasados a JS -->
                    <script>
                        const initialCalc = {
                            imc: <?= isset($calc_imc) ? json_encode($calc_imc) : '{"valor":0, "diagnostico":"N/A"}' ?>,
                            pesoIdeal: <?= $calc_pesoIdeal ?? 0 ?>,
                            geb: <?= $calc_geb ?? 0 ?>,
                            get: <?= $calc_get ?? 0 ?>,
                            edad: <?= $edad ?? 0 ?>
                        };
                    </script>
                    
                    <div class="row text-center mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-white shadow-sm rounded border-start border-4 border-primary">
                                <h6 class="text-muted mb-1">IMC</h6>
                                <h3 id="modal_imc_val" class="mb-0 text-primary">0</h3>
                                <small id="modal_imc_diag" class="fw-bold">N/A</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-white shadow-sm rounded border-start border-4 border-info">
                                <h6 class="text-muted mb-1">Peso Ideal</h6>
                                <h3 class="mb-0 text-info"><span id="modal_peso_ideal">0</span> <small class="fs-6">kg</small></h3>
                                <small class="text-muted">Fórmula Lorentz</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-white shadow-sm rounded border-start border-4 border-warning">
                                <h6 class="text-muted mb-1">GEB (Reposo)</h6>
                                <h3 class="mb-0 text-warning"><span id="modal_geb">0</span> <small class="fs-6">kcal</small></h3>
                                <small class="text-muted">Mifflin-St. Jeor</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-white shadow-sm rounded border-start border-4 border-success">
                                <h6 class="text-muted mb-1">GET (Total)</h6>
                                <h3 class="mb-0 text-success"><span id="modal_get">0</span> <small class="fs-6">kcal</small></h3>
                                <small class="text-muted">Gasto Diario Total</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i> 
                        Estos valores se recalculan dinámicamente si cambias el <strong>Peso</strong>, <strong>Estatura</strong>, <strong>Sexo</strong> o <strong>Nivel de Actividad</strong> en el formulario principal.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Opción de Auto-completar Plan (Simulado) -->
                    <button type="button" class="btn btn-success" onclick="alert('Funcionalidad para aplicar estas calorías a un nuevo plan en desarrollo.')">
                        <i class="fa-solid fa-check"></i> Usar GET como Objetivo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function actualizarCalculadoraInteractiva() {
            // Obtener valores del formulario
            const peso = parseFloat(document.getElementById('calc_peso').value) || 0;
            const estaturaCm = parseFloat(document.getElementById('calc_estatura').value) || 0;
            const sexo = document.getElementById('calc_sexo').value;
            const naf = parseFloat(document.getElementById('calc_naf').value) || 1.2;
            const edad = initialCalc.edad; // Mantenemos la edad del servidor por ahora
            
            // 1. Calcular IMC
            let imc = 0;
            let diag = "N/A";
            if (estaturaCm > 0 && peso > 0) {
                const estaturaM = estaturaCm / 100;
                imc = peso / (estaturaM * estaturaM);
                if (imc < 18.5) diag = "Bajo peso";
                else if (imc < 25) diag = "Normopeso";
                else if (imc < 30) diag = "Sobrepeso";
                else if (imc < 35) diag = "Obesidad I";
                else if (imc < 40) diag = "Obesidad II";
                else diag = "Obesidad III";
            }
            document.getElementById('modal_imc_val').innerText = imc.toFixed(1);
            document.getElementById('modal_imc_diag').innerText = diag;

            // 2. Peso Ideal (Lorentz)
            let pesoIdeal = 0;
            if (estaturaCm > 100) {
                if (sexo === 'M') {
                    pesoIdeal = (estaturaCm - 100) - ((estaturaCm - 150) / 4);
                } else {
                    pesoIdeal = (estaturaCm - 100) - ((estaturaCm - 150) / 2.5);
                }
            }
            document.getElementById('modal_peso_ideal').innerText = pesoIdeal.toFixed(1);

            // 3. GEB (Mifflin-St. Jeor)
            let geb = 0;
            if (peso > 0 && estaturaCm > 0 && edad > 0) {
                if (sexo === 'M') {
                    geb = (10 * peso) + (6.25 * estaturaCm) - (5 * edad) + 5;
                } else {
                    geb = (10 * peso) + (6.25 * estaturaCm) - (5 * edad) - 161;
                }
            }
            document.getElementById('modal_geb').innerText = Math.round(geb);

            // 4. GET
            const get = geb * naf;
            document.getElementById('modal_get').innerText = Math.round(get);
        }

        // Ligar eventos de cambio a los inputs para que se actualice en tiempo real si el modal está abierto
        document.getElementById('calc_peso').addEventListener('input', actualizarCalculadoraInteractiva);
        document.getElementById('calc_estatura').addEventListener('input', actualizarCalculadoraInteractiva);
        document.getElementById('calc_sexo').addEventListener('change', actualizarCalculadoraInteractiva);
        document.getElementById('calc_naf').addEventListener('change', actualizarCalculadoraInteractiva);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
