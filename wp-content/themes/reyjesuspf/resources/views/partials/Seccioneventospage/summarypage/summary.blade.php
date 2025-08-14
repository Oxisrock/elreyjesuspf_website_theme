<div class="bg-white container mx-auto px-6 sm:px-8 md:px-16 lg:px-18 py-12 md:py-16 min-h-screen">

    <div class="container mx-auto max-w-14xl">
        <div class="text-left pl-4 md:pl-10">
            <?php
            // Asegúrate de que estamos en una publicación individual de tu CPT
            if (is_singular('events')) {
                global $post; // Objeto del post actual
            
                // 1. Enlace al Archivo del CPT (Ej: "Eventos")
                $post_type = get_post_type_object(get_post_type());
                if ($post_type) {
                    echo '<a href="' . get_post_type_archive_link($post_type->name) . '"><span class="text-sm font-semibold text-black-600">' . esc_html($post_type->labels->name) . '</span></a>';
                    echo '<span class="separator"> › </span>';
                }
            
                // 2. Enlace a la Categoría del Evento (Término de la Taxonomía, ej: "ELIAP")
                // Reemplaza 'tu_taxonomia_slug' con el slug de tu taxonomía (ej: 'tipo_de_evento')
                $terms = get_the_terms($post->ID, 'tipo_evento');
            
                if (!empty($terms) && !is_wp_error($terms)) {
                    // Usamos el primer término asignado al evento
                    $the_term = $terms[0];
                    echo '<a href="' . get_term_link($the_term) . '"><span class="text-sm font-semibold text-black-600">' . esc_html($the_term->name) . '</span></a>';
                    echo '<span class="separator"> › </span>';
                }
            
                // 3. Título del Evento Actual (no es un enlace, ej: "ELIAP 2025")
                echo '<span class="text-sm font-semibold text-blue-600">' . get_the_title() . '</span>';
            }
            ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-10 rounded-xl overflow-hidden">

            <div
                class="md:col-span-4 md:p-10 py-10 flex flex-col justify-center space-y-6 md:order-first order-last text-left sm:text-left">

                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 leading-tight md:order-1 order-1 ">
                    <?php the_title(); ?></h1>

                <div
                    class="flex flex-col md:flex-row items-left mb:items-center space-x-0 md:space-x-2 md:order-2 order-3 ">
                    <div class="flex items-center space-x-1 text-zinc-900 md:text-gray-700 text-xs md:text-sm ">
                        <svg class="w-6 h-6 md:w-4 md:h-4  text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span><?php the_field('fecha_del_evento'); ?></span>
                    </div>
                    <div class="flex items-center space-x-1 text-zinc-900 md:text-gray-700 text-xs md:text-sm">
                        <svg class="w-6 h-6 md:w-4 md:h-4 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span><?php the_field('lugar_de_evento_titulo'); ?></span>
                    </div>
                </div>

                <p class="text-gray-600 text-sm md:order-3 order-2">
                    <?php the_field('descripcion_del_evento_'); ?>
                </p>

                <div class="space-y-2 md:order-5 order-5">

                    <!-- 1. Envolvemos todo en una etiqueta <form> -->
                    <form id="event-registration-form" class="space-y-2">
                        <div class="w-full items-center space-y-2 md:space-y-2">

                            <!-- 2. Añadimos un campo oculto para el ID del evento -->
                            <input type="hidden" name="event_id" value="<?php echo get_the_ID(); ?>">

                            <!-- Nuevo div para email y teléfono, con flex-col -->
                            <div class="flex flex-col md:flex-row w-full space-x-2">
                                <div class="w-[240px]">
                                    <input type="text" name="nombre" placeholder="Nombre Completo"
                                        class="w-full p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-[240px]">
                                    <input type="tel" name="phone_number" placeholder="Teléfono"
                                        class="w-full p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="space-x-2">
                                <input type="email" name="email" placeholder="Ingresa tu correo electrónico"
                                    required
                                    class="w-[475px] p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <!-- Reemplaza tu botón de envío con este -->
                            <div class="flex justify-center items-center pt-2">
                                <button type="submit"
                                    class="w-[340px] md:w-[250px] flex-shrink-0 bg-blue-600 text-white py-2 px-7 rounded-full hover:bg-blue-700 transition-colors flex items-center justify-center">
                                    <span class="button-text">Registrarme</span>
                                    <svg class="animate-spin ml-2 h-5 w-5 text-white hidden button-spinner"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- 4. Contenedor para mostrar mensajes de éxito o error -->
                        <div id="form-messages" class="text-sm text-center mt-2"></div>
                    </form>


                    <p class="text-sm md:text-xs text-zinc-500 md:text-gray-500 md:order-6 order-6">
                        Al hacer clic en Registrarse, confirma que está de acuerdo con nuestros <a href="#"
                            class="underline">términos y condiciones</a>.
                    </p>
                </div>
            </div>

            <div class="md:col-span-6 relative overflow-hidden md:order-last order-first">
                <img src="<?php the_post_thumbnail_url('full'); ?>" alt="<?php the_title_attribute(); ?>"
                    class="rounded-3xl w-full h-full object-center object-cover">
            </div>

        </div>
    </div>

</div>
