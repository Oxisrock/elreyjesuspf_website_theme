jQuery(function ($) {
    // Variable para mantener el estado de la categoría actual y la página
    let currentCategory = 'all';
    const postsPerPage = 3; // El número de eventos a mostrar inicialmente

    // Función principal para cargar eventos, ahora más versátil
    // Acepta la categoría, cuántos posts mostrar, y si debe ocultar el botón "Ver más"
    function cargarEventos(categoria, postsToShow, hideLoadMore) {
        const spinnerHTML = `
            <div class="flex justify-center items-center h-64 col-span-full">
                <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-600"></div>
            </div>
        `;

        $.ajax({
            url: eventos_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'filtrar_eventos', // Nuestra acción de WordPress
                category: categoria,       // La categoría a filtrar
                posts_per_page: postsToShow // -1 para todos, 6 para el estado inicial
            },
            beforeSend: function () {
                $('#eventos-container').html(spinnerHTML);
                // Deshabilitamos los botones mientras se carga
                $('#loadMoreBtn, #showLessBtn').prop('disabled', true);
            },
            success: function (response) {
                if (response.success) {
                    // Reemplazamos el contenedor con el HTML de los eventos
                    $('#eventos-container').html(response.data.html);

                    // Lógica para mostrar/ocultar los botones
                    const totalPosts = response.data.total_posts;

                    if (hideLoadMore) {
                        // Si se hace clic en "Mostrar menos", volvemos al estado inicial
                        $('#loadMoreBtn').show();
                        $('#showLessBtn').hide();
                    } else if (postsToShow === -1) {
                         // Si se mostraron todos ("Ver más")
                        $('#loadMoreBtn').hide();
                        $('#showLessBtn').show();
                    }

                    // Si el total de posts es menor o igual al número inicial, no mostramos ningún botón
                    if (totalPosts <= postsPerPage) {
                        $('#loadMoreBtn').hide();
                        $('#showLessBtn').hide();
                    }
                } else {
                    $('#eventos-container').html('<p class="col-span-full text-center">No se encontraron eventos.</p>');
                }
            },
            error: function () {
                $('#eventos-container').html('<p class="col-span-full text-center">Hubo un error al cargar los eventos. Intenta de nuevo.</p>');
            },
            complete: function() {
                 // Habilitamos los botones de nuevo al terminar
                 $('#loadMoreBtn, #showLessBtn').prop('disabled', false);
            }
        });
    }

    // --- MANEJADORES DE EVENTOS ---

    // 1. Carga inicial de eventos al entrar en la página
    cargarEventos(currentCategory, postsPerPage);

    // 2. Clic en un filtro de categoría
    $('#filtros-eventos').on('click', '.filtro-categoria', function (e) {
        e.preventDefault();

        const $esteEnlace = $(this);
        currentCategory = $esteEnlace.data('slug'); // Actualizamos la categoría actual

        // Estilos del filtro
        $('.filtro-categoria').removeClass('border-blue-600 text-blue-600 font-semibold').addClass('border-transparent text-gray-500');
        $esteEnlace.addClass('border-blue-600 text-blue-600 font-semibold').removeClass('border-transparent text-gray-500');

        // Cargamos los 6 primeros eventos de la nueva categoría
        cargarEventos(currentCategory, postsPerPage, true);
    });

    // 3. Clic en el botón "Ver más"
    $('#loadMoreBtn').on('click', function () {
        // Le pedimos a la función que cargue TODOS los eventos (-1) de la categoría actual
        cargarEventos(currentCategory, -1, false);
    });

    // 4. Clic en el botón "Mostrar menos"
    $('#showLessBtn').on('click', function () {
        // Le pedimos a la función que cargue los 6 eventos iniciales de la categoría actual
        cargarEventos(currentCategory, postsPerPage, true);

        // Opcional: Hacer scroll suave hacia la parte superior de la sección de eventos
        $('html, body').animate({
            scrollTop: $('#eventos-container').offset().top - 100 // -100px para un poco de espacio
        }, 500);
    });
});