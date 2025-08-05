<div class="bg-blue-50">
    <section class="py-12 md:py-24">
        <div class="container mx-auto max-w-6xl px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">

                <div class="text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold text-blue-600 leading-tight">
                        <?php the_field('aporte_titulo', 'option' ); ?>
                    </h2>
                </div>

                <div class="flex flex-col space-y-6">
                    <div class="space-y-4 text-gray-700 text-sm md:text-md">
                        <p>
                            <?php the_field('aporte_descripcion_1', 'option' ); ?>
                        </p>
                        <p>
                            <?php the_field('aporte_descripcion_2', 'option' ); ?>
                        </p>
                    </div>
                    
                    <div class="flex items-center justify-center md:justify-start space-x-6 mt-4">
                        <a href="#" class="bg-blue-600 text-white font-semibold px-8 py-2 rounded-full hover:bg-blue-700 transition-colors duration-300">
                            Sembrar
                        </a>
                        <a href="/sign-up" class="text-blue-600 font-semibold flex items-center group">
                            Registrarme
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>