<!-- views/landing.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud Íntegra - Plataforma Integral de Nutrición Clínica, SARA 2 & HEARTS</title>
    
    <!-- Google Fonts: Outfit & Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6.4.0 (Gastronomía, Nutrición, Cardiología y Salud) -->
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
            --accent-red: #e74c3c;
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
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(46, 204, 113, 0.15);
            padding: 12px 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.65rem;
            color: var(--dark) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand .brand-icon { 
            width: 42px;
            height: 42px;
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
            margin: 0 8px;
            position: relative;
            transition: all 0.3s;
            font-size: 0.92rem;
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

        /* Botones de Navegación */
        .btn-portal-paciente {
            background-color: #ffffff;
            border: 1.5px solid var(--primary-green);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 50px;
            transition: all 0.3s;
            font-size: 0.88rem;
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
            padding: 9px 22px;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 18px rgba(46, 204, 113, 0.32);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.92rem;
        }

        .btn-gradient-nutri:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 24px rgba(39, 174, 96, 0.45);
            color: white !important;
        }

        /* Hero Section */
        .hero-section {
            padding: 135px 0 80px;
            position: relative;
            background: linear-gradient(180deg, var(--subtle-green) 0%, #ffffff 100%);
            overflow: hidden;
        }

        .hero-floating-item {
            position: absolute;
            filter: drop-shadow(0 15px 25px rgba(46, 204, 113, 0.12));
            z-index: 0;
            opacity: 0.45;
            animation: floatSlow 8s infinite ease-in-out alternate;
        }

        .float-1 { top: 12%; left: 3%; font-size: 3rem; color: #2ecc71; animation-delay: 0s; }
        .float-2 { bottom: 15%; left: 6%; font-size: 2.5rem; color: #f39c12; animation-delay: -3s; }
        .float-3 { top: 16%; right: 4%; font-size: 2.8rem; color: #3498db; animation-delay: -5s; }
        .float-4 { bottom: 20%; right: 6%; font-size: 3rem; color: #e74c3c; animation-delay: -2s; }

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
            border: 1px solid rgba(46, 204, 113, 0.35);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.1);
            margin-bottom: 22px;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.18;
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
            font-size: 1.12rem;
            color: var(--text-muted);
            margin-bottom: 35px;
            line-height: 1.7;
            max-width: 600px;
        }

        .hero-visual-card {
            background: white;
            border-radius: 28px;
            padding: 22px;
            box-shadow: 0 25px 60px rgba(46, 204, 113, 0.15);
            border: 1px solid rgba(46, 204, 113, 0.25);
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
        .badge-pos-1 { bottom: -18px; left: -15px; }
        .badge-pos-2 { top: -18px; right: -15px; }

        /* Barra de Certificaciones Científicas */
        .scientific-bar {
            background: #ffffff;
            border-top: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
            padding: 24px 0;
        }

        .cert-badge-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: var(--bg-light);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.25s ease;
        }
        .cert-badge-item:hover {
            border-color: var(--primary-green);
            background: var(--light-green);
            transform: translateY(-2px);
        }
        .cert-badge-item i {
            font-size: 1.15rem;
        }

        /* Sección Switch de Audiencias (Nutricionistas vs Pacientes) */
        .audience-section {
            padding: 85px 0 75px;
            background: #ffffff;
        }

        .audience-toggle-container {
            display: inline-flex;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }

        .audience-btn {
            border: none;
            background: transparent;
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            color: #64748b;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .audience-btn.active {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.35);
        }

        .audience-card {
            background: white;
            border-radius: 24px;
            padding: 35px;
            border: 1px solid rgba(46, 204, 113, 0.2);
            box-shadow: 0 15px 40px rgba(0,0,0,0.04);
            height: 100%;
            transition: all 0.3s;
        }

        .audience-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px;
        }

        .audience-feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--light-green);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        /* Sección Showcase / Nuevas Features Show Don't Tell */
        .innovations-section {
            padding: 85px 0;
            background: linear-gradient(180deg, #ffffff 0%, var(--subtle-green) 50%, #ffffff 100%);
        }

        .innov-tab-btn {
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            text-align: left;
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 12px;
            width: 100%;
        }

        .innov-tab-btn:hover {
            border-color: var(--primary-green);
            background: var(--light-green);
        }

        .innov-tab-btn.active {
            background: white;
            border-left: 5px solid var(--primary-green);
            border-color: rgba(46, 204, 113, 0.3);
            box-shadow: 0 10px 25px rgba(46, 204, 113, 0.12);
        }

        .innov-preview-card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            border: 1px solid rgba(46, 204, 113, 0.25);
            box-shadow: 0 20px 50px rgba(46, 204, 113, 0.1);
        }

        /* Tabla Comparativa: Antes vs Con NutriSalud */
        .comparison-section {
            padding: 85px 0;
            background: #ffffff;
        }

        .comparison-table-wrapper {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 15px 45px rgba(0,0,0,0.04);
        }

        .comparison-header {
            padding: 24px 30px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .compare-row {
            padding: 18px 30px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
        }
        .compare-row:last-child { border-bottom: none; }
        .compare-row:nth-child(even) { background: #fafbfc; }

        .tag-antes {
            background: #fee2e2;
            color: #991b1b;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .tag-despues {
            background: var(--light-green);
            color: var(--dark-green);
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        /* General Features */
        .section-padding { padding: 85px 0; }
        
        .section-header {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 55px;
        }
        
        .section-tag {
            display: inline-block;
            background: var(--light-green);
            color: var(--primary-dark);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
        }

        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .feature-card {
            background: white;
            border-radius: 22px;
            padding: 32px 26px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid #edf2f7;
            transition: all 0.35s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 45px rgba(46, 204, 113, 0.12);
            border-color: rgba(46, 204, 113, 0.35);
        }

        .icon-box-thematic {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--light-green), #ffffff);
            border: 1.5px solid rgba(46, 204, 113, 0.25);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .icon-box-thematic {
            transform: scale(1.1) rotate(6deg);
            background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
            color: white;
        }

        /* Showcase App Pacientes */
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
            transform: translateY(-8px);
            box-shadow: 0 25px 55px rgba(46, 204, 113, 0.16);
            border-color: var(--primary-green);
        }

        .showcase-header {
            padding: 18px 22px;
            background: linear-gradient(135deg, var(--light-green), #ffffff);
            border-bottom: 1px solid rgba(46, 204, 113, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .showcase-mockup-body {
            padding: 22px;
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
            padding: 16px;
            text-align: center;
            border: 1px solid #bee3f8;
            margin-bottom: 15px;
        }

        .water-glasses-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 10px 0;
            flex-wrap: wrap;
        }

        .water-glass-icon {
            font-size: 1.3rem;
            color: #3182ce;
        }
        .water-glass-icon.empty { color: #cbd5e0; }

        .chat-bubble-mockup {
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 10px;
            font-size: 0.86rem;
        }
        .chat-nutri {
            background: var(--light-green);
            border-left: 3px solid var(--primary-green);
            color: var(--dark-green);
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
            width: 40px;
            height: 40px;
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

        @media (max-width: 991px) {
            .hero-title { font-size: 2.5rem; }
            .hero-section { padding: 110px 0 60px; }
        }
    </style>
</head>
<body id="inicio">

    <!-- ========================================================== -->
    <!-- 1. BARRA DE NAVEGACIÓN (HEADER CON DOBLE ACCESO)          -->
    <!-- ========================================================== -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#inicio">
                <span class="brand-icon"><i class="fa-solid fa-leaf"></i></span>
                <span>NutriSalud</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Navegación">
                <i class="fa-solid fa-bars text-success fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#inicio"><i class="fa-solid fa-house-chimney me-1 text-success"></i> Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#audiencias"><i class="fa-solid fa-users-viewfinder me-1 text-success"></i> Soluciones</a></li>
                    <li class="nav-item"><a class="nav-link" href="#innovaciones"><i class="fa-solid fa-microchip me-1 text-success"></i> Innovaciones</a></li>
                    <li class="nav-item"><a class="nav-link" href="#comparativa"><i class="fa-solid fa-scale-balanced me-1 text-success"></i> Comparativa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#showcase"><i class="fa-solid fa-mobile-screen-button me-1 text-success"></i> App Pacientes</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <a href="index.php?action=login_paciente" class="btn btn-portal-paciente">
                        <i class="fa-solid fa-user"></i> Ingreso Pacientes
                    </a>
                    <a href="index.php?action=login_nutri" class="btn btn-gradient-nutri">
                        <i class="fa-solid fa-stethoscope"></i> Acceso Profesional
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========================================================== -->
    <!-- 2. HERO SECTION (PRIMICIA & DOBLE AUDIENCIA)               -->
    <!-- ========================================================== -->
    <section class="hero-section">
        <i class="fa-solid fa-apple-whole hero-floating-item float-1" aria-hidden="true"></i>
        <i class="fa-solid fa-carrot hero-floating-item float-2" aria-hidden="true"></i>
        <i class="fa-solid fa-heart-pulse hero-floating-item float-3" aria-hidden="true"></i>
        <i class="fa-solid fa-flask-vial hero-floating-item float-4" aria-hidden="true"></i>

        <div class="container position-relative" style="z-index: 1;">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <div class="hero-pill">
                        <i class="fa-solid fa-shield-halved text-success"></i> 🚀 Suite Nutricional con Validación Científica Oficial
                    </div>
                    <h1 class="hero-title">
                        La plataforma que transforma la dietoterapia compleja en una <span class="text-gradient-green">experiencia digital simple</span>
                    </h1>
                    <p class="hero-subtitle">
                        Automatizá la Fórmula Desarrollada oficial (SARA 2), evaluá el Riesgo Cardiovascular HEARTS (OPS/OMS) y gestioná tu agenda con notificaciones automáticas, mientras tus pacientes siguen su plan día a día desde el celular.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        <a href="index.php?action=login_nutri" class="btn btn-gradient-nutri btn-lg px-4 py-3 fs-6">
                            <i class="fa-solid fa-rocket me-2"></i> Probar Demo Profesional
                        </a>
                        <a href="#showcase" class="btn btn-portal-paciente btn-lg px-4 py-3 fs-6 justify-content-center">
                            <i class="fa-solid fa-mobile-screen me-2"></i> ¿Sos paciente? Conocé tu portal
                        </a>
                    </div>
                </div>

                <!-- Mockup Visual Hero Card con Badges Científicos -->
                <div class="col-lg-6">
                    <div class="hero-visual-card">
                        <div class="hero-badge-floating badge-pos-1">
                            <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-flask-vial fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 0.72rem;">SARA 2 OFICIAL</small>
                                <span class="fw-bold text-dark fs-6">39 Nutrientes & Atwater</span>
                            </div>
                        </div>

                        <div class="hero-badge-floating badge-pos-2">
                            <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger">
                                <i class="fa-solid fa-heart-pulse fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold" style="font-size: 0.72rem;">HEARTS OPS/OMS</small>
                                <span class="fw-bold text-dark fs-6">Riesgo CV a 10 Años</span>
                            </div>
                        </div>

                        <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Nutrición clínica basada en evidencia" class="img-fluid rounded-4 shadow-sm w-100" style="object-fit: cover; max-height: 380px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 3. BARRA DE VALIDACIÓN CIENTÍFICA & SOCIAL PROOF          -->
    <!-- ========================================================== -->
    <section class="scientific-bar">
        <div class="container">
            <div class="row g-3 justify-content-center align-items-center">
                <div class="col-auto">
                    <div class="cert-badge-item">
                        <i class="fa-solid fa-award text-success"></i>
                        <span>Basado en SARA 2 (Ministerio de Salud / ENNyS 2)</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="cert-badge-item">
                        <i class="fa-solid fa-heart-circle-check text-danger"></i>
                        <span>Estratificación HEARTS (OPS / OMS 2019)</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="cert-badge-item">
                        <i class="fa-solid fa-child-reaching text-primary"></i>
                        <span>Criterios Antropométricos OMS</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="cert-badge-item">
                        <i class="fa-solid fa-lock text-warning"></i>
                        <span>Aislamiento Multi-Tenant & Datos Protegidos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 4. SELECTOR DE AUDIENCIA: "DOS MUNDOS, UN SISTEMA"        -->
    <!-- ========================================================== -->
    <section id="audiencias" class="audience-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Dos Mundos, Un Ecosistema</span>
                <h2 class="section-title">Diseñado con Precisión para Profesionales, Pensado con Empatía para Pacientes</h2>
                <p class="text-muted">Descubrí cómo NutriSalud resuelve las necesidades del consultorio moderno en ambos lados de la consulta.</p>
                
                <div class="audience-toggle-container mt-3">
                    <button class="audience-btn active" id="tabBtnNutri" onclick="cambiarAudiencia('nutri')">
                        <i class="fa-solid fa-stethoscope"></i> Para Nutricionistas
                    </button>
                    <button class="audience-btn" id="tabBtnPaciente" onclick="cambiarAudiencia('paciente')">
                        <i class="fa-solid fa-user-heart"></i> Para Pacientes
                    </button>
                </div>
            </div>

            <!-- Contenido Audiencia 1: Nutricionistas -->
            <div id="viewAudienceNutri" class="audience-view-panel">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon"><i class="fa-solid fa-calculator"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Fórmula SARA 2</h5>
                            <p class="text-muted small mb-0">
                                Adiós al Excel. Cálculo automático de 39 nutrientes, cocientes de Woodyatt, AVB y densidad calórica en tiempo real.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon" style="background:#fee2e2; color:#dc2626;"><i class="fa-solid fa-heart-pulse"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Calculadora HEARTS</h5>
                            <p class="text-muted small mb-0">
                                Estratificación del riesgo cardiovascular a 10 años (OPS/OMS) con simulador interactivo de metas terapéuticas.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-regular fa-calendar-check"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Agenda Multivista</h5>
                            <p class="text-muted small mb-0">
                                Calendario con vistas mes, semana y día, Drag & Drop y notificaciones automáticas por Gmail con botón Google Calendar.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-palette"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Marca Blanca</h5>
                            <p class="text-muted small mb-0">
                                Personalizá la plataforma con tu logo, matrícula y color corporativo en historias clínicas, portal e impresiones A4.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Audiencia 2: Pacientes (Oculto inicialmente) -->
            <div id="viewAudiencePaciente" class="audience-view-panel d-none">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon"><i class="fa-solid fa-id-card"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Acceso Fácil con DNI</h5>
                            <p class="text-muted small mb-0">
                                Sin descargas obligatorias de apps pesadas. Ingresá desde cualquier teléfono o navegador web en 1 segundo.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-utensils"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Menú Semanal Claro</h5>
                            <p class="text-muted small mb-0">
                                Visualización ordenada de comidas por momentos del día (desayuno, almuerzo, merienda y cena) con porciones exactas.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-book-open-reader"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Educación en 1 Clic</h5>
                            <p class="text-muted small mb-0">
                                Método del plato, lectura de rótulos, recetas saludables y recordatorio de hidratación siempre a mano.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="audience-card">
                            <div class="audience-feature-icon" style="background:#ecfdf5; color:#059669;"><i class="fa-solid fa-calendar-day"></i></div>
                            <h5 class="fw-bold text-dark mb-2">Autogestión de Turnos</h5>
                            <p class="text-muted small mb-0">
                                Solicitá, reprogramá o consultá tus citas y enlaces de videollamada directamente desde tu cuenta personal.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 5. INNOVACIONES TÉCNICAS: "SHOW, DON'T TELL"              -->
    <!-- ========================================================== -->
    <section id="innovaciones" class="innovations-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fa-solid fa-wand-magic-sparkles me-1"></i> Tecnología Clínica de Vanguardia</span>
                <h2 class="section-title">Potencia Diagnóstica que Revoluciona tu Consulta</h2>
                <p class="text-muted">Conocé las tres innovaciones clave incorporadas al núcleo de NutriSalud SaaS.</p>
            </div>

            <div class="row g-4 align-items-center">
                <!-- Selector de Pestañas Interactivas -->
                <div class="col-lg-5">
                    <!-- Feature 1: SARA 2 -->
                    <div class="innov-tab-btn active" onclick="mostrarInnovacion(1, this)">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success fs-5">
                                <i class="fa-solid fa-flask-vial"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">1. Fórmula Desarrollada SARA 2</h6>
                                <p class="text-muted small mb-0">26 grupos oficiales, 39 nutrientes, Atwater y gráficos Chart.js en vivo.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2: HEARTS -->
                    <div class="innov-tab-btn" onclick="mostrarInnovacion(2, this)">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger fs-5">
                                <i class="fa-solid fa-heart-pulse"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">2. Evaluador HEARTS (OPS/OMS)</h6>
                                <p class="text-muted small mb-0">Riesgo a 10 años, semáforo visual y simulador motivacional What-If.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3: Agenda Inteligente -->
                    <div class="innov-tab-btn" onclick="mostrarInnovacion(3, this)">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary fs-5">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">3. Agenda Inteligente & Notificaciones</h6>
                                <p class="text-muted small mb-0">FullCalendar v6 con Drag & Drop, Google Calendar y correos Gmail.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Showcase Visual Interactivo -->
                <div class="col-lg-7">
                    <!-- Preview 1: SARA 2 Mockup -->
                    <div id="innovPreview1" class="innov-preview-card">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold me-2">NORMA SARA 2</span>
                                <strong class="text-dark">Planilla Dietoterápica Dinámica</strong>
                            </div>
                            <span class="small text-muted"><i class="fa-solid fa-bolt text-warning me-1"></i> Cálculo Atwater en Vivo</span>
                        </div>
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="row text-center g-2 small">
                                <div class="col-3"><div class="fw-bold text-dark">1.850 kcal</div><small class="text-muted">VCT Total</small></div>
                                <div class="col-3"><div class="fw-bold text-success">55% HC</div><small class="text-muted">Carbohidratos</small></div>
                                <div class="col-3"><div class="fw-bold text-primary">18% Prot</div><small class="text-muted">Proteínas (AVB)</small></div>
                                <div class="col-3"><div class="fw-bold text-warning">27% Lip</div><small class="text-muted">Lípidos</small></div>
                            </div>
                        </div>
                        <div class="row g-3 align-items-center">
                            <div class="col-6 text-center">
                                <div class="p-3 bg-white border rounded-3 shadow-sm">
                                    <i class="fa-solid fa-chart-pie fa-2x text-success mb-2"></i>
                                    <div class="small fw-bold text-dark">Distribución Calórica</div>
                                    <small class="text-muted" style="font-size:0.75rem;">Doughnut Chart.js</small>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="p-3 bg-white border rounded-3 shadow-sm">
                                    <i class="fa-solid fa-chart-radar fa-2x text-primary mb-2"></i>
                                    <div class="small fw-bold text-dark">Adecuación IDR Minerales</div>
                                    <small class="text-muted" style="font-size:0.75rem;">Radar Chart.js</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview 2: HEARTS Mockup -->
                    <div id="innovPreview2" class="innov-preview-card d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold me-2">HEARTS / OPS</span>
                                <strong class="text-dark">Estratificación Cardiovascular</strong>
                            </div>
                            <span class="small text-danger fw-bold"><i class="fa-solid fa-heart-pulse me-1"></i> Matriz Oficial 2019</span>
                        </div>
                        <div class="p-3 rounded-3 mb-3 border text-center" style="background:#fff1f2;">
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <h2 class="mb-0 fw-bold text-danger">14.2%</h2>
                                <span class="badge bg-warning text-dark px-3 py-2 fw-bold">Riesgo Alto (10-20%)</span>
                            </div>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 55%"></div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <strong class="text-primary"><i class="fa-solid fa-wand-magic-sparkles me-1"></i> Simulador "¿Qué pasaría si...?"</strong>
                                <span class="badge bg-success">Δ -7.8 pts</span>
                            </div>
                            <small class="text-muted d-block">Al cesar el tabaquismo y reducir la PAS a 125 mmHg, el riesgo desciende al estrato <strong>Moderado (5-10%)</strong>.</small>
                        </div>
                    </div>

                    <!-- Preview 3: Agenda Mockup -->
                    <div id="innovPreview3" class="innov-preview-card d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold me-2">FULLCALENDAR V6</span>
                                <strong class="text-dark">Gestión Multivista & Gmail</strong>
                            </div>
                            <span class="small text-muted"><i class="fa-solid fa-envelope-circle-check text-success me-1"></i> SMTP Transaccional</span>
                        </div>
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold text-dark">Consulta Nutricional Online</div>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> Jueves 18 Sep • 16:30 hs</small>
                                </div>
                                <span class="badge bg-success rounded-pill px-3 py-2">Confirmado</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 flex-fill fw-semibold disabled">
                                <i class="fa-regular fa-calendar-plus me-1"></i> Google Calendar
                            </button>
                            <button class="btn btn-sm btn-success rounded-pill px-3 flex-fill fw-semibold disabled">
                                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 6. SECCIÓN COMPARATIVA: ANTES VS CON NUTRISALUD          -->
    <!-- ========================================================== -->
    <section id="comparativa" class="comparison-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">El Valor del Cambio</span>
                <h2 class="section-title">¿Por Qué los Profesionales Migran a NutriSalud?</h2>
                <p class="text-muted">Compará el flujo de trabajo manual y disperso contra la automatización clínica integrada.</p>
            </div>

            <div class="comparison-table-wrapper">
                <div class="comparison-header">
                    <div class="row fw-bold text-dark text-uppercase small">
                        <div class="col-md-3">Área de Trabajo</div>
                        <div class="col-md-4 text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Antes (Método Tradicional)</div>
                        <div class="col-md-5 text-success"><i class="fa-solid fa-circle-check me-1"></i> Con NutriSalud SaaS</div>
                    </div>
                </div>

                <!-- Fila 1: Fórmulas -->
                <div class="compare-row">
                    <div class="col-md-3 fw-bold text-dark">Fórmulas Desarrolladas</div>
                    <div class="col-md-4 text-muted small pe-3">
                        <span class="tag-antes mb-1 d-inline-block">Excel Disperso</span><br>
                        Planillas manuales de Excel propensas a errores de fórmula y desactualizadas.
                    </div>
                    <div class="col-md-5 text-dark small ps-2">
                        <span class="tag-despues mb-1 d-inline-block">SARA 2 Nativo</span><br>
                        <strong>39 componentes oficiales</strong>, Atwater dinámico y gráficos visuales con 1 clic.
                    </div>
                </div>

                <!-- Fila 2: Riesgo Cardiovascular -->
                <div class="compare-row">
                    <div class="col-md-3 fw-bold text-dark">Riesgo Cardiovascular</div>
                    <div class="col-md-4 text-muted small pe-3">
                        <span class="tag-antes mb-1 d-inline-block">Webs Externas</span><br>
                        Dependencia de calculadoras web de terceros sin guardado en la ficha del paciente.
                    </div>
                    <div class="col-md-5 text-dark small ps-2">
                        <span class="tag-despues mb-1 d-inline-block">HEARTS Integrado</span><br>
                        <strong>Estratificación OPS/OMS</strong> en la HCE, semáforos, what-if y pautas terapéuticas.
                    </div>
                </div>

                <!-- Fila 3: Turnos y Notificaciones -->
                <div class="compare-row">
                    <div class="col-md-3 fw-bold text-dark">Gestión de Turnos</div>
                    <div class="col-md-4 text-muted small pe-3">
                        <span class="tag-antes mb-1 d-inline-block">Chat Manual</span><br>
                        Coordinación por WhatsApp personal, olvidos de citas y reprogramaciones caóticas.
                    </div>
                    <div class="col-md-5 text-dark small ps-2">
                        <span class="tag-despues mb-1 d-inline-block">Agenda & Correo</span><br>
                        <strong>FullCalendar v6</strong> con Drag & Drop, botón Google Calendar y emails automáticos.
                    </div>
                </div>

                <!-- Fila 4: Entrega al Paciente -->
                <div class="compare-row">
                    <div class="col-md-3 fw-bold text-dark">Experiencia del Paciente</div>
                    <div class="col-md-4 text-muted small pe-3">
                        <span class="tag-antes mb-1 d-inline-block">Papel / PDF Plano</span><br>
                        Hojas impresas que se pierden o PDFs estáticos difíciles de leer en el celular.
                    </div>
                    <div class="col-md-5 text-dark small ps-2">
                        <span class="tag-despues mb-1 d-inline-block">Portal 24/7 con DNI</span><br>
                        <strong>Portal digital interactivo</strong> con menú por comidas, registro de hábitos y consultas.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 7. SECCIÓN SHOWCASE: EXPERIENCIA DEL PACIENTE             -->
    <!-- ========================================================== -->
    <section id="showcase" class="section-padding showcase-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fa-solid fa-mobile-screen me-1"></i> Vitrina de la App</span>
                <h2 class="section-title">Así Experimentan tus Pacientes su Plan Saludable</h2>
                <p class="text-muted">Una interfaz amigable, clara e intuitiva diseñada para motivar hábitos sostenibles desde cualquier smartphone.</p>
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
                                Tu paciente consulta sus platos estructurados, porciones exactas e indicaciones personalizadas desde cualquier lugar.
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
                                Registro interactivo de consumo de agua, cumplimiento de actividades y evolución física.
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
                            <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3 border mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 46px; height: 46px; background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); font-size: 1.1rem;">
                                    LO
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">Lic. Leila Olmedo</span>
                                    <small class="text-muted">M.P. 310 • Nutrición Clínica</small>
                                </div>
                            </div>

                            <div class="chat-bubble-mockup chat-nutri">
                                <i class="fa-solid fa-comment-dots me-1"></i> "¡Excelente avance esta semana! Recuerda sumar las semillas de chía al desayuno."
                            </div>

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
                                Canal de comunicación instantáneo por WhatsApp, gestión de citas y resolución ágil de dudas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 8. PORTALES DE ACCESO (CALL TO ACTION FINAL)              -->
    <!-- ========================================================== -->
    <section class="portals-section">
        <div class="container">
            <div class="section-header text-center mb-5">
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill mb-3">Empezá Hoy Mismo</span>
                <h2 class="text-white fw-bold">Transformá tu Consulta Nutricional Hoy</h2>
                <p class="text-white-50">Elegí tu punto de entrada según tu perfil en la plataforma.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="portal-box">
                        <i class="fa-solid fa-user-doctor portal-icon-large"></i>
                        <h3 class="fw-bold fs-4 mb-3">Acceso Nutricionistas</h3>
                        <p class="text-white-50 small mb-4">Gestioná historias clínicas, fórmulas SARA 2, evaluador HEARTS, turnos y diseño de planes alimentarios.</p>
                        <a href="index.php?action=login_nutri" class="btn btn-gradient-nutri w-100 py-3 justify-content-center">
                            Ingresar al Consultorio <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5">
                    <div class="portal-box">
                        <i class="fa-solid fa-mobile-screen-button portal-icon-large text-primary"></i>
                        <h3 class="fw-bold fs-4 mb-3">Portal de Pacientes</h3>
                        <p class="text-white-50 small mb-4">Revisá tu menú diario por comidas, seguimiento de hidratación y próximos turnos con tu profesional.</p>
                        <a href="index.php?action=login_paciente" class="btn btn-outline-light rounded-pill w-100 py-3 justify-content-center fw-semibold">
                            Ver Mi Plan Nutricional <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================== -->
    <!-- 9. FOOTER INSTITUCIONAL                                    -->
    <!-- ========================================================== -->
    <footer class="footer-main">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-4 text-center text-lg-start">
                    <a class="navbar-brand mb-2 justify-content-center justify-content-lg-start" href="#inicio">
                        <span class="brand-icon"><i class="fa-solid fa-leaf"></i></span>
                        <span>NutriSalud</span>
                    </a>
                    <p class="text-muted small mb-0 mt-2">Tecnología y rigor científico al servicio de la nutrición clínica y la salud integral.</p>
                </div>
                
                <div class="col-lg-4 text-center">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="javascript:void(0)" class="text-muted text-decoration-none small fw-medium" data-bs-toggle="modal" data-bs-target="#modalTerminos">Términos del Servicio</a>
                        <span class="text-muted">•</span>
                        <a href="javascript:void(0)" class="text-muted text-decoration-none small fw-medium" data-bs-toggle="modal" data-bs-target="#modalPrivacidad">Privacidad Médica</a>
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
                <p class="text-muted small mb-0">&copy; <?= date("Y") ?> NutriSalud Íntegra SaaS (nutrisaludintegra.com). Todos los derechos reservados.</p>
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

    <!-- MODAL 2: POLÍTICA DE PRIVACIDAD -->
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

    <!-- MODAL 3: SOPORTE TÉCNICO -->
    <div class="modal fade" id="modalSoporte" tabindex="-1" aria-labelledby="modalSoporteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                    <h5 class="modal-title fw-bold" id="modalSoporteLabel"><i class="fa-solid fa-headset me-2"></i> Soporte & Asistencia Clínica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted small mb-4">¿Tenés alguna duda técnica o necesitás asistencia para configurar tu consultorio? Nuestro equipo está listo para ayudarte.</p>
                    <div class="d-grid gap-2">
                        <a href="https://wa.me/" target="_blank" class="btn btn-success py-2 rounded-pill fw-semibold">
                            <i class="fa-brands fa-whatsapp me-2"></i> Contactar por WhatsApp Directo
                        </a>
                        <a href="mailto:soporte@nutrisaludintegra.com" class="btn btn-outline-secondary py-2 rounded-pill fw-semibold">
                            <i class="fa-regular fa-envelope me-2"></i> Enviar Correo a Soporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap JS & Vanilla JS Reactivo -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Switch de Audiencias (Nutricionistas <-> Pacientes)
        function cambiarAudiencia(tipo) {
            const btnNutri = document.getElementById('tabBtnNutri');
            const btnPaciente = document.getElementById('tabBtnPaciente');
            const viewNutri = document.getElementById('viewAudienceNutri');
            const viewPaciente = document.getElementById('viewAudiencePaciente');

            if (tipo === 'nutri') {
                btnNutri.classList.add('active');
                btnPaciente.classList.remove('active');
                viewNutri.classList.remove('d-none');
                viewPaciente.classList.add('d-none');
            } else {
                btnPaciente.classList.add('active');
                btnNutri.classList.remove('active');
                viewPaciente.classList.remove('d-none');
                viewNutri.classList.add('d-none');
            }
        }

        // Showcase de Innovaciones (Show, Don't Tell)
        function mostrarInnovacion(num, btnElement) {
            document.querySelectorAll('.innov-tab-btn').forEach(btn => btn.classList.remove('active'));
            if (btnElement) btnElement.classList.add('active');

            document.getElementById('innovPreview1').classList.add('d-none');
            document.getElementById('innovPreview2').classList.add('d-none');
            document.getElementById('innovPreview3').classList.add('d-none');

            const activePreview = document.getElementById('innovPreview' + num);
            if (activePreview) activePreview.classList.remove('d-none');
        }
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
