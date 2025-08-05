<div class="bg-white font-sans">
    <div class="max-w-4xl mx-auto py-16 lg:py-24 px-6">
        <div class="text-left md:text-center mb-12">
            <h1 class="text-3xl md:text-3xl font-bold text-slate-800">
                Preguntas Frecuentes
            </h1>
            <p class="text-left md:text-center mt-4 text-sm text-gray-600 max-w-2xl mx-auto">
                Aquí encontrarás respuestas a las dudas más comunes. Si no encuentras lo que buscas, no dudes en
                contactarnos.
            </p>
        </div>

        <div class="space-y-6">
            <?php
            // Argumentos para la consulta de WordPress
            $args = array(
                'post_type'      => 'faq',          // Llama a nuestro Custom Post Type 'faq'
                'posts_per_page' => 6,              // Pide un máximo de 6 entradas
                'orderby'        => 'date',         // Las ordena por fecha de publicación
                'order'          => 'DESC'          // En orden descendente (las más nuevas primero)
            );

            // Creamos una nueva instancia de WP_Query
            $faq_query = new WP_Query($args);

            // El Loop de WordPress
            if ($faq_query->have_posts()) :
                while ($faq_query->have_posts()) : $faq_query->the_post();
            ?>
            <div class="faq-item">
                <button class="accordion-header flex items-center justify-between w-full text-left py-3">
                    <span class="text-base font-medium text-gray-900"><?php the_title(); ?></span>
                    <span class="transform transition-transform duration-300">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </span>
                </button>
                <div class="accordion-content hidden mt-2">
                    <div class="text-sm text-gray-600">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <?php
                // Añade una línea divisoria <hr> entre los elementos, pero no después del último.
                if ( $faq_query->current_post + 1 < $faq_query->post_count ) {
                    echo '<hr class="border-t border-gray-200 my-4">';
                }
                endwhile;
                // Restaura los datos originales del post
                wp_reset_postdata();
            else :
                // Mensaje por si no se encuentran FAQs
                echo '<p>No hay preguntas frecuentes en este momento.</p>';
            endif;
            ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const accordionHeaders = document.querySelectorAll('.accordion-header');

        accordionHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const iconSpan = header.querySelector('span:last-child');
                const iconSvg = iconSpan.querySelector(
                'svg'); // Seleccionamos el SVG dentro del span

                // Alternar la visibilidad del contenido
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    // Rota el ícono hacia arriba
                    iconSvg.style.transform = 'rotate(180deg)';
                } else {
                    content.classList.add('hidden');
                    // Devuelve el ícono a su posición original
                    iconSvg.style.transform = 'rotate(0deg)';
                }
            });
        });
    });
</script>
