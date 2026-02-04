<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">

        <div class="w-full md:w-2/5 bg-white flex flex-col justify-center p-8 sm:p-12">
            <div class="w-full max-w-sm mx-auto">

                <div class="flex items-center justify-center">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-6">
                </div>

                <h1 class="text-2xl font-bold text-center text-blue-600 mb-2"><?php the_field('titulo_siembra_page', 'option'); ?></h1>
                <p class="text-base text-gray-500 mb-4 text-center"><?php the_field('descripcion_siembra_page', 'option'); ?></p>
                <p class="text-sm text-gray-400 mb-6 text-center">Los campos marcados con <span class="text-red-500">*</span> son obligatorios</p>

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
                    <span class="block sm:inline">Hubo un problema al registrar tu siembra. Por favor, intenta de nuevo.</span>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Stepper -->
                <div class="mb-10">
                    <div class="flex items-center justify-between relative px-4">
                        <div class="absolute top-1/2 left-0 w-full h-[1px] bg-gray-100 -translate-y-1/2 z-0"></div>
                        <div id="progress-bar" class="absolute top-1/2 left-0 w-0 h-[1px] bg-blue-600 -translate-y-1/2 z-0 transition-all duration-500"></div>
                        
                        <div class="step-dot z-10 flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold border-2 border-white ring-1 ring-gray-100 transition-all duration-300">1</div>
                            <span class="text-[9px] uppercase tracking-tighter font-bold mt-1.5 text-blue-600">Siembra</span>
                        </div>
                        <div class="step-dot z-10 flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full bg-white text-gray-400 flex items-center justify-center text-xs font-bold border-2 border-white ring-1 ring-gray-100 transition-all duration-300">2</div>
                            <span class="text-[9px] uppercase tracking-tighter font-bold mt-1.5 text-gray-400">Datos</span>
                        </div>
                        <div class="step-dot z-10 flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full bg-white text-gray-400 flex items-center justify-center text-xs font-bold border-2 border-white ring-1 ring-gray-100 transition-all duration-300">3</div>
                            <span class="text-[9px] uppercase tracking-tighter font-bold mt-1.5 text-gray-400">Pago</span>
                        </div>
                    </div>
                </div>

                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" id="siembra-form">

                    <input type="hidden" name="action" value="procesar_formulario_siembra">
                    <?php wp_nonce_field('mi_form_siembra_nonce', 'mi_nonce'); ?>

                    <!-- Step 1: Siembra Details -->
                    <div class="step-content space-y-4 transition-all duration-300 origin-top" data-step="1">
                        <div>
                            <label for="tipo_siembra" class="block text-sm font-semibold text-gray-700 mb-1">Tipo de siembra</label>
                            <select id="tipo_siembra" name="tipo_siembra" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="" disabled selected>¿Qué deseas realizar?</option>
                                <option value="diezmo">Diezmo</option>
                                <option value="pacto">Pacto</option>
                                <option value="primicia">Primicia</option>
                                <option value="ofrenda">Ofrenda</option>
                            </select>
                        </div>

                        <div>
                            <label for="monto" class="block text-sm font-semibold text-gray-700 mb-1 text-center">Monto a sembrar</label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span id="currency-symbol" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                    <input type="number" id="monto" name="monto" required step="0.01"
                                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="0.00">
                                </div>
                                <select id="currency-selector" name="divisa"
                                    class="w-24 px-2 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm font-bold text-gray-600">
                                    <option value="USD" selected>$ (USD)</option>
                                    <option value="BS">Bs (VES)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="selectBanco" class="block text-sm font-semibold text-gray-700 mb-1">Método de Pago</label>
                            <select id="selectBanco" name="metodo_de_pago" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="" disabled selected>Selecciona un método...</option>
                                <option value="Zelle">Zelle</option>
                                <option value="BNC">Transferencia / Pago Móvil</option>
                            </select>
                        </div>

                        <div id="contenedor-Zelle" class="hidden mt-4 p-5 rounded-2xl bg-blue-50 border border-blue-100 animate-fade-in shadow-sm">
                            <p class="text-xs uppercase font-bold text-blue-600 mb-2">Datos Zelle</p>
                            <p class="text-sm font-medium text-gray-700 mb-1"><span class="text-gray-400">Correo:</span> nhradriver@yahoo.com</p>
                            <p class="text-sm font-medium text-gray-700"><span class="text-gray-400">Titular:</span> Luis Bracho</p>
                        </div>

                        <div id="contenedor-BNC" class="hidden mt-4 p-5 rounded-2xl bg-blue-50 border border-blue-100 animate-fade-in divide-y divide-blue-100 shadow-sm">
                            <div class="pb-3">
                                <p class="text-xs uppercase font-bold text-blue-600 mb-2">Pago Móvil (BNC)</p>
                                <p class="text-sm font-medium text-gray-700 mb-1"><span class="text-gray-400">RIF:</span> J-40389600-3</p>
                                <p class="text-sm font-medium text-gray-700"><span class="text-gray-400">Tel:</span> 0412-427-6773</p>
                            </div>
                            <div class="pt-3">
                                <p class="text-xs uppercase font-bold text-blue-600 mb-2">Transferencia</p>
                                <p class="text-sm font-medium text-gray-700 mb-1"><span class="text-gray-400">Cuenta:</span> 0191-0263-77-2100071126</p>
                                <p class="text-sm font-medium text-gray-700"><span class="text-gray-400">Tipo:</span> Corriente</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Personal Info -->
                    <div class="step-content space-y-4 hidden transition-all duration-300 origin-top" data-step="2">
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                            <input type="text" id="nombre" name="nombre" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="Ingresa tu nombre">
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-semibold text-gray-700 mb-1">Número de Teléfono <span class="text-red-500">*</span></label>
                            <input type="tel" id="telefono" name="telefono" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="Ej: 04121234567">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="tu@email.com">
                        </div>
                    </div>

                    <!-- Step 3: Reference and Prayer -->
                    <div class="step-content space-y-4 hidden transition-all duration-300 origin-top" data-step="3">
                        <div class="bg-gray-50 p-4 rounded-2xl border border-dashed border-gray-200 mb-4">
                            <p class="text-xs text-gray-500 text-center italic">Casi listos. Ingresa los datos del comprobante para finalizar.</p>
                        </div>
                        <div>
                            <label for="referencia" class="block text-sm font-semibold text-gray-700 mb-1">Número de Referencia <span class="text-red-500">*</span></label>
                            <input type="text" id="referencia" name="referencia" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="Pega aquí el código de referencia">
                        </div>

                        <div>
                            <label for="mensaje" class="block text-sm font-semibold text-gray-700 mb-1">Petición de Oración</label>
                            <textarea id="mensaje" name="mensaje" rows="4"
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="Escribe aquí tu petición..."></textarea>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="pt-8 flex gap-3">
                        <button type="button" id="prev-btn" class="hidden flex-1 px-4 py-3 border border-gray-200 text-gray-600 font-bold rounded-2xl hover:bg-gray-50 transition-all active:scale-95">
                            Atrás
                        </button>
                        <button type="button" id="next-btn" class="flex-1 px-4 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                            Siguiente
                        </button>
                        <button type="submit" id="submit-btn" class="hidden flex-1 px-4 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all active:scale-95 border-none outline-none">
                            Registrar Siembra
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:flex w-3/5 relative bg-cover bg-center"
            style="background-image: url('<?php the_field('siembra_imagen', 'option'); ?>');">
            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-blue-600/10 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-blue-600/10 transform -skew-y-12 bottom-[-10%] left-[-25%]">
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
    .shake {
        animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
    }
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('siembra-form');
        const steps = document.querySelectorAll('.step-content');
        const stepDots = document.querySelectorAll('.step-dot div');
        const stepLabels = document.querySelectorAll('.step-dot span');
        const progressBar = document.getElementById('progress-bar');
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const submitBtn = document.getElementById('submit-btn');
        const selectBanco = document.getElementById('selectBanco');
        
        let currentStep = 1;

        function updateUI() {
            // Update steps visibility
            steps.forEach(step => {
                if(parseInt(step.dataset.step) === currentStep) {
                    step.classList.remove('hidden');
                    step.classList.add('animate-fade-in');
                } else {
                    step.classList.add('hidden');
                    step.classList.remove('animate-fade-in');
                }
            });

            // Update Stepper
            stepDots.forEach((dot, index) => {
                const stepNum = index + 1;
                if(stepNum < currentStep) {
                    dot.classList.add('bg-blue-600', 'text-white');
                    dot.classList.remove('bg-gray-200', 'text-gray-500');
                    dot.innerHTML = '✓';
                } else if(stepNum === currentStep) {
                    dot.classList.add('bg-blue-600', 'text-white');
                    dot.classList.remove('bg-gray-200', 'text-gray-500');
                    dot.innerHTML = stepNum;
                } else {
                    dot.classList.add('bg-gray-200', 'text-gray-500');
                    dot.classList.remove('bg-blue-600', 'text-white');
                    dot.innerHTML = stepNum;
                }
            });

            stepLabels.forEach((label, index) => {
                const stepNum = index + 1;
                if(stepNum <= currentStep) {
                    label.classList.add('text-blue-600');
                    label.classList.remove('text-gray-400');
                } else {
                    label.classList.add('text-gray-400');
                    label.classList.remove('text-blue-600');
                }
            });

            const progress = ((currentStep - 1) / (steps.length - 1)) * 100;
            if(progressBar) progressBar.style.width = `${progress}%`;

            // Update Buttons
            if(currentStep === 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            } else if(currentStep === steps.length) {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function validateStep(step) {
            const currentStepEl = document.querySelector(`.step-content[data-step="${step}"]`);
            const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if(!input.value.trim()) {
                    isValid = false;
                    input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    input.classList.add('shake');
                    setTimeout(() => input.classList.remove('shake'), 400);
                } else {
                    input.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                }
            });

            return isValid;
        }

        // Navigation
        nextBtn.addEventListener('click', () => {
            if(validateStep(currentStep)) {
                currentStep++;
                updateUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        prevBtn.addEventListener('click', () => {
            currentStep--;
            updateUI();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Payment Toggle
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
                contenedores[valorSeleccionado].classList.add('animate-fade-in');
            }
        // Currency selector logic
        const currencySelector = document.getElementById('currency-selector');
        const currencySymbol = document.getElementById('currency-symbol');

        currencySelector.addEventListener('change', function() {
            currencySymbol.innerText = this.value === 'USD' ? '$' : 'Bs';
            // Adjust padding for the input field
            const montoInput = document.getElementById('monto');
            if(this.value === 'BS') {
                montoInput.style.paddingLeft = '3.5rem';
                currencySymbol.style.left = '1rem';
            } else {
                montoInput.style.paddingLeft = '2.5rem';
                currencySymbol.style.left = '1rem';
            }
        });

        updateUI();
    });
</script>
