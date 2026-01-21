<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">
        {{-- Columna del Formulario --}}
        <div class="w-full md:w-2/5 bg-white flex flex-col justify-center p-6 sm:p-8 md:p-12">
            <div class="w-full max-w-sm mx-auto">
                <div class="flex items-center justify-center">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-8">
                </div>

                {{-- Contenedor del Spinner (Oculto por defecto) --}}
                <div id="loadingSpinner" class="hidden text-center py-16">
                    <div class="inline-block h-12 w-12 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]"
                        role="status"></div>
                    <p class="mt-4 text-gray-600">Procesando tu registro...</p>
                </div>

                {{-- Mensaje de Éxito (Oculto por defecto) --}}
                <div id="successMessage" class="hidden text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="mt-4 text-2xl font-bold text-gray-800">¡Registro Completado!</h2>
                    <p class="mt-2 text-gray-600">Gracias por unirte.</p>
                </div>

                {{-- Mensaje de Error de cuenta duplicada --}}
                <div id="registrationErrorState" class="hidden text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="mt-4 text-2xl font-bold text-gray-800">Parece que ya tienes una cuenta</h2>
                    <p id="errorStateMessage" class="mt-2 text-gray-600"></p>
                </div>

                {{-- Contenedor del Formulario --}}
                <div id="formContainer">
                    <h1 class="text-2xl font-bold text-center text-blue-600 mb-2"><?php the_field('titulo_de_registrarme', 'option'); ?></h1>
                    <p class="text-sm text-gray-500 mb-8 text-center"><?php the_field('descripcion_de_registrarme', 'option'); ?></p>

                    <div id="ajaxErrors" class="hidden mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                    </div>

                    <form id="registrationForm" method="POST" class="space-y-2">
                        <input type="hidden" id="recaptcha_response" name="recaptcha_response">
                        <input type="hidden" name="action" value="ajax_custom_register">
                        @php wp_nonce_field('custom_register_nonce', 'security_nonce'); @endphp

                        <div>
                            <input type="text" name="full_name" placeholder="Nombre Completo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <div id="full_name-error" class="text-red-500 text-xs mt-1 ml-4 h-4"></div>
                        </div>

                        <div>
                            <input type="tel" name="phone" placeholder="Teléfono"
                                class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div id="phone-error" class="text-red-500 text-xs mt-1 ml-4 h-4"></div>
                        </div>

                        <div>
                            <input type="email" name="email" placeholder="Correo electrónico"
                                class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <div id="email-error" class="text-red-500 text-xs mt-1 ml-4 h-4"></div>
                        </div>

                        <div>
                            <input type="password" name="password" placeholder="Contraseña"
                                class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <div id="password-error" class="text-red-500 text-xs mt-1 ml-4 h-4"></div>
                        </div>

                        <div>
                            <input type="password" name="password_confirm" placeholder="Confirmar contraseña"
                                class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <div id="password_confirm-error" class="text-red-500 text-xs mt-1 ml-4 h-4"></div>
                        </div>

                        <div class="pt-2">
                            <div class="flex items-center">
                                <input id="terms" name="terms" type="checkbox"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                                <label for="terms" class="ml-2 block text-sm text-gray-700">Acepto los <a
                                        href="#" class="font-medium text-blue-600 hover:underline">Términos y
                                        Condiciones</a></label>
                            </div>
                            <div id="terms-error" class="text-red-500 text-xs mt-1 ml-4 h-4"></div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-bold py-3 rounded-full hover:bg-blue-700 transition-colors">
                                REGISTRARME
                            </button>
                            <a href="{{ home_url() }}"
                                class="block text-center mt-4 text-sm font-bold text-blue-600 hover:underline">
                                VOLVER
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Columna de la Imagen --}}
        <div class="hidden md:flex w-full md:w-3/5 md:h-auto relative bg-cover bg-center"
            style="background-image: url('<?php the_field('imagen_de_registrarme', 'option'); ?>');">
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
    const form = document.getElementById('registrationForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Mostrar loading
        submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Procesando...</span>';
        submitBtn.disabled = true;

        // Ejecutar reCAPTCHA
        grecaptcha.ready(function() {
            grecaptcha.execute('6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc', {action: 'register'}).then(function(token) {
                // Asignar el token al campo hidden
                document.getElementById('recaptcha_response').value = token;

                // Crear FormData con los datos del formulario
                const formData = new FormData(form);

                // Enviar vía AJAX
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Éxito - redirigir o mostrar mensaje
                        window.location.href = '<?php echo home_url('/login?registered=true'); ?>';
                    } else {
                        // Error - mostrar mensaje
                        const errorDiv = document.getElementById('ajaxErrors');
                        errorDiv.classList.remove('hidden');

                        if (data.data.errors) {
                            // Mostrar errores de validación
                            let errorHtml = '<ul>';
                            for (const [field, message] of Object.entries(data.data.errors)) {
                                errorHtml += `<li>${message}</li>`;
                            }
                            errorHtml += '</ul>';
                            errorDiv.innerHTML = errorHtml;
                        } else if (data.data.message) {
                            errorDiv.innerHTML = data.data.message;
                        } else if (data.data.error_type === 'duplicate_email') {
                            errorDiv.innerHTML = data.data.message;
                        }

                        // Resetear botón
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorDiv = document.getElementById('ajaxErrors');
                    errorDiv.classList.remove('hidden');
                    errorDiv.innerHTML = 'Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo.';
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
            });
        });
    });
});
</script>