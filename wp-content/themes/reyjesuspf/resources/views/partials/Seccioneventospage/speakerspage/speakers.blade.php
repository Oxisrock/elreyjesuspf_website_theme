<div class="bg-white">
    <div class="container mx-auto px-6 sm:px-8 md:px-16 lg:px-20 py-10 md:py-12">
        <section class="py-12 sm:py-16 lg:py-18">
            <div class="text-center">
                <h2 class="text-lg font-bold text-indigo-600">
                    Speakers
                </h2>
                <p class="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                    <?php the_field('titulo_speakers'); ?>
                </p>
                <p class="mt-4 max-w-2xl mx-auto text-base text-gray-500">
                    <?php the_field('descripcion_de_speakers'); ?>
                </p>
            </div>

            <?php
            // Comprueba si existe el campo repetidor 'ponentes'.
            // ¡¡ASEGÚRATE DE QUE 'ponentes' ES EL NOMBRE CORRECTO DE TU REPETIDOR!!
            if( have_rows('speakers_del_evento') ): // <--- LÍNEA CORREGIDA
            ?>
            <div class="mt-12 grid grid-cols-1 gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-12">

                <?php
                // Inicia el loop del Repetidor 'ponentes'.
                while( have_rows('speakers_del_evento') ) : the_row(); // <--- LÍNEA CORREGIDA
                    if( get_row_layout() == 'speakers' ):
                    // Guardamos los datos en variables
                    $imagen = get_sub_field('imagen_del_speakers');
                    $nombre = get_sub_field('nombre_del_speakers');
                    $cargo = get_sub_field('ocupacion_del_speakers');
                    $descripcion = get_sub_field('bibliografica_del_speakers');
                    $facebook_url = get_sub_field('facebook_del_speakers');
                    $instagram_url = get_sub_field('instagram_del_speakers');
                    $twitter_url = get_sub_field('x_del_speakers');
                    $linkedin_url = get_sub_field('linkedin_del_speakers');
                ?>

                <div class="speaker-card flex flex-col">
                    <?php if ($imagen): ?>
                    <img class="rounded-2xl w-full h-80 object-cover object-top" src="<?php echo $imagen['url']; ?>"
                        alt="<?php echo $imagen['alt']; ?>">
                    <?php endif; ?>
                    <div class="mt-4 flex flex-col flex-grow">
                        <h3 class="text-xl text-center font-bold text-gray-900"><?php echo esc_html($nombre); ?></h3>
                        <p class="text-center font-semibold text-indigo-600"><?php echo esc_html($cargo); ?></p>
                        <div class="mt-2 text-center text-gray-600 flex-grow">
                            <?php echo wp_kses_post($descripcion); ?>
                        </div>

                        <div class="mt-4 flex items-center justify-center space-x-4">
                            <?php if ($facebook_url): ?>
                            <a href="<?php echo $facebook_url; ?>" class="text-indigo-600 hover:text-indigo-800" target="_blank"
                                rel="noopener noreferrer">
                                <span class="sr-only">Facebook</span>
                                <i class="fa-brands fa-square-facebook text-2xl" aria-hidden="true"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($instagram_url): ?>
                            <a href="<?php echo $instagram_url; ?>" class="text-indigo-600 hover:text-indigo-800" target="_blank"
                                rel="noopener noreferrer">
                                <span class="sr-only">Instagram</span>
                                <i class="fa-brands fa-instagram text-2xl" aria-hidden="true"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($twitter_url): ?>
                            <a href="<?php echo $twitter_url; ?>" class="text-indigo-600 hover:text-indigo-800" target="_blank"
                                rel="noopener noreferrer">
                                <span class="sr-only">X (Twitter)</span>
                                <i class="fa-brands fa-square-x-twitter text-2xl" aria-hidden="true"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($linkedin_url): ?>
                            <a href="<?php echo $linkedin_url; ?>" class="text-indigo-600 hover:text-indigo-800" target="_blank"
                                rel="noopener noreferrer">
                                <span class="sr-only">LinkedIn</span>
                                <i class="fa-brands fa-square-linkedin text-2xl" aria-hidden="true"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php 
                endif;
                endwhile; // Fin del loop del Repetidor
                ?>
            </div>
            <?php 
            endif; // Fin de la comprobación del Repetidor
            ?>
        </section>
    </div>
</div>
