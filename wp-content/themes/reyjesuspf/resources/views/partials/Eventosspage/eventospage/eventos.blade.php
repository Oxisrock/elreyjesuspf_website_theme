<div class="bg-white">
    <div class="container mx-auto max-w-7xl p-4 sm:p-8 md:p-16">

        <div class="text-center mb-10">
            <h2 class="text-4xl font-bold text-blue-600 mb-4">Nuestros eventos</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Encuentra mensajes de esperanza y crecimiento espiritual a través de videos, podcasts, alabanzas y
                transmisiones en vivo.
            </p>
        </div>

        <div class="flex mb-14 overflow-x-auto md:justify-center no-scrollbar">
            <nav class="flex space-x-6" id="filtros-eventos">

                {{-- Enlace "Todo", con data-slug="all" para identificarlo en JS --}}
                <a href="#" data-slug="all"
                    class="filtro-categoria py-4 px-1 border-b-2 border-blue-600 text-blue-600 font-semibold flex-shrink-0 whitespace-nowrap">
                    Todo
                </a>

                <?php
                $terms = get_terms([
                    'taxonomy' => 'tipo_evento',
                    'hide_empty' => true,
                ]);
                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        // Usamos data-slug para pasar el slug al JS. El href se queda en "#".
                        echo '<a href="#" data-slug="' . esc_attr($term->slug) . '" class="filtro-categoria py-4 px-1 border-b-2 border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 flex-shrink-0 whitespace-nowrap">' . esc_html($term->name) . '</a>';
                    }
                }
                ?>
            </nav>
        </div>

        <div id="eventos-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-8">


            <?php
            // 1. Argumentos para la consulta de WordPress
            $args = array(
                'post_type'      => 'events',      // Tu Custom Post Type
                'posts_per_page' => 6,             // Muestra 6 eventos inicialmente. Cambia a -1 para mostrar todos.
                'post_status'    => 'publish',     // Solo eventos publicados
                'order'          => 'DESC',        // Los más nuevos primero
            );

            // 2. Crear una nueva instancia de WP_Query
            $events_query = new WP_Query($args);

            // 3. El Loop de WordPress
            if ($events_query->have_posts()) :
                while ($events_query->have_posts()) : $events_query->the_post();
            ?>

            {{-- Esta es la tarjeta de evento que se repetirá para cada evento --}}
            <div
                class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">

                <a href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                    <img class="aspect-video w-full object-cover" src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php else : ?>
                    <img class="aspect-video w-full object-cover" src="@asset('images/placeholder.jpg')" alt="Imagen no disponible">
                    <?php endif; ?>
                </a>

                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <?php
                        // Obtener los términos de la taxonomía 'tipo_evento' para el post actual
                        $event_terms = get_the_terms(get_the_ID(), 'tipo_evento');
                        if ($event_terms && !is_wp_error($event_terms)) {
                            // Muestra el primer término como una etiqueta
                            $term = array_shift($event_terms);
                            echo '<span class="text-xs font-semibold px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full">' . esc_html($term->name) . '</span>';
                        }
                        ?>
                    </div>

                    <h3 class="mt-2 text-xl font-bold text-gray-800">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <a href="<?php the_permalink(); ?>"
                        class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                        Ver detalles
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <?php
                endwhile;
                // Restablecer datos del post original
                wp_reset_postdata();
            else :
                // Mensaje si no se encuentran eventos
                echo '<p class="col-span-full text-center">No hay eventos disponibles en este momento.</p>';
            endif;
            ?>
            {{-- Inicialmente, el script de JS pondrá un mensaje de "Cargando..." --}}
        </div>

        {{-- El botón "Ver más" requerirá JavaScript (AJAX) para cargar más posts sin recargar la página. Este código PHP sienta las bases para ello. --}}
        <div class="text-center mt-12 sm:mt-20">
            <button id="loadMoreBtn"
                class="bg-blue-600 text-white font-bold py-3 px-8 rounded-full hover:bg-blue-700 transition-colors">
                Ver más
            </button>
            <button id="showLessBtn"
                class="bg-gray-600 text-white font-bold py-3 px-8 rounded-full hover:bg-gray-700 transition-colors hidden">
                Mostrar menos
            </button>
        </div>
    </div>
</div>

