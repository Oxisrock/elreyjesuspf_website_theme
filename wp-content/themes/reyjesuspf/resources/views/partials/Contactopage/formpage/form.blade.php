<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">

        <div class="w-full md:w-2/5 bg-white flex flex-col justify-center p-8 sm:p-12">
            <div class="w-full max-w-sm mx-auto">

                <div class="flex items-center justify-center">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-6">
                </div>

                <h1 class="text-2xl font-bold text-center text-blue-600 mb-2"><?php the_field('titulo_contactos_page', 'option'); ?></h1>
                <p class="text-base text-gray-500 mb-8 text-center"><?php the_field('descripcion_contactos_page', 'option'); ?></p>

                <?php // --- ZONA DE MENSAJES DE ESTADO --- ?>
                <?php if (isset($_GET['enviado'])) : ?>
                    <?php if ($_GET['enviado'] == 'true') : ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative text-center mb-6" role="alert">
                            <strong class="font-bold">¡Mensaje Enviado!</strong>
                            <span class="block sm:inline">Dios te bendiga y Gracias por contactarnos.</span>
                        </div>
                    <?php else : // Si 'enviado' no es 'true', es un error. ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative text-center mb-6" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">No se pudo enviar tu mensaje. Por favor, inténtalo de nuevo.</span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="space-y-4">

                    <input type="hidden" name="action" value="procesar_formulario_contacto">
                    <?php wp_nonce_field('mi_form_contacto_nonce', 'mi_nonce'); ?>
                    <input type="hidden" id="recaptcha_response" name="recaptcha_response">

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="asunto" class="block text-sm font-medium text-gray-700">Asunto</o>
                            <select id="asunto" name="asunto" required
                                class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled selected>Selecciona un motivo...</option>
                                <option value="peticion_oracion">Petición de Oración</option>
                                <option value="informacion_general">Información General</option>
                                <option value="voluntariado">Quiero ser voluntario</option>
                                <option value="visita_pastoral">Solicitar una visita pastoral</option>
                                <option value="otro">Otro</option>
                            </select>
                    </div>

                    <div>
                        <label for="mensaje" class="block text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-full hover:bg-blue-700 transition-colors">
                            Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:flex w-3/5 relative bg-cover bg-center"
            style="background-image: url('<?php the_field('imagen_de_contactos', 'option'); ?>');">
            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 bottom-[-10%] left-[-25%]">
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

    form.addEventListener('submit', function(e) {
        // Solo interceptar si hay botón de submit
        if (submitBtn) {
            e.preventDefault();

            // Mostrar loading
            submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Enviando...</span>';
            submitBtn.disabled = true;
        }

        // Ejecutar reCAPTCHA
        grecaptcha.ready(function() {
            grecaptcha.execute('6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc', {action: 'contact'}).then(function(token) {
                // Asignar el token al campo hidden
                document.getElementById('recaptcha_response').value = token;

                // Enviar el formulario normalmente
                if (submitBtn) {
                    form.submit();
                }
            });
        });
    });
});
</script>
