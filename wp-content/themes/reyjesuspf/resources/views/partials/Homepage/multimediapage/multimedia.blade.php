<section id="multimedia">
    <div class="bg-white">
        <div class="container mx-auto max-w-6xl py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Descubre</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">
                    Encuentra mensajes de esperanza y crecimiento espiritual a través de videos, podcasts, alabanzas y
                    transmisiones en vivo.
                </p>
            </div>
            <div class="flex border-b border-gray-200 mb-14 overflow-x-auto md:justify-center no-scrollbar">
                <nav class="flex space-x-6" id="filtros-multimedia">

                    {{-- Enlace "Todo", con data-slug="all" para identificarlo en JS --}}
                    <a href="#" data-slug="all"
                        class="filtro-categoria py-4 px-1 border-b-2 border-blue-600 text-blue-600 font-semibold flex-shrink-0 whitespace-nowrap">
                        Todo
                    </a>

                    <?php
                    $terms = get_terms([
                        'taxonomy' => 'categoria_multimedia',
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

            <?php
            // Argumentos para la consulta de WordPress
            $args = array(
                'post_type'      => 'multimedia', // Asegúrate que este sea el slug de tu CPT
                'posts_per_page' => 6,            // Obtener los últimos 6 posts
                'orderby'        => 'date',       // Ordenar por fecha de publicación
                'order'          => 'DESC',       // En orden descendente (los más nuevos primero)
            );

            $multimedia_query = new WP_Query($args);
            $counter = 0; // Un contador para la clase 'hidden'

            if ($multimedia_query->have_posts()) :
            ?>

            <div id="events-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-10">

                <?php
                    while ($multimedia_query->have_posts()) : $multimedia_query->the_post();
                        $counter++;

                        // Determina si el item debe estar oculto inicialmente
                        $hidden_class = ($counter > 3) ? 'hidden' : '';

                        // Obtiene la URL del video desde el campo ACF (cambia 'video_url' por el nombre de tu campo)
                       // --- LÓGICA ACTUALIZADA PARA CAMPO IFRAME DE ACF ---
                        
                        // 1. Obtenemos el código HTML completo del iframe
                        $iframe_code = get_field('iframe');
                        $video_url = ''; // Variable para guardar la URL extraída

                        // 2. Extraemos solo la URL (el 'src') del código del iframe
                        if ($iframe_code) {
                            preg_match('/src="([^"]+)"/', $iframe_code, $matches);
                            if (isset($matches[1])) {
                                $video_url = $matches[1];
                            }
                        }
                        
                        // Obtiene la URL de la imagen destacada
                        $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                        // Fallback a una imagen por defecto si no hay imagen destacada
                        if (!$thumbnail_url) {
                            $thumbnail_url = get_template_directory_uri() . '/assets/images/default-thumbnail.jpg'; // Ajusta esta ruta
                        }
                    ?>
                <div class="event-item <?php echo $hidden_class; ?>">
                    <div class="relative group cursor-pointer" data-video-src="<?php echo $video_url; ?>">
                        <div class="aspect-video w-full bg-gray-200 rounded-lg overflow-hidden">
                            <img src="<?php echo $thumbnail_url; ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                        </div>
                        <div
                            class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h3 class="font-semibold text-gray-800"><?php the_title(); ?></h3>

                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="text-center mt-12">
                <button id="toggle-videos-btn"
                    class="bg-blue-600 text-white font-bold py-3 px-8 rounded-full hover:bg-blue-700 transition-colors">
                    Ver más
                </button>
            </div>
            <?php
                wp_reset_postdata(); // Restaura los datos del post original
            else :
                echo '<p class="text-center">No hay videos para mostrar.</p>';
            endif;
            ?>
        </div>
    </div>
</section>

<div id="video-modal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="bg-black p-2 rounded-lg relative w-11/12 md:w-3/4 lg:w-1/2">
        <button id="close-modal-btn"
            class="absolute -top-4 -right-4 md:-right-6 text-white text-3xl z-10">&times;</button>

        <div class="aspect-video relative">
            <div id="modal-preloader" class="absolute inset-0 flex items-center justify-center bg-black">
                <svg class="custom-spinner animate-spin h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="#1d4ed8" stroke-width="4"></circle>
                    <path fill="#FFFF"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>

            <iframe id="video-iframe" class="w-full h-full" style="visibility: hidden;" src="" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    </div>
</div>

<style>
    /* Animation for the spinner */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Apply the animation and size to the SVG */
    .custom-spinner {
        animation: spin 1s linear infinite;
        width: 2rem;
        /* 32px */
        height: 2rem;
        /* 32px */
    }
</style>
