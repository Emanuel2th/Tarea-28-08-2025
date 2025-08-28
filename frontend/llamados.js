// jobs.js
// Este script obtiene los llamados desde la API y los muestra dinámicamente en el HTML

document.addEventListener('DOMContentLoaded', function() {
    // Load jobs
    loadJobs();
    
    // Add event listeners for profile navigation
    document.getElementById('mi-red-link').addEventListener('click', function(e) {
        e.preventDefault();
        showProfile();
    });
});

function loadJobs() {
    fetch('../backend/api/routes/llamados.php')
        .then(response => response.json())
        .then(data => {
            const jobsGrid = document.getElementById('container');
            jobsGrid.innerHTML = '';
            // Actualizar el número de empleos activos
            const empleosActivos = document.getElementById('empleos-activos');
            if (empleosActivos) {
                empleosActivos.textContent = data.llamados.length;
            }
            data.llamados.forEach(llamado => {
                // Obtener la primera letra del título
                const letra = llamado.titulo.charAt(0).toUpperCase();
                // Card dinámico
                const card = document.createElement('div');
                card.className = 'job-card bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md cursor-pointer';
                card.innerHTML = `
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            ${llamado.logo ? `<img src="${llamado.logo}" alt="Logo" class="w-12 h-12 rounded-lg object-cover">` : ''}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors">${llamado.titulo}</h3>
                                <p class="text-sm text-gray-500">Empresa: ${llamado.empresa_nombre}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                           ${llamado.id}
                        </span>
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-4">${llamado.descripcion}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 110-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                ${llamado.tipo || 'No especificado'}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                ${(() => {
                                    if (!llamado.fecha) return 'Fecha no especificada';
                                    const fechaLlamado = new Date(llamado.fecha);
                                    const hoy = new Date();
                                    // Normalizar a medianoche para evitar problemas de zona horaria
                                    fechaLlamado.setHours(0,0,0,0);
                                    hoy.setHours(0,0,0,0);
                                    const diffTime = hoy - fechaLlamado;
                                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                                    return diffDays === 0 ? 'Hoy' : `Hace ${diffDays} día${diffDays > 1 ? 's' : ''}`;
                                })()}
                            </span>
                        </div>
                        <button class="apply-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" data-job-id="${llamado.id}">
                            Postular
                        </button>
                    </div>
                `;
                jobsGrid.appendChild(card);
            });
            
            // Add event listeners to apply buttons
            document.querySelectorAll('.apply-button').forEach(button => {
                button.addEventListener('click', function() {
                    const jobId = this.getAttribute('data-job-id');
                    applyForJob(jobId);
                });
            });
        })
        .catch(error => {
            console.error('Error al cargar los llamados:', error);
        });
}

function applyForJob(jobId) {
    // Check if user is logged in
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        alert('Debes iniciar sesión para postularte a un trabajo');
        // Show auth container
        document.getElementById('auth-container').classList.remove('hidden');
        document.getElementById('jobs-container').classList.add('hidden');
        return;
    }
    
    // Send application request
    fetch('../backend/api/routes/llamados.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'postular',
            usuario_id: user.id,
            llamado_id: jobId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Disable the button to prevent multiple applications
            document.querySelector(`.apply-button[data-job-id="${jobId}"]`).disabled = true;
            document.querySelector(`.apply-button[data-job-id="${jobId}"]`).textContent = 'Postulado';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al postularse al trabajo');
    });
}

function showProfile() {
    // Check if user is logged in
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
        alert('Debes iniciar sesión para ver tu perfil');
        // Show auth container
        document.getElementById('auth-container').classList.remove('hidden');
        document.getElementById('jobs-container').classList.add('hidden');
        document.getElementById('profile-container').classList.add('hidden');
        return;
    }
    
    // Hide jobs container and show profile container
    document.getElementById('jobs-container').classList.add('hidden');
    document.getElementById('profile-container').classList.remove('hidden');
    
    // Update profile information
    document.getElementById('profile-name').textContent = user.nombre;
    document.getElementById('profile-email').textContent = user.email;
    document.getElementById('profile-initial').textContent = user.nombre.charAt(0).toUpperCase();
    
    // Load user applications
    loadUserApplications(user.id);
}

function loadUserApplications(userId) {
    fetch('../backend/api/routes/llamados.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'get_user_applications',
            usuario_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        const applicationsContainer = document.getElementById('applications-container');
        applicationsContainer.innerHTML = '';
        
        if (data.applications.length === 0) {
            applicationsContainer.innerHTML = '<p class="text-gray-500">No te has postulado a ningún trabajo aún.</p>';
            return;
        }
        
        data.applications.forEach(application => {
            const card = document.createElement('div');
            card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-6';
            card.innerHTML = `
                <h3 class="text-lg font-semibold text-gray-900 mb-2">${application.titulo}</h3>
                <p class="text-gray-700 text-sm mb-4">${application.descripcion}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Empresa: ${application.empresa_nombre}</span>
                    <span class="text-sm text-gray-500">Postulado: ${new Date(application.fecha_postulacion).toLocaleDateString()}</span>
                </div>
            `;
            applicationsContainer.appendChild(card);
        });
    })
    .catch(error => {
        console.error('Error al cargar las postulaciones:', error);
        document.getElementById('applications-container').innerHTML = '<p class="text-gray-500">Error al cargar las postulaciones.</p>';
    });
}
