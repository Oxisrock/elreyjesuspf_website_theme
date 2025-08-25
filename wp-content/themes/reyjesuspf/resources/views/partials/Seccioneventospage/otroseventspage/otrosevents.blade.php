{{-- Corregí 'bg-white-500' a 'bg-gray-50' para un fondo suave, o puedes cambiarlo a 'bg-white' --}}
<div class="bg-gray-50">

    <div class="container mx-auto max-w-7xl p-12">

        <div class="text-center mb-10">
            <h2 class="text-4xl font-bold text-blue-600">Más Eventos</h2>
            {{-- Estructura de subtítulo mejorada para mayor claridad --}}
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                Encuentra mensajes de esperanza y crecimiento espiritual a través de videos, podcasts, alabanzas y trasmisiones en vivo.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-8">

            <?php
            // Argumentos para la consulta: traer 3 posts del tipo 'events'
            $args = array(
                'post_type'      => 'events',
                'posts_per_page' => 3, // La clave: solo trae 3
                'post_status'    => 'publish',
                'order'          => 'DESC', // Ordenados del más nuevo al más viejo
            );

            $more_events_query = new WP_Query($args);

            if ($more_events_query->have_posts()) :
                while ($more_events_query->have_posts()) : $more_events_query->the_post();
            ?>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <img class="aspect-video w-full object-cover" src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title_attribute(); ?>">
                            <?php else : ?>
                                <div class="aspect-video w-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Imagen no disponible</span>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <?php
                                $terms = get_the_terms(get_the_ID(), 'tipo_evento');
                                if ($terms && !is_wp_error($terms)) {
                                    $term = array_shift($terms);
                                    echo '<span class="text-xs font-semibold px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full">' . esc_html($term->name) . '</span>';
                                }
                                ?>
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-gray-800">
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
                wp_reset_postdata();
            else :
                echo '<p class="col-span-full text-center">No hay más eventos para mostrar.</p>';
            endif;
            ?>
        </div>

        <div class="text-center mt-20">
            <a href="<?php echo esc_url(get_post_type_archive_link('events')); ?>" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-full hover:bg-blue-700 transition-colors">
                Ver más
            </a>
        </div>

    </div>

</div>