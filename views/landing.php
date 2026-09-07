<!-- views/landing.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Plataforma Integral de Nutrición & Bienestar</title>
    
    <!-- Google Fonts: Outfit & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6.4.0 (Gastronomía, Nutrición y Salud) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Paleta Verde Orgánica y Saludable Institucional */
            --primary-green: #2ecc71;
            --primary-dark: #27ae60;
            --dark-green: #1e824c;
            --light-green: #eafaf1;
            --subtle-green: #f0fdf4;
            --accent-orange: #f39c12;
            --accent-blue: #3498db;
            --dark: #1e293b;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-border: rgba(46, 204, 113, 0.15);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', 'Outfit', sans-serif;
            color: var(--text-dark);
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Glassmorphism Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(46, 204, 113, 0.12);
            padding: 14px 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.7rem;
            color: var(--dark) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .navbar-brand .brand-icon { 
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
            color: white;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            margin: 0 10px;
            position: relative;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary-green);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after { width: 100%; }
        .nav-link:hover { color: var(--primary-dark) !important; }

        /* Botones Estilizados */
        .btn-portal-paciente {
            background-color: #ffffff;
            border: 1.5px solid var(--primary-green);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 9px 20px;
            border-radius: 50px;
            transition: all 0.3s;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-portal-paciente:hover {
            background-color: var(--light-green);
            color: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(46, 204, 113, 0.15);
        }

        .btn-gradient-nutri {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
            color: white !important;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 18px rgba(46, 204, 113, 0.35);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .btn-gradient-nutri:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 24px rgba(39, 174, 96, 0.45);
            color: white !important;
        }

        /* Hero Section */
        .hero-section {
            padding: 130px 0 90px;
            position: relative;
            background: linear-gradient(180deg, var(--subtle-green) 0%, #ffffff 100%);
            overflow: hidden;
        }

        .hero-floating-item {
            position: absolute;
            filter: drop-shadow(0 15px 25px rgba(46, 204, 113, 0.12));
            z-index: 0;
            opacity: 0.55;
            animation: floatSlow 8s infinite ease-in-out alternate;
        }

        .float-1 { top: 12%; left: 3%; font-size: 3rem; color: #2ecc71; animation-delay: 0s; }
        .float-2 { bottom: 15%; left: 8%; font-size: 2.5rem; color: #f39c12; animation-delay: -3s; }
        .float-3 { top: 18%; right: 4%; font-size: 2.8rem; color: #3498db; animation-delay: -5s; }
        .float-4 { bottom: 20%; right: 7%; font-size: 3rem; color: #e67e22; animation-delay: -2s; }

        @keyframes floatSlow {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-20px) rotate(8deg); }
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: var(--primary-dark);
            border: 1px solid rgba(46, 204, 113, 0.3);
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .text-gradient-green {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 35px;
            line-height: 1.65;
            max-width: 580px;
        }

        .hero-visual-card {
            background: white;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 25px 60px rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.2);
            position: relative;
            z-index: 1;
        }

        .hero-badge-floating {
            position: absolute;
            background: white;
            border-radius: 16px;
            padding: 12px 18px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            border: 1px solid rgba(46, 204, 113, 0.25);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
        }
        .badge-pos-1 { bottom: -20px; left: -20px; }
        .badge-pos-2 { top: -20px; right: -15px; }

        /* Sección de Métricas Rápidas */
        .stats-bar {
            background: white;
            border-top: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
            padding: 40px 0;
        }

        .stat-item {
            text-align: center;
        }
        .stat-icon {
            font-size: 2rem;
            color: var(--primary-green);
            margin-bottom: 8px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 2px;
        }
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Sección Features / Herramientas */
        .section-padding { padding: 90px 0; }
        
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }
        
        .section-tag {
            display: inline-block;
            background: var(--light-green);
            color: var(--primary-dark);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
        }

        .section-title {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .feature-card {
            background: white;
            border-radius: 22px;
            padding: 35px 28px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid #edf2f7;
            transition: all 0.35s ease;
            height: 100%;
            position: relative;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 45px rgba(46, 204, 113, 0.12);
            border-color: rgba(46, 204, 113, 0.35);
        }

        .icon-box-thematic {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--light-green), #ffffff);
            border: 1.5px solid rgba(46, 204, 113, 0.25);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 22px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .icon-box-thematic {
            transform: scale(1.1) rotate(6deg);
            background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
            color: white;
        }

        /* SECCIÓN SHOWCASE: Vitrina Interactiva de la App de Pacientes */
        .showcase-section {
            background: linear-gradient(180deg, #ffffff 0%, var(--subtle-green) 50%, #ffffff 100%);
            position: relative;
        }

        .showcase-card {
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(46, 204, 113, 0.2);
            box-shadow: 0 20px 50px rgba(46, 204, 113, 0.08);
            overflow: hidden;
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .showcase-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(46, 204, 113, 0.18);
            border-color: var(--primary-green);
        }

        .showcase-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--light-green), #ffffff);
            border-bottom: 1px solid rgba(46, 204, 113, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .showcase-mockup-body {
            padding: 24px;
            flex-grow: 1;
        }

        .mockup-meal-row {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 10px;
            border-left: 4px solid var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .water-tracker-container {
            background: #ebf8ff;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            border: 1px solid #bee3f8;
            margin-bottom: 15px;
        }

        .water-glasses-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 12px 0;
            flex-wrap: wrap;
        }

        .water-glass-icon {
            font-size: 1.4rem;
            color: #3182ce;
            transition: transform 0.2s;
        }

        .water-glass-icon.empty {
            color: #cbd5e0;
        }

        .chat-bubble-mockup {
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 12px;
            font-size: 0.88rem;
        }

        .chat-nutri {
            background: var(--light-green);
            border-left: 3px solid var(--primary-green);
            color: var(--dark-green);
        }

        .chat-paciente {
            background: #f1f5f9;
            color: var(--text-dark);
            text-align: right;
        }

        /* Sección Portales */
        .portals-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 90px 0;
        }

        .portal-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 26px;
            padding: 45px 35px;
            text-align: center;
            transition: all 0.35s ease;
            height: 100%;
        }

        .portal-box:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(46, 204, 113, 0.4);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .portal-icon-large {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 20px;
        }

        /* Footer */
        .footer-main {
            background: white;
            padding: 70px 0 30px;
            border-top: 1px solid #edf2f7;
        }

        .social-link-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--bg-light);
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            text-decoration: none;
            margin-right: 8px;
        }

        .social-link-btn:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-3px);
        }
    </style>
