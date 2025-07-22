    <style>
        .hero-background-overlay {
            /* Asegúrate de que la ruta a tu imagen sea correcta */
            background-image: url('@asset('images/yoconstruyo-page/IMG-20221125-WA0111 (1).jpg')');
            background-position: center center;
            background-size: cover;
            /* No es necesario 'opacity' aquí si usamos 'background-color' con alpha */
        }
    </style>
<div class="bg-zinc-950">

    <div class="font-sans antialiased">
        <div class="relative h-screen flex items-center justify-center text-white">
            <div class="absolute inset-0 hero-background-overlay"></div>
            <div class="absolute inset-0 bg-black opacity-60"></div>

            <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
                <h1 class="text-4xl sm:text-5xl md:text-5xl font-extrabold leading-tight mb-6">
                    ¡Manos a la Obra!
                </h1>
                <p class="text-lg sm:text-xl md:text-xl font-medium mb-10 leading-relaxed">
                    Tu Siembra en <span class="text-blue-400">#YoConstruyo</span>, es un aporte para el Templo donde tus generaciones conocerán la presencia de Dios.
                </p>

                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <button
                        class="w-full sm:w-auto bg-blue-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75">
                        Sembrar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>