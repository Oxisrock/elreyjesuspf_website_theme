<div class="bg-white-500">

    <div class="container mx-auto max-w-7xl p-12">

        <div class="text-center mb-10">
            <h2 class="text-4xl font-bold text-blue-600">Más Eventos</h2>

            <div class="text-center mb-10>
            <h4 class="text-xl font-bold text-gray-200"><br>Encuentra mensajes de esperanza y crecimiento espiritual a traves de videos,
                <br>Podcast,Alabanzas y trasmisiones en vivo.</h4>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-4">

            <div
                class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img class="aspect-video w-full object-cover" src="<?= \Roots\asset('images/events-page/Domingo de la vision .webp'); ?>" alt="Evento Pijamada de Mujeres">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <span
                            class="text-xs font-semibold px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full">Últimos
                            cupos</span>
                        <span class="text-sm text-gray-500">11 Abril de 2025</span>
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-800">Pijamada de Mujeres</h3>
                    <a href="#"
                        class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                        Ver detalles
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <div
                class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img class="aspect-video w-full object-cover" src="<?= \Roots\asset('images/events-page/Eliap 2025.jpg'); ?>" alt="Evento Revival">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <span
                            class="text-xs font-semibold px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-full">Nuevo</span>
                        <span class="text-sm text-gray-500">11 Abril de 2025</span>
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-800">Revival</h3>
                    <a href="#"
                        class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                        Ver detalles
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <div
                class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <img class="aspect-video w-full object-cover" src="<?= \Roots\asset('images/events-page/Gran Cruzada .webp'); ?>"
                    alt="Evento Servicio de Primicias">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <span
                            class="text-xs font-semibold px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full">Últimos
                            cupos</span>
                        <span class="text-sm text-gray-500">11 Abril de 2025</span>
                    </div>
                    <h3 class="mt-2 text-lg font-bold text-gray-800">Servicio de primicias para el templo</h3>
                    <a href="#"
                        class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                        Ver detalles
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

        </div>

        <div class="text-center mt-20">
            <a href="<?php echo e(get_post_type_archive_link('events')); ?>"
                class="bg-blue-600 text-white font-bold py-3 px-8 rounded-full hover:bg-blue-700 transition-colors">
                Ver más
            </a>
        </div>

    </div>

</div>
<?php /**PATH /var/www/html/wp-content/themes/reyjesuspf/resources/views/partials/otroseventspage/otrosevents.blade.php ENDPATH**/ ?>