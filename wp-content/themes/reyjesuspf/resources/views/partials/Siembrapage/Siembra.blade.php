<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">

        <div class="w-full md:w-2/5 bg-white flex flex-col justify-center p-8 sm:p-12">
            <div class="w-full max-w-sm mx-auto">

                <div class="flex items-center justify-center">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-6">
                </div>

                <h1 class="text-2xl font-bold text-center text-blue-600 mb-2"><?php the_field('titulo_siembra_page', 'option'); ?></h1>
                <p class="text-base text-gray-500 mb-8 text-center"><?php the_field('descripcion_siembra_page', 'option'); ?></p>

                <?php if (isset($_GET['enviado'])) : ?>
                <?php if ($_GET['enviado'] == 'true') : ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative text-center mb-6"
                    role="alert">
                    <strong class="font-bold">¡Siembra registrada!</strong>
                    <span class="block sm:inline">Dios te bendiga y gracias por tu generosidad.</span>
                </div>
                <?php else : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative text-center mb-6"
                    role="alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline">La verificación falló. Por favor, intenta de nuevo.</span>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="space-y-4">

                    <input type="hidden" name="action" value="procesar_formulario_siembra">
                    <input type="hidden" id="recaptcha_response" name="recaptcha_response">
                    <?php wp_nonce_field('mi_form_siembra_nonce', 'mi_nonce'); ?>

                    <div>
                        <label for="tipo_siembra" class="block text-sm font-medium text-gray-700">Tipo de
                            siembra</label>
                        <select id="tipo_siembra" name="tipo_siembra" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Selecciona un tipo...</option>
                            <option value="diezmo">Diezmo</option>
                            <option value="pacto">Pacto</option>
                            <option value="primicia">Primicia</option>
                            <option value="ofrenda">Ofrenda</option>
                        </select>
                    </div>

                    <div>
                        <label for="selectBanco" class="block text-sm font-medium text-gray-700">Metodo de Pago</label>
                        <select id="selectBanco" name="metodo_de_pago" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Selecciona un tipo...</option>
                            <option value="Zelle">Zelle</option>
                            <option value="BNC">Transferencia Bancaria o Pago Movil</option>
                        </select>
                    </div>

                    <div id="contenedor-Zelle" class="hidden mt-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        <h3 class="text-lg font-bold">Datos de Pago Zelle</h3>
                        <p>Correo: nhradriver@yahoo.com</p>
                        <p>Nombre: Luis Bracho</p>
                    </div>

                    <div id="contenedor-BNC" class="hidden mt-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        <h3 class="text-lg font-bold">Datos Bancarios</h3>
                        <p>Asociación civil Iglesia El Rey Jesús Punto Fijo</p>
                        <div class="my-3">
                            <p>Pago movil</p>
                            <p>Banco Nacional de Crédito</p>
                            <p>RIF: J-40389600-3</p>
                            <p>Tel: 0412-427-6773</p>
                        </div>
                        <hr class="my-3">
                        <div class="my-3">
                            <p>Transferencia</p>
                            <p>RIF: J-40389600-3</p>
                            <p>Cuenta: 0191-0263-77-2100071126</p>
                            <p>Tipo de Cuenta: Corriente</p>
                        </div>
                    </div>

                    <div>
                        <label for="monto" class="block text-sm font-medium text-gray-700">Monto a sembrar</label>
                        <input type="number" id="monto" name="monto" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ej: 50.00">
                    </div>

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" id="nombre" name="nombre"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700">Numero de Telefono</label>
                        <input type="tel" id="telefono" name="telefono"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" id="email" name="email"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="referencia" class="block text-sm font-medium text-gray-700">Numero de
                            Referencia</label>
                        <input type="text" id="referencia" name="referencia"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mensaje" class="block text-sm font-medium text-gray-700">Petición De Oración</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-full hover:bg-blue-700 transition-colors">
                            Proceder al Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:flex w-3/5 relative bg-cover bg-center"
            style="background-image: url('<?php the_field('siembra_imagen', 'option'); ?>');">
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

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Mostrar loading
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Procesando...</span>';
        submitBtn.disabled = true;

        // Ejecutar reCAPTCHA
        grecaptcha.ready(function() {
            grecaptcha.execute('6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc', {action: 'donation'}).then(function(token) {
                // Asignar el token al campo hidden
                document.getElementById('recaptcha_response').value = token;

                // Enviar el formulario
                form.submit();
            });
        });
    });
});
</script>

<script>
    // Script para mostrar/ocultar métodos de pago
    document.addEventListener('DOMContentLoaded', function() {
        const selectBanco = document.getElementById('selectBanco');
        const contenedores = {
            'Zelle': document.getElementById('contenedor-Zelle'),
            'BNC': document.getElementById('contenedor-BNC'),
        };

        selectBanco.addEventListener('change', function() {
            const valorSeleccionado = this.value;
            for (const key in contenedores) {
                if (contenedores[key]) {
                    contenedores[key].classList.add('hidden');
                }
            }
            if (contenedores[valorSeleccionado]) {
                contenedores[valorSeleccionado].classList.remove('hidden');
            }
        });
    });
</script>
