<?php
// Variable para guardar mensajes
$message = '';
$error = '';
$show_confirmation = false;

// 1. VERIFICAR SI EL FORMULARIO FUE ENVIADO
if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $email = sanitize_email($_POST['email']);

    if (empty($email)) {
        $error = 'Por favor, introduce una dirección de correo electrónico.';
    } elseif (!is_email($email)) {
        $error = 'La dirección de correo electrónico no es válida.';
    } else {
        // 2. USAR LA FUNCIÓN DE WORDPRESS PARA RECUPERAR CONTRASEÑA
        $result = retrieve_password($email);

        if (is_wp_error($result)) {
            // Si hubo un error (ej: el correo no existe)
            $error = $result->get_error_message();
        } else {
            // Si todo salió bien, mostramos el mensaje de confirmación
            $show_confirmation = true;
        }
    }
}
?>

<div class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="max-w-md w-full bg-white p-8 shadow-lg rounded-lg">

            <?php if ($show_confirmation) : ?>

                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-800 mt-4">Revisa tu correo</h1>
                    <p class="text-gray-500 mt-2">
                        Hemos enviado un enlace para restablecer la contraseña a tu dirección de correo.
                    </p>
                    <div class="mt-6">
                        <a href="/login" class="text-sm text-blue-600 hover:underline">
                            Volver a Inicio de Sesión
                        </a>
                    </div>
                </div>

            <?php else : ?>

                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">¿Olvidaste tu contraseña?</h1>
                    <p class="text-gray-500 mt-2">Ingresa tu correo y te enviaremos un enlace para recuperarla.</p>
                </div>
                
                <?php if (!empty($error)) : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?php echo esc_url(get_permalink()); ?>" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="email" id="email" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Enviar Enlace de Recuperación
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <a href="/login" class="text-sm text-blue-600 hover:underline">
                        Volver a Inicio de Sesión
                    </a>
                </div>
            
            <?php endif; ?>
        </div>
    </div>
</div>