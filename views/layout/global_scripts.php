<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /**
     * Función global para confirmaciones con SweetAlert2
     * Reemplaza el uso de onclick="return confirm(...)"
     * @param {string} url - La URL a la que se redirige si el usuario confirma
     * @param {string} mensaje - El texto de advertencia (opcional)
     * @param {string} colorBtn - Color del botón de confirmación (opcional)
     * @param {string} icon - Ícono del modal: 'warning', 'error', 'success', 'info', 'question' (opcional)
     */
    function confirmarAccion(url, mensaje = '¡Esta acción no se puede deshacer!', colorBtn = '#e74c3c', icon = 'warning') {
        Swal.fire({
            title: '¿Estás seguro?',
            text: mensaje,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: colorBtn,
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
            background: '#ffffff',
            customClass: {
                title: 'fw-bold text-dark',
                content: 'text-muted'
            }
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

    // Sincronización de variables de tema de color
    (function() {
        const themeColor = '<?= $_SESSION['ColorTema'] ?? '#2ecc71' ?>';
        if (themeColor) {
            document.documentElement.style.setProperty('--primary-green', themeColor);
            document.documentElement.style.setProperty('--dark-green', `color-mix(in srgb, ${themeColor} 75%, black)`);
            document.documentElement.style.setProperty('--light-green', `color-mix(in srgb, ${themeColor} 15%, white)`);
        }
    })();
</script>
