<?php
// 1. FUNCIÓN PARA MOSTRAR EL CAMPO EN LA PÁGINA DE PERFIL
// Se ejecuta cuando se muestra el perfil de un usuario.
add_action('show_user_profile', __NAMESPACE__ . '\\jj_mostrar_campo_telefono_en_perfil');
add_action('edit_user_profile', __NAMESPACE__ . '\\jj_mostrar_campo_telefono_en_perfil');

function jj_mostrar_campo_telefono_en_perfil($user)
{
    // Obtenemos el teléfono guardado para este usuario.
    $telefono = get_user_meta($user->ID, 'phone', true);
?>
    <h3>Información Adicional</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone">Número de Teléfono</label></th>
            <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr($telefono); ?>" class="regular-text" />
                <p class="description">Número de contacto del usuario.</p>
            </td>
        </tr>
    </table>
<?php
}

// 2. FUNCIÓN PARA GUARDAR EL CAMPO CUANDO SE ACTUALIZA EL PERFIL
// Se ejecuta cuando el usuario hace clic en "Actualizar perfil".
add_action('personal_options_update', __NAMESPACE__ . '\\jj_guardar_campo_telefono_del_perfil');
add_action('edit_user_profile_update', __NAMESPACE__ . '\\jj_guardar_campo_telefono_del_perfil');

