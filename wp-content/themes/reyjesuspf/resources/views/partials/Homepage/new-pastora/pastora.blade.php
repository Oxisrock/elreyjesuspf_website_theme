<div class="bg-sky-50 min-h-screen flex items-center justify-center p-4 md:p-8">
    <div class="container max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-20 items-center">

            <div class="flex items-center justify-center md:hidden">
                <div class="px-4">
                    <img src="<?php the_field('imagen_pastora', 'option'); ?>"
                         alt="Pastora Gisela Bracho predicando"
                         class="rounded-xl w-full max-w-xs mx-auto h-auto object-cover">
                </div>
            </div>

            <div class="text-center md:text-left">
                <h1 class="text-3xl lg:text-4xl font-bold text-blue-700 mb-3">
                    <?php the_field('nombre_pastora', 'option'); ?>
                </h1>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-8">
                    <?php the_field('titulo_pastora', 'option'); ?>
                </h2>
                <p class="text-gray-600 mb-5 text-sm lg:text-base">
                    <?php the_field('1_descripcion_pastora', 'option'); ?>
                </p>
                <p class="text-gray-600 mb-5 text-sm lg:text-base">
                    <?php the_field('2_descripcion_pastora', 'option'); ?>
                </p>
                <p class="text-gray-600 text-sm lg:text-base">
                    <?php the_field('3_descripcion_pastora', 'option'); ?>
                </p>
            </div>

            <div class="hidden md:flex items-center justify-center">
                <div class="px-20 rounded-2xl transform hover:scale-105 transition-transform duration-300">
                    <img src="@asset('images/pastora-page/Nuestra Pastora(gisela B).jpg')"
                         alt="Pastora Gisela Bracho predicando en un escenario"
                         class="rounded-xl w-full h-[450px] object-cover">
                </div>
            </div>

        </div>
    </div>
</div>