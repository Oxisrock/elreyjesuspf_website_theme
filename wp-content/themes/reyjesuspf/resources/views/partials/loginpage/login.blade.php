<style>
    /* Estilos adicionales para un aspecto más pulido si es necesario */
    @import url('@asset('images/hero-page/Un camino de fe.13f7f6.jpg')');

    body {
        font-family: 'Poppins', sans-serif;
    }
</style>

<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">

        <div class="hidden md:flex w-full md:w-3/5 md:h-auto relative bg-cover bg-center"
             style="background-image: url('@asset('images/hero-page/Un camino de fe.13f7f6.jpg')');">
            
            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 bottom-[-10%] left-[-25%]"></div>
            </div>
        </div>
        <div class="w-full md:w-2/5 bg-white flex flex-col justify-between p-6 sm:p-8 md:p-12">
            <div class="w-full max-w-sm mx-auto my-auto">
                <div class="flex items-center justify-center">
                    <img src="@asset('images/header-page/Logoiglesiaazul.png')" alt="Logo de la iglesia" class="h-14 w-14 mb-8">
                </div>
                <h1 class="text-2xl text-center font-bold text-blue-600 mb-2">Inicia sesión</h1>
                <p class="text-sm text-gray-500 text-center mb-8">Recibe invitaciones directamente a tu correo <br>electrónico y participa en nuestros eventos.</p>

                <form action="#" method="POST">
                    <div class="mb-4">
                        <input type="email" id="email" name="email" placeholder="Correo electrónico"
                               class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>

                    <div class="mb-6">
                        <input type="password" id="password" name="password" placeholder="Contraseña"
                               class="w-full px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                        <a href="#" class="block text-right text-xs text-blue-600 hover:underline mt-2">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white font-bold py-2 rounded-full hover:bg-blue-700 transition-colors duration-300 ease-in-out">
                        ENVIAR
                    </button>
                    <button type="button"
                            class="w-full mt-4 bg-white text-blue-600 border border-blue-600 font-bold py-2 rounded-full hover:bg-gray-100 transition-colors duration-300 ease-in-out">
                        REGISTRARME
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>