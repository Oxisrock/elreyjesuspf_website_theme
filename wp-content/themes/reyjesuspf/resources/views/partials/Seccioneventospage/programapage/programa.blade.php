<div class="bg-blue-50">
    <section class="py-16 sm:py-20 lg:py-24">
        <div class="container mx-auto px-6 md:px-8">

            <div class="text-left md:text-center">
                <h2 class="text-3xl font-bold tracking-tight text-blue-500 sm:text-4xl lg:text-5xl">
                    <?php the_field('titulo_de_programa'); ?>
                </h2>
                <p class="text-left md:text-center mt-4 text-sm leading-7 text-gray-600 max-w-2xl mx-auto sm:text-sm">
                    <?php the_field('descripcion_de_programa'); ?>
                </p>
            </div>

            <div class="mt-16 max-w-3xl mx-auto">
                <div class="flow-root">
                    <div class="-my-8 divide-y divide-gray-300">
                        <?php if( have_rows('programa_de_eventos') ): ?>
                        <?php while( have_rows('programa_de_eventos') ): the_row(); ?>
                        <?php if( get_row_layout() == 'programa' ): ?>
                    
                        <div class="py-8">
                            <div class="flex items-center space-x-3">
                                <svg class="h-5 w-5 flex-shrink-0 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs font-medium text-blue-500"><?php the_sub_field('tiempo_del_programa'); ?></span>
                            </div>
                            <h3 class="mt-3 text-lg font-semibold text-gray-900 sm:text-lg">
                                <?php the_sub_field('titulo_del_programa'); ?>
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                <?php the_sub_field('descripcion_del_programa'); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        <?php endwhile; ?>
                        <?php endif; ?>


                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
