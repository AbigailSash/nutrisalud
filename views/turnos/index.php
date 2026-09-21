<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Agenda y Calendario Clínico</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- FullCalendar v6 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 12%, white);
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --sidebar-bg: #1a252f;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light); 
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.85rem;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            overflow: hidden;
            transition: all 0.3s;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 50px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 14px rgba(46, 204, 113, 0.25);
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.35);
            color: white;
        }

        /* FullCalendar Customization */
        #turnosCalendar {
            font-family: 'Poppins', sans-serif;
            padding: 1rem;
        }

        .fc .fc-toolbar-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: capitalize;
        }

        .fc .fc-button-primary {
            background-color: white;
            border-color: #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 7px 15px;
            border-radius: 10px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }

        .fc .fc-button-primary:hover, .fc .fc-button-primary:focus {
            background-color: var(--light-green);
            border-color: var(--primary-green);
            color: var(--dark-green);
            box-shadow: none;
        }

        .fc .fc-button-primary.fc-button-active {
            background-color: var(--primary-green) !important;
            border-color: var(--primary-green) !important;
            color: white !important;
        }

        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #f1f5f9;
        }

        .fc .fc-col-header-cell {
            background-color: #f8fafc;
            padding: 12px 0;
            font-weight: 600;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fc-daygrid-day-number {
            font-weight: 600;
            color: #475569;
            padding: 6px 10px !important;
            font-size: 0.9rem;
        }

        .fc-day-today {
            background-color: var(--light-green) !important;
        }

        .fc-event {
            border-radius: 8px;
            padding: 3px 6px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }

        .fc-event:hover {
            transform: scale(1.02);
        }

        /* Filter Pills */
        .filter-pill {
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-pill:hover {
            background: #f8fafc;
            color: var(--text-dark);
        }

        .filter-pill.active {
            background: var(--text-dark);
            color: white;
            border-color: var(--text-dark);
        }

        .filter-pill.active.pill-confirmado { background: #10b981; border-color: #10b981; }
        .filter-pill.active.pill-pendiente { background: #f59e0b; border-color: #f59e0b; }
        .filter-pill.active.pill-atendido { background: #3b82f6; border-color: #3b82f6; }
        .filter-pill.active.pill-cancelado { background: #ef4444; border-color: #ef4444; }

        /* View Toggle Tabs */
        .view-switcher .btn {
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 18px;
        }

        .view-switcher .btn.active {
            background-color: var(--primary-green);
            color: white;
            border-color: var(--primary-green);
        }

        /* Modern Table */
        .table-custom thead th {
            background-color: var(--light-green);
            color: var(--dark-green);
            font-weight: 600;
            border-bottom: none;
            padding: 14px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .table-custom tbody td {
            padding: 15px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.92rem;
        }

        .user-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            margin-right: 10px;
            background: #e2e8f0;
            color: #334155;
            font-weight: 700;
        }

        @media (max-width: 991px) {
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'views/layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header Modular -->
        <?php include 'views/layout/header.php'; ?>

        <!-- Alertas Flash -->
        <?php include 'views/layout/alertas.php'; ?>

        <!-- Encabezado de Página -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="page-title"><i class="fa-solid fa-calendar-days me-2" style="color:var(--primary-green)"></i> Agenda y Turnos</h1>
                <p class="text-muted mb-0">Gestiona tus consultas presenciales y virtuales con sincronización inteligente.</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <!-- Selector de Vista -->
                <div class="btn-group view-switcher bg-white p-1 rounded-3 shadow-sm border">
                    <button type="button" class="btn active" id="btnVistaCalendario">
                        <i class="fa-solid fa-calendar-alt me-1"></i> Calendario
                    </button>
                    <button type="button" class="btn" id="btnVistaLista">
                        <i class="fa-solid fa-list-ul me-1"></i> Lista
                    </button>
                </div>

                <button type="button" onclick="abrirModalNuevoTurno()" class="btn btn-gradient text-decoration-none">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Turno
                </button>
            </div>
        </div>

        <!-- Barra de Filtros de Estado -->
        <div class="dashboard-card p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-bold text-uppercase me-2"><i class="fa-solid fa-filter me-1"></i> Filtrar por:</span>
                    <button type="button" class="filter-pill btn-filter-estado active" data-estado="Todos">
                        Todos
                    </button>
                    <button type="button" class="filter-pill btn-filter-estado pill-confirmado" data-estado="Confirmado">
                        <i class="fa-solid fa-circle me-1" style="color: #10b981; font-size: 8px;"></i> Confirmados
                    </button>
                    <button type="button" class="filter-pill btn-filter-estado pill-pendiente" data-estado="Pendiente">
                        <i class="fa-solid fa-circle me-1" style="color: #f59e0b; font-size: 8px;"></i> Pendientes
                    </button>
                    <button type="button" class="filter-pill btn-filter-estado pill-atendido" data-estado="Atendido">
                        <i class="fa-solid fa-circle me-1" style="color: #3b82f6; font-size: 8px;"></i> Atendidos
                    </button>
                    <button type="button" class="filter-pill btn-filter-estado pill-cancelado" data-estado="Cancelado">
                        <i class="fa-solid fa-circle me-1" style="color: #ef4444; font-size: 8px;"></i> Cancelados
                    </button>
                </div>

                <div class="small text-muted d-none d-md-block">
                    <i class="fa-solid fa-hand-pointer me-1 text-primary"></i> <em>Haz clic en un espacio para agendar o arrastra para reprogramar</em>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 1: VISTA FULLCALENDAR -->
        <div id="seccionCalendario" class="dashboard-card p-3 mb-4">
            <div id="turnosCalendar"></div>
        </div>

        <!-- SECCIÓN 2: VISTA TABLA LISTA (Alternativa) -->
        <div id="seccionLista" class="dashboard-card d-none mb-4">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-list me-2" style="color:var(--primary-green)"></i> Listado Histórico de Turnos</h6>
                <span class="badge bg-light text-dark border px-3 py-2">Total: <?= count($turnos ?? []) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Paciente</th>
                            <th>Modalidad</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($turnos)): ?>
                            <?php foreach($turnos as $t): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['Fecha'])) ?></div>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?= substr($t['Hora'], 0, 5) ?> hs</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-small">
                                            <?= strtoupper(substr($t['PacienteNombre'] ?? 'X',0,1) . substr($t['PacienteApellido'] ?? 'X',0,1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars(($t['PacienteNombre'] ?? '') . ' ' . ($t['PacienteApellido'] ?? '')) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if(($t['Modalidad'] ?? 'Presencial') === 'Online'): ?>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="fa-solid fa-video me-1"></i> Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border px-2 py-1"><i class="fa-solid fa-user-doctor me-1"></i> Presencial</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-muted small"><?= htmlspecialchars($t['Motivo_Consulta'] ?? 'Consulta Nutricional') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($t['Estado_Turno'] === 'Confirmado') $badgeClass = 'bg-success text-white';
                                    elseif ($t['Estado_Turno'] === 'Cancelado') $badgeClass = 'bg-danger text-white';
                                    elseif ($t['Estado_Turno'] === 'Atendido') $badgeClass = 'bg-primary text-white';
                                    ?>
                                    <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2 fw-semibold">
                                        <?= htmlspecialchars($t['Estado_Turno']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?action=editar_turno&id=<?= $t['IdTurno'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold me-1" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button onclick="confirmarEliminacion(<?= $t['IdTurno'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted py-3">
                                        <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0">No hay turnos registrados en el sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================== -->
    <!-- MODAL 1: NUEVO TURNO / AGENDAMIENTO RÁPIDO                 -->
    <!-- ========================================================== -->
    <div class="modal fade" id="modalNuevoTurno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                    <div>
                        <h5 class="modal-title fw-bold mb-0"><i class="fa-solid fa-calendar-plus me-2"></i> Agendar Consulta Nutricional</h5>
                        <p class="small mb-0 opacity-90">Completa los datos del turno para el paciente.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevoTurno">
                    <div class="modal-body p-4">
                        <!-- Paciente -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Paciente *</label>
                            <select name="id_paciente" id="nt_id_paciente" class="form-select rounded-3 py-2" required>
                                <option value="">Seleccione un paciente...</option>
                                <?php foreach($pacientes as $p): ?>
                                    <option value="<?= $p['IdPaciente'] ?>">
                                        <?= htmlspecialchars($p['Apellido'] . ', ' . $p['Nombre']) ?> (DNI: <?= htmlspecialchars($p['DNI']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-bold small text-muted text-uppercase">Fecha *</label>
                                <input type="date" name="fecha" id="nt_fecha" class="form-control rounded-3 py-2" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold small text-muted text-uppercase">Hora *</label>
                                <input type="time" name="hora" id="nt_hora" class="form-control rounded-3 py-2" value="09:00" required>
                            </div>
                        </div>

                        <!-- Modalidad y Estado -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Modalidad *</label>
                                <select name="modalidad" id="nt_modalidad" class="form-select rounded-3 py-2">
                                    <option value="Presencial" selected>🏢 Presencial</option>
                                    <option value="Online">💻 Online (Videollamada)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Estado *</label>
                                <select name="estado" id="nt_estado" class="form-select rounded-3 py-2">
                                    <option value="Confirmado" selected>✅ Confirmado</option>
                                    <option value="Pendiente">⏳ Pendiente</option>
                                </select>
                            </div>
                        </div>

                        <!-- Motivo de Consulta -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Motivo de Consulta</label>
                            <input type="text" name="motivo_consulta" id="nt_motivo" class="form-control rounded-3 py-2" placeholder="Ej: Control nutricional, Plan deportivo, Primera consulta..." value="Consulta Nutricional">
                        </div>

                        <!-- Link de Reunión (Condicional Online) -->
                        <div class="mb-3 d-none" id="nt_div_link">
                            <label class="form-label fw-bold small text-muted text-uppercase"><i class="fa-solid fa-video me-1 text-primary"></i> Enlace de Videollamada (Meet / Zoom)</label>
                            <input type="url" name="link_reunion" id="nt_link" class="form-control rounded-3 py-2" placeholder="https://meet.google.com/abc-defg-hij">
                        </div>

                        <!-- Dirección (Condicional Presencial) -->
                        <div class="mb-3" id="nt_div_direccion">
                            <label class="form-label fw-bold small text-muted text-uppercase"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Consultorio / Dirección</label>
                            <input type="text" name="direccion" id="nt_direccion" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($nutri['Direccion'] ?? 'Consultorio') ?>" placeholder="Dirección del consultorio">
                        </div>

                        <!-- Notas / Indicaciones -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Indicaciones / Notas para el Paciente</label>
                            <textarea name="notas" id="nt_notas" class="form-control rounded-3" rows="2" placeholder="Ej: Traer últimos análisis de laboratorio, concurrir con ropa cómoda..."></textarea>
                        </div>

                        <!-- Checkbox Notificación por Correo -->
                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-3" type="checkbox" name="notificar_paciente" id="nt_notificar" value="1" checked style="cursor:pointer; width:2.5em; height:1.3em;">
                            <label class="form-check-label fw-semibold small text-dark" for="nt_notificar" style="cursor:pointer;">
                                <i class="fa-solid fa-envelope me-1 text-success"></i> Enviar confirmación por correo (Gmail) con botón <strong>Google Calendar</strong> y <strong>WhatsApp</strong>.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-gradient rounded-pill px-4">
                            <i class="fa-solid fa-check me-2"></i> Confirmar y Agendar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 2: DETALLE DEL TURNO Y ACCIONES RÁPIDAS             -->
    <!-- ========================================================== -->
    <div class="modal fade" id="modalDetalleTurno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <input type="hidden" id="dt_id_turno">
                
                <div class="modal-header border-0 bg-dark text-white p-4">
                    <div class="d-flex align-items-center justify-content-between w-100 pe-3">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-small bg-white text-dark me-3" style="width:45px; height:45px; font-size:1.1rem;">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="dt_paciente_nombre">Paciente</h5>
                                <div class="small opacity-75">
                                    <span id="dt_paciente_dni"></span> • <span id="dt_paciente_obra"></span>
                                </div>
                            </div>
                        </div>
                        <span id="dt_estado_badge" class="badge rounded-pill px-3 py-2 fw-semibold">Estado</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Tarjeta de Horario y Modalidad -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-bold text-dark">
                                <i class="fa-solid fa-calendar-day me-2 text-primary"></i> <span id="dt_fecha_hora"></span>
                            </div>
                        </div>
                        <div class="small text-muted mb-1">
                            <strong>Modalidad:</strong> <span id="dt_modalidad" class="badge bg-secondary-subtle text-secondary border ms-1"></span>
                            <span class="ms-3"><strong>Motivo:</strong> <span id="dt_motivo" class="text-dark"></span></span>
                        </div>
                        <div id="dt_contenedor_ubicacion" class="small text-muted mt-2 pt-2 border-top d-none"></div>
                    </div>

                    <!-- Notas / Indicaciones -->
                    <div id="dt_contenedor_notas" class="alert alert-secondary py-2 px-3 small rounded-3 mb-3 d-none">
                        <strong><i class="fa-regular fa-note-sticky me-1"></i> Indicaciones:</strong> <span id="dt_notas_texto"></span>
                    </div>

                    <!-- Botones de Enlace Rápido (WhatsApp & Google Calendar & HCE) -->
                    <div class="d-flex gap-2 mb-4 flex-wrap">
                        <a id="dt_btn_whatsapp" href="#" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-semibold flex-fill shadow-sm">
                            <i class="fa-brands fa-whatsapp me-1 fa-lg"></i> WhatsApp
                        </a>
                        <a id="dt_btn_gcal" href="#" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 fw-semibold flex-fill">
                            <i class="fa-regular fa-calendar-plus me-1"></i> Google Calendar
                        </a>
                        <a id="dt_btn_hce" href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold flex-fill">
                            <i class="fa-solid fa-folder-open me-1"></i> Ver HCE
                        </a>
                    </div>

                    <!-- Barra de Acciones de Estado -->
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Acciones Rápidas de Estado:</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" onclick="cambiarEstadoTurno('Confirmado')" class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 fw-semibold flex-fill">
                            <i class="fa-solid fa-check me-1"></i> Confirmar
                        </button>
                        <button type="button" onclick="cambiarEstadoTurno('Atendido')" class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 fw-semibold flex-fill text-dark">
                            <i class="fa-solid fa-clipboard-check me-1"></i> Atendido
                        </button>
                        <button type="button" onclick="cambiarEstadoTurno('Cancelado')" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 fw-semibold flex-fill">
                            <i class="fa-solid fa-ban me-1"></i> Cancelar
                        </button>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0 justify-content-between">
                    <button type="button" onclick="eliminarTurnoDesdeDetalle()" class="btn btn-link text-danger text-decoration-none p-0 small fw-bold">
                        <i class="fa-solid fa-trash me-1"></i> Eliminar Turno
                    </button>
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-muted" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap, SweetAlert2 y Calendario -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="public/js/turnos_calendar.js"></script>
    
    <script>
        function confirmarEliminacion(idTurno) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El turno será eliminado permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#27ae60',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?action=eliminar_turno&id=' + idTurno;
                }
            });
        }
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
