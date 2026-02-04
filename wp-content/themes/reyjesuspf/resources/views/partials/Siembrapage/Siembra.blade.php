<div class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800">

    <div class="w-full md:w-1/2 lg:w-5/12 bg-white flex flex-col justify-center p-6 sm:p-10 relative z-20 shadow-2xl">
        
        <div class="w-full max-w-md mx-auto">
            
            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center shrink-0">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo" class="h-8 w-auto">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 leading-tight"><?php the_field('titulo_siembra_page', 'option'); ?></h1>
                    <p class="text-xs text-slate-500 font-medium">Siembra y diezma con propósito</p>
                </div>
            </div>

            <?php if (isset($_GET['enviado'])) : ?>
                <div class="mb-6 p-4 rounded-2xl <?php echo $_GET['enviado'] == 'true' ? 'bg-green-50 border-green-100 text-green-800' : 'bg-red-50 border-red-100 text-red-800'; ?> border flex gap-3 items-center animate-fade-in">
                    <span class="text-2xl"><?php echo $_GET['enviado'] == 'true' ? '🙌' : '⚠️'; ?></span>
                    <div>
                        <p class="font-bold text-sm"><?php echo $_GET['enviado'] == 'true' ? '¡Siembra Recibida!' : 'Hubo un error'; ?></p>
                        <p class="text-xs opacity-80"><?php echo $_GET['enviado'] == 'true' ? 'Dios bendiga tu generosidad.' : 'Intenta nuevamente.'; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center mb-8 relative px-2">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -z-10 rounded-full"></div>
                <div class="step-indicator flex flex-col items-center gap-1 cursor-pointer transition-all" onclick="goToStep(1)">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm ring-4 ring-white shadow-md transition-all">1</div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Monto</span>
                </div>
                <div class="step-indicator flex flex-col items-center gap-1 opacity-40 transition-all">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm ring-4 ring-white transition-all">2</div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Datos</span>
                </div>
                <div class="step-indicator flex flex-col items-center gap-1 opacity-40 transition-all">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm ring-4 ring-white transition-all">3</div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pago</span>
                </div>
            </div>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" id="siembra-form" class="relative min-h-[420px]">
                <input type="hidden" name="action" value="procesar_formulario_siembra">
                <?php wp_nonce_field('mi_form_siembra_nonce', 'mi_nonce'); ?>

                <div class="step-content absolute inset-0 transition-all duration-300" data-step="1">
                    
                    <div class="bg-slate-100 p-1.5 rounded-2xl flex relative mb-6">
                        <div id="currency-bg" class="absolute left-1.5 top-1.5 bottom-1.5 w-[calc(50%-6px)] bg-white rounded-xl shadow-sm transition-all duration-300 ease-out"></div>
                        
                        <label class="flex-1 text-center py-2.5 z-10 cursor-pointer select-none relative" onclick="setCurrency('USD')">
                            <input type="radio" name="divisa" value="USD" class="sr-only" checked onchange="updateCurrencyUI()">
                            <span class="text-sm font-bold transition-colors duration-300" id="label-usd">🇺🇸 Dólar ($)</span>
                        </label>
                        
                        <label class="flex-1 text-center py-2.5 z-10 cursor-pointer select-none relative" onclick="setCurrency('BS')">
                            <input type="radio" name="divisa" value="BS" class="sr-only" onchange="updateCurrencyUI()">
                            <span class="text-sm font-bold text-slate-500 transition-colors duration-300" id="label-bs">🇻🇪 Bolívar (Bs)</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="diezmo" class="peer sr-only" required>
                            <div class="h-full p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center">
                                <span class="text-2xl mb-1 block">🌱</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Diezmo</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="ofrenda" class="peer sr-only" required>
                            <div class="h-full p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center">
                                <span class="text-2xl mb-1 block">🎁</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Ofrenda</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="pacto" class="peer sr-only" required>
                            <div class="h-full p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center">
                                <span class="text-2xl mb-1 block">🤝</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Pacto</span>
                            </div>
                        </label>
                         <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="primicia" class="peer sr-only" required>
                            <div class="h-full p-4 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center">
                                <span class="text-2xl mb-1 block">🍞</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Primicia</span>
                            </div>
                        </label>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Monto a Sembrar</label>
                        <div class="relative group">
                            <span class="currency-symbol absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold group-focus-within:text-blue-600 transition-colors">$</span>
                            <input type="text" inputmode="decimal" id="monto" name="monto" required 
                                class="w-full pl-10 pr-4 py-4 text-2xl font-bold bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all placeholder-slate-300 text-slate-800"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="step-content absolute inset-0 transition-all duration-300 opacity-0 translate-x-full pointer-events-none" data-step="2">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Tu Nombre</label>
                            <input type="text" name="nombre" required 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all font-semibold"
                                placeholder="Nombre y Apellido">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Teléfono</label>
                            <input type="tel" name="telefono" inputmode="tel" required 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all font-semibold"
                                placeholder="0412 000 0000">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Correo</label>
                            <input type="email" name="email" inputmode="email" required 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all font-semibold"
                                placeholder="ejemplo@correo.com">
                        </div>
                    </div>
                </div>

                <div class="step-content absolute inset-0 transition-all duration-300 opacity-0 translate-x-full pointer-events-none" data-step="3">
                    
                    <p class="text-center text-sm text-slate-500 mb-4">
                        Vas a sembrar <strong id="summary-monto" class="text-slate-900 text-lg"></strong>
                        en <strong id="summary-currency"></strong>
                    </p>

                    <div class="space-y-3 mb-6">
                        
                        <div id="option-zelle" class="payment-option">
                            <label class="cursor-pointer relative block">
                                <input type="radio" name="metodo_de_pago" value="Zelle" class="peer sr-only" id="radio-zelle">
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white hover:bg-purple-50 peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white transition-all group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-[10px] group-peer-checked:bg-white/20 group-peer-checked:text-white">Z</div>
                                            <span class="font-bold">Zelle</span>
                                        </div>
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-md group-peer-checked:bg-white/20 group-peer-checked:text-white">USD Only</span>
                                    </div>
                                    
                                    <div class="hidden peer-checked:block mt-3 pt-3 border-t border-white/20 text-sm">
                                        <p class="opacity-80 text-xs uppercase mb-1">Enviar a:</p>
                                        <p class="font-mono font-bold text-lg select-all">nhradriver@yahoo.com</p>
                                        <p class="text-sm">Luis Bracho</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div id="option-bnc" class="payment-option">
                            <label class="cursor-pointer relative block">
                                <input type="radio" name="metodo_de_pago" value="BNC" class="peer sr-only" id="radio-bnc">
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white hover:bg-green-50 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white transition-all group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-[10px] group-peer-checked:bg-white/20 group-peer-checked:text-white">Bs</div>
                                            <span class="font-bold">Pago Móvil / BNC</span>
                                        </div>
                                    </div>

                                    <div class="hidden peer-checked:block mt-3 pt-3 border-t border-white/20 text-sm font-mono">
                                        <div class="flex justify-between py-1"><span>RIF:</span> <span class="font-bold select-all">J-40389600-3</span></div>
                                        <div class="flex justify-between py-1"><span>Tel:</span> <span class="font-bold select-all">0412-427-6773</span></div>
                                        <div class="flex justify-between py-1"><span>BNC:</span> <span class="font-bold select-all">0191-0263-77...</span></div>
                                    </div>
                                </div>
                            </label>
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Referencia / Comprobante</label>
                        <input type="text" name="referencia" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all font-mono uppercase"
                            placeholder="Últimos 4 o 6 dígitos">
                    </div>

                    <div class="mt-4">
                         <label class="text-xs font-bold text-slate-500 uppercase ml-1">Petición (Opcional)</label>
                        <textarea name="mensaje" rows="2" class="w-full px-5 py-3 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 transition-all text-sm" placeholder="Escribe tu petición de oración..."></textarea>
                    </div>

                </div>
            </form>

            <div class="fixed bottom-0 left-0 w-full p-4 bg-white border-t border-slate-100 md:static md:bg-transparent md:border-none md:p-0 mt-6 z-50">
                <div class="flex gap-3 max-w-md mx-auto">
                    <button type="button" id="prev-btn" class="hidden px-6 py-4 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 active:scale-95 transition-all">
                        ←
                    </button>
                    <button type="button" id="next-btn" class="flex-1 px-6 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all flex items-center justify-center gap-2">
                        Siguiente
                    </button>
                    <button type="submit" form="siembra-form" id="submit-btn" class="hidden flex-1 px-6 py-4 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 active:scale-95 transition-all flex items-center justify-center gap-2">
                        Confirmar y Sembrar
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div class="hidden md:block md:w-1/2 lg:w-7/12 relative bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-60 scale-105 hover:scale-100 transition-transform duration-[3s]" 
             style="background-image: url('<?php the_field('siembra_imagen', 'option'); ?>');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-900/40 to-transparent"></div>
        
        <div class="absolute bottom-20 left-12 max-w-xl text-white">
            <div class="h-1 w-20 bg-yellow-400 mb-6 rounded-full"></div>
            <h2 class="text-4xl lg:text-5xl font-extrabold mb-4 leading-tight">Cada semilla cuenta<br>para el Reino.</h2>
            <p class="text-lg text-blue-100 font-light">Gracias por ser parte de la visión de la iglesia El Rey Jesús.</p>
        </div>
    </div>

