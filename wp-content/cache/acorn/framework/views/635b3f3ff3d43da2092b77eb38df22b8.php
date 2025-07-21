<style>
    /* Opcional: para una fuente que coincida mejor con la imagen */
    @import url('<?= \Roots\asset('images/hero-page/Un camino de fe.13f7f6.jpg'); ?>');
    body {
        font-family: 'Poppins', sans-serif;
    }
</style>
<div class="bg-gray-100">

    <div class="flex flex-col md:flex-row min-h-screen">

        <div class="w-full md:w-2/5 bg-white flex flex-col justify-center p-6 sm:p-8 md:p-12">
            <div class="w-full max-w-sm mx-auto">
                <div class="flex items-center justify-center">
                    <img src="<?= \Roots\asset('images/header-page/Logoiglesiaazul.png'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-8">
                </div>

                <h1 class="text-2xl font-bold text-center text-blue-600 mb-2">Crea tu cuenta</h1>
                <p class="text-sm text-gray-500 mb-8 text-center">Recibe invitaciones directamente a tu correo <br>electrónico y participa en nuestros eventos.</p>

                <form action="#" method="POST" class="space-y-4">
                    <input type="text" placeholder="Nombre Completo" class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="tel" placeholder="Teléfono" class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="email" placeholder="Correo electrónico" class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="password" placeholder="Contraseña" class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="password" placeholder="Confirmar contraseña" class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    
                    <div class="flex items-center pt-2">
                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="terms" class="ml-2 block text-sm text-gray-700">Acepto los <a href="#" class="font-medium text-blue-600 hover:underline">Términos y Condiciones</a></label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-full hover:bg-blue-700 transition-colors">
                            REGISTRARME
                        </button>
                        <a href="#" class="block text-center mt-4 text-sm font-bold text-blue-600 hover:underline">
                            VOLVER
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:flex w-full md:w-3/5 md:h-auto relative bg-cover bg-center" style="background-image: url('<?= \Roots\asset('images/hero-page/Un camino de fe.13f7f6.jpg'); ?>');">
            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 bottom-[-10%] left-[-25%]"></div>
            </div>
        </div>
        </div>

</div><?php /**PATH /var/www/html/wp-content/themes/reyjesuspf/resources/views/partials/signuppage/signup.blade.php ENDPATH**/ ?>