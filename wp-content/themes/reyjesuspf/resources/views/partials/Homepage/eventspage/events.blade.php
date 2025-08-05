<div class="bg-white">
    <div class="container mx-auto max-w-7xl p-4 sm:p-8 md:p-16">

        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-blue-600 mb-4">Nuestros eventos</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Encuentra mensajes de esperanza y crecimiento espiritual a través de videos, podcasts, alabanzas y
                transmisiones en vivo.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-8">
            <?php
            // 1. Definir los argumentos para obtener los 6 últimos eventos
            $args = array(
                'post_type'      => 'events',
                'posts_per_page' => 6, // Clave: Trae exactamente 6 posts
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC', // Los más recientes primero
            );

            // 2. Crear la consulta
            $latest_events = new WP_Query($args);

            // 3. Iniciar el Loop
            if ($latest_events->have_posts()) :
                while ($latest_events->have_posts()) : $latest_events->the_post();
            ?>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
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
                                // Muestra el primer término de la taxonomía 'tipo_evento'
                                $terms = get_the_terms(get_the_ID(), 'tipo_evento');
                                if ($terms && !is_wp_error($terms)) {
                                    $term = array_shift($terms);
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
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php
                endwhile;
                // Restablecer los datos del post para no afectar otras consultas
                wp_reset_postdata();
            else :
                // Mensaje por si no se encuentran eventos
                echo '<p class="col-span-full text-center">No se encontraron eventos.</p>';
            endif;
            ?>
        </div>

        <div class="text-center mt-12 sm:mt-20">
            <a href="<?php echo esc_url(get_post_type_archive_link('events')); ?>" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-full hover:bg-blue-700 transition-colors">
                Ver más
            </a>
        </div>
    </div>
</div>