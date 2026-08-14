<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Creador de Informes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .form-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .btn-gradient { background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; border: none; font-weight: 600; border-radius: 50px; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold">Creador de Informes (JSON Builder)</h2>
            <a href="index.php?action=listar_informes" class="btn btn-outline-secondary rounded-pill">Volver</a>
        </div>
        
        <form action="index.php?action=guardar_informe" method="POST" class="form-card p-5">
            <h4 class="fw-bold text-success mb-3"><i class="fa-solid fa-user"></i> Datos Base</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Seleccionar Paciente</label>
                    <select name="id_paciente" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?= $p['IdPaciente'] ?>"><?= htmlspecialchars($p['Nombre'].' '.$p['Apellido']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Objetivo General</label>
                <input type="text" name="objetivo_general" class="form-control" placeholder="Ej: Descenso de peso y recomposición">
            </div>

            <div class="mb-5">
                <label class="form-label fw-bold">Recordatorio Clave</label>
                <input type="text" name="recordatorio_clave" class="form-control" placeholder="Ej: Beber 2L de agua diarios">
            </div>

            <hr class="mb-4">

            <h4 class="fw-bold text-success mb-3"><i class="fa-solid fa-check-circle"></i> Diagnóstico Positivo</h4>
            <div id="diagnosticos-container">
                <div class="input-group mb-2">
                    <input type="text" name="diag_texto[]" class="form-control" placeholder="Hábito positivo (ej: Toma desayuno todos los días)">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="addDiag()">+ Agregar otro hábito</button>

            <hr class="mb-4">

            <h4 class="fw-bold text-success mb-3"><i class="fa-solid fa-list-check"></i> Ejes de Trabajo (Pilares)</h4>
            <div id="ejes-container">
                <div class="border rounded p-3 mb-3 bg-light">
                    <input type="text" name="eje_titulo[]" class="form-control mb-2 fw-bold" placeholder="Título (Ej: Más fibra)" required>
                    <input type="text" name="eje_subtitulo[]" class="form-control mb-2" placeholder="Subtítulo explicativo">
                    <textarea name="eje_acciones[]" class="form-control" rows="2" placeholder="Acciones concretas (una por línea)"></textarea>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mb-4" onclick="addEje()">+ Agregar otro pilar</button>

            <hr class="mb-4">

            <h4 class="fw-bold text-success mb-3"><i class="fa-solid fa-signature"></i> Cierre</h4>
            <div class="mb-4">
                <label class="form-label fw-bold">Mensaje Motivacional</label>
                <input type="text" name="mensaje_motivacional" class="form-control" value="Los cambios pequeños y sostenidos generan grandes resultados. ¡Vos podés lograrlo!">
            </div>

            <button type="submit" class="btn btn-gradient w-100 py-3 mt-3">Guardar y Generar Informe JSON</button>
        </form>
    </div>

    <script>
        function addDiag() {
            const container = document.getElementById('diagnosticos-container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = '<input type="text" name="diag_texto[]" class="form-control" placeholder="Hábito positivo">';
            container.appendChild(div);
        }
        function addEje() {
            const container = document.getElementById('ejes-container');
            const div = document.createElement('div');
            div.className = 'border rounded p-3 mb-3 bg-light';
            div.innerHTML = `
                <input type="text" name="eje_titulo[]" class="form-control mb-2 fw-bold" placeholder="Título" required>
                <input type="text" name="eje_subtitulo[]" class="form-control mb-2" placeholder="Subtítulo">
                <textarea name="eje_acciones[]" class="form-control" rows="2" placeholder="Acciones (una por línea)"></textarea>
            `;
            container.appendChild(div);
        }
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
