<!-- views/auth/recuperar_password.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Recuperar Contraseña</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #2ecc71;
            --dark-green: #27ae60;
            --light-green: #eafaf1;
            --text-dark: #2c3e50;
            --text-gray: #7f8c8d;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--light-green) 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            padding: 20px;
        }
        
        .login-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            background: white;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            padding: 35px 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 40px;
            background: white;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        
        .login-body {
            padding: 30px 40px 40px 40px;
            background-color: white;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #e1e8ed;
            color: var(--primary-green);
            border-right: none;
            padding-left: 20px;
        }

        .form-control {
            background-color: #f8fafc;
            border: 1px solid #e1e8ed;
            border-left: none;
            padding: 14px 15px 14px 0;
            font-size: 0.95rem;
            box-shadow: none !important;
            transition: all 0.3s;
        }

        .form-control:focus, .input-group:focus-within .input-group-text {
            border-color: var(--primary-green);
            background-color: white;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1) !important;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 14px;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.3);
            width: 100%;
            font-size: 1.05rem;
            margin-top: 15px;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(46, 204, 113, 0.4);
            color: white;
        }

        .back-link {
            color: var(--text-gray);
            transition: color 0.3s;
        }
        .back-link:hover { color: var(--primary-green); }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="login-card">
            <div class="login-header">
                <i class="fa-solid fa-key fs-1 mb-2"></i>
                <h3 class="fw-bold mb-1">Recuperar Acceso</h3>
                <p class="mb-0 text-white-50" style="font-weight: 500;">NutriSalud Seguridad</p>
            </div>
            <div class="login-body">
                <h5 class="text-center text-dark mb-2 fw-bold fs-5">¿Olvidaste tu Contraseña?</h5>
                <p class="text-center text-muted small mb-4">Ingresa tu correo electrónico y te enviaremos un enlace seguro para restablecerla.</p>
                
                <?php if (!empty($mensajeExito)): ?>
                    <div class="alert alert-success shadow-sm border-0 py-3" style="border-radius: 12px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-check fs-4 text-success"></i>
                            <div>
                                <strong class="d-block">¡Solicitud Procesada!</strong>
                                <span class="small"><?= htmlspecialchars($mensajeExito) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center shadow-sm border-0 py-2" style="border-radius: 10px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($mensajeExito)): ?>
                <form action="index.php?action=procesar_solicitar_recuperar" method="POST">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($_GET['tipo'] ?? 'nutri') ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Cuenta</label>
                        <select name="tipo_usuario" class="form-select shadow-sm" style="border-radius: 12px; padding: 12px;">
                            <option value="nutricionista" <?= (($_GET['tipo'] ?? '') === 'nutri') ? 'selected' : '' ?>>Soy Nutricionista / Profesional</option>
                            <option value="paciente" <?= (($_GET['tipo'] ?? '') === 'paciente') ? 'selected' : '' ?>>Soy Paciente</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Correo Electrónico Registrado</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control rounded-end-pill" placeholder="tu@correo.com" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-gradient mb-4">
                        <i class="fa-solid fa-paper-plane me-2"></i> Enviar Enlace de Recuperación
                    </button>
                </form>
                <?php endif; ?>

                <div class="text-center pt-2">
                    <?php if (($_GET['tipo'] ?? '') === 'paciente'): ?>
                        <a href="index.php?action=login_paciente" class="text-decoration-none small fw-medium back-link">
                            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Iniciar Sesión Paciente
                        </a>
                    <?php else: ?>
                        <a href="index.php?action=login_nutri" class="text-decoration-none small fw-medium back-link">
                            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Iniciar Sesión Profesional
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
