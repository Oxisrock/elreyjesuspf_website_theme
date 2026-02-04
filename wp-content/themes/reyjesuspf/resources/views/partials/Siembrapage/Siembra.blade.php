<div class="min-h-screen bg-slate-50 flex flex-col md:flex-row font-sans text-slate-800">

    <div class="w-full md:w-1/2 lg:w-5/12 bg-white flex flex-col justify-start md:justify-center relative z-20 shadow-none md:shadow-2xl">
        
        <div class="w-full max-w-md mx-auto p-5 sm:p-8 md:p-12 pb-32"> 
            
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-200">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo" class="h-7 w-auto brightness-0 invert">
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 leading-tight"><?php the_field('titulo_siembra_page', 'option'); ?></h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Iglesia El Rey Jesús</p>
                </div>
            </div>

            <div class="md:hidden mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 p-8 text-white shadow-2xl shadow-blue-200">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-5 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-white opacity-5 blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-full mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Tu siembra importa</span>
                    </div>
                    <h3 class="text-2xl font-extrabold leading-tight mb-3">"Dios ama al dador alegre."</h3>
                    <p class="text-sm text-blue-100 font-medium italic">— 2 Corintios 9:7</p>
                </div>
            </div>

            <?php if (isset($_GET['enviado'])) : ?>
                <div class="mb-6 p-4 rounded-2xl <?php echo $_GET['enviado'] == 'true' ? 'bg-green-50 border-green-100 text-green-800' : 'bg-red-50 border-red-100 text-red-800'; ?> border flex gap-3 items-center animate-fade-in">
                    <span class="text-2xl"><?php echo $_GET['enviado'] == 'true' ? '🙌' : '⚠️'; ?></span>
                    <div>
                        <p class="font-bold text-sm"><?php echo $_GET['enviado'] == 'true' ? '¡Siembra Recibida!' : 'Hubo un error'; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex justify-center gap-2 mb-6">
                <div class="h-1.5 w-8 rounded-full bg-blue-600 transition-all step-dot" data-index="1"></div>
                <div class="h-1.5 w-8 rounded-full bg-slate-200 transition-all step-dot" data-index="2"></div>
                <div class="h-1.5 w-8 rounded-full bg-slate-200 transition-all step-dot" data-index="3"></div>
            </div>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" id="siembra-form" class="relative">
                <input type="hidden" name="action" value="procesar_formulario_siembra">
                <?php wp_nonce_field('mi_form_siembra_nonce', 'mi_nonce'); ?>

                <div id="step-1" class="step-content block animate-fade-in">
                    
                    <div class="bg-slate-100 p-1 rounded-xl flex relative mb-6 w-full max-w-[220px] mx-auto">
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

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 text-center">Selecciona el propósito</p>
                    
                    <div class="grid grid-cols-3 gap-2.5 mb-6">
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="diezmo" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-600 transition-all text-center h-full flex flex-col items-center justify-center gap-1 shadow-sm">
                                <span class="text-2xl filter drop-shadow-sm mb-0.5">🌱</span>
                                <span class="text-[10px] font-bold text-slate-600 peer-checked:text-blue-700 leading-tight">Diezmo</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="ofrenda" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-600 transition-all text-center h-full flex flex-col items-center justify-center gap-1 shadow-sm">
                                <span class="text-2xl filter drop-shadow-sm mb-0.5">🎁</span>
                                <span class="text-[10px] font-bold text-slate-600 peer-checked:text-blue-700 leading-tight">Ofrenda</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="pacto" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-600 transition-all text-center h-full flex flex-col items-center justify-center gap-1 shadow-sm">
                                <span class="text-2xl filter drop-shadow-sm mb-0.5">🤝</span>
                                <span class="text-[10px] font-bold text-slate-600 peer-checked:text-blue-700 leading-tight">Pacto</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="primicia" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-600 transition-all text-center h-full flex flex-col items-center justify-center gap-1 shadow-sm">
                                <span class="text-2xl filter drop-shadow-sm mb-0.5">🍞</span>
                                <span class="text-[10px] font-bold text-slate-600 peer-checked:text-blue-700 leading-tight">Primicia</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="yo_construyo" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-600 transition-all text-center h-full flex flex-col items-center justify-center gap-1 shadow-sm">
                                <span class="text-2xl filter drop-shadow-sm mb-0.5">🏗️</span>
                                <span class="text-[10px] font-bold text-slate-600 peer-checked:text-blue-700 leading-tight">Yo Construyo</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="tipo_siembra" value="misiones" class="peer sr-only" required>
                            <div class="p-3 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-600 transition-all text-center h-full flex flex-col items-center justify-center gap-1 shadow-sm">
                                <span class="text-2xl filter drop-shadow-sm mb-0.5">🌍</span>
                                <span class="text-[10px] font-bold text-slate-600 peer-checked:text-blue-700 leading-tight">Misiones</span>
                            </div>
                        </label>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Monto a Sembrar</label>
                        <div class="relative group">
                            <span class="currency-symbol absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold transition-colors">$</span>
                            <input type="text" inputmode="decimal" id="monto" name="monto" required 
                                class="w-full pl-10 pr-4 py-4 text-3xl font-bold bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all placeholder-slate-300 text-slate-800"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div id="step-2" class="step-content hidden animate-fade-in">
                    <h2 class="text-lg font-bold text-slate-800 mb-6">Tus Datos</h2>
                    <div class="space-y-5">
                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 mb-1 block group-focus-within:text-blue-600 transition-colors">Nombre Completo</label>
                            <input type="text" name="nombre" required 
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all font-semibold text-lg"
                                placeholder="Nombre y Apellido">
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 mb-1 block group-focus-within:text-blue-600 transition-colors">Teléfono</label>
                            <input type="tel" name="telefono" inputmode="tel" required 
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all font-semibold text-lg"
                                placeholder="0412 000 0000">
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 mb-1 block group-focus-within:text-blue-600 transition-colors">Correo Electrónico</label>
                            <input type="email" name="email" inputmode="email" required 
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all font-semibold text-lg"
                                placeholder="ejemplo@correo.com">
                        </div>
                    </div>
                </div>

                <div id="step-3" class="step-content hidden animate-fade-in">
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total a Sembrar</p>
                        <div class="flex items-baseline justify-center gap-1 mb-2">
                            <span id="resumen-simbolo" class="text-xl font-bold text-slate-400">$</span>
                            <span id="resumen-monto" class="text-4xl font-extrabold text-slate-900 tracking-tight">0.00</span>
                        </div>
                        <span id="resumen-tipo" class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wide">Diezmo</span>
                    </div>

                    <div id="card-zelle" class="hidden">
                        <input type="radio" name="metodo_de_pago" value="Zelle" class="hidden" id="radio-zelle">
                        <div class="bg-gradient-to-br from-[#5c2d91] to-[#4c1d95] rounded-2xl p-6 text-white shadow-xl shadow-purple-200 mb-6 relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                            
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-xs font-bold uppercase tracking-wider opacity-80">Zelle</span>
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center font-bold">Z</div>
                            </div>

                            <div class="space-y-5 relative z-10">
                                <div onclick="copyToClipboard('nhradriver@yahoo.com')" class="active:scale-95 transition-transform cursor-pointer">
                                    <p class="text-[10px] uppercase opacity-60 mb-1">Correo Electrónico</p>
                                    <div class="flex items-center gap-2">
                                        <p class="font-mono text-xl font-bold break-all">nhradriver@yahoo.com</p>
                                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </div>
                                </div>
                                <div class="border-t border-white/10 pt-4">
                                    <p class="text-[10px] uppercase opacity-60 mb-1">Titular</p>
                                    <p class="font-bold text-lg">Luis Bracho</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="card-bnc" class="hidden">
                        <input type="radio" name="metodo_de_pago" value="BNC" class="hidden" id="radio-bnc">
                        <div class="bg-white border-2 border-slate-100 rounded-2xl p-5 shadow-lg shadow-slate-100 mb-6 group hover:border-green-200 transition-colors">
                            <div class="flex justify-between items-center mb-4 border-b border-slate-50 pb-3">
                                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded uppercase">Pago Móvil</span>
                                <span class="font-bold text-slate-400 text-xs">BNC (0191)</span>
                            </div>

                            <div class="space-y-4">
                                <div onclick="copyToClipboard('J403896003')" class="active:scale-95 transition-transform cursor-pointer flex justify-between items-center p-2 hover:bg-slate-50 rounded-lg">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase font-bold">RIF</p>
                                        <p class="font-mono text-lg font-bold text-slate-700">J-40389600-3</p>
                                    </div>
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>

                                <div onclick="copyToClipboard('04124276773')" class="active:scale-95 transition-transform cursor-pointer flex justify-between items-center p-2 hover:bg-slate-50 rounded-lg">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase font-bold">Teléfono</p>
                                        <p class="font-mono text-lg font-bold text-slate-700">0412-427-6773</p>
                                    </div>
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 mb-1 block">Nro. de Referencia</label>
                            <input type="text" name="referencia" required 
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all font-mono uppercase text-lg"
                                placeholder="Ej: 123456">
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 mb-1 block">Petición (Opcional)</label>
                            <textarea name="mensaje" rows="2" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all" placeholder="Escribe aquí..."></textarea>
                        </div>
                    </div>
                </div>
            </form>

            <div class="fixed bottom-0 left-0 w-full p-4 bg-white/90 backdrop-blur-lg border-t border-slate-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] md:static md:bg-transparent md:border-none md:p-0 md:mt-8 z-50 md:shadow-none">
                <div class="flex gap-3 max-w-md mx-auto">
                    <button type="button" id="prev-btn" class="hidden w-14 flex items-center justify-center rounded-2xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 active:scale-95 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    
                    <button type="button" id="next-btn" class="flex-1 h-14 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all flex items-center justify-center gap-2 text-lg">
                        Continuar
                    </button>
                    
                    <button type="submit" form="siembra-form" id="submit-btn" class="hidden flex-1 h-14 bg-green-600 text-white font-bold rounded-2xl shadow-lg shadow-green-200 hover:bg-green-700 active:scale-95 transition-all flex items-center justify-center gap-2 text-lg">
                        <span>Confirmar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div class="hidden md:block md:w-1/2 lg:w-7/12 relative bg-slate-900 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-50 scale-105 hover:scale-100 transition-transform duration-[5s]" 
             style="background-image: url('<?php the_field('siembra_imagen', 'option'); ?>');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
        <div class="absolute bottom-20 left-16 right-16 max-w-2xl text-white">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full mb-6 border border-white/20">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <span class="text-sm font-bold uppercase tracking-wider">Siembra con propósito</span>
            </div>
            <h2 class="text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">Cada semilla<br>transforma vidas.</h2>
            <div class="bg-white/10 backdrop-blur-md border-l-4 border-amber-400 pl-6 pr-6 py-4 rounded-r-2xl">
                <p class="text-xl text-blue-100 font-medium italic leading-relaxed">"Dad, y se os dará; medida buena, apretada, remecida y rebosando..."</p>
                <p class="text-sm text-amber-300 font-bold mt-2">— Lucas 6:38</p>
            </div>
        </div>
    </div>
