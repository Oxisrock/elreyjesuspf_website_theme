import domReady from '@roots/sage/client/dom-ready';

/**
 * Application entrypoint
 */
domReady(async () => {
    // Seleccionar los elementos del DOM
    const eventsGrid = document.getElementById('eventsGrid');
    const eventCards = Array.from(eventsGrid.children);
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const showLessBtn = document.getElementById('showLessBtn');
    
    // Número de eventos a mostrar inicialmente
    const itemsToShow = 3;

    // Ocultar los eventos que exceden el límite inicial
    // La clase 'hidden' ya está en el HTML, pero este bucle lo haría dinámico si fuera necesario.
    // eventCards.slice(itemsToShow).forEach(card => card.classList.add('hidden'));

    // Función para el botón "Ver más"
    loadMoreBtn.addEventListener('click', function () {
        // Muestra todos los eventos
        eventCards.forEach(card => card.classList.remove('hidden'));
        
        // Oculta el botón "Ver más" y muestra el botón "Mostrar menos"
        loadMoreBtn.classList.add('hidden');
        showLessBtn.classList.remove('hidden');
    });

    // Función para el botón "Mostrar menos"
    showLessBtn.addEventListener('click', function () {
        // Oculta los eventos que exceden el límite inicial
        eventCards.slice(itemsToShow).forEach(card => card.classList.add('hidden'));

        // Oculta el botón "Mostrar menos" y muestra el botón "Ver más"
        showLessBtn.classList.add('hidden');
        loadMoreBtn.classList.remove('hidden');
    });

});

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