</head>
<body id="inicio">

    <!-- 1. BARRA DE NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#inicio">
                <span class="brand-icon"><i class="fa-solid fa-seedling"></i></span>
                <span>NutriSalud</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Navegación">
                <i class="fa-solid fa-bars text-success fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#inicio"><i class="fa-solid fa-house-chimney me-1 text-success"></i> Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#herramientas"><i class="fa-solid fa-utensils me-1 text-success"></i> Herramientas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#showcase"><i class="fa-solid fa-mobile-screen-button me-1 text-success"></i> App Pacientes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#beneficios"><i class="fa-solid fa-heart-pulse me-1 text-success"></i> Beneficios</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <a href="index.php?action=login_paciente" class="btn btn-portal-paciente">
                        <i class="fa-solid fa-user"></i> Portal Pacientes
                    </a>
                    <a href="index.php?action=login_nutri" class="btn btn-gradient-nutri">
                        <i class="fa-solid fa-stethoscope"></i> Acceso Profesionales
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <section class="hero-section">
        <!-- Íconos Flotantes Alegóricos a Nutrición & Bienestar -->
        <i class="fa-solid fa-apple-whole hero-floating-item float-1" aria-hidden="true"></i>
        <i class="fa-solid fa-carrot hero-floating-item float-2" aria-hidden="true"></i>
        <i class="fa-solid fa-glass-water hero-floating-item float-3" aria-hidden="true"></i>
        <i class="fa-solid fa-lemon hero-floating-item float-4" aria-hidden="true"></i>

        <div class="container position-relative" style="z-index: 1;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <div class="hero-pill">
                        <i class="fa-solid fa-leaf text-success"></i> Ecosistema Clínico & Nutricional 100% Integrado
                    </div>
                    <h1 class="hero-title">
                        Nutrición inteligente y bienestar para <span class="text-gradient-green">profesionales y pacientes</span>
                    </h1>
                    <p class="hero-subtitle">
                        Diseña planes de alimentación en minutos, automatiza el cálculo de requerimientos energéticos y brinda a tus pacientes una app interactiva para seguir su plan día a día.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        <a href="index.php?action=login_nutri" class="btn btn-gradient-nutri btn-lg px-4 py-3 fs-6">
                            <i class="fa-solid fa-rocket me-2"></i> Comenzar Ahora (Gratis)
                        </a>
                        <a href="#showcase" class="btn btn-outline-secondary rounded-pill px-4 py-3 fw-semibold fs-6">
                            <i class="fa-solid fa-eye me-2"></i> Ver App de Pacientes
                        </a>
                    </div>
                </div>

                <!-- Mockup / Visual Hero Card -->
                <div class="col-lg-6">
                    <div class="hero-visual-card">
                        <!-- Floating Badge 1 -->
                        <div class="hero-badge-floating badge-pos-1">
                            <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-weight-scale fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 0.75rem;">ANTROPOMETRÍA OMS</small>
                                <span class="fw-bold text-dark fs-6">IMC & ICC Automático</span>
                            </div>
                        </div>

                        <!-- Floating Badge 2 -->
                        <div class="hero-badge-floating badge-pos-2">
                            <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                                <i class="fa-solid fa-fire-flame-curved fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 0.75rem;">CÁLCULO CALÓRICO</small>
                                <span class="fw-bold text-dark fs-6">GEB Mifflin & FAO</span>
                            </div>
                        </div>

                        <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Alimentación saludable y nutrición equilibrada" class="img-fluid rounded-4 shadow-sm w-100" style="object-fit: cover; max-height: 380px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. BARRA DE IMPACTO / MÉTRICAS -->
    <section class="stats-bar">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3 stat-item">
                    <i class="fa-solid fa-plate-wheat stat-icon"></i>
                    <div class="stat-number">+10.000</div>
                    <div class="stat-label">Planes Prescritos</div>
                </div>
                <div class="col-6 col-md-3 stat-item">
                    <i class="fa-solid fa-heart-pulse stat-icon text-danger"></i>
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Adherencia de Pacientes</div>
                </div>
                <div class="col-6 col-md-3 stat-item">
                    <i class="fa-solid fa-user-doctor stat-icon"></i>
                    <div class="stat-number">+500</div>
                    <div class="stat-label">Nutricionistas Activos</div>
                </div>
                <div class="col-6 col-md-3 stat-item">
                    <i class="fa-solid fa-clock-rotate-left stat-icon text-warning"></i>
                    <div class="stat-number">-65%</div>
                    <div class="stat-label">Tiempo en Consultas</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. SECCIÓN HERRAMIENTAS CLÍNICAS -->
    <section id="herramientas" class="section-padding">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Todo en una sola plataforma</span>
                <h2 class="section-title">Herramientas Especializadas para Nutrición Clínica</h2>
                <p class="text-muted">Diseñado por y para nutricionistas, contemplando el flujo de trabajo real del consultorio presencial y virtual.</p>
            </div>

            <div class="row g-4">
                <!-- Card 1: Planes y Menús -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-box-thematic">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Diseñador de Menús Atómicos</h4>
                        <p class="text-muted small lh-lg mb-0">
                            Crea distribuciones semanales por momentos del día (Desayuno, Almuerzo, Merienda, Cena) con macronutrientes calculados y descarga en PDF A4 profesional.
                        </p>
                    </div>
                </div>

                <!-- Card 2: Antropometría & Fórmulas -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-box-thematic">
                            <i class="fa-solid fa-weight-scale"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Evaluación Antropométrica OMS</h4>
                        <p class="text-muted small lh-lg mb-0">
                            Cálculo reactivo de IMC, relación cintura/cadera (ICC), peso ideal por Lorentz y gasto energético por Mifflin-St Jeor y FAO/OMS en tiempo real.
                        </p>
                    </div>
                </div>

                <!-- Card 3: Educación Nutricional -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-box-thematic">
                            <i class="fa-solid fa-book-medical"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Guías & Educación Nutricional</h4>
                        <p class="text-muted small lh-lg mb-0">
                            Adjunta folletos educativos, pautas de hidratación y técnicas de cocción saludable directamente integrados dentro del plan alimentario del paciente.
                        </p>
                    </div>
                </div>

                <!-- Card 4: Agenda y Turnos -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-box-thematic">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Gestión de Turnos & Agenda</h4>
                        <p class="text-muted small lh-lg mb-0">
                            Organiza tus consultas diarias, reprograma citas con 1 clic y mantén sincronizados los recordatorios con tus pacientes para evitar ausencias.
                        </p>
                    </div>
                </div>

                <!-- Card 5: Suite Clínica Rápida -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-box-thematic">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Suite Clínica de Diagnóstico</h4>
                        <p class="text-muted small lh-lg mb-0">
                            Calculadora exprés de Nitrógeno Ureico Urinario (NUU), índice catabólico, conversor de electrolitos (mEq a mg) y curvas de crecimiento pediátricas.
                        </p>
                    </div>
                </div>

                <!-- Card 6: Marca Personal & Colores -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-box-thematic">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        <h4 class="fw-bold fs-5 mb-2 text-dark">Personalización con tu Marca</h4>
                        <p class="text-muted small lh-lg mb-0">
                            Personaliza la plataforma con los colores y logotipo de tu consultorio. Tus pacientes verán tu identidad visual en su portal y en las guías impresas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. SECCIÓN SHOWCASE: EXPERIENCIA DEL PACIENTE -->
    <section id="showcase" class="section-padding showcase-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fa-solid fa-mobile-screen me-1"></i> Vitrina de la App</span>
                <h2 class="section-title">Así Experimentan tus Pacientes su Plan Saludable</h2>
                <p class="text-muted">Una interfaz amigable, clara e intuitiva diseñada para motivar hábitos sostenibles desde cualquier dispositivo móvil o computadora.</p>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Mockup 1: Plan Nutricional / Menú Semanal -->
                <div class="col-lg-4 col-md-6">
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-utensils text-success fs-5"></i>
                                <span class="fw-bold small text-dark">Mi Plan de Hoy (Lunes)</span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Activo</span>
                        </div>
                        <div class="showcase-mockup-body">
                            <!-- Desayuno -->
                            <div class="mockup-meal-row">
                                <div>
                                    <small class="text-success fw-bold text-uppercase d-block" style="font-size: 0.72rem;"><i class="fa-regular fa-sun me-1"></i> Desayuno</small>
                                    <span class="fw-semibold text-dark small">Bowl de yogur con avena y frutos rojos</span>
                                </div>
                                <span class="badge bg-white border text-muted small">320 kcal</span>
                            </div>

                            <!-- Almuerzo -->
                            <div class="mockup-meal-row" style="border-left-color: #3498db;">
                                <div>
                                    <small class="text-primary fw-bold text-uppercase d-block" style="font-size: 0.72rem;"><i class="fa-solid fa-bowl-food me-1"></i> Almuerzo</small>
                                    <span class="fw-semibold text-dark small">Salmón grillado con quinoa y palta</span>
                                </div>
                                <span class="badge bg-white border text-muted small">540 kcal</span>
                            </div>

                            <!-- Merienda / Cena -->
                            <div class="mockup-meal-row" style="border-left-color: #f39c12;">
                                <div>
                                    <small class="text-warning fw-bold text-uppercase d-block" style="font-size: 0.72rem;"><i class="fa-solid fa-moon me-1"></i> Cena</small>
                                    <span class="fw-semibold text-dark small">Tortilla de espinacas y ensalada tibia</span>
                                </div>
                                <span class="badge bg-white border text-muted small">380 kcal</span>
                            </div>

                            <hr class="my-3 border-light">
                            <h6 class="fw-bold text-dark fs-6 mb-1">Acceso Fácil a tu Plan Nutricional</h6>
                            <p class="text-muted small mb-0">
                                Tu paciente consulta sus platos estructurados, porciones exactas e indicaciones personalizadas desde cualquier lugar y en cualquier momento.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mockup 2: Seguimiento de Progreso & Hidratación -->
                <div class="col-lg-4 col-md-6">
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-bottle-water text-primary fs-5"></i>
                                <span class="fw-bold small text-dark">Hábitos & Hidratación</span>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small">Hoy</span>
                        </div>
                        <div class="showcase-mockup-body">
                            <!-- Hidratación Widget -->
                            <div class="water-tracker-container">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small"><i class="fa-solid fa-droplet text-primary me-1"></i> Agua Consumida</span>
                                    <span class="fw-bold text-primary small">2.1 L / 2.5 L</span>
                                </div>
                                <div class="water-glasses-row">
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon"></i>
                                    <i class="fa-solid fa-glass-water water-glass-icon empty"></i>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 84%"></div>
                                </div>
                            </div>

                            <!-- Progreso Peso -->
                            <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mb-2 border">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">EVOLUCIÓN DE PESO</small>
                                    <span class="fw-bold text-dark">72.4 kg <small class="text-success fw-semibold">(-3.6 kg)</small></span>
                                </div>
                                <i class="fa-solid fa-chart-line text-success fs-4"></i>
                            </div>

                            <hr class="my-3 border-light">
                            <h6 class="fw-bold text-dark fs-6 mb-1">Monitoreo de Hábitos en Tiempo Real</h6>
                            <p class="text-muted small mb-0">
                                Registro interactivo de consumo de agua, cumplimiento de actividades y evolución física para mantener alta la motivación.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mockup 3: Feedback Directo & Turnos -->
                <div class="col-lg-4 col-md-6">
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-user-doctor text-success fs-5"></i>
                                <span class="fw-bold small text-dark">Tu Nutricionista a Cargo</span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">Online</span>
                        </div>
                        <div class="showcase-mockup-body">
                            <!-- Profesional Badge -->
                            <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3 border mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); font-size: 1.1rem;">
                                    LO
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">Lic. Leila Olmedo</span>
                                    <small class="text-muted">M.P. 310 • Nutrición Clínica</small>
                                </div>
                            </div>

                            <!-- Chat / Recordatorio -->
                            <div class="chat-bubble-mockup chat-nutri">
                                <i class="fa-solid fa-comment-dots me-1"></i> "¡Excelente avance esta semana! Recuerda sumar las semillas de chía al desayuno."
                            </div>

                            <!-- Próxima Cita -->
                            <div class="p-2 px-3 bg-white border rounded-3 d-flex justify-content-between align-items-center mb-3">
                                <div class="small">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">PRÓXIMO TURNO</small>
                                    <span class="fw-bold text-dark">Jueves 18 Sep • 16:30 hs</span>
                                </div>
                                <span class="badge bg-success"><i class="fa-solid fa-check"></i> Agendado</span>
                            </div>

                            <hr class="my-3 border-light">
                            <h6 class="fw-bold text-dark fs-6 mb-1">Contacto y Recordatorios Directos</h6>
                            <p class="text-muted small mb-0">
                                Canal de comunicación instantáneo por WhatsApp, gestión de próximas consultas y resolución ágil de dudas nutricionales.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. SECCIÓN BENEFICIOS / POR QUÉ ELEGIRNOS -->
    <section id="beneficios" class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="section-tag">Ventaja Competitiva</span>
                    <h2 class="section-title">El Software que Transforma tu Práctica Profesional</h2>
                    <p class="text-muted mb-4">
                        Deja atrás las planillas de cálculo desordenadas y los PDFs genéricos. NutriSalud te brinda una suite completa que eleva la percepción de valor de tus consultas.
                    </p>
                    
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success mt-1">
                            <i class="fa-solid fa-shield-halved fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Aislamiento y Privacidad Multi-Tenant</h6>
                            <p class="text-muted small mb-0">Tus datos y las historias clínicas de tus pacientes están 100% aisladas y protegidas bajo estrictos estándares de seguridad médica.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success mt-1">
                            <i class="fa-solid fa-envelope-circle-check fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Envío Automático de Credenciales</h6>
                            <p class="text-muted small mb-0">Al registrar a un paciente, el sistema le envía inmediatamente un correo electrónico con su usuario, clave y enlace de acceso a su portal.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success mt-1">
                            <i class="fa-solid fa-print fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Impresión Clínica A4 de Alta Calidad</h6>
                            <p class="text-muted small mb-0">Genera hojas de ruta dietéticas listas para imprimir o enviar en PDF con el membrete y colores de tu consultorio.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1543362906-acfc16c67564?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Nutricionista planificando dieta equilibrada" class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; max-height: 420px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. PORTALES DE ACCESO -->
    <section class="portals-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill mb-3">Accesos Directos</span>
                <h2 class="text-white fw-bold">Elige tu Punto de Entrada</h2>
                <p class="text-white-50">Ingresa a tu entorno según tu rol en la plataforma.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="portal-box">
                        <i class="fa-solid fa-user-doctor portal-icon-large"></i>
                        <h3 class="fw-bold fs-4 mb-3">Acceso Nutricionistas</h3>
                        <p class="text-white-50 small mb-4">Gestiona historias clínicas, recetas, cálculos calóricos, turnero y diseño de planes alimentarios.</p>
                        <a href="index.php?action=login_nutri" class="btn btn-gradient-nutri w-100 py-3 justify-content-center">
                            Ingresar al Consultorio <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5">
                    <div class="portal-box">
                        <i class="fa-solid fa-mobile-screen-button portal-icon-large text-primary"></i>
                        <h3 class="fw-bold fs-4 mb-3">Portal de Pacientes</h3>
                        <p class="text-white-50 small mb-4">Revisa tu menú diario, horarios de comidas, seguimiento de hábitos y próximos turnos con tu nutricionista.</p>
                        <a href="index.php?action=login_paciente" class="btn btn-outline-light rounded-pill w-100 py-3 justify-content-center fw-semibold">
                            Ver Mi Plan Nutricional <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FOOTER -->
    <footer class="footer-main">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-4 text-center text-lg-start">
                    <a class="navbar-brand mb-2 justify-content-center justify-content-lg-start" href="#inicio">
                        <span class="brand-icon"><i class="fa-solid fa-seedling"></i></span>
                        <span>NutriSalud</span>
                    </a>
                    <p class="text-muted small mb-0 mt-2">Tecnología y bienestar gastronómico al servicio de la nutrición clínica.</p>
                </div>
                
                <div class="col-lg-4 text-center">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none small fw-medium" data-bs-toggle="modal" data-bs-target="#modalTerminos">Términos del Servicio</a>
                        <span class="text-muted">•</span>
                        <a href="javascript:void(0)" class="text-muted text-decoration-none small fw-medium" data-bs-toggle="modal" data-bs-target="#modalPrivacidad">Privacidad</a>
                        <span class="text-muted">•</span>
                        <a href="javascript:void(0)" class="text-muted text-decoration-none small fw-medium" data-bs-toggle="modal" data-bs-target="#modalSoporte">Soporte</a>
                    </div>
                </div>

                <div class="col-lg-4 text-center text-lg-end">
                    <a href="https://instagram.com" target="_blank" class="social-link-btn" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://linkedin.com" target="_blank" class="social-link-btn" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="https://wa.me/" target="_blank" class="social-link-btn" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="text-center mt-4 pt-4 border-top">
                <p class="text-muted small mb-0">&copy; <?= date("Y") ?> NutriSalud SaaS. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- MODAL 1: TÉRMINOS DEL SERVICIO -->
    <div class="modal fade" id="modalTerminos" tabindex="-1" aria-labelledby="modalTerminosLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTerminosLabel"><i class="fa-solid fa-file-contract text-success me-2"></i> Términos del Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4 small text-muted lh-lg">
                    <h6>1. Uso Profesional</h6>
                    <p>NutriSalud es un software de asistencia clínica nutricional. Cada profesional habilitado es el único responsable de la prescripción dietética y diagnóstico de sus pacientes.</p>
                    <h6>2. Confidencialidad de Datos</h6>
                    <p>Los datos médicos y antropométricos ingresados en el sistema están protegidos por secreto profesional y encriptación de extremo a extremo.</p>
                    <h6>3. Disponibilidad</h6>
                    <p>Garantizamos una disponibilidad de plataforma del 99.9% para acceso ininterrumpido a historias clínicas y portales de pacientes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: POLÍTICA DE PRIVACIDAD -->
    <div class="modal fade" id="modalPrivacidad" tabindex="-1" aria-labelledby="modalPrivacidadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalPrivacidadLabel"><i class="fa-solid fa-shield-halved text-success me-2"></i> Política de Privacidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4 small text-muted lh-lg">
                    <h6>Protección de Historias Clínicas</h6>
                    <p>Cumplimos con las normativas internacionales de protección de datos de salud (HIPAA / GDPR compliance). La información de cada paciente solo es accesible por su nutricionista titular.</p>
                    <h6>Copias de Seguridad</h6>
                    <p>Se realizan respaldos automáticos diarios de todas las bases de datos para garantizar la integridad y recuperación de datos clínicos.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: SOPORTE TÉCNICO -->
    <div class="modal fade" id="modalSoporte" tabindex="-1" aria-labelledby="modalSoporteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                    <h5 class="modal-title fw-bold" id="modalSoporteLabel"><i class="fa-solid fa-headset me-2"></i> Soporte & Asistencia Clínica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted small mb-4">¿Tienes alguna duda técnica o necesitas asistencia para configurar tu consultorio? Nuestro equipo está listo para ayudarte.</p>
                    <div class="d-grid gap-2">
                        <a href="https://wa.me/" target="_blank" class="btn btn-success py-2 rounded-pill fw-semibold">
                            <i class="fa-brands fa-whatsapp me-2"></i> Contactar por WhatsApp Directo
                        </a>
                        <a href="mailto:soporte@nutrisalud.com" class="btn btn-outline-secondary py-2 rounded-pill fw-semibold">
                            <i class="fa-regular fa-envelope me-2"></i> Enviar Correo a Soporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle y Scripts Globales -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
