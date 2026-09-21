/**
 * turnos_calendar.js
 * Módulo reactivo para la gestión de Agenda y Calendario Multivista con FullCalendar v6.
 * NutriSalud SaaS
 */

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('turnosCalendar');
    if (!calendarEl) return;

    let calendar;
    let currentFilterEstado = 'Todos';

    // Inicialización de FullCalendar v6
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 768 ? 'timeGridDay' : 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            day:      'Día',
            list:     'Agenda'
        },
        firstDay: 1, // Lunes
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        slotDuration: '00:30:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6], // Lunes a Sábado
            startTime: '08:00',
            endTime: '20:00',
        },
        navLinks: true,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true, // Mostrar enlace "+ más" si hay muchos eventos
        nowIndicator: true,
        
        // Carga de eventos asíncrona desde el endpoint MVC
        events: function(fetchInfo, successCallback, failureCallback) {
            const url = new URL('index.php?action=api_eventos_calendario', window.location.origin + window.location.pathname);
            url.searchParams.append('start', fetchInfo.startStr);
            url.searchParams.append('end', fetchInfo.endStr);
            if (currentFilterEstado !== 'Todos') {
                url.searchParams.append('estado', currentFilterEstado);
            }

            fetch(url.toString())
                .then(res => res.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(err => {
                    console.error('Error cargando eventos:', err);
                    failureCallback(err);
                });
        },

        // Click en un día o franja horaria vacía -> Abrir modal de nuevo turno
        dateClick: function(info) {
            abrirModalNuevoTurno(info.dateStr);
        },

        // Click en un turno existente -> Abrir modal de detalle
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            abrirModalDetalleTurno(info.event);
        },

        // Drag & Drop: Reprogramación ágil de turnos
        eventDrop: function(info) {
            confirmarReprogramacion(info);
        },

        // Event Resize: Ajuste de duración
        eventResize: function(info) {
            confirmarReprogramacion(info);
        },

        eventDidMount: function(info) {
            // Tooltip nativo rápido con Bootstrap o título
            const props = info.event.extendedProps;
            const tooltipText = `${props.paciente_nombre} (${props.hora} hs) - ${props.modalidad} [${props.estado}]`;
            info.el.setAttribute('title', tooltipText);
        }
    });

    calendar.render();

    // Redimensionar al cambiar el tamaño de la ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768 && calendar.view.type === 'dayGridMonth') {
            calendar.changeView('timeGridDay');
        }
    });

    // ==========================================================
    // FILTROS DE ESTADO REACTIVOS
    // ==========================================================
    const filterButtons = document.querySelectorAll('.btn-filter-estado');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilterEstado = this.getAttribute('data-estado') || 'Todos';
            calendar.refetchEvents();
        });
    });

    // ==========================================================
    // CAMBIO ENTRE VISTA CALENDARIO Y VISTA LISTA
    // ==========================================================
    const btnVistaCalendario = document.getElementById('btnVistaCalendario');
    const btnVistaLista = document.getElementById('btnVistaLista');
    const seccionCalendario = document.getElementById('seccionCalendario');
    const seccionLista = document.getElementById('seccionLista');

    if (btnVistaCalendario && btnVistaLista) {
        btnVistaCalendario.addEventListener('click', function() {
            btnVistaCalendario.classList.add('active');
            btnVistaLista.classList.remove('active');
            seccionCalendario.classList.remove('d-none');
            seccionLista.classList.add('d-none');
            calendar.render();
        });

        btnVistaLista.addEventListener('click', function() {
            btnVistaLista.classList.add('active');
            btnVistaCalendario.classList.remove('active');
            seccionLista.classList.remove('d-none');
            seccionCalendario.classList.add('d-none');
        });
    }

    // ==========================================================
    // MODAL: NUEVO TURNO
    // ==========================================================
    window.abrirModalNuevoTurno = function(fechaHoraStr = null) {
        const modalEl = document.getElementById('modalNuevoTurno');
        if (!modalEl) return;

        const form = document.getElementById('formNuevoTurno');
        if (form) form.reset();

        // Control condicional de Modalidad (Presencial vs Online)
        toggleCamposModalidad('Presencial');

        if (fechaHoraStr) {
            let fecha = '';
            let hora = '09:00';

            if (fechaHoraStr.includes('T')) {
                const parts = fechaHoraStr.split('T');
                fecha = parts[0];
                hora = parts[1].substring(0, 5);
            } else {
                fecha = fechaHoraStr;
            }

            const inputFecha = document.getElementById('nt_fecha');
            const inputHora = document.getElementById('nt_hora');
            if (inputFecha) inputFecha.value = fecha;
            if (inputHora) inputHora.value = hora;
        } else {
            const inputFecha = document.getElementById('nt_fecha');
            if (inputFecha && !inputFecha.value) {
                inputFecha.value = new Date().toISOString().split('T')[0];
            }
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    // Toggle de modalidad en formulario
    const selectModalidad = document.getElementById('nt_modalidad');
    if (selectModalidad) {
        selectModalidad.addEventListener('change', function() {
            toggleCamposModalidad(this.value);
        });
    }

    function toggleCamposModalidad(modalidad) {
        const divLink = document.getElementById('nt_div_link');
        const divDireccion = document.getElementById('nt_div_direccion');
        if (!divLink || !divDireccion) return;

        if (modalidad === 'Online') {
            divLink.classList.remove('d-none');
            divDireccion.classList.add('d-none');
        } else {
            divLink.classList.add('d-none');
            divDireccion.classList.remove('d-none');
        }
    }

    // Envío del formulario de Nuevo Turno vía AJAX
    const formNuevoTurno = document.getElementById('formNuevoTurno');
    if (formNuevoTurno) {
        formNuevoTurno.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = this.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Guardando y Notificando...';

            const formData = new FormData(this);

            fetch('index.php?action=api_guardar_turno_calendario', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;

                if (res.success) {
                    const modalEl = document.getElementById('modalNuevoTurno');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    calendar.refetchEvents();

                    let msjExtra = '';
                    if (res.email_status === 'enviado_ok') {
                        msjExtra = '<br><small class="text-success"><i class="fa-solid fa-envelope-circle-check me-1"></i> Notificación enviada al paciente y al nutricionista con éxito.</small>';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Turno Agendado!',
                        html: res.mensaje + msjExtra,
                        confirmButtonColor: '#2ecc71',
                        confirmButtonText: 'Excelente'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al agendar',
                        text: res.error || 'No se pudo guardar el turno.',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonColor: '#e74c3c'
                });
            });
        });
    }

    // ==========================================================
    // MODAL: DETALLE DEL TURNO
    // ==========================================================
    window.abrirModalDetalleTurno = function(event) {
        const props = event.extendedProps;
        const modalEl = document.getElementById('modalDetalleTurno');
        if (!modalEl) return;

        // Cargar datos en el modal
        document.getElementById('dt_id_turno').value = props.id_turno;
        document.getElementById('dt_paciente_nombre').textContent = props.paciente_nombre;
        document.getElementById('dt_paciente_dni').textContent = props.paciente_dni ? `DNI: ${props.paciente_dni}` : '';
        document.getElementById('dt_paciente_obra').textContent = props.paciente_obra_social || 'Particular';
        
        // Fecha y Hora
        const fechaObj = new Date(props.fecha + 'T00:00:00');
        const fechaFmt = fechaObj.toLocaleDateString('es-AR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('dt_fecha_hora').textContent = `${fechaFmt} a las ${props.hora} hs`;

        // Modalidad y Motivo
        document.getElementById('dt_modalidad').textContent = props.modalidad;
        document.getElementById('dt_motivo').textContent = props.motivo || 'Consulta Nutricional';

        // Badge de Estado
        const badgeEstado = document.getElementById('dt_estado_badge');
        badgeEstado.textContent = props.estado;
        badgeEstado.className = 'badge rounded-pill px-3 py-2 fw-semibold';
        if (props.estado === 'Confirmado') {
            badgeEstado.classList.add('bg-success', 'text-white');
        } else if (props.estado === 'Pendiente') {
            badgeEstado.classList.add('bg-warning', 'text-dark');
        } else if (props.estado === 'Atendido') {
            badgeEstado.classList.add('bg-primary', 'text-white');
        } else if (props.estado === 'Cancelado') {
            badgeEstado.classList.add('bg-danger', 'text-white');
        }

        // Link o Dirección
        const contenedorUbicacion = document.getElementById('dt_contenedor_ubicacion');
        if (props.modalidad === 'Online' && props.link) {
            contenedorUbicacion.innerHTML = `<strong>Enlace de Videollamada:</strong> <a href="${props.link}" target="_blank" class="text-primary fw-bold ms-1"><i class="fa-solid fa-video me-1"></i> Abrir Reunión</a>`;
            contenedorUbicacion.classList.remove('d-none');
        } else if (props.modalidad === 'Presencial' && props.direccion) {
            contenedorUbicacion.innerHTML = `<strong>Dirección:</strong> <span class="text-muted ms-1"><i class="fa-solid fa-location-dot me-1"></i> ${props.direccion}</span>`;
            contenedorUbicacion.classList.remove('d-none');
        } else {
            contenedorUbicacion.classList.add('d-none');
        }

        // Notas
        const contenedorNotas = document.getElementById('dt_contenedor_notas');
        if (props.notas) {
            document.getElementById('dt_notas_texto').textContent = props.notas;
            contenedorNotas.classList.remove('d-none');
        } else {
            contenedorNotas.classList.add('d-none');
        }

        // Botón WhatsApp
        const btnWpp = document.getElementById('dt_btn_whatsapp');
        if (btnWpp) {
            if (props.paciente_telefono) {
                let telLimpio = props.paciente_telefono.replace(/[^0-9]/g, '');
                if (telLimpio.length === 10 && !telLimpio.startsWith('54')) {
                    telLimpio = '549' + telLimpio;
                }
                const msj = encodeURIComponent(`Hola ${props.paciente_nombre}, te contacto desde NutriSalud por tu turno del ${props.fecha} a las ${props.hora} hs.`);
                btnWpp.href = `https://wa.me/${telLimpio}?text=${msj}`;
                btnWpp.classList.remove('disabled');
            } else {
                btnWpp.href = '#';
                btnWpp.classList.add('disabled');
            }
        }

        // Botón Google Calendar
        const btnGcal = document.getElementById('dt_btn_gcal');
        if (btnGcal) {
            const startDt = props.fecha.replace(/-/g, '') + 'T' + props.hora.replace(/:/g, '') + '00';
            const endHourParts = props.hora.split(':');
            let endHour = parseInt(endHourParts[0]);
            let endMin = parseInt(endHourParts[1]) + 45;
            if (endMin >= 60) { endHour += 1; endMin -= 60; }
            const endDt = props.fecha.replace(/-/g, '') + 'T' + String(endHour).padStart(2, '0') + String(endMin).padStart(2, '0') + '00';
            
            const gcalUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent('Consulta Nutricional - ' + props.paciente_nombre)}&dates=${startDt}/${endDt}&details=${encodeURIComponent('Turno agendado en NutriSalud. Motivo: ' + props.motivo)}&location=${encodeURIComponent(props.modalidad === 'Online' ? props.link : props.direccion)}`;
            btnGcal.href = gcalUrl;
        }

        // Botón Ver Historia Clínica
        const btnHce = document.getElementById('dt_btn_hce');
        if (btnHce) {
            btnHce.href = `index.php?action=ver_historia_clinica&id=${props.id_paciente}`;
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    // ==========================================================
    // ACCIONES RÁPIDAS DE ESTADO (CONFIRMAR, ATENDIDO, CANCELAR)
    // ==========================================================
    window.cambiarEstadoTurno = function(nuevoEstado) {
        const idTurno = document.getElementById('dt_id_turno').value;
        if (!idTurno) return;

        if (nuevoEstado === 'Cancelado') {
            Swal.fire({
                title: '¿Deseas cancelar el turno?',
                text: "Puedes ingresar un motivo para notificar al paciente por correo.",
                input: 'text',
                inputPlaceholder: 'Ej: Imprevisto de fuerza mayor...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Sí, cancelar y notificar',
                cancelButtonText: 'No cancelar',
                showDenyButton: true,
                denyButtonText: 'Cancelar sin email',
                denyButtonColor: '#7f8c8d'
            }).then((result) => {
                if (result.isConfirmed || result.isDenied) {
                    const notificar = result.isConfirmed ? 1 : 0;
                    const motivo = result.value || '';
                    ejecutarCambioEstado(idTurno, nuevoEstado, motivo, notificar);
                }
            });
        } else {
            ejecutarCambioEstado(idTurno, nuevoEstado, '', 0);
        }
    };

    function ejecutarCambioEstado(idTurno, estado, motivoCancelacion = '', notificar = 0) {
        const formData = new FormData();
        formData.append('id_turno', idTurno);
        formData.append('estado', estado);
        formData.append('motivo_cancelacion', motivoCancelacion);
        formData.append('notificar_paciente', notificar);

        fetch('index.php?action=api_cambiar_estado_turno', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const modalEl = document.getElementById('modalDetalleTurno');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                calendar.refetchEvents();

                Swal.fire({
                    icon: 'success',
                    title: 'Estado Actualizado',
                    text: res.mensaje,
                    confirmButtonColor: '#2ecc71',
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.error || 'No se pudo actualizar el estado.',
                    confirmButtonColor: '#e74c3c'
                });
            }
        })
        .catch(err => {
            console.error('Error:', err);
        });
    }

    // ==========================================================
    // CONFIRMACIÓN DE REPROGRAMACIÓN (DRAG & DROP)
    // ==========================================================
    function confirmarReprogramacion(info) {
        const nuevaFecha = info.event.startStr.split('T')[0];
        let nuevaHora = '09:00';
        if (info.event.startStr.includes('T')) {
            nuevaHora = info.event.startStr.split('T')[1].substring(0, 5);
        }

        const props = info.event.extendedProps;
        const nombrePaciente = props.paciente_nombre;

        Swal.fire({
            title: '¿Reprogramar Turno?',
            html: `¿Confirmas mover el turno de <strong>${nombrePaciente}</strong> al día <strong>${nuevaFecha}</strong> a las <strong>${nuevaHora} hs</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2ecc71',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Sí, reprogramar y notificar',
            cancelButtonText: 'Cancelar',
            showDenyButton: true,
            denyButtonText: 'Reprogramar sin email',
            denyButtonColor: '#7f8c8d'
        }).then((result) => {
            if (result.isConfirmed || result.isDenied) {
                const notificar = result.isConfirmed ? 1 : 0;
                
                const formData = new FormData();
                formData.append('id_turno', info.event.id);
                formData.append('fecha', nuevaFecha);
                formData.append('hora', nuevaHora);
                formData.append('notificar_paciente', notificar);

                fetch('index.php?action=api_reprogramar_turno', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Turno Reprogramado!',
                            text: res.mensaje + (notificar ? ' Se envió el correo con el nuevo enlace de Google Calendar.' : ''),
                            confirmButtonColor: '#2ecc71',
                            timer: 2500
                        });
                        calendar.refetchEvents();
                    } else {
                        info.revert();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.error || 'No se pudo reprogramar el turno.',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                })
                .catch(err => {
                    info.revert();
                    console.error(err);
                });
            } else {
                info.revert();
            }
        });
    }

    // ==========================================================
    // ELIMINAR TURNO DESDE MODAL
    // ==========================================================
    window.eliminarTurnoDesdeDetalle = function() {
        const idTurno = document.getElementById('dt_id_turno').value;
        if (!idTurno) return;

        Swal.fire({
            title: '¿Estás seguro?',
            text: "El turno será eliminado permanentemente de la base de datos.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `index.php?action=eliminar_turno&id=${idTurno}`;
            }
        });
    };
});
