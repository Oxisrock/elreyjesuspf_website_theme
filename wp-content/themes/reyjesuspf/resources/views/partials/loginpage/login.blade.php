<?php
/**
 * Template Name: Página de Login Personalizada
 */

// -- LÓGICA PARA MOSTRAR ERRORES --
// Leemos el código de error que nos llega por la URL y preparamos los mensajes.
$email_error = '';
$password_error = '';
$email_input_class = 'border-gray-300 focus:ring-blue-500';
$password_input_class = 'border-gray-300 focus:ring-blue-500';

if (isset($_GET['login_error'])) {
    $error_code = sanitize_key($_GET['login_error']);
    
    if (in_array($error_code, ['invalid_username', 'invalid_email'])) {
        $email_error = 'No existe ningún usuario con este correo electrónico.';
    } elseif ($error_code === 'incorrect_password') {
        $password_error = 'La contraseña que has introducido no es correcta.';
    } elseif ($error_code === 'empty_fields') {
        $password_error = 'Por favor, completa ambos campos.';
    } elseif (in_array($error_code, ['recaptcha_token', 'recaptcha_fail'])) {
        $password_error = 'Error de verificación. Por favor, intenta de nuevo.';
    } else {
        $password_error = 'Ha ocurrido un error. Por favor, inténtalo de nuevo.';
    }
    
    // Asignamos clases de error a los campos si es necesario.
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
                
                <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="custom_login">
                    <input type="hidden" id="recaptcha_response" name="recaptcha_response">

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
    
    <script src="https://www.google.com/recaptcha/api.js?render=6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('6LePAbwrAAAAAKyfRATtLV8-bekhYdta6VpzCroc', { action: 'login' }).then(function(token) {
                var recaptchaResponse = document.getElementById('recaptcha_response');
                recaptchaResponse.value = token;
            });
        });
    </script>
</div>