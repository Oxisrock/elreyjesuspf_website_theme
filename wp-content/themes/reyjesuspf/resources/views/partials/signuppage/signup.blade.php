@php
    // Hacemos la variable de errores globalmente accesible en la plantilla
    global $registration_errors;
@endphp

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

                <div id="registrationErrorState" class="hidden text-center py-16">
                    {{-- Icono de X --}}
                    <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="mt-4 text-2xl font-bold text-gray-800">Parece que ya tienes una cuenta</h2>
                    {{-- El mensaje con los enlaces se insertará aquí --}}
                    <p id="errorStateMessage" class="mt-2 text-gray-600"></p>

                </div>

                {{-- Contenedor del Formulario --}}
                <div id="formContainer">
                    <h1 class="text-2xl font-bold text-center text-blue-600 mb-2"><?php the_field('titulo_de_registrarme', 'option'); ?></h1>
                    <p class="text-sm text-gray-500 mb-8 text-center"><?php the_field('descripcion_de_registrarme', 'option'); ?></p>

                    {{-- Contenedor para errores de AJAX --}}
                    <div id="ajaxErrors" class="hidden mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                    </div>

                    <form id="registrationForm" method="POST" class="space-y-2"> {{-- Reduje el space-y para que los errores se vean más cerca --}}
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
            {{-- ... el resto de tu columna de imagen ... --}}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registrationForm');
        const formContainer = document.getElementById('formContainer');
        const spinner = document.getElementById('loadingSpinner');
        const successMessage = document.getElementById('successMessage');

        // Nuevos elementos para la pantalla de error
        const errorState = document.getElementById('registrationErrorState');
        const errorStateMessage = document.getElementById('errorStateMessage');
        const tryAgainBtn = document.getElementById('tryAgainBtn');

        const errorDivs = form.querySelectorAll('[id$="-error"]');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            errorDivs.forEach(div => div.textContent = '');
            formContainer.classList.add('hidden');
            errorState.classList.add('hidden'); // Ocultar pantalla de error si estaba visible
            spinner.classList.remove('hidden');

            const formData = new FormData(form);
            const ajaxUrl = '{{ admin_url('admin-ajax.php') }}';

            fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    spinner.classList.add('hidden');

                    if (data.success) {
                        successMessage.classList.remove('hidden');
                    } else {
                        // ---- INICIO DE LA NUEVA LÓGICA DE ERRORES ----

                        // Verificamos si es nuestro error especial de email duplicado
                        if (data.data && data.data.error_type === 'duplicate_email') {
                            // Es el error de email duplicado: mostramos la pantalla completa
                            errorStateMessage.innerHTML = data.data
                            .message; // Usamos innerHTML por los enlaces <a>
                            errorState.classList.remove('hidden');
                        } else {
                            // Es un error de validación normal: mostramos errores en las casillas
                            if (data.data.errors) {
                                for (const fieldName in data.data.errors) {
                                    const errorDiv = document.getElementById(fieldName + '-error');
                                    if (errorDiv) {
                                        errorDiv.innerHTML = data.data.errors[fieldName];
                                    }
                                }
                            }
                            formContainer.classList.remove(
                            'hidden'); // Mostramos el formulario de nuevo
                        }
                        // ---- FIN DE LA NUEVA LÓGICA DE ERRORES ----
                    }
                })
                .catch(error => {
                    spinner.classList.add('hidden');
                    const generalErrorDiv = document.getElementById('full_name-error');
                    if (generalErrorDiv) generalErrorDiv.textContent =
                        'Error de conexión. Intenta de nuevo.';
                    formContainer.classList.remove('hidden');
                    console.error('Error:', error);
                });
        });

        // Evento para el botón "VOLVER A INTENTAR"
        tryAgainBtn.addEventListener('click', function() {
            errorState.classList.add('hidden'); // Ocultar la pantalla de error
            formContainer.classList.remove('hidden'); // Mostrar el formulario
        });
    });
</script>
