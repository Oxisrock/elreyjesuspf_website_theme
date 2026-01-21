<div class="bg-white font-sans">

    <div class="relative flex items-center justify-center mt-8 sm:mt-8 md:mt-24 p-6 sm:p-8 md:p-24">
        
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center max-w-6xl">
            
            <div class="text-center md:text-left space-y-4">
                <h1 class="text-3xl sm:text-5xl font-bold text-blue-600">
                    <?php the_field('titulo_about' ); ?>
                </h1>
                <p class="text-medium text-gray-600 italic">
                    <?php the_field('parabola'); ?>
                </p>
                <p class="text-base text-black-500 font-bold">
                    <?php the_field('versiculo'); ?>
                </p>
            </div>

            <div class="flex justify-center p-4">
                <div class="rounded-2xl overflow-hidden">
                    <img src="<?php the_field('imagen_about'); ?>" alt="Construcción del templo" class="rounded-xl w-full h-[300px] md:h-[450px] object-cover object-top">
                </div>
            </div>

        </div>

    </div>

</div>


