<?php global $current_user;
wp_get_current_user(); ?>
<nav class="bg-white shadow-sm sticky top-0 z-30">
    <div class="container mx-auto px-6 md:px-8 lg:px-12">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-2">
                <img src="<?php the_field('logo', 'option'); ?>" alt="Logo" class="max-h-8 max-w-full"> <span
                    class="text-gray-800 font-bold text-medium md:text-xl ">El Rey Jesús Punto Fijo</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <?php
                // Desktop Menu
                wp_nav_menu([
                    'theme_location' => 'primary_navigation',
                    'walker' => new \App\Walkers\TailwindNavWalker(),
                    'container' => false,
                    'items_wrap' => '%3$s',
                    'depth' => 2,
                ]);
                ?>
            </div>

            <!-- ====== MODIFIED DESKTOP SECTION ====== -->
            <div class="hidden md:flex items-center space-x-4">
                <?php if (!is_user_logged_in()) : ?>
                <!-- Buttons for logged-out users -->
                <a href="/login"
                    class="my-button bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md transition duration-300">
                    Ingresar
                </a>
                <a href="/sign-up"
                    class="my-button bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-2 px-6 rounded-md transition duration-300">
                    Registrarme
                </a>
                <?php else : ?>
                <!-- Dropdown menu for logged-in users (code from the previous artifact) -->
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click="open = !open" type="button"
                        class="inline-flex w-full justify-center items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold transition-colors duration-150 hover:text-blue-600"
                        :class="{ 'text-blue-600 bg-gray-50': open, 'text-gray-900': !open }" id="menu-button"
                        aria-expanded="true" aria-haspopup="true">
                        Hola, <?php echo esc_html($current_user->display_name); ?>

                        <!-- MODIFIED ICON with dynamic classes and transitions -->
                        <i class="fa-solid fa-chevron-down transition-transform duration-200"
                            :class="{ 'rotate-180 text-blue-600': open, 'text-gray-400': !open }"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        style="display: none;">
                        <div class="py-1" role="none">
                            <a href="<?php echo wp_logout_url(home_url()); ?>"
                                class="text-gray-700 block px-4 py-2 text-sm  hover:text-blue-600"
                                role="menuitem" tabindex="-1" id="menu-item-0">
                                Cerrar sesion
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="md:hidden flex items-center space-x-4">
                <a href="/siembra"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-300">
                    <?php the_field('boton_hero_page_2', 'option'); ?>
                </a>
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

<!-- Mobile Menu -->
<div id="menu-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>
<div id="mobile-menu"
    class="fixed top-0 left-0 h-full w-64 bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="flex justify-between items-center p-4 border-b">
        <span class="font-bold">Menu</span>
        <button id="close-menu-button" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>
    </div>
    <div class="p-4 flex-grow">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary_mobile_navigation',
            'walker' => new \App\Walkers\TailwindMobileNavWalker(),
            'container' => false,
            'items_wrap' => '%3$s',
            'depth' => 2,
        ]);
        ?>
    </div>

    <!-- ====== MODIFIED MOBILE MENU FOOTER SECTION ====== -->
    <div class="w-full p-4 border-t bg-white">
        <div class="flex flex-col space-y-2">
            <?php if (!is_user_logged_in()) : ?>
            <a href="/login"
                class="my-button w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-md text-center">
                Ingresar
            </a>
            <a href="/sign-up"
                class="my-button w-full bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold py-3 px-4 rounded-md text-center">
                Registrarme
            </a>
            <?php else: ?>
            <!-- Dropdown menu for logged-in users (code from the previous artifact) -->
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click="open = !open" type="button"
                        class="inline-flex w-full justify-content-left gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold transition-colors duration-150 hover:text-blue-600"
                        :class="{ 'text-blue-600 bg-gray-50': open, 'text-gray-900': !open }" id="menu-button"
                        aria-expanded="true" aria-haspopup="true">
                        Hola, <?php echo esc_html($current_user->display_name); ?>

                        <!-- MODIFIED ICON with dynamic classes and transitions -->
                        <i class="fa-solid fa-chevron-down transition-transform duration-200"
                            :class="{ 'rotate-180 text-blue-600': open, 'text-gray-400': !open }"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        style="display: none;">
                        <div class="py-1" role="none">
                            <a href="<?php echo wp_logout_url(home_url()); ?>"
                                class="text-gray-700 block px-4 py-2 text-sm  hover:text-blue-600"
                                role="menuitem" tabindex="-1" id="menu-item-0">
                                Cerrar sesion
                            </a>
                        </div>
                    </div>
                </div>
            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>"
                class="my-button w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-md text-center">
                Cerrar Sesion
            </a>
            <?php endif; ?>
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
