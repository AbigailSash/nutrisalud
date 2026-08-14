document.addEventListener('DOMContentLoaded', () => {
    cargarNutricionistas();
});

let nutricionistasCache = [];

function cargarNutricionistas() {
    fetch('index.php?action=api_nutricionistas')
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                nutricionistasCache = res.data;
                renderTabla(res.data);
            } else {
                alert('Error al cargar datos: ' + res.message);
            }
        })
        .catch(err => console.error(err));
}

function renderTabla(data) {
    const tbody = document.querySelector('#tablaNutricionistas tbody');
    tbody.innerHTML = '';
    
    data.forEach(n => {
        const estadoBadge = n.Estado_Cuenta === 'A' 
            ? '<span class="badge bg-success">Activo</span>' 
            : '<span class="badge bg-danger">Inactivo</span>';
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${n.IdNutri}</td>
            <td>${n.DNI || '-'}</td>
            <td>
                <div class="fw-bold">${n.Nombre} ${n.Apellido}</div>
                <small class="text-muted">M.P. ${n.Matricula}</small>
            </td>
            <td>${n.Email}</td>
            <td><span class="badge bg-info text-dark rounded-pill">${n.TotalPacientes}</span></td>
            <td>${estadoBadge}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-1" onclick='editarNutricionista(${n.IdNutri})'><i class="fa-solid fa-pen"></i></button>
                <button class="btn btn-sm ${n.Estado_Cuenta === 'A' ? 'btn-outline-danger' : 'btn-outline-success'}" onclick='cambiarEstado(${n.IdNutri})'>
                    <i class="fa-solid ${n.Estado_Cuenta === 'A' ? 'fa-ban' : 'fa-check'}"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function limpiarFormulario() {
    document.getElementById('formNutricionista').reset();
    document.getElementById('nutri_id').value = '';
    document.getElementById('modalTitle').innerText = 'Nuevo Nutricionista';
}

function editarNutricionista(id) {
    const n = nutricionistasCache.find(x => x.IdNutri == id);
    if (!n) return;
    
    document.getElementById('modalTitle').innerText = 'Editar Nutricionista';
    document.getElementById('nutri_id').value = n.IdNutri;
    document.getElementById('nutri_dni').value = n.DNI || '';
    document.getElementById('nutri_matricula').value = n.Matricula || '';
    document.getElementById('nutri_nombre').value = n.Nombre || '';
    document.getElementById('nutri_apellido').value = n.Apellido || '';
    document.getElementById('nutri_email').value = n.Email || '';
    document.getElementById('nutri_password').value = '';
    document.getElementById('nutri_estado').value = n.Estado_Cuenta || 'A';
    
    const modal = new bootstrap.Modal(document.getElementById('modalNutricionista'));
    modal.show();
}

function guardarNutricionista() {
    const form = document.getElementById('formNutricionista');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    fetch('index.php?action=admin_guardar_nutricionista', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const modalEl = document.getElementById('modalNutricionista');
            const modalInst = bootstrap.Modal.getInstance(modalEl);
            if (modalInst) modalInst.hide();
            cargarNutricionistas();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => console.error(err));
}

function cambiarEstado(id) {
    if(!confirm('¿Seguro que deseas cambiar el estado de este nutricionista?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('index.php?action=admin_estado_nutricionista', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            cargarNutricionistas();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => console.error(err));
}
