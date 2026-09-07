<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Login Paciente</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3498db;
            --dark-blue: #2980b9;
            --light-blue: #ebf5fb;
            --text-dark: #2c3e50;
            --text-gray: #7f8c8d;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--light-blue) 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
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
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            padding: 40px 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        /* Curva decorativa en el header */
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
            color: var(--primary-blue);
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
            border-color: var(--primary-blue);
            background-color: white;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1) !important;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            border: none;
            font-weight: 600;
            padding: 14px;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
            width: 100%;
            font-size: 1.1rem;
            margin-top: 15px;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.4);
            color: white;
        }

        .back-link {
            color: var(--text-gray);
            transition: color 0.3s;
        }
        .back-link:hover { color: var(--primary-blue); }

    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="login-card">
            <div class="login-header">
                <i class="fa-solid fa-apple-whole fs-1 mb-2"></i>
                <h3 class="fw-bold mb-1">NutriSalud</h3>
                <p class="mb-0 text-white-50" style="font-weight: 500;">Portal de Pacientes</p>
            </div>
            <div class="login-body">
                <h5 class="text-center text-dark mb-4 fw-bold fs-4">Iniciar Sesión</h5>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger text-center shadow-sm border-0 py-2" style="border-radius: 10px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=procesar_login_paciente" method="POST">
                    <div class="mb-4">
                        <label class="form-label">DNI</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-id-card"></i></span>
                            <input type="text" name="dni" class="form-control rounded-end-pill" placeholder="Tu número de documento" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contraseña (o Correo si no la cambiaste)</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="email" class="form-control" placeholder="Ingresa tu contraseña o email" required style="border-right: none;">
                            <button type="button" class="btn btn-toggle-password rounded-end-pill input-group-text" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-end mb-4">
                        <a href="index.php?action=recuperar_password&tipo=paciente" class="text-primary text-decoration-none small fw-medium">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button type="submit" class="btn-gradient mb-4">
                        Ingresar <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>
                <div class="text-center">
                    <a href="index.php?action=landing" class="text-decoration-none small fw-medium back-link">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver a la página principal
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
