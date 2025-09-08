document.addEventListener('DOMContentLoaded', function () {
    // --- SELECTORES ---
    const filtrosContainer = document.getElementById('filtros-multimedia');
    const grid = document.getElementById('events-grid'); 
    // Gracias al Paso 1, este selector ahora es 100% fiable.
    const toggleBtnContainer = document.getElementById('ver-mas-container'); 

    // Controlador para evitar múltiples listeners en el botón "Ver más"
    let verMasController = new AbortController();

    // --- LÓGICA DEL MODAL (Sin cambios) ---
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
            const videoItem = e.target.closest('.group[data-video-src]');
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
    
    // --- LÓGICA DE FILTRADO AJAX (Sin cambios) ---
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
                    // Volvemos a inicializar la lógica del botón con el nuevo contenido
                    initializeToggleVideos(); 
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

        // Usamos los selectores globales, más robusto
        if (!toggleBtn || !grid || !toggleBtnContainer) {
            if (toggleBtnContainer) toggleBtnContainer.style.display = 'none';
            return;
        }

        const allItems = Array.from(grid.querySelectorAll('.event-item'));
        
        // --- CAMBIO CLAVE ---
        // Aquí definimos que el límite de videos visibles por defecto es 3.
        const VISIBLE_ITEMS_DEFAULT = 3;
        
        let isExpanded = false; // Estado inicial: no expandido (se muestran 3)

        function updateVisibility() {
            allItems.forEach((item, index) => {
                // Si NO está expandido Y el item está más allá del límite (índice 3, 4, 5...), lo ocultamos.
                // Si SÍ está expandido, esta condición siempre es falsa, por lo que se muestran todos.
                item.classList.toggle('hidden', !isExpanded && index >= VISIBLE_ITEMS_DEFAULT);
            });
            
            toggleBtn.textContent = isExpanded ? 'Mostrar menos' : 'Ver más';
            toggleBtn.classList.toggle('bg-gray-600', isExpanded);
            toggleBtn.classList.toggle('hover:bg-gray-700', isExpanded);
            toggleBtn.classList.toggle('bg-blue-600', !isExpanded);
            toggleBtn.classList.toggle('hover:bg-blue-700', !isExpanded);
        }

        // --- LÓGICA PARA OCULTAR EL BOTÓN ---
        // Si la cantidad total de videos es 3 o menos, ocultamos el botón y terminamos.
        // Esto funciona tanto en la carga inicial como después de filtrar.
        if (allItems.length <= VISIBLE_ITEMS_DEFAULT) {
            toggleBtnContainer.style.display = 'none';
            return;
        } 
        
        // Si llegamos aquí, significa que hay más de 3 videos, así que mostramos el botón.
        toggleBtnContainer.style.display = 'block';
        
        // Evita que se añadan múltiples listeners al botón después de filtrar.
        verMasController.abort();
        verMasController = new AbortController();

        toggleBtn.addEventListener('click', () => {
            isExpanded = !isExpanded; // Invierte el estado
            updateVisibility();
        }, { signal: verMasController.signal });

        // Llamada inicial para establecer el estado correcto al cargar (ocultar del 4 en adelante).
        updateVisibility();
    }

    // Llamada inicial al cargar la página por primera vez.
    initializeToggleVideos();
});