</div>

<div class="h-24 md:hidden"></div>

<style>
    /* Clases personalizadas utilitarias */
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Eliminar flechas de input number */
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
</style>

<script>
    // Variables Globales
    let currentStep = 1;
    let currency = 'USD'; // Valor por defecto

    document.addEventListener('DOMContentLoaded', () => {
        updateCurrencyUI();
        updateButtons();
    });

    // Lógica de Moneda (Switch Visual)
    function setCurrency(curr) {
        currency = curr;
        const radio = document.querySelector(`input[name="divisa"][value="${curr}"]`);
        if(radio) {
            radio.checked = true;
            updateCurrencyUI();
        }
    }

    function updateCurrencyUI() {
        const bg = document.getElementById('currency-bg');
        const symbol = document.querySelector('.currency-symbol');
        const labelUsd = document.getElementById('label-usd');
        const labelBs = document.getElementById('label-bs');

        if(currency === 'USD') {
            bg.style.transform = 'translateX(0%)';
            symbol.textContent = '$';
            labelUsd.classList.add('text-blue-600');
            labelUsd.classList.remove('text-slate-500');
            labelBs.classList.remove('text-blue-600');
            labelBs.classList.add('text-slate-500');
        } else {
            bg.style.transform = 'translateX(100%)'; // Mueve el fondo blanco a la derecha
            // Ajuste fino para el margen en el cálculo de width
            bg.style.left = '-6px'; 
            symbol.textContent = 'Bs';
            labelBs.classList.add('text-blue-600');
            labelBs.classList.remove('text-slate-500');
            labelUsd.classList.remove('text-blue-600');
            labelUsd.classList.add('text-slate-500');
        }
    }

    // Navegación de Pasos
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const submitBtn = document.getElementById('submit-btn');

    nextBtn.addEventListener('click', () => {
        if(validateStep(currentStep)) {
            changeStep(currentStep + 1);
        }
    });

    prevBtn.addEventListener('click', () => {
        changeStep(currentStep - 1);
    });

    function changeStep(step) {
        // Actualizar indicadores visuales (círculos de arriba)
        const indicators = document.querySelectorAll('.step-indicator');
        indicators.forEach((ind, idx) => {
            if(idx + 1 === step) {
                ind.classList.remove('opacity-40');
                ind.querySelector('div').classList.add('bg-blue-600', 'text-white');
                ind.querySelector('div').classList.remove('bg-slate-200', 'text-slate-500');
            } else if (idx + 1 < step) {
                ind.classList.remove('opacity-40');
                ind.querySelector('div').classList.add('bg-green-500', 'text-white'); // Paso completado
                ind.querySelector('div').innerHTML = '✓';
            } else {
                ind.classList.add('opacity-40');
                ind.querySelector('div').classList.remove('bg-blue-600', 'text-white', 'bg-green-500');
                ind.querySelector('div').classList.add('bg-slate-200', 'text-slate-500');
                ind.querySelector('div').innerHTML = idx + 1;
            }
        });

        // Transición de Contenido
        const contents = document.querySelectorAll('.step-content');
        contents.forEach(el => {
            const elStep = parseInt(el.dataset.step);
            if(elStep === step) {
                el.classList.remove('opacity-0', 'translate-x-full', '-translate-x-full', 'pointer-events-none');
            } else if (elStep < step) {
                el.classList.add('opacity-0', '-translate-x-full', 'pointer-events-none');
                el.classList.remove('translate-x-full');
            } else {
                el.classList.add('opacity-0', 'translate-x-full', 'pointer-events-none');
                el.classList.remove('-translate-x-full');
            }
        });

        currentStep = step;
        updateButtons();
        
        // Si entramos al paso 3 (Pago), ejecutar la lógica de filtrado
        if(step === 3) preparePaymentStep();
    }

    function preparePaymentStep() {
        const monto = document.getElementById('monto').value;
        document.getElementById('summary-monto').textContent = monto;
        document.getElementById('summary-currency').textContent = currency === 'USD' ? 'Dólares ($)' : 'Bolívares (Bs)';

        const zelleOption = document.getElementById('option-zelle');
        const bncOption = document.getElementById('option-bnc');
        const zelleRadio = document.getElementById('radio-zelle');
        const bncRadio = document.getElementById('radio-bnc');

        // Resetear selecciones previas para evitar conflictos
        zelleRadio.checked = false;
        bncRadio.checked = false;

        if (currency === 'BS') {
            // Si es Bolívares: Ocultar Zelle, Forzar BNC
            zelleOption.classList.add('hidden');
            bncRadio.checked = true; // Auto-seleccionar BNC
        } else {
            // Si es Dólares: Mostrar ambos
            zelleOption.classList.remove('hidden');
            zelleRadio.checked = true; // Auto-seleccionar Zelle por defecto (más probable)
        }
    }

    function updateButtons() {
        if(currentStep === 1) {
            prevBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        } else if (currentStep === 3) {
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
        const currentContent = document.querySelector(`.step-content[data-step="${step}"]`);
        const inputs = currentContent.querySelectorAll('input[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if(!input.value) {
                valid = false;
                input.classList.add('ring-2', 'ring-red-400', 'bg-red-50');
                setTimeout(() => input.classList.remove('ring-2', 'ring-red-400', 'bg-red-50'), 1000);
            }
        });
        
        return valid;
    }
</script>