</div>

<div id="toast-copy" class="fixed top-6 left-1/2 -translate-x-1/2 bg-slate-800/95 backdrop-blur text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-2xl transform -translate-y-24 transition-all duration-300 z-[60] flex items-center gap-3">
    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    <span>¡Copiado!</span>
</div>

<script>
    // Variables y Funciones JS (Exactamente igual que la versión robusta anterior)
    let currentStep = 1;
    let currency = 'USD'; 

    document.addEventListener('DOMContentLoaded', () => {
        updateCurrencyUI();
        updateButtons();
    });

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
            bg.style.left = '-4px';
            symbol.textContent = 'Bs';
            labelBs.classList.replace('text-slate-500', 'text-blue-600');
            labelUsd.classList.replace('text-blue-600', 'text-slate-500');
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.getElementById('toast-copy');
            toast.classList.remove('-translate-y-24');
            toast.classList.add('translate-y-0');
            setTimeout(() => {
                toast.classList.remove('translate-y-0');
                toast.classList.add('-translate-y-24');
            }, 2000);
        });
    }

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
        // Steps Dot Logic
        document.querySelectorAll('.step-dot').forEach((dot, idx) => {
            if (idx + 1 === step) dot.classList.replace('bg-slate-200', 'bg-blue-600');
            else if (idx + 1 < step) dot.classList.replace('bg-slate-200', 'bg-green-500');
            else {
                dot.classList.remove('bg-green-500', 'bg-blue-600');
                dot.classList.add('bg-slate-200');
            }
        });

        // Hide/Show Steps
        document.getElementById('step-1').classList.add('hidden');
        document.getElementById('step-2').classList.add('hidden');
        document.getElementById('step-3').classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');

        currentStep = step;
        updateButtons();
        
        if(step === 3) loadPaymentDetails();

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function loadPaymentDetails() {
        const monto = document.getElementById('monto').value;
        const tipoInput = document.querySelector('input[name="tipo_siembra"]:checked');
        const tipo = tipoInput ? tipoInput.nextElementSibling.querySelector('span:last-child').innerText : 'Siembra';
        
        document.getElementById('resumen-monto').textContent = monto;
        document.getElementById('resumen-simbolo').textContent = currency === 'USD' ? '$' : 'Bs';
        document.getElementById('resumen-tipo').textContent = tipo;

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
            radioZelle.checked = true;
        } else {
            cardBnc.classList.remove('hidden');
            radioBnc.checked = true;
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
        const currentContent = document.getElementById(`step-${step}`);
        const inputs = currentContent.querySelectorAll('input[required]');
        let valid = true;
        inputs.forEach(input => {
            if(!input.value) {
                valid = false;
                // Add error class to parent for better visibility
                if(input.parentElement.classList.contains('group')) {
                    input.classList.add('border-red-400', 'bg-red-50');
                    setTimeout(() => input.classList.remove('border-red-400', 'bg-red-50'), 1000);
                } else if(input.parentElement.parentElement.classList.contains('group')) { // Radio buttons
                     // Logic for radios if needed
                } else {
                    input.classList.add('border-red-400', 'bg-red-50');
                    setTimeout(() => input.classList.remove('border-red-400', 'bg-red-50'), 1000);
                }
            }
        });
        return valid;
    }
</script>