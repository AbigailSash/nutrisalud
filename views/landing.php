<!-- views/landing.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Plataforma Premium para Nutricionistas</title>
    
    <!-- Google Fonts: Outfit for a modern, sleek look -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #10b981; /* Emerald 500 */
            --primary-dark: #059669; /* Emerald 600 */
            --primary-light: #d1fae5; /* Emerald 100 */
            --secondary: #3b82f6; /* Blue 500 */
            --dark: #0f172a; /* Slate 900 */
            --text: #334155; /* Slate 700 */
            --text-light: #64748b; /* Slate 500 */
            --bg: #f8fafc; /* Slate 50 */
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--text);
            background-color: var(--bg);
            overflow-x: hidden;
        }

        /* Glassmorphism Navbar */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 15px 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--dark) !important;
            letter-spacing: -0.5px;
        }
        
        .navbar-brand i { 
            color: var(--primary);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--text) !important;
            margin: 0 15px;
            position: relative;
            transition: color 0.3s;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after { width: 100%; }

        /* Buttons */
        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: var(--dark);
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 50px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            z-index: -1;
            transition: opacity 0.4s ease;
            opacity: 0;
        }

        .btn-gradient:hover::before { opacity: 1; }
        .btn-gradient:hover {
            color: white;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }

        /* Hero Section with Animated Background */
        .hero-section {
            padding: 140px 0 100px;
            position: relative;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .hero-blob {
            position: absolute;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out alternate;
        }
        .blob-1 { top: -10%; left: -5%; width: 400px; height: 400px; background: var(--primary-light); }
        .blob-2 { bottom: -10%; right: -5%; width: 500px; height: 500px; background: rgba(59, 130, 246, 0.15); animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 50px) scale(1.1); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 25px;
            color: var(--dark);
            letter-spacing: -1px;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-light);
            margin-bottom: 40px;
            font-weight: 400;
            max-width: 90%;
        }

        /* Glassmorphism Image Display */
        .hero-image-wrapper {
            position: relative;
            z-index: 1;
            border-radius: 24px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 30px 60px rgba(0,0,0,0.08);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s ease;
        }

        .hero-image-wrapper:hover {
            transform: perspective(1000px) rotateY(0deg) translateY(-10px);
        }

        .hero-image-wrapper img {
            border-radius: 16px;
            width: 100%;
            height: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Features Section */
        .features-section { padding: 100px 0; background-color: white; position: relative; }
        
        .section-header {
            text-align: center;
            margin-bottom: 70px;
        }
        
        .section-tag {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
        }

        .feature-card {
            background: var(--bg);
            border: 1px solid rgba(0,0,0,0.03);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: left;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            height: 100%;
            z-index: 1;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--primary-light), transparent);
            opacity: 0;
            z-index: -1;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .feature-card:hover::before { opacity: 0.5; }

        .icon-box {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
            transition: transform 0.4s ease;
        }

        .feature-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }
        .feature-title { font-weight: 700; font-size: 1.3rem; margin-bottom: 15px; color: var(--dark); }
        .feature-text { color: var(--text-light); font-size: 1rem; line-height: 1.6; }

        /* Modern Portals Section */
        .portals-section { 
            padding: 100px 0; 
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            position: relative;
            overflow: hidden;
        }

        .portal-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            color: white;
            transition: all 0.4s ease;
        }

        .portal-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .portal-icon { 
            font-size: 3.5rem; 
            margin-bottom: 25px; 
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .portal-title { font-weight: 700; font-size: 1.8rem; margin-bottom: 20px; }
        
        .portal-btn {
            background-color: white;
            color: var(--dark);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 25px;
            transition: all 0.3s;
        }
        
        .portal-btn:hover { 
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3); 
            transform: translateY(-3px); 
        }

        /* Footer */
        .footer {
            background-color: white;
            padding: 60px 0 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .footer-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--dark);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--bg);
            border-radius: 50%;
            color: var(--text);
            transition: all 0.3s;
            margin: 0 5px;
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Animations */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s forwards ease-out;
        }
        
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- 1. NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa-solid fa-leaf"></i> NutriSalud
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Herramientas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Comunidad</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="index.php?action=login_paciente" class="btn btn-glass">Portal Pacientes</a>
                    <a href="index.php?action=login_nutri" class="btn btn-gradient">Acceso Profesionales</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <section class="hero-section overflow-hidden">
        <div class="hero-blob blob-1"></div>
        <div class="hero-blob blob-2"></div>
        
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start fade-up">
                    <span class="badge bg-white text-primary border border-success-subtle px-3 py-2 rounded-pill mb-4 shadow-sm">
                        <i class="fa-solid fa-bolt me-1"></i> Nueva Generación SaaS 2.0
                    </span>
                    <h1 class="hero-title">
                        Gestión inteligente para <span class="text-gradient">nutricionistas</span> modernos
                    </h1>
                    <p class="hero-subtitle delay-1 fade-up">
                        Automatiza tus planes, agiliza tus turnos y mejora la adherencia de tus pacientes con la plataforma más avanzada y elegante del mercado.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3 delay-2 fade-up">
                        <a href="index.php?action=login_nutri" class="btn btn-gradient btn-lg">
                            Comienza Ahora <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 delay-3 fade-up">
                    <div class="hero-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Nutricionista moderna">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. FEATURES -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header fade-up">
                <span class="section-tag">Todo en uno</span>
                <h2 class="section-title">Herramientas de Nivel Profesional</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="icon-box"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                        <h4 class="feature-title">Planes Dinámicos</h4>
                        <p class="feature-text">Calcula macros y arma planes alimentarios atómicos en minutos. Integración perfecta con plantillas predefinidas.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up delay-1">
                        <div class="icon-box"><i class="fa-regular fa-calendar-check"></i></div>
                        <h4 class="feature-title">Turnero Inteligente</h4>
                        <p class="feature-text">Permite a tus pacientes agendar sus citas online, reduciendo ausencias y optimizando tu valioso tiempo.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up delay-2">
                        <div class="icon-box"><i class="fa-solid fa-chart-pie"></i></div>
                        <h4 class="feature-title">Evolución Detallada</h4>
                        <p class="feature-text">Gráficos antropométricos, seguimiento de bioimpedancia y adherencia al plan en tiempo real.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. PORTALS -->
    <section class="portals-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="portal-card fade-up">
                        <i class="fa-solid fa-user-doctor portal-icon"></i>
                        <h3 class="portal-title">Panel para Profesionales</h3>
                        <p class="mb-4 text-light opacity-75">Gestiona toda tu clínica, accede a historiales, agenda y plantillas de dietas con tecnología avanzada y seguridad total.</p>
                        <a href="index.php?action=login_nutri" class="portal-btn">Acceder al Panel <i class="fa-solid fa-chevron-right ms-1"></i></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="portal-card fade-up delay-1">
                        <i class="fa-solid fa-mobile-screen portal-icon"></i>
                        <h3 class="portal-title">Portal de Pacientes</h3>
                        <p class="mb-4 text-light opacity-75">El lugar donde tus pacientes revisan su plan alimentario, próximos turnos y registran sus avances desde su celular.</p>
                        <a href="index.php?action=login_paciente" class="portal-btn">Ver Mi Progreso <i class="fa-solid fa-chevron-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 mb-4 mb-md-0 text-center text-md-start">
                    <div class="footer-brand"><i class="fa-solid fa-leaf text-success me-1"></i> NutriSalud</div>
                    <p class="text-muted small mt-2 mb-0">Elevando la nutrición clínica con tecnología.</p>
                </div>
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <a href="#" class="text-muted text-decoration-none mx-2 small">Términos</a>
                    <a href="#" class="text-muted text-decoration-none mx-2 small">Privacidad</a>
                    <a href="#" class="text-muted text-decoration-none mx-2 small">Soporte</a>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="text-center mt-4 pt-4 border-top">
                <p class="text-muted small mb-0">&copy; <?php echo date("Y"); ?> NutriSalud SaaS. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
