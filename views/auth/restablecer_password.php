<!-- views/auth/restablecer_password.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Restablecer Contraseña</title>
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
                <i class="fa-solid fa-shield-halved fs-1 mb-2"></i>
                <h3 class="fw-bold mb-1">Nueva Contraseña</h3>
                <p class="mb-0 text-white-50" style="font-weight: 500;">NutriSalud Seguridad</p>
            </div>
            <div class="login-body">
                
                <?php if (empty($tokenValido)): ?>
                    <!-- ESTADO: TOKEN EXPIRADO O INVÁLIDO -->
                    <div class="text-center py-3">
                        <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger d-inline-flex mb-3">
                            <i class="fa-solid fa-clock-rotate-left fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Enlace no Válido o Expirado</h4>
                        <p class="text-muted small mb-4">
                            Por motivos de seguridad, los enlaces de recuperación caducan automáticamente a los <strong>30 minutos</strong> o quedan invalidados tras ser utilizados.
                        </p>
                        <a href="index.php?action=recuperar_password" class="btn btn-gradient py-3">
                            <i class="fa-solid fa-rotate-right me-2"></i> Solicitar Nuevo Enlace
                        </a>
                        <div class="mt-4">
                            <a href="index.php?action=landing" class="text-decoration-none small text-muted">
                                <i class="fa-solid fa-arrow-left me-1"></i> Volver a la página principal
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- ESTADO: FORMULARIO DE RESTABLECIMIENTO -->
                    <h5 class="text-center text-dark mb-1 fw-bold fs-5">Crea tu Nueva Contraseña</h5>
                    <p class="text-center text-muted small mb-4">Ingresa tu nueva contraseña para la cuenta <strong><?= htmlspecialchars($tokenValido['Email']) ?></strong>.</p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center shadow-sm border-0 py-2 mb-3" style="border-radius: 10px;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?action=procesar_restablecer_password" method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="nueva_password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6" style="border-right: none;">
                                <button type="button" class="btn btn-toggle-password rounded-end-pill input-group-text" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="confirmar_password" class="form-control" placeholder="Repite tu nueva contraseña" required minlength="6" style="border-right: none;">
                                <button type="button" class="btn btn-toggle-password rounded-end-pill input-group-text" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient mb-4">
                            <i class="fa-solid fa-check me-2"></i> Guardar y Acceder
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="index.php?action=landing" class="text-decoration-none small fw-medium back-link">
                            <i class="fa-solid fa-arrow-left me-1"></i> Cancelar y volver
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
