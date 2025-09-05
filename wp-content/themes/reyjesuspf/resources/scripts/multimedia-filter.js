document.addEventListener('DOMContentLoaded', function () {
    // --- SELECTORES ---
    const filtrosContainer = document.getElementById('filtros-multimedia');
    const grid = document.getElementById('events-grid');
    // MEJORA: Seleccionamos el contenedor del botón "Ver más" por su propio ID para más robustez.
    // Asegúrate de que tu HTML tenga <div id="ver-mas-container">...</div>
    const toggleBtnContainer = document.getElementById('ver-mas-container');

    // CORRECCIÓN: Variable para controlar el listener del botón "Ver Más"
    let verMasController = new AbortController();

    // --- LÓGICA DEL MODAL (Sin cambios, ya estaba bien) ---
    const modal = document.getElementById('video-modal');
    const iframe = document.getElementById('video-iframe');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const preloader = document.getElementById('modal-preloader');

    const closeModal = () => {
        if (modal && iframe) {
            modal.classList.add('hidden');
            iframe.src = '';
            iframe.style.visibility = 'hidden';
            preloader.style.display = 'flex';
        }
    };

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    }

    if (iframe) {
        iframe.addEventListener('load', () => {
            preloader.style.display = 'none';
            iframe.style.visibility = 'visible';
        });
    }
    
    if (grid) {
        grid.addEventListener('click', (e) => {
            const videoItem = e.target.closest('.group');
            if (videoItem) {
                const videoSrc = videoItem.dataset.videoSrc;
                if (videoSrc && modal && iframe) {
                    modal.classList.remove('hidden');
                    preloader.style.display = 'flex';
                    const embedUrl = videoSrc.includes('embed') ? videoSrc : videoSrc.replace("watch?v=", "embed/");
                    iframe.src = embedUrl;
                }
            }
        });
    }
    
    // --- LÓGICA DE FILTRADO AJAX (Lógica del Spinner mejorada) ---
    if (filtrosContainer && grid) {
        const spinnerHTML = `
            <div class="flex justify-center items-center h-64 col-span-full">
                <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-600"></div>
            </div>
        `;

        filtrosContainer.addEventListener('click', function (e) {
            e.preventDefault();
            const filtroActivo = e.target.closest('.filtro-categoria');
            if (!filtroActivo) return;

            // Actualizar estilo del filtro activo
            filtrosContainer.querySelectorAll('.filtro-categoria').forEach(link => {
                link.classList.remove('border-blue-600', 'text-blue-600', 'font-semibold');
                link.classList.add('border-transparent', 'text-gray-500');
            });
            filtroActivo.classList.add('border-blue-600', 'text-blue-600', 'font-semibold');
            filtroActivo.classList.remove('border-transparent', 'text-gray-500');

            const categorySlug = filtroActivo.dataset.slug;

            grid.innerHTML = spinnerHTML;
            if (toggleBtnContainer) toggleBtnContainer.style.display = 'none';

            const data = new URLSearchParams();
            data.append('action', 'filtrar_multimedia');
            data.append('nonce', multimedia_ajax_object.nonce);
            data.append('category', categorySlug);

            fetch(multimedia_ajax_object.ajax_url, {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    grid.innerHTML = result.data.html;
                    initializeToggleVideos(); // Re-inicializamos el botón con el nuevo contenido
                } else {
                    grid.innerHTML = `<p class="text-center col-span-full">${result.data || 'Ocurrió un error.'}</p>`;
                }
            })
            .catch(error => {
                grid.innerHTML = `<p class="text-center col-span-full">Error de conexión.</p>`;
                console.error('Error:', error);
            });
        });
    }

    // --- LÓGICA DEL BOTÓN "VER MÁS" (CORREGIDA) ---
    function initializeToggleVideos() {
        const toggleBtn = document.getElementById('toggle-videos-btn');

        if (!toggleBtn || !grid || !toggleBtnContainer) {
            if (toggleBtnContainer) toggleBtnContainer.style.display = 'none';
            return;
        }

        const hiddenItems = grid.querySelectorAll('.event-item.hidden');
        let areVideosVisible = false;

        if (hiddenItems.length === 0) {
            toggleBtnContainer.style.display = 'none';
        } else {
            toggleBtnContainer.style.display = 'block';
            toggleBtn.textContent = 'Ver más';
            toggleBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            toggleBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }

        // CORRECCIÓN: Usamos AbortController para evitar múltiples listeners
        verMasController.abort(); // Cancelamos el listener anterior
        verMasController = new AbortController(); // Creamos un nuevo controlador

        toggleBtn.addEventListener('click', function () {
            hiddenItems.forEach(item => {
                item.classList.toggle('hidden');
            });

            areVideosVisible = !areVideosVisible;
            if (areVideosVisible) {
                this.textContent = 'Mostrar menos';
                this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                this.classList.add('bg-gray-600', 'hover:bg-gray-700');
            } else {
                this.textContent = 'Ver más';
                this.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                this.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }
        }, { signal: verMasController.signal }); // Adjuntamos la "señal" al nuevo listener
    }
    
    // Llamada inicial al cargar la página
    initializeToggleVideos();
});