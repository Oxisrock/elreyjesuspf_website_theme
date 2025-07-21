<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">

        <div class="w-full md:w-2/5 bg-white flex flex-col justify-center p-8 sm:p-12">
            <div class="w-full max-w-sm mx-auto">
                
                <div class="flex items-center justify-center">
                    <img src="@asset('images/header-page/Logoiglesiaazul.png')" alt="Logo de la iglesia" class="h-14 w-14 mb-6">
                </div>

                <h1 class="text-2xl font-bold text-center text-blue-600 mb-2">Ponte en Contacto</h1>
                <p class="text-base text-gray-500 mb-8 text-center">Estamos aquí para servirte. <br>Déjanos tu mensaje y te responderemos pronto.</p>

                <form action="https://formspree.io/f/TU_ID_UNICO" method="POST" class="space-y-4">
                    
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" required class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="asunto" class="block text-sm font-medium text-gray-700">Asunto</o>
                        <select id="asunto" name="asunto" required class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Selecciona un motivo...</option>
                            <option value="peticion_oracion">Petición de Oración</option>
                            <option value="informacion_general">Información General</option>
                            <option value="voluntariado">Quiero ser voluntario</option>
                            <option value="visita_pastoral">Solicitar una visita pastoral</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div>
                        <label for="mensaje" class="block text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-full hover:bg-blue-700 transition-colors">
                            Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:flex w-3/5 relative bg-cover bg-center" style="background-image: url('@asset('images/hero-page/Un camino de fe.13f7f6.jpg')');">
            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 bottom-[-10%] left-[-25%]"></div>
            </div>
        </div>

    </div>
</div>