function jj_guardar_campo_telefono_del_perfil($user_id)
{
    // Verificación de seguridad: ¿el usuario actual tiene permiso?
    if (! current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Si el campo 'phone' fue enviado, lo limpiamos y guardamos.
    if (isset($_POST['phone'])) {
        $telefono_sanitizado = sanitize_text_field($_POST['phone']);
        update_user_meta($user_id, 'phone', $telefono_sanitizado);
    }
}

/**
 * Función para manejar el registro de usuario personalizado a través de AJAX.
 * Se activará con la acción 'wp_ajax_nopriv_ajax_custom_register'.
 */
function handle_custom_registration()
{

    // 1. Verify the security nonce
    if (!isset($_POST['security_nonce']) || !wp_verify_nonce($_POST['security_nonce'], 'custom_register_nonce')) {
        $response['data'] = ['message' => 'Security error. Please reload the page and try again.'];
        wp_send_json_error($response['data'], 403);
    }

    // 2. VERIFICACIÓN DE GOOGLE RECAPTCHA V3
    $recaptcha_secret_key = '6LePAbwrAAAAAGT4G4s6FngmaTEK3O0UdPqGfOfT';
    $recaptcha_token = isset($_POST['recaptcha_response']) ? sanitize_text_field($_POST['recaptcha_response']) : '';

    if (empty($recaptcha_token)) {
        $response['data']['errors']['recaptcha'] = 'Error de verificación de seguridad. Por favor, recarga la página e intenta de nuevo.';
        wp_send_json_error($response['data'], 400);
    }

    $verification_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => $recaptcha_secret_key,
            'response' => $recaptcha_token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]
    ]);

    if (is_wp_error($verification_response)) {
        $response['data']['errors']['recaptcha'] = 'No se pudo conectar con el servicio de verificación.';
        wp_send_json_error($response['data'], 500);
    }

    $response_data = json_decode(wp_remote_retrieve_body($verification_response));

    // Verificar si la comunicación con Google fue exitosa
    if (!$response_data || !$response_data->success) {
        $response['data']['errors']['recaptcha'] = 'Error de comunicación con el servicio reCAPTCHA.';
        wp_send_json_error($response['data'], 400);
    }

    // Verificar si estamos en entorno local
    $is_local_environment = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    // La validación del SCORE solo se aplica si NO estamos en entorno local
    if (!$is_local_environment && $response_data->score < 0.5) {
        $response['data']['errors']['recaptcha'] = 'Falló la verificación de humanidad. Intenta de nuevo.';
        wp_send_json_error($response['data'], 400);
    }

    // Si el código llega aquí, el reCAPTCHA es válido (ya sea por buen score o por estar en local).

    // ⚠️ FIN DE LA MODIFICACIÓN ⚠️

    // SI LLEGAMOS AQUÍ, RECAPTCHA ES VÁLIDO. CONTINUAMOS CON TU LÓGICA DE REGISTRO.

    // 3. Limpiar y validar los datos recibidos
    $full_name        = sanitize_text_field($_POST['full_name']);
    $phone            = sanitize_text_field($_POST['phone']);
    $email            = sanitize_email($_POST['email']);
    $password         = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $terms            = isset($_POST['terms']);

    // Obtener el nombre de usuario desde el email (primera parte antes del @)

    $username = explode('@', $email)[0];


    // Validaciones

    if (empty($full_name)) {
        $response['data']['errors']['full_name'] = 'Por favor, introduce tu nombre completo.';
    }


    // Validación del correo electrónico (formato y existencia)
    if (!is_email($email)) {
        $response['data']['errors']['email'] = 'El correo electrónico no es válido.';
    } elseif (email_exists($email)) {
        // Este es un caso especial: el correo es válido pero ya existe.
        // Detenemos la ejecución y enviamos una respuesta específica que el frontend sabe cómo manejar.
        $login_url = home_url('/login');
        $reset_url = home_url('/solicitar-clave');
        $error_message = '¿Quieres <a href="' . $login_url . '"><strong>iniciar sesión</strong></a> o <a href="' . $reset_url . '"><strong>recuperar tu contraseña</strong></a>?';

        $response['data'] = [
            'error_type' => 'duplicate_email',
            'message'    => $error_message
        ];
        wp_send_json_error($response['data'], 409); // 409 Conflict es un código ideal para este caso.
    }
    // Validación de la contraseña
    if (strlen($password) < 8) {
        $response['data']['errors']['password'] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $password_confirm) {
        $response['data']['errors']['password_confirm'] = 'Las contraseñas no coinciden.';
    }

    // Validación de los términos y condiciones
    if (!$terms) {
        $response['data']['errors']['terms'] = 'Debes aceptar los términos y condiciones.';
    }

    // 5. Comprobar si hubo errores de validación
    // ==============================================
    if (!empty($response['data']['errors'])) {
        // Si el array de errores no está vacío, enviamos todos los errores juntos y detenemos el script.
        wp_send_json_error($response['data'], 400); // 400 Bad Request es el código estándar para errores de validación.
    }

    // SI LLEGAMOS AQUÍ, TODOS LOS DATOS SON VÁLIDOS. PROCEDEMOS A CREAR EL USUARIO.

    // 6. Preparar y crear el nuevo usuario
    // =====================================

    // Generar un nombre de usuario único a partir del email.
    $username_parts = explode('@', $email);
    $username = $username_parts[0];
    // Si el nombre de usuario ya existe, le añadimos un timestamp para garantizar que sea único.
    if (username_exists($username)) {
        $username = $username . time();
    }

    $user_id = wp_create_user($username, $password, $email);

    // 7. Manejar el resultado de la creación del usuario
    // ==================================================
    if (is_wp_error($user_id)) {
        // Si WordPress devuelve un error, enviamos una respuesta de error genérica.
        $response['data'] = ['message' => 'Hubo un error inesperado al crear el usuario. Por favor, intenta de nuevo.'];
        wp_send_json_error($response['data'], 500); // 500 Internal Server Error.
    } else {

        // ¡ÉXITO! El usuario fue creado.

        // 8. Añadir metadatos adicionales y enviar correo de bienvenida
        // ==========================================================

        // Actualizar el nombre para mostrar (display_name)
        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $full_name
        ]);

        // Almacenar el teléfono si fue proporcionado
        if (!empty($phone)) {
            update_user_meta($user_id, 'billing_phone', $phone);
        }
        // 9. Enviar correo de bienvenida
        // ==========================================
        $subject = '¡Bienvenido(a) a nuestra comunidad!';
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        // (Aquí va tu código para construir el cuerpo del correo HTML, que no necesita cambios)
        $logo_url = get_field('logo_en_negro', 'option');
        $url_inicio = esc_url(home_url('/'));
        $titulo_sitio = esc_attr(get_bloginfo('name'));
        $enlace_html = '<a href="' . $url_inicio . '" title="' . $titulo_sitio . '" style="color: #ffffff; text-decoration: none;">' . $titulo_sitio . '</a>';

        $body = <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head><title>$subject</title></head>

