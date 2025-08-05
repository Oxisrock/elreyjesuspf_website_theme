<style>
    .hero-background-overlay {
        /* This is where you would put your background image */
        /* CHANGE THIS PATH TO YOUR ACTUAL IMAGE! */
        background-image: url('<?php the_field('imagen_hero_page', 'option'); ?>');
        background-size: cover;
        background-position: center;
        /* Overlay for text readability */
        background-color: rgba(46, 81, 219, 0.4);
        /* Darker overlay for better text contrast */
        backdrop-filter: blur(2px);
        /* Optional: A slight blur effect */
    }
</style>
<div class="font-sans antialiased">

    <div class="relative h-screen bg-blue-800 flex items-center justify-center text-white">
        <div class="absolute inset-0 hero-background-overlay"></div>

        <div class="relative z-10 text-center px-4 max-w-3xl mx-auto">
            <h1 class="text-3xl sm:text-5xl md:text-5xl font-extrabold leading-tight mb-6">
               <?php the_field('titulo_hero_page', 'option'); ?>
            </h1>
            <p class="text-base sm:text-lg md:text-xl font-medium mb-10 leading-relaxed">
                <?php the_field('descripcion_hero_page', 'option'); ?>
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="/sign-up" class="my-button w-full sm:w-auto bg-white text-blue-600 font-semibold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <?php the_field('boton_hero_page_1', 'option'); ?>
                </a>
                <button
                    class="w-full sm:w-auto bg-blue-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    <?php the_field('boton_hero_page_2', 'option'); ?>
                </button>
            </div>
        </div>
    </div>

</div>