<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8; color: #333; margin: 0; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 5px solid #2ecc71; }
        h1 { color: #2c3e50; margin-top: 0; }
        .success { color: #27ae60; font-weight: 600; font-size: 1.1em; padding: 15px; background: #e8f8f5; border-radius: 5px; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; transition: background 0.3s; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido a NutriSalud</h1>
        <div class="success">✅ ¡La arquitectura MVC está funcionando correctamente!</div>
        <p>Este es tu <b>Dashboard (Vista)</b>. Has llegado aquí a través del <code>DashboardController</code> procesado por el <code>Router</code>.</p>
        <p>A partir de aquí, puedes empezar a crear tus otros controladores como <code>PacienteController</code> o <code>PlanController</code>.</p>
        
        <a href="test.php" class="btn">Ver Prueba POO Original</a>
    </div>
</body>
</html>
