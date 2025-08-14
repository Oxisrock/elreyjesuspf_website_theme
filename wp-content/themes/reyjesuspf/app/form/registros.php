<?php
// 1. FUNCIÓN PARA MOSTRAR EL CAMPO EN LA PÁGINA DE PERFIL
// Se ejecuta cuando se muestra el perfil de un usuario.
add_action( 'show_user_profile', __NAMESPACE__ . '\\jj_mostrar_campo_telefono_en_perfil' );
add_action( 'edit_user_profile', __NAMESPACE__ . '\\jj_mostrar_campo_telefono_en_perfil' );

function jj_mostrar_campo_telefono_en_perfil( $user ) {
    // Obtenemos el teléfono guardado para este usuario.
    $telefono = get_user_meta( $user->ID, 'phone', true );
    ?>
    <h3>Información Adicional</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone">Número de Teléfono</label></th>
            <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr( $telefono ); ?>" class="regular-text" />
                <p class="description">Número de contacto del usuario.</p>
            </td>
        </tr>
    </table>
    <?php
}

// 2. FUNCIÓN PARA GUARDAR EL CAMPO CUANDO SE ACTUALIZA EL PERFIL
// Se ejecuta cuando el usuario hace clic en "Actualizar perfil".
add_action( 'personal_options_update', __NAMESPACE__ . '\\jj_guardar_campo_telefono_del_perfil' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\jj_guardar_campo_telefono_del_perfil' );

function jj_guardar_campo_telefono_del_perfil( $user_id ) {
    // Verificación de seguridad: ¿el usuario actual tiene permiso?
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    // Si el campo 'phone' fue enviado, lo limpiamos y guardamos.
    if ( isset( $_POST['phone'] ) ) {
        $telefono_sanitizado = sanitize_text_field( $_POST['phone'] );
        update_user_meta( $user_id, 'phone', $telefono_sanitizado );
    }
}

/**
 * Función para manejar el registro de usuario personalizado a través de AJAX.
 * Se activará con la acción 'wp_ajax_nopriv_ajax_custom_register'.
 */
function handle_custom_registration() {
    // Definir la respuesta por defecto
    $response = ['success' => false, 'data' => ['errors' => []]];

    // 1. Verificar el nonce de seguridad
    if (!isset($_POST['security_nonce']) || !wp_verify_nonce($_POST['security_nonce'], 'custom_register_nonce')) {
        $response['data'] = ['message' => 'Error de seguridad. Por favor, recarga la página e intenta de nuevo.'];
        wp_send_json_error($response['data'], 403);
    }

    // 2. Limpiar y validar los datos recibidos
    $full_name        = sanitize_text_field($_POST['full_name']);
    $phone            = sanitize_text_field($_POST['phone']);
    $email            = sanitize_email($_POST['email']);
    $password         = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $terms            = isset($_POST['terms']) ? true : false;
    
    // Obtener el nombre de usuario desde el email (primera parte antes del @)
    $username = explode('@', $email)[0];
    
    // Validaciones
    if (empty($full_name)) {
        $response['data']['errors']['full_name'] = 'Por favor, introduce tu nombre completo.';
    }
    if (!is_email($email)) {
        $response['data']['errors']['email'] = 'El correo electrónico no es válido.';
    } elseif (email_exists($email)) {
        $login_url = wp_login_url();
        $reset_url = wp_lostpassword_url();
        $error_message = '¿Quieres <a href="' . $login_url . '"><strong>iniciar sesión</strong></a> o <a href="' . $reset_url . '"><strong>recuperar tu contraseña</strong></a>?';
        
        $response['data'] = [
            'error_type' => 'duplicate_email',
            'message'    => $error_message
        ];
        wp_send_json_error($response['data'], 409);
    }
    if (username_exists($username)) {
        $username = $username . time();
    }
    if (strlen($password) < 8) {
        $response['data']['errors']['password'] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $password_confirm) {
        $response['data']['errors']['password_confirm'] = 'Las contraseñas no coinciden.';
    }
    if (!$terms) {
        $response['data']['errors']['terms'] = 'Debes aceptar los términos y condiciones.';
    }

    // Si hay errores, enviar la respuesta y terminar
    if (!empty($response['data']['errors'])) {
        wp_send_json_error($response['data'], 400);
    }

    // 3. Crear el nuevo usuario de WordPress
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        $response['data'] = ['message' => 'Hubo un error inesperado al crear el usuario. Por favor, intenta de nuevo.'];
        wp_send_json_error($response['data'], 500);
    } else {
        // 4. Actualizar el nombre completo del usuario
        wp_update_user([
            'ID'         => $user_id,
            'display_name' => $full_name
        ]);
        
        // Opcional: Almacenar el teléfono en los metadatos del usuario
        if (!empty($phone)) {
            update_user_meta($user_id, 'billing_phone', $phone); // Se usa 'billing_phone' si usas WooCommerce, o un meta key personalizado
        }

        // 5. Enviar el correo de bienvenida con tu plantilla
        $subject = '¡Bienvenido(a) a nuestra comunidad!'; // Asunto para el correo de registro
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        // **INICIO DE TU PLANTILLA DE CORREO HTML**
        $logo_url = get_field('logo_en_negro', 'option'); // Asegúrate de que esta URL del logo es correcta para el registro
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
                                            ¡Gracias por unirte a nuestra comunidad! Nos alegra mucho tenerte con nosotros.
                                        </p>
                                        <p style="color: #555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                            Recuerda que Dios está contigo:
                                        </p>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                            <tr>
                                                <td align="center" style="padding: 20px; border-left: 3px solid #bda071;">
                                                    <p style="color: #555; font-size: 17px; line-height: 1.7; font-style: italic; margin: 0;">
                                                        ¡Levántate y resplandece que tu luz ha llegado!
                                                        ¡La gloria del Señor brilla sobre ti!
                                                        Mira, las tinieblas cubren la tierra
                                                        y una densa oscuridad se cierne sobre los pueblos.
                                                        Pero la aurora del Señor brillará sobre ti;
                                                        ¡sobre ti se manifestará su gloria!<br>
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

        wp_mail($email, $subject, $body, $headers);

        // 6. Enviar respuesta de éxito
        $response['success'] = true;
        wp_send_json_success($response);
    }
}

// Conectar la función al hook de AJAX para usuarios no logueados (para el registro)
add_action('wp_ajax_ajax_custom_register', __NAMESPACE__ . '\\handle_custom_registration');
add_action('wp_ajax_nopriv_ajax_custom_register', __NAMESPACE__ . '\\handle_custom_registration');