<body style="margin: 0; padding: 0; background-color: #ffffff; font-family: 'Georgia', serif;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td align="center" style="padding: 40px 0;">
                                <img src="$logo_url" alt="Logo de la Iglesia" width="120" style="display: block;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 30px 30px 30px; text-align: center;">
                                <h1 style="color: #333; margin: 0; font-weight: normal;">Hola, $full_name</h1>
                                <p style="color: #555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                Recuerda que Dios está contigo:
                                </p>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                    <tr>
                                        <td align="center" style="padding: 20px; border-left: 3px solid #bda071;">
                                            <p style="color: #555; font-size: 17px; line-height: 1.7; font-style: italic; margin: 0;">
                                                ¡Levántate y resplandece que tu luz ha llegado! ¡La gloria del Señor brilla sobre ti!...<br>
                                                <span style="display: block; margin-top: 10px; font-style: normal;">- Isaías 60:1-2</span>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#1520A6" style="padding: 25px 30px;">
                                <p style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px; text-align: center; margin: 0;">
                                    En Su amor y servicio,<br>
                                    $enlace_html
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
HTML;

        // **FIN DE TU PLANTILLA DE CORREO HTML**

        // Enviar el correo de bienvenida
        wp_mail($email, $subject, $body, $headers);

        // 10. Enviar respuesta final de éxito
        // ===================================
        $response['success'] = true;
        wp_send_json_success($response);
    }
}

// Conectar la función al hook de AJAX para usuarios no logueados (para el registro)
add_action('wp_ajax_ajax_custom_register', __NAMESPACE__ . '\\handle_custom_registration');
add_action('wp_ajax_nopriv_ajax_custom_register', __NAMESPACE__ . '\\handle_custom_registration');

function custom_password_reset_email_message($message, $key, $user_login, $user_data)
{

    $site_name = get_bloginfo('name');
    $reset_link = home_url('/nueva-contrasena/?key=' . $key . '&login=' . rawurlencode($user_login));
    $logo_url = get_field('logo', 'option');

    // --> 1. AQUÍ AÑADES LA NUEVA LÍNEA
    $display_name = ! empty($user_data->first_name) ? $user_data->first_name : $user_login;

    // Aquí pegas tu plantilla adaptada
    $html_template = <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Restablecer tu Contraseña</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #ffffff; font-family: 'Georgia', serif;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td align="center" style="padding: 40px 0;">
                                <img src="{$logo_url}" alt="Logo de la Iglesia" width="120" style="display: block;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0 30px 30px 30px; text-align: center;">
                                <h1 style="color: #333; margin: 0 0 20px 0; font-weight: normal;">Restablecer tu Contraseña</h1>
                                <p style="color: #555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                    Hola, <strong>{$display_name}</strong>.
                                </p>
                                <p style="color: #555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                    Recibimos una solicitud para restablecer tu contraseña. Si no has sido tú, puedes ignorar este mensaje.
                                </p>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                    <tr>
                                        <td align="center">
                                            <a href="{$reset_link}" target="_blank" style="display: inline-block; padding: 15px 30px; background-color: #1520A6; color: #ffffff; font-family: Arial, sans-serif; font-size: 16px; text-decoration: none; border-radius: 5px;">
                                                Crear Nueva Contraseña
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <p style="color: #777; font-size: 14px; line-height: 1.6; margin: 20px 0;">
                                    Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                                    <a href="{$reset_link}" target="_blank" style="color: #1520A6; word-break: break-all;">{$reset_link}</a>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#1520A6" style="padding: 25px 30px;">
                                <p style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px; text-align: center; margin: 0;">
                                    &copy; 2025 {$site_name}. Todos los derechos reservados.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    HTML;

    return $html_template;
}
/**
 * Personaliza el Asunto del correo.
 */
function custom_password_reset_email_subject($subject)
{
    $site_name = get_bloginfo('name');
    return "[{$site_name}] Restablecimiento de tu contraseña";
}

/**
 * Establece el tipo de contenido del correo a HTML.
 */
function set_html_content_type()
{
    return 'text/html';
}

// No olvides mantener los otros filtros que te di
add_filter('retrieve_password_message', __NAMESPACE__ . '\\custom_password_reset_email_message', 10, 4);
add_filter('retrieve_password_title', __NAMESPACE__ . '\\custom_password_reset_email_subject', 10);
add_filter('wp_mail_content_type', __NAMESPACE__ . '\\set_html_content_type');
