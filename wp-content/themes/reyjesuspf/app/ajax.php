<?php
function filtrar_eventos_por_categoria() {
    // 1. Obtener los datos del AJAX: categoría Y número de posts a mostrar
    $categoria_slug = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    // --> CAMBIO CLAVE 1: Obtenemos el número de posts que pide el JavaScript.
    // Usamos intval() para asegurar que sea un número. 6 es el valor por defecto.
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 3;

    // 2. Preparar los argumentos para WP_Query
    $args = [
        'post_type'      => 'events',
        // --> CAMBIO CLAVE 2: Usamos la variable en lugar de un número fijo.
        // Será 6 para "Mostrar menos" y -1 para "Ver más".
        'posts_per_page' => $posts_per_page,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ];

    if ($categoria_slug !== 'all') {
        $args['tax_query'] = [
            [
                'taxonomy' => 'tipo_evento',
                'field'    => 'slug',
                'terms'    => $categoria_slug,
            ],
        ];
    }

    $query = new WP_Query($args);

    // --> CAMBIO CLAVE 3: Para saber el total de posts que coinciden (sin límite), usamos found_posts
    $total_posts = $query->found_posts;

    // --> CAMBIO CLAVE 4: Usamos un buffer de salida para capturar todo el HTML en una variable
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Tu código de tarjeta de evento (sin cambios, está perfecto)
            ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <a href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <img class="aspect-video w-full object-cover" src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php else : ?>
                        <img class="aspect-video w-full object-cover" src="<?php echo get_template_directory_uri() . '/assets/images/placeholder.jpg'; ?>" alt="Imagen no disponible">
                    <?php endif; ?>
                </a>
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <?php
                        $event_terms = get_the_terms(get_the_ID(), 'tipo_evento');
                        if ($event_terms && !is_wp_error($event_terms)) {
                            $term = array_shift($event_terms);
                            echo '<span class="text-xs font-semibold px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full">' . esc_html($term->name) . '</span>';
                        }
                        ?>
                        <span class="text-sm text-gray-500"><?php echo get_the_date('j F Y'); ?></span>
                    </div>
                    <h3 class="mt-2 text-xl font-bold text-gray-800">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <a href="<?php the_permalink(); ?>" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                        Ver detalles
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="col-span-full text-center">No se encontraron eventos en esta categoría.</p>';
    }
    
    wp_reset_postdata();

    // --> CAMBIO CLAVE 5: Guardamos el HTML capturado en una variable
    $html = ob_get_clean();

    // --> CAMBIO CLAVE 6: Enviamos una respuesta JSON estructurada que el JavaScript pueda entender
    wp_send_json_success([
        'html'        => $html,
        'total_posts' => $total_posts,
    ]);

    // wp_die() ya no es necesario aquí, wp_send_json_success se encarga de terminar la ejecución.
}

// Tus actions están correctos, no necesitas cambiarlos.
add_action('wp_ajax_filtrar_eventos', __NAMESPACE__ . '\\filtrar_eventos_por_categoria');
add_action('wp_ajax_nopriv_filtrar_eventos', __NAMESPACE__ . '\\filtrar_eventos_por_categoria');

function filtrar_multimedia_handler() {
    // Seguridad primero: Verificar el nonce
    if (!check_ajax_referer('multimedia_filter_nonce', 'nonce', false)) {
        wp_send_json_error('Nonce inválido.', 401);
        return;
    }

    // Limpiar la categoría recibida desde JS
    $category_slug = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';

    // Argumentos para la consulta
    $args = array(
        'post_type'      => 'multimedia',
        'posts_per_page' => 6, // Traemos todos para manejar "Ver más" con JS, o puedes paginar aquí.
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Si la categoría no es 'all', añadimos el filtro de taxonomía
    if ($category_slug !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'categoria_multimedia',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        );
    }

    $multimedia_query = new WP_Query($args);

    // Usamos un buffer de salida para capturar todo el HTML
    ob_start();

    if ($multimedia_query->have_posts()) :
        $counter = 0;
        while ($multimedia_query->have_posts()) : $multimedia_query->the_post();
            $counter++;
            $hidden_class = ($counter > 3) ? 'hidden' : ''; // Oculta a partir del 4to item

            // Extraer URL del iframe de ACF
            $iframe_code = get_field('iframe');
            $video_url = '';
            if ($iframe_code) {
                preg_match('/src="([^"]+)"/', $iframe_code, $matches);
                if (isset($matches[1])) {
                    $video_url = $matches[1];
                }
            }
            
            // Obtener imagen destacada
            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$thumbnail_url) {
                $thumbnail_url = get_template_directory_uri() . '/assets/images/default-thumbnail.jpg';
            }
            ?>
            <div class="event-item <?php echo $hidden_class; ?>">
                <div class="relative group cursor-pointer" data-video-src="<?php echo esc_url($video_url); ?>">
                    <div class="aspect-video w-full bg-gray-200 rounded-lg overflow-hidden">
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="font-semibold text-gray-800"><?php the_title(); ?></h3>
                </div>
            </div>
            <?php
        endwhile;
    else :
        echo '<p class="text-center col-span-full">No hay videos para mostrar en esta categoría.</p>';
    endif;
    
    wp_reset_postdata();

    // Capturamos el HTML del buffer y lo enviamos como respuesta JSON
    $html = ob_get_clean();
    wp_send_json_success(array('html' => $html));
}

// Enganchamos la función a las acciones de AJAX de WordPress
add_action('wp_ajax_filtrar_multimedia', __NAMESPACE__ . '\\filtrar_multimedia_handler');       // Para usuarios logueados
add_action('wp_ajax_nopriv_filtrar_multimedia', __NAMESPACE__ . '\\filtrar_multimedia_handler'); // Para usuarios no logueados