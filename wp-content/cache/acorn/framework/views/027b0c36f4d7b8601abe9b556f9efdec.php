<nav class="bg-white shadow-sm sticky top-0 z-30">
    <div class="container mx-auto px-6 md:px-8 lg:px-12">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-2">
                <img src="<?= \Roots\asset('images/header-page/Logoiglesiaazul.png'); ?>" alt="Logo" class="max-h-8 max-w-full"> <span
                    class="text-gray-800 font-bold text-xl">El Rey Jesús</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <?php
                // Desktop Menu
                wp_nav_menu([
                    'theme_location' => 'primary_navigation',
                    'walker' => new \App\Walkers\TailwindNavWalker(),
                    'container' => false, // We don't want a container div
                    'items_wrap' => '%3$s', // We don't want the container <ul> tag, the Walker already handles it if needed
                    'depth' => 2, // Support one level of submenu
                ]);
                ?>
            </div>

            <div class="hidden md:flex items-center space-x-4">
                <a href="/login"
                    class="my-button bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md transition duration-300">
                    Ingresar
                </a>
                <a href="/sign-up"
                    class="my-button bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-2 px-6 rounded-md transition duration-300">
                    Registrarme
                </a>
            </div>

            <div class="md:hidden flex items-center space-x-4">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                    Sembrar
                </button>
                <button id="open-menu-button" class="text-gray-600 hover:text-blue-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<div id="menu-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>
<div id="mobile-menu"
    class="fixed top-0 left-0 h-full w-64 bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex justify-between items-center p-4 border-b">
        <span class="font-bold">Menú</span>
        <button id="close-menu-button" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>
    </div>
    <div class="p-4">
        <?php
        // Desktop Menu
        wp_nav_menu([
            'theme_location' => 'primary_mobile_navigation',
            'walker' => new \App\Walkers\TailwindMobileNavWalker(),
            'container' => false, // We don't want a container div
            'items_wrap' => '%3$s', // We don't want the container <ul> tag, the Walker already handles it if needed
            'depth' => 2, // Support one level of submenu
        ]);
        ?>
    </div>
    <div class="absolute bottom-0 left-0 w-full p-4 border-t bg-white">
        <div class="flex flex-col space-y-2">
            <a href="/login"
                class="my-button w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-md text-center">
                Ingresar
            </a>
            <a href="/sign-up"
                class="my-button w-full bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-3 px-4 rounded-md text-center">
                Registrarme
            </a>
        </div>
    </div>
</div>

<script>
    const openMenuButton = document.getElementById('open-menu-button');
    const closeMenuButton = document.getElementById('close-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOverlay = document.getElementById('menu-overlay');

    function openMenu() {
        mobileMenu.classList.remove('-translate-x-full');
        menuOverlay.classList.remove('hidden');
    }

    function closeMenu() {
        mobileMenu.classList.add('-translate-x-full');
        menuOverlay.classList.add('hidden');
    }

    openMenuButton.addEventListener('click', openMenu);
    closeMenuButton.addEventListener('click', closeMenu);
    menuOverlay.addEventListener('click', closeMenu);
</script>
<?php /**PATH /var/www/html/wp-content/themes/reyjesuspf/resources/views/sections/header.blade.php ENDPATH**/ ?>