import domReady from '@roots/sage/client/dom-ready';

/**
 * Application entrypoint
 */
domReady(async () => {
    // Seleccionar los elementos del DOM
    const eventsGrid = document.getElementById('eventsGrid');
// ✅ VERIFICACIÓN: Solo ejecuta el resto del código SI el elemento "eventsGrid" existe.
if (eventsGrid) {
    const gridChildren = Array.from(eventsGrid.children);
    const loadMoreBtn = document.getElementById("loadMoreBtn");
    const showLessBtn = document.getElementById("showLessBtn");

    
    // Número de eventos a mostrar inicialmente
    const itemsToShow = 3;

    // Ocultar los eventos que exceden el límite inicial
    // La clase 'hidden' ya está en el HTML, pero este bucle lo haría dinámico si fuera necesario.
    // eventCards.slice(itemsToShow).forEach(card => card.classList.add('hidden'));

// Asegúrate de que los botones también existan antes de añadirles eventos
    if (loadMoreBtn && showLessBtn) {
        loadMoreBtn.addEventListener("click", function() {
            gridChildren.forEach(child => child.classList.remove("hidden"));
            loadMoreBtn.classList.add("hidden");
            showLessBtn.classList.remove("hidden");
        });

    // Función para el botón "Mostrar menos"
        showLessBtn.addEventListener("click", function() {
            // Esconde todos los elementos después del tercero
            gridChildren.slice(3).forEach(child => child.classList.add("hidden"));
            showLessBtn.classList.add("hidden");
            loadMoreBtn.classList.remove("hidden");
        });

        }
    }

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
});