<?php
/**
 * Template Name: Página de Login Personalizada
 */

// -- INICIO DE LA LÓGICA DE LOGIN --
$email_error = '';
$password_error = '';
// Clases CSS por defecto para los campos de entrada
$email_input_class = 'border-gray-300 focus:ring-blue-500';
$password_input_class = 'border-gray-300 focus:ring-blue-500';

// Verificamos si el formulario fue enviado.
if (isset($_POST['login_submit'])) {
    // 1. Obtenemos y saneamos los datos del formulario.
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // 2. Validación del lado del servidor para campos vacíos.
    if (empty($email)) {
        $email_error = 'Por favor, introduce tu correo electrónico.';
    }
    if (empty($password)) {
        $password_error = 'Por favor, introduce tu contraseña.';
    }

    // 3. Si no hay errores de validación, intentamos iniciar sesión.
    if (empty($email_error) && empty($password_error)) {
        $creds = [
            'user_login' => $email,
            'user_password' => $password,
            'remember' => true,
        ];

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            // Verificamos el código de error para mostrar el mensaje en el campo correcto.
            $error_code = $user->get_error_code();
            if (in_array($error_code, ['invalid_username', 'invalid_email'])) {
                $email_error = 'No existe ningún usuario con este correo electrónico.';
            } elseif ('incorrect_password' === $error_code) {
                $password_error = 'La contraseña que has introducido no es correcta.';
            } else {
                // Error genérico para otros casos, se muestra debajo del campo de contraseña.
                $password_error = 'Error desconocido. Por favor, inténtalo de nuevo.';
            }
        } else {
            // Si el inicio de sesión es exitoso, redirigimos.
            wp_safe_redirect(home_url('/'));
            exit();
        }
    }

    // 4. Asignamos clases de error a los campos si existen errores.
    if (!empty($email_error)) {
        $email_input_class = 'border-red-500 focus:ring-red-500';
    }
    if (!empty($password_error)) {
        $password_input_class = 'border-red-500 focus:ring-red-500';
    }
}
// -- FIN DE LA LÓGICA DE LOGIN --
?>

<div class="bg-gray-100">
    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Columna de la imagen -->
        <div class="hidden md:flex w-full md:w-3/5 md:h-auto relative bg-cover bg-center"
            style="background-image: url('<?php the_field('imagen_de_inicio_de_sesion', 'option'); ?>');">

            <div class="absolute inset-0 w-full h-full overflow-hidden">
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 top-[-10%] left-[-25%]"></div>
                <div class="absolute h-3/5 w-[150%] bg-cyan-600/20 transform -skew-y-12 bottom-[-10%] left-[-25%]"></div>
            </div>
        </div>

        <!-- Columna del formulario -->
        <div class="w-full md:w-2/5 bg-white flex flex-col justify-between p-6 sm:p-8 md:p-12">
            <div class="w-full max-w-sm mx-auto my-auto">
                <div class="flex items-center justify-center">
                    <img src="<?php the_field('logo', 'option'); ?>" alt="Logo de la iglesia" class="h-14 w-14 mb-8">
                </div>
                <h1 class="text-2xl text-center font-bold text-blue-600 mb-2"><?php the_field('titulo_de_inicio_de_sesion', 'option'); ?></h1>
                <p class="text-sm text-gray-500 text-center mb-8"><?php the_field('descripcion_de_inicio_de_sesion', 'option'); ?></p>

                <form method="POST" action="">

                    <div class="mb-4">
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" required
                            class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 transition duration-200 <?php echo $email_input_class; ?>">
                        <?php if ( ! empty( $email_error ) ) : ?>
                        <p class="text-red-600 text-xs mt-2 ml-4"><?php echo $email_error; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-6">
                        <input type="password" id="password" name="password" placeholder="Contraseña" required
                            class="w-full px-4 py-3 border rounded-full focus:outline-none focus:ring-2 transition duration-200 <?php echo $password_input_class; ?>">
                        <?php if ( ! empty( $password_error ) ) : ?>
                        <p class="text-red-600 text-xs mt-2 ml-4"><?php echo $password_error; ?></p>
                        <?php endif; ?>
                        <a href="/solicitar-clave"
                            class="block text-right text-xs text-blue-600 hover:underline mt-2">¿Olvidaste tu
                            contraseña?</a>
                    </div>

                    <button type="submit" name="login_submit"
                        class="w-full bg-blue-600 text-white font-bold py-2 rounded-full hover:bg-blue-700 transition-colors duration-300 ease-in-out">
                        INICIAR SESION
                    </button>
                    <a href="/sign-up">
                        <div
                            class="w-full mt-4 bg-white text-blue-600 border border-blue-600 font-bold py-2 rounded-full hover:bg-gray-100 transition-colors duration-300 ease-in-out text-center">
                            REGISTRARME
                        </div>
                    </a>
                </form>
            </div>
        </div>

    </div>
</div>
