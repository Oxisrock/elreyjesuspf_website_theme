<?php
/**
 * Template Name: Página de Login Personalizada
 */

// -- LÓGICA PARA MOSTRAR ERRORES Y ÉXITO --
$email_error = '';
$password_error = '';
$success_message = '';
$email_input_class = 'border-gray-300 focus:ring-blue-500';
$password_input_class = 'border-gray-300 focus:ring-blue-500';

if (isset($_GET['login_success']) && $_GET['login_success'] === 'true') {
    $success_message = '¡Inicio de sesión exitoso! Bienvenido de vuelta.';
} elseif (isset($_GET['login_error'])) {
    $error_code = sanitize_key($_GET['login_error']);
    
    if (in_array($error_code, ['invalid_username', 'invalid_email'])) {
        $email_error = 'No existe ningún usuario con este correo electrónico.';
    } elseif ($error_code === 'incorrect_password') {
        $password_error = 'La contraseña que has introducido no es correcta.';
    } elseif ($error_code === 'empty_fields') {
        $password_error = 'Por favor, completa ambos campos.';
    } else {
        // Eliminamos la referencia a errores de recaptcha aquí para simplificar
        $password_error = 'Ha ocurrido un error. Por favor, inténtalo de nuevo.';
    }
    
    if (!empty($email_error)) {
        $email_input_class = 'border-red-500 focus:ring-red-500';
    }
    if (!empty($password_error)) {
        $password_input_class = 'border-red-500 focus:ring-red-500';
    }
}
?>

<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">
        
        <div class="hidden md:flex w-full md:w-3/5 md:h-auto relative bg-cover bg-center" style="background-image: url('<?php the_field('imagen_de_inicio_de_sesion', 'option'); ?>');">
            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 bottom-[-10%] left-[-25%]"></div>
            </div>
        </div>
        
        <div class="w-full md:w-2/5 bg-white flex flex-col justify-between p-6 sm:p-8 md:p-12">
            <div class="w-full max-w-sm mx-auto my-auto">
                <div class="flex items-center justify-center">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-8">
                </div>
                <h1 class="text-2xl text-center font-bold text-blue-600 mb-2"><?php the_field('titulo_de_inicio_de_sesion', 'option'); ?></h1>
                <p class="text-sm text-gray-500 text-center mb-8"><?php the_field('descripcion_de_inicio_de_sesion', 'option'); ?></p>
                
                <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative text-center mb-6" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline"><?php echo $success_message; ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="custom_login">
                    <input type="hidden" id="recaptcha_response" name="recaptcha_response">
                    <?php wp_nonce_field('custom_login_nonce', 'login_nonce'); ?>

                    <div class="mb-4">
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 transition duration-200 <?php echo $email_input_class; ?>">
                        <?php if (!empty($email_error)): ?>
                            <p class="text-red-600 text-xs mt-2 ml-4"><?php echo $email_error; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-6">
                        <input type="password" id="password" name="password" placeholder="Contraseña" required class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 transition duration-200 <?php echo $password_input_class; ?>">
                        <?php if (!empty($password_error)): ?>
                            <p class="text-red-600 text-xs mt-2 ml-4"><?php echo $password_error; ?></p>
                        <?php endif; ?>
                        <a href="/solicitar-clave" class="block text-right text-xs text-blue-600 hover:underline mt-2">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" name="login_submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-full hover:bg-blue-700 transition-colors duration-300 ease-in-out">INICIAR SESION</button>
                    
                    <a href="/sign-up">
                        <div class="w-full mt-4 bg-white text-blue-600 border border-blue-600 font-bold py-2 rounded-full hover:bg-gray-100 transition-colors duration-300 ease-in-out text-center">REGISTRARME</div>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Mostrar loading
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Procesando...</span>';
        submitBtn.disabled = true;

        // Ejecutar reCAPTCHA
        grecaptcha.ready(function() {
            grecaptcha.execute('6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc', {action: 'login'}).then(function(token) {
                // Asignar el token al campo hidden
                document.getElementById('recaptcha_response').value = token;

                // Enviar el formulario
                form.submit();
            });
        });
    });
});
</script>