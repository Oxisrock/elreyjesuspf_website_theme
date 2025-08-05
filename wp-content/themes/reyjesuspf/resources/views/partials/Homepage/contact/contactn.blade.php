<div class="bg-white p-16 md py-10">
    <div class="container mx-auto px-6 py-12 sm:px-8 md:py-16">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl font-medium text-blue-600 sm:text-4xl mb-3">
                <?php the_field('titulo_contactos', 'option'); ?>
            </h2>
            <p class="mx-auto max-w-xl text-base text-gray-600 sm:text-balance">
                <?php the_field('sub_titulos_contactos', 'option'); ?>
            </p>
        </div>

        <div class="grid grid-cols-1 gap-y-12 gap-x-8 md:grid-cols-3">

            <div class="text-center md:text-left">
                <div class="flex justify-center md:justify-start mb-4">
                    <i class="fa-solid fa-envelope text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <?php the_field('titulo_correo', 'option'); ?>
                </h3>
                <p class="text-gray-600 text-sm mb-1">
                    <?php the_field('descripcion_de_correo', 'option'); ?>
                </p>
                <a href="mailto:Hola@reyjesus.com" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                    <?php the_field('correo', 'option'); ?>
                </a>
            </div>

            <div class="text-center md:text-left">
                <div class="flex justify-center md:justify-start mb-4">
                    <i class="fa-solid fa-phone text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <?php the_field('titulo_telefono', 'option'); ?>
                </h3>
                <p class="text-gray-600 text-sm mb-1">
                    <?php the_field('descripcion_telefono', 'option'); ?>
                </p>
                <a href="tel:+585550000000" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                    <?php the_field('telefono', 'option'); ?>
                </a>
            </div>

            <div class="text-center md:text-left">
                <div class="flex justify-center md:justify-start mb-4">
                    <i class="fa-solid fa-location-dot text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <?php the_field('titulo_direccion_', 'option'); ?>
                </h3>
                <p class="text-gray-600 text-sm mb-1">
                   <?php the_field('descripcion_direccion', 'option'); ?>
                </p>
                <p class="text-sm text-blue-600">
                    <?php the_field('direccion', 'option'); ?>
                </p>
            </div>

        </div>
    </div>
</div>