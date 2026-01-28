<div class="bg-sky-50 min-h-screen">

    <div class="container max-w-6xl mx-auto py-12 px-4">
        <div class="flex flex-col lg:flex-row items-center">

            <div class="w-full lg:w-1/2 mb-8 lg:mb-0">
                <img class="object-cover w-full h-auto rounded-xl" src="<?php the_field('imagen_paso_de_fe', 'option'); ?>"
                    alt="Persona en un escenario frente a una multitud con luces">
            </div>

            <div class="w-full lg:w-1/2 p-6 lg:p-20">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    <?php the_field('titulo_paso_de_fe', 'option'); ?>
                </h2>
                <p class="text-gray-600 text-sm mb-8">
                    <?php the_field('descripcion_paso_de_fe', 'option'); ?>
                </p>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                    <div class="mb-6">
                        <input type="text" id="nombre-completo" name="nombre" placeholder="Nombre Completo"
                            class="w-full p-2 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-6">
                        <input type="email" id="correo" name="email" placeholder="Correo electrónico"
                            class="w-full p-2 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p class="text-xs text-gray-500 mb-6">
                        Al hacer clic en registrarme, acepto nuestros <a href="#"
                            class="text-blue-600 hover:underline">Términos y condiciones</a>.
                    </p>

                    <input type="hidden" name="action" value="procesar_formulario_boletines">
                    <input type="hidden" id="recaptcha_response" name="recaptcha_response">
                    <?php wp_nonce_field('mi_form_boletin_nonce', 'mi_nonce'); ?>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        Registrarme
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LfflFgsAAAAAOYKX6iPoJkKCVJNWiN5fq7vQdsj"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Mostrar loading
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Registrando...</span>';
        submitBtn.disabled = true;

        // Ejecutar reCAPTCHA
        grecaptcha.ready(function() {
            grecaptcha.execute('6LfflFgsAAAAAOYKX6iPoJkKCVJNWiN5fq7vQdsj', {action: 'newsletter'}).then(function(token) {
                // Asignar el token al campo hidden
                document.getElementById('recaptcha_response').value = token;

                // Enviar el formulario
                form.submit();
            });
        });
    });
});
</script>
