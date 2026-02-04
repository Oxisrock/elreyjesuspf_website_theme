<div class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800">

    <div class="w-full md:w-1/2 lg:w-5/12 bg-white flex flex-col justify-center p-6 sm:p-8 md:p-12 relative z-20 shadow-2xl">
        
        <div class="w-full max-w-md mx-auto">
            
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center shrink-0">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo" class="h-8 w-auto">
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 leading-tight"><?php the_field('titulo_siembra_page', 'option'); ?></h1>
                    <p class="text-xs text-slate-500 font-medium">Iglesia El Rey Jesús</p>
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

            <div class="flex justify-center gap-2 mb-8">
                <div class="h-1.5 w-8 rounded-full bg-blue-600 transition-all step-dot" data-index="1"></div>
                <div class="h-1.5 w-8 rounded-full bg-slate-200 transition-all step-dot" data-index="2"></div>
                <div class="h-1.5 w-8 rounded-full bg-slate-200 transition-all step-dot" data-index="3"></div>
            </div>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" id="siembra-form" class="relative min-h-[450px]">
                <input type="hidden" name="action" value="procesar_formulario_siembra">
                <?php wp_nonce_field('mi_form_siembra_nonce', 'mi_nonce'); ?>

                <div class="step-content absolute inset-0 transition-all duration-300" data-step="1">
                    
                    <div class="bg-slate-100 p-1 rounded-xl flex relative mb-6 w-full max-w-[200px] mx-auto">
                        <div id="currency-bg" class="absolute left-1 top-1 bottom-1 w-[calc(50%-4px)] bg-white rounded-lg shadow-sm transition-all duration-300 ease-out"></div>
                        
                        <label class="flex-1 text-center py-2 z-10 cursor-pointer select-none" onclick="setCurrency('USD')">
                            <input type="radio" name="divisa" value="USD" class="sr-only" checked>
                            <span class="text-xs font-bold transition-colors duration-300" id="label-usd">USD ($)</span>
                        </label>
                        
                        <label class="flex-1 text-center py-2 z-10 cursor-pointer select-none" onclick="setCurrency('BS')">
                            <input type="radio" name="divisa" value="BS" class="sr-only">
                            <span class="text-xs font-bold text-slate-500 transition-colors duration-300" id="label-bs">VES (Bs)</span>
                        </label>
                    </div>

                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 text-center">Selecciona el propósito</p>
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="diezmo" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center h-full flex flex-col items-center justify-center">
                                <span class="text-xl mb-1">🌱</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Diezmo</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="ofrenda" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center h-full flex flex-col items-center justify-center">
                                <span class="text-xl mb-1">🎁</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Ofrenda</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="pacto" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center h-full flex flex-col items-center justify-center">
                                <span class="text-xl mb-1">🤝</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Pacto</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="primicia" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border-2 border-slate-100 bg-white hover:border-blue-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all text-center h-full flex flex-col items-center justify-center">
                                <span class="text-xl mb-1">🍞</span>
                                <span class="text-sm font-bold text-slate-600 peer-checked:text-blue-800">Primicia</span>
                            </div>
                        </label>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Monto a Sembrar</label>
                        <div class="relative group">
                            <span class="currency-symbol absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold transition-colors">$</span>
                            <input type="text" inputmode="decimal" id="monto" name="monto" required 
                                class="w-full pl-10 pr-4 py-4 text-2xl font-bold bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all placeholder-slate-300 text-slate-800 shadow-sm"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="step-content absolute inset-0 transition-all duration-300 opacity-0 translate-x-full pointer-events-none" data-step="2">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Tus Datos</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nombre Completo</label>
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
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Correo Electrónico</label>
                            <input type="email" name="email" inputmode="email" required 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all font-semibold"
                                placeholder="ejemplo@correo.com">
                        </div>
                    </div>
                </div>

                <div class="step-content absolute inset-0 transition-all duration-300 opacity-0 translate-x-full pointer-events-none overflow-y-auto pb-20" data-step="3">
                    
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 mb-5 text-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-purple-500"></div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Resumen de Siembra</p>
                        <div class="flex items-baseline justify-center gap-1">
                            <span id="resumen-simbolo" class="text-lg font-medium text-slate-500">$</span>
                            <span id="resumen-monto" class="text-3xl font-extrabold text-slate-800">0.00</span>
                        </div>
                        <span id="resumen-tipo" class="inline-block mt-2 px-3 py-1 bg-white border border-slate-200 rounded-full text-xs font-bold text-blue-600 uppercase shadow-sm">Diezmo</span>
                    </div>

                    <div id="card-zelle" class="hidden animate-fade-in">
                        <input type="radio" name="metodo_de_pago" value="Zelle" class="hidden" id="radio-zelle">
                        <div class="bg-gradient-to-br from-[#6d28d9] to-[#4c1d95] rounded-2xl p-5 text-white shadow-lg shadow-purple-200 mb-6 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                            
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-xs font-bold text-purple-200 uppercase tracking-wider">Transferir vía Zelle</p>
                                    <p class="text-sm opacity-80 mt-1">Iglesia El Rey Jesús</p>
                                </div>
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                    <span class="text-purple-700 font-bold text-xs">Z</span>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm border border-white/10 flex justify-between items-center group cursor-pointer hover:bg-white/20 transition-all" onclick="copyToClipboard('nhradriver@yahoo.com')">
                                    <div>
                                        <p class="text-[10px] text-purple-200 uppercase">Correo Zelle</p>
                                        <p class="font-mono font-bold text-lg">nhradriver@yahoo.com</p>
                                    </div>
                                    <button type="button" class="p-2 text-purple-200 hover:text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>
                                
                                <div class="flex justify-between items-center px-2">
                                    <span class="text-sm text-purple-200">Titular:</span>
                                    <span class="font-bold">Luis Bracho</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="card-bnc" class="hidden animate-fade-in">
                        <input type="radio" name="metodo_de_pago" value="BNC" class="hidden" id="radio-bnc">
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-lg shadow-slate-100 mb-6 relative">
                            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                                <div>
                                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider bg-green-50 px-2 py-1 rounded inline-block">Pago Móvil</p>
                                </div>
                                <span class="font-bold text-slate-400 text-sm">BNC (0191)</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors" onclick="copyToClipboard('J403896003')">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase font-bold">RIF</p>
                                        <p class="font-mono font-bold text-slate-700">J-40389600-3</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>

                                <div class="flex justify-between items-center p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors" onclick="copyToClipboard('04124276773')">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase font-bold">Teléfono</p>
                                        <p class="font-mono font-bold text-slate-700">0412-427-6773</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="relative">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nro. de Referencia</label>
                            <input type="text" name="referencia" required 
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all font-mono uppercase placeholder-slate-300"
                                placeholder="Ej: 123456">
                        </div>
                        <div class="relative">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Petición de Oración (Opcional)</label>
                            <textarea name="mensaje" rows="2" class="w-full px-5 py-3 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none transition-all text-sm" placeholder="Escribe aquí..."></textarea>
                        </div>
                    </div>
                </div>
            </form>

            <div class="fixed bottom-0 left-0 w-full p-4 bg-white/90 backdrop-blur-md border-t border-slate-200 md:static md:bg-transparent md:border-none md:p-0 md:mt-6 z-50">
                <div class="flex gap-3 max-w-md mx-auto">
                    <button type="button" id="prev-btn" class="hidden px-5 py-3.5 rounded-xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 active:scale-95 transition-all">
                        ←
                    </button>
                    <button type="button" id="next-btn" class="flex-1 px-6 py-3.5 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all flex items-center justify-center gap-2">
                        Continuar
                    </button>
                    <button type="submit" form="siembra-form" id="submit-btn" class="hidden flex-1 px-6 py-3.5 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span>Confirmar Siembra</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
        <div class="absolute bottom-16 left-12 max-w-xl text-white p-6">
            <h2 class="text-4xl lg:text-5xl font-extrabold mb-4 leading-tight">Cada semilla<br>transforma vidas.</h2>
            <p class="text-lg text-blue-100 font-light border-l-4 border-yellow-400 pl-4">"Dad, y se os dará; medida buena, apretada, remecida y rebosando..."</p>
        </div>
    </div>
