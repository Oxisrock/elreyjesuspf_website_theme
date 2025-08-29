<div class="bg-white text-gray-800">
    <main class="min-h-screen flex items-center justify-center p-6">
        <div class="container flex flex-col md:flex-row items-center justify-center gap-12 text-center md:text-center">
            
            <div class="w-1/3 md:w-1/4 flex items-center justify-center">
                <img src="<?php the_field('logo', 'option'); ?>" alt="Logo">
            </div>
            
            <div class="md:w-1/4">
                <h1 class="text-8xl md:text-9xl font-black text-blue-600">404</h1>
                <p class="text-xl md:text-2xl font-semibold mt-4">LOOKS LIKE YOU'RE LOST</p>
                <p class="text-gray-500 mt-2 mb-6">The page you are looking for is not available!</p>
                <a href="<?php echo home_url(); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800 hover:text-blue-500 transition-colors">
                    GO TO HOME
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </main>

</div>