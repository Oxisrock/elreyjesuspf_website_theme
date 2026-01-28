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
                        <input type="hidden" name="recaptcha_response" id="recaptcha_response_event">
                        <div class="w-full items-center space-y-2 md:space-y-2">

                            <!-- 2. Añadimos un campo oculto para el ID del evento -->
                            <input type="hidden" name="event_id" value="<?php echo get_the_ID(); ?>">

                            <!-- Nuevo div para email y teléfono, con flex-col -->
                            <div class="flex flex-col items-center md:flex-row md:justify-center w-full md:space-x-2">
                                <div class="w-[340px] md:w-[240px] mb-2 md:mb-0">
                                    <input type="text" name="nombre" placeholder="Nombre Completo"
                                        class="w-full p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-[340px] md:w-[240px]">
                                    <input type="cedula" name="cedula" placeholder="Cedula"
                                        class="w-full p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="flex flex-col items-center md:flex-row md:justify-center w-full md:space-x-2">
                                <div class="w-[340px] md:w-[240px] mb-2 md:mb-0">
                                    <input type="tel" name="phone_number" placeholder="Teléfono"
                                        class="w-full p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-[340px] md:w-[240px]">
                                    <input type="email" name="email" placeholder="Ingresa tu correo electrónico"
                                        required
                                        class="w-full p-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="flex justify-center items-end space-x-2">

                                <div class="w-full max-w-xs">
                                    <div class="relative">
                                        <select id="iglesia" name="iglesia"
                                            class="w-full appearance-none rounded-full border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm">
                                            <option value="" disabled selected>Selecciona una iglesia</option>
                                            <option value="particular">Particular</option>
                                            <option value="ERJPF">El Rey Jesús Punto Fijo (ERJPF)</option>
                                            <option value="Iglesia Cobertura">Iglesia Cobertura</option>
                                            <option value="Iglesia Local">Iglesia Local</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="campo-red" class="w-full max-w-xs hidden">
                                    <div class="relative">
                                        <select id="red" name="red"
                                            class="block w-full appearance-none rounded-full border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm">
                                            <option value="" disabled selected>Selecciona tu red</option>
                                            <option value="1">Red 1</option>
                                            <option value="2">Red 2</option>
                                            <option value="3">Red 3</option>
                                            <option value="4">Red 4</option>
                                            <option value="5">Red 5</option>
                                            <option value="6">Red 6</option>
                                            <option value="7">Red 7</option>
                                            <option value="8">Red 8</option>
                                            <option value="9">Red 9</option>
                                        </select>
                                    </div>
                                </div>

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
<script src="https://www.google.com/recaptcha/api.js?render=6LfflFgsAAAAAOYKX6iPoJkKCVJNWiN5fq7vQdsj"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA PARA LOS SELECTORES DE IGLESIA Y RED ---
    const selectIglesia = document.getElementById('iglesia');
    const campoRed = document.getElementById('campo-red');

        // 2. Creamos la función que controla la visibilidad

        function toggleRedSelector() {

            // Comprobamos el VALOR del select, no si está "checked"

            if (selectIglesia.value === 'ERJPF') {

                // Si es ERJPF, le quitamos la clase 'hidden' para mostrarlo

                campoRed.classList.remove('hidden');

            } else {

                // Para cualquier otro valor, le añadimos la clase 'hidden' para ocultarlo

                campoRed.classList.add('hidden');
            }
        }

        // 3. Añadimos el "escuchador" al primer select

        selectIglesia.addEventListener('change', toggleRedSelector);

        // 4. Ejecutamos la función una vez al cargar la página para asegurar el estado inicial

        toggleRedSelector();

    });
</script>