</div>

<div id="toast-copy" class="fixed top-10 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-4 py-2 rounded-full text-xs font-bold shadow-xl transform -translate-y-20 transition-all duration-300 z-[60] flex items-center gap-2">
    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    <span>¡Copiado al portapapeles!</span>
</div>

<div class="h-20 md:hidden"></div>

<style>
    /* Estilos base y animaciones */
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<script>
    // Variables de Estado
    let currentStep = 1;
    let currency = 'USD'; 

    document.addEventListener('DOMContentLoaded', () => {
        updateCurrencyUI();
        updateButtons();
    });

    // Lógica Moneda
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
            labelUsd.classList.replace('text-slate-500', 'text-blue-600');
            labelBs.classList.replace('text-blue-600', 'text-slate-500');
        } else {
            bg.style.transform = 'translateX(100%)';
            bg.style.left = '-4px'; // Ajuste fino
            symbol.textContent = 'Bs';
            labelBs.classList.replace('text-slate-500', 'text-blue-600');
            labelUsd.classList.replace('text-blue-600', 'text-slate-500');
        }
    }

    // Funcionalidad de Copiado (UX Key)
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.getElementById('toast-copy');
            toast.classList.remove('-translate-y-20');
            toast.classList.add('translate-y-0');
            
            setTimeout(() => {
                toast.classList.remove('translate-y-0');
                toast.classList.add('-translate-y-20');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    }

    // Navegación
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const submitBtn = document.getElementById('submit-btn');

    nextBtn.addEventListener('click', () => {
        if(validateStep(currentStep)) changeStep(currentStep + 1);
    });

    prevBtn.addEventListener('click', () => {
        changeStep(currentStep - 1);
    });

    function changeStep(step) {
        // Actualizar barra de progreso
        document.querySelectorAll('.step-dot').forEach((dot, idx) => {
            if (idx + 1 === step) dot.classList.replace('bg-slate-200', 'bg-blue-600');
            else if (idx + 1 < step) dot.classList.replace('bg-slate-200', 'bg-green-500');
            else dot.classList.replace('bg-blue-600', 'bg-slate-200');
            
            // Fix para volver atrás colores
            if(idx + 1 > step) {
                 dot.classList.remove('bg-green-500', 'bg-blue-600');
                 dot.classList.add('bg-slate-200');
            }
        });

        // Transición de Contenido
        document.querySelectorAll('.step-content').forEach(el => {
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
        if(step === 3) loadPaymentDetails();
    }

    function loadPaymentDetails() {
        // 1. Cargar Datos del Resumen
        const monto = document.getElementById('monto').value;
        const tipoInput = document.querySelector('input[name="tipo_siembra"]:checked');
        const tipo = tipoInput ? tipoInput.value : 'Siembra';
        
        document.getElementById('resumen-monto').textContent = monto;
        document.getElementById('resumen-simbolo').textContent = currency === 'USD' ? '$' : 'Bs';
        document.getElementById('resumen-tipo').textContent = tipo;

        // 2. Mostrar Tarjeta Correcta
        const cardZelle = document.getElementById('card-zelle');
        const cardBnc = document.getElementById('card-bnc');
        const radioZelle = document.getElementById('radio-zelle');
        const radioBnc = document.getElementById('radio-bnc');

        cardZelle.classList.add('hidden');
        cardBnc.classList.add('hidden');
        radioZelle.checked = false;
        radioBnc.checked = false;

        if (currency === 'USD') {
            cardZelle.classList.remove('hidden');
            radioZelle.checked = true; // Auto-select Zelle for backend
        } else {
            cardBnc.classList.remove('hidden');
            radioBnc.checked = true; // Auto-select BNC for backend
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