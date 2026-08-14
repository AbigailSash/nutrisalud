document.addEventListener('DOMContentLoaded', () => {
    const inputAlimento = document.getElementById('search-alimento');
    const sugerenciasContainer = document.getElementById('sugerencias-alimentos');
    
    if (!inputAlimento || !sugerenciasContainer) return;

    let debounceTimer;

    inputAlimento.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const term = e.target.value.trim();

        if (term.length < 2) {
            sugerenciasContainer.innerHTML = '';
            sugerenciasContainer.classList.add('d-none');
            return;
        }

        debounceTimer = setTimeout(() => {
            // Simulated AJAX fetch
            fetch(`index.php?action=api_buscar_alimento&term=${encodeURIComponent(term)}`)
                .then(res => res.json())
                .then(data => {
                    sugerenciasContainer.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'list-group-item list-group-item-action';
                            div.style.cursor = 'pointer';
                            div.textContent = `${item.Nombre} (Cal: ${item.Calorias})`;
                            div.onclick = () => {
                                inputAlimento.value = item.Nombre;
                                sugerenciasContainer.classList.add('d-none');
                            };
                            sugerenciasContainer.appendChild(div);
                        });
                        sugerenciasContainer.classList.remove('d-none');
                    } else {
                        sugerenciasContainer.classList.add('d-none');
                    }
                })
                .catch(err => console.error(err));
        }, 300); // 300ms Debounce
    });
    
    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!inputAlimento.contains(e.target) && !sugerenciasContainer.contains(e.target)) {
            sugerenciasContainer.classList.add('d-none');
        }
    });
});
