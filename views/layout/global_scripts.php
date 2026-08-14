<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
$globalColorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<style id="global-dynamic-theme-css">
    :root {
        --primary-green: <?= htmlspecialchars($globalColorTema) ?>;
        --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
        --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
        --theme-color: var(--primary-green);
    }
    .btn-gradient, .btn-theme, .btn-success {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        border-color: var(--primary-green) !important;
        color: white !important;
    }
    .btn-gradient:hover, .btn-theme:hover, .btn-success:hover {
        opacity: 0.93 !important;
        color: white !important;
        transform: translateY(-1px);
    }
    .btn-outline-theme, .btn-outline-success {
        color: var(--dark-green) !important;
        border-color: var(--primary-green) !important;
    }
    .btn-outline-theme:hover, .btn-outline-success:hover {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        color: white !important;
    }
    .text-theme, .text-success {
        color: var(--dark-green) !important;
    }
    .badge-theme, .badge.bg-success {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        color: white !important;
    }
    .border-theme, .border-success {
        border-color: var(--primary-green) !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-green) !important;
        box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--primary-green) 25%, transparent) !important;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        color: white !important;
    }
    .nav-tabs-clinical .nav-link.active {
        color: var(--dark-green) !important;
        border-bottom: 3px solid var(--primary-green) !important;
    }
</style>

<script>
    // SweetAlert2 Toast Global
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Función global para confirmar eliminaciones o acciones críticas
    function confirmarAccion(url, mensaje = '¿Estás seguro de realizar esta acción?', color = '#e74c3c', icono = 'warning') {
        Swal.fire({
            title: '¿Confirmación?',
            text: mensaje,
            icon: icono,
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    // Interceptar automáticamente todos los enlaces con data-confirm="true"
    document.addEventListener("DOMContentLoaded", function() {
        const confirmLinks = document.querySelectorAll('a[data-confirm="true"], button[data-confirm="true"]');
        confirmLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let url = this.getAttribute('href') || this.dataset.url;
                let msj = this.dataset.mensaje || '¿Estás seguro de realizar esta acción?';
                let color = this.dataset.color || '#e74c3c';
                let icono = this.dataset.icono || 'warning';
                
                if(url) {
                    confirmarAccion(url, msj, color, icono);
                }
            });
        });
    });

    // Sincronización en vivo de variables CSS globales de tema
    (function() {
        const themeColor = '<?= $globalColorTema ?>';
        if (themeColor) {
            document.documentElement.style.setProperty('--primary-green', themeColor);
            document.documentElement.style.setProperty('--dark-green', `color-mix(in srgb, ${themeColor} 75%, black)`);
            document.documentElement.style.setProperty('--light-green', `color-mix(in srgb, ${themeColor} 15%, white)`);
        }
    })();
</script>
