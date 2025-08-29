<?php /* Template Name: Nueva Contraseña */ ?>
<?php
$error = '';
$success = false;
$key = isset($_GET['key']) ? $_GET['key'] : null;
$login = isset($_GET['login']) ? $_GET['login'] : null;

// 1. VERIFICAR EL TOKEN AL CARGAR LA PÁGINA
$user = check_password_reset_key($key, $login);

if (!$key || !$login || is_wp_error($user)) {
    // Si la clave no es válida o ha expirado, no mostramos el formulario
    $show_form = false;
    $error = 'El enlace de recuperación no es válido o ha expirado. Por favor, solicita uno nuevo.';
} else {
    $show_form = true;

    // 2. PROCESAR EL FORMULARIO SI FUE ENVIADO
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        $new_password = $_POST['new-password'];
        $confirm_password = $_POST['confirm-password'];

        if (empty($new_password) || empty($confirm_password)) {
            $error = 'Ambos campos de contraseña son obligatorios.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            // 3. USAR LA FUNCIÓN DE WORDPRESS PARA CAMBIAR LA CONTRASEÑA
            reset_password($user, $new_password);
            $success = true;
            $show_form = false; // Ocultamos el form y mostramos mensaje de éxito
        }
    }
}
?>

<div class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="max-w-md w-full bg-white p-8 shadow-lg rounded-lg">
            
            <?php if ($success) : ?>
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <h1 class="text-2xl font-bold text-gray-800 mt-4">¡Contraseña actualizada!</h1>
                    <p class="text-gray-500 mt-2">Tu contraseña ha sido cambiada exitosamente.</p>
                    <div class="mt-6">
                        <a href="/login" class="w-full inline-block text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            Ir a Inicio de Sesión
                        </a>
                    </div>
                </div>
            
            <?php elseif ($show_form) : ?>
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Crea una nueva contraseña</h1>
                    <p class="text-gray-500 mt-2">Asegúrate de que tu nueva contraseña sea segura.</p>
                </div>
                
                <?php if (!empty($error)) : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?php echo esc_url(add_query_arg(array('key' => $key, 'login' => $login), get_permalink())); ?>" method="POST" class="space-y-4">
                    <div>
                        <label for="new-password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                        <input type="password" name="new-password" id="new-password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirm-password" id="confirm-password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Guardar Contraseña
                        </button>
                    </div>
                </form>
                
            <?php else : ?>
                 <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-800 mt-4">Enlace Inválido</h1>
                    <p class="text-gray-500 mt-2"><?php echo $error; ?></p>
                    <div class="mt-6">
                        <a href="/solicitar-clave" class="text-sm text-blue-600 hover:underline">
                           Solicitar un nuevo enlace
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
