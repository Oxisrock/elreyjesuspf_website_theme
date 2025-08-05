<div class="bg-white font-sans">
    <div class="relative max-w-6xl mx-auto my-12 p-8 sm:p-12">
        
        <div class="absolute top-12 bottom-12 left-1/2 -translate-x-1/2 w-0.5 bg-blue-200/70 hidden md:block z-0"></div>

        <div class="relative z-10 space-y-16">

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[40%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:order-last">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('evangelizar_titulo' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed ">
                       <?php the_field('evangelizar_descripcion' ); ?>
                    </p>
                </div>
                <div>
                    <img src="<?php the_field('evangelizar_foto' ); ?>" alt="Evangelizar" class="rounded-3xl w-[450px] h-auto object-cover">
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[37%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:text-right md:order-first">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('afirmar_titulo' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        <?php the_field('afirmar_descripcion' ); ?>
                    </p>
                </div>
                <div class="md:order-last">
                    <img src="<?php the_field('afirmar_foto' ); ?>" alt="Afirmar" class="rounded-2xl w-full h-[450px] object-cover">
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[37%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:order-last">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('discipular_titulo' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                       <?php the_field('discipular_descripcion' ); ?>
                    </p>
                </div>
                <div>
                    <img src="<?php the_field('discipular_foto' ); ?>" alt="Discipular" class="rounded-3xl w-[470px] h-auto object-cover">
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="hidden md:flex absolute top-[39%] left-1/2 -translate-x-1/2 w-8 h-8 items-center justify-center z-20">
                    <div class="w-4 h-4 bg-blue-500 rounded-full "></div>
                    <div class="absolute w-4 h-2 bg-blue-500 rounded-full"></div>
                </div>
                <div class="text-left md:text-right md:order-first">
                    <h3 class="text-3xl font-bold text-gray-800"><?php the_field('enviar_titulo' ); ?></h3>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        <?php the_field('enviar_descripcion' ); ?>
                    </p>
                </div>
                <div class="md:order-last">
                    <img src="<?php the_field('enviar_foto' ); ?>" class="rounded-3xl mb-8 w-full h-[470px] object-top object-cover">
                </div>
            </div>

        </div>
    </div>
</div>