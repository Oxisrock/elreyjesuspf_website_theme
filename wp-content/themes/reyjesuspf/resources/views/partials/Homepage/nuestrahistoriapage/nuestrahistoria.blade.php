<div class="bg-white font-sans">
    <div class="container mx-auto max-w-7xl p-8 md:p-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-start">
            
            <div class="flex flex-col w-full">
                
                <div class="block md:hidden">
                    <img src="<?php the_field('imagen_nuestra_historia', 'option'); ?>" alt="Equipo de ReyCristo (Móvil)" 
                         class="rounded-xl mb-8 w-full h-full object-center object-cover">
                </div>

                <div class="hidden md:block">
                    <img src="<?php the_field('imagen_nuestra_historia', 'option'); ?>" alt="Equipo de ReyCristo (Escritorio)" 
                         class="rounded-xl mb-8 w-full h-[300px] max-h-[400px] object-center object-cover">
                </div>

                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    <?php the_field('titulo_nuestra_historia', 'option'); ?>
                </h2>
                <p class="text-gray-600 leading-relaxed mb-8">
                    <?php the_field('descripcion_nuestra_historia', 'option'); ?>
                </p>
                <div class="flex items-center space-x-6">
                    <a href="/siembra" class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-full shadow-sm hover:bg-blue-700 transition-colors">
                        <?php the_field('boton_hero_page_2', 'option'); ?>
                    </a>
                    <a href="/quienes-somos" class="text-blue-600 font-semibold flex items-center space-x-2 hover:underline">
                        <span>Conoce más</span>
                        <span>&gt;</span>
                    </a>
                </div>
            </div>

            <div class="relative w-full mt-10 md:mt-0 md:ml-5">
                <div class="border-r-2 border-gray-500 border-dotted absolute h-full top-0 z-0" style="left: 7px"></div>
                <ul class="list-none m-0 p-0">
                    <li class="mb-8">
                        <div class="flex items-center mb-1">
                            <div class="bg-indigo-600 rounded-full h-4 w-4 border-gray-200 border-2 z-10">
                            </div>
                            <div class="text-blue-600 font-semibold text-2xl flex-1 ml-4 md:ml-10 font-sans"><?php the_field('historia_titulo_1', 'option'); ?></div>
                        </div>
                        <div class="ml-10 md:ml-14">
                            <h3 class="text-lg font-sans font-semibold"><?php the_field('historia_sub_titulo_1', 'option'); ?></h3>
                            <p class="font-sans text-gray-600">
                                <?php the_field('historia_descripcion_1', 'option'); ?>
                            </p>
                        </div>
                    </li>
                    <li class="mb-8">
                        <div class="flex items-center mb-1">
                            <div class="bg-indigo-600 rounded-full h-4 w-4 border-gray-200 border-2 z-10">
                            </div>
                            <div class="text-blue-600 font-semibold text-2xl flex-1 ml-4 md:ml-10 font-sans"><?php the_field('historia_titulo_2', 'option'); ?></div>
                        </div>
                        <div class="ml-10 md:ml-14">
                            <h3 class="text-lg font-sans font-semibold"><?php the_field('historia_sub_titulo_2', 'option'); ?></h3>
                            <p class="font-sans text-gray-600">
                                <?php the_field('historia_descripcion_2', 'option'); ?>
                            </p>
                        </div>
                    </li>
                    <li class="mb-2">
                        <div class="flex items-center mb-1">
                            <div class="bg-indigo-600 rounded-full h-4 w-4 border-gray-200 border-2 z-10">
                            </div>
                            <div class="text-blue-600 font-semibold text-2xl flex-1 ml-4 md:ml-10 font-sans"><?php the_field('historia_titulo_3', 'option'); ?></div>
                        </div>
                        <div class="ml-10 md:ml-14">
                            <h3 class="text-lg font-sans font-semibold"><?php the_field('historia_sub_titulo_3', 'option'); ?></h3>
                            <p class="font-sans text-gray-600">
                               <?php the_field('historia_descripcion_3', 'option'); ?>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>