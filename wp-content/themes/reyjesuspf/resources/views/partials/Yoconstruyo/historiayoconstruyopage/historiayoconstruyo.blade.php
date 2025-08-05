<div class="bg-white font-sans">
    <div class="relative max-w-6xl mx-auto my-12 p-8 sm:p-12">
        
        <div class="absolute top-12 bottom-12 left-1/2 -translate-x-1/2 w-0.5 bg-blue-200/70 hidden md:block z-0"></div>

        <div class="relative z-10 space-y-16">

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[37%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:order-last">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('historia_del_templo_titulo_1' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed ">
                        <?php the_field('historia_del_templo_descripcion_1' ); ?>
                    </p>
                </div>
                <div>
                    <img src="<?php the_field('historia_del_templo_imagen_1' ); ?>" alt="Inicio" class="rounded-3xl w-[450px] h-auto object-cover">
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[37%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:text-right md:order-first">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('historia_del_templo_titulo_2' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        <?php the_field('historia_del_templo_descripcion_2' ); ?>
                    </p>
                </div>
                <div class="md:order-last">
                    <img src="<?php the_field('historia_del_templo_imagen_2' ); ?>" alt="Afirmar" class="rounded-3xl w-[450px] h-auto object-cover">
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[37%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:order-last">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('historia_del_templo_titulo_3' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        <?php the_field('historia_del_templo_descripcion_3' ); ?>
                    </p>
                </div>
                <div>
                    <img src="<?php the_field('historia_del_templo_imagen_3' ); ?>" alt="Discipular" class="rounded-3xl w-[450px] h-auto object-cover">
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[37%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:text-right md:order-first">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('historia_del_templo_titulo_4' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        <?php the_field('historia_del_templo_descripcion_4' ); ?>
                    </p>
                </div>
                <div class="md:order-last">
                    <img src="<?php the_field('historia_del_templo_imagen_4' ); ?>" alt="Enviar" class="rounded-3xl w-[450px] h-auto object-cover">
                </div>
            </div>

        </div>
    </div>
</div>