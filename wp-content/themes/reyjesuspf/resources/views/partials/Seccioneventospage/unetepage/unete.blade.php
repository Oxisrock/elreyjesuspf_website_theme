<div>
    <section class="w-full bg-blue-50 p-4 py-16 sm:py-24">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center justify-center gap-4 md:flex-row md:gap-10">
                <div class="text-center md:w-[40%] md:text-left">
                    <h2 class="text-3xl font-bold text-[#2C3E50] mb-3">
                        Únete a Nuestra Familia
                    </h2>
                    <p class="text-medium text-gray-500">
                        Recibe invitaciones directamente a tu correo electrónico y participa<br />
                        en nuestros eventos.
                    </p>
                </div>

                <div class="w-full max-w-lg md:w-1/2">
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                        <div class="mb-4 flex flex-col gap-4">
                            <input type="text" id="nombre-completo" name="nombre" placeholder="Nombre Completo"
                                class="w-full rounded-3xl border border-gray-200 bg-white px-4 py-2 shadow-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="email" id="correo" name="email" placeholder="Correo electrónico"
                                class="w-full rounded-3xl border border-gray-200 bg-white px-4 py-2 shadow-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <p class="text-center text-xs text-gray-500 sm:text-left">
                                Al hacer clic en Registrarse, confirma que está de acuerdo con
                                nuestros
                                <a href="#" class="underline hover:text-blue-600">Términos y condiciones</a>.
                            </p>
                            <input type="hidden" name="action" value="procesar_formulario_boletines">
                            <input type="hidden" id="recaptcha_response" name="recaptcha_response">
                            <?php wp_nonce_field('mi_form_boletin_nonce', 'mi_nonce'); ?>
                            <button type="submit"
                                class="w-full whitespace-nowrap rounded-3xl bg-blue-600 px-12 py-2 font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                                Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Mostrar loading
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Enviando...</span>';
        submitBtn.disabled = true;

        // Ejecutar reCAPTCHA
        grecaptcha.ready(function() {
            grecaptcha.execute('6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc', {action: 'newsletter'}).then(function(token) {
                // Asignar el token al campo hidden
                document.getElementById('recaptcha_response').value = token;

                // Enviar el formulario
                form.submit();
            });
        });
    });
});
</script>
