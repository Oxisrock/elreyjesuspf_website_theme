<div class="bg-sky-50 min-h-screen">

    <div class="container max-w-6xl mx-auto py-12 px-4">
        <div class="flex flex-col lg:flex-row items-center">

            <div class="w-full lg:w-1/2 mb-8 lg:mb-0">
                <img class="object-cover w-full h-auto rounded-xl" src="<?php the_field('imagen_paso_de_fe', 'option'); ?>"
                    alt="Persona en un escenario frente a una multitud con luces">
            </div>

            <div class="w-full lg:w-1/2 p-6 lg:p-20">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    <?php the_field('titulo_paso_de_fe', 'option'); ?>
                </h2>
                <p class="text-gray-600 text-sm mb-8">
                    <?php the_field('descripcion_paso_de_fe', 'option'); ?>
                </p>
                <form>
                    <div class="mb-6">
                        <input type="text" id="nombre-completo" name="nombre-completo" placeholder="Nombre Completo"
                            class="w-full p-2 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-6">
                        <input type="email" id="correo" name="correo" placeholder="Correo electrónico"
                            class="w-full p-2 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p class="text-xs text-gray-500 mb-6">
                        Al hacer clic en registrarme, acepto nuestros <a href="#"
                            class="text-blue-600 hover:underline">Términos y condiciones</a>.
                    </p>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        Registrarme
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>