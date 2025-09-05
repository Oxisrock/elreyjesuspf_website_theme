<?php

/**
 * --------------------------------------------------------------------------
 * PARTE A: CREACIÓN DE LA TABLA EN LA BASE DE DATOS
 * Se ejecuta 1 sola vez al reactivar el tema.
 * --------------------------------------------------------------------------
 */
function event_registrations_create_db_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_registrations';
    $charset_collate = $wpdb->get_charset_collate();
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(100) NOT NULL,
        cedula varchar(20) DEFAULT '' NULL,
        event_id bigint(20) UNSIGNED NOT NULL,
        email varchar(100) NOT NULL,
        phone_number varchar(20) DEFAULT '' NULL,
        iglesia varchar(100) DEFAULT '' NOT NULL,    
        red varchar(50) DEFAULT '' NOT NULL,         
        registration_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id),
        KEY event_id (event_id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
event_registrations_create_db_table();


/**
 * --------------------------------------------------------------------------
 * PARTE B: PÁGINA DE ADMINISTRACIÓN PARA VER REGISTROS
 * --------------------------------------------------------------------------
 */
// 1. Añadir el submenú "Registros" bajo el CPT "Events"
function add_event_registrations_submenu_page()
{
    add_submenu_page(
        'edit.php?post_type=events',
        'Registros de Eventos',
        'Registros',
        'manage_options',
        'event-registrations',
        'render_event_registrations_page'
    );
}
add_action('admin_menu', __NAMESPACE__ . '\\add_event_registrations_submenu_page');

// 2. Mostrar la tabla con los datos de nuestra tabla personalizada
function render_event_registrations_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_registrations';

    $query = "SELECT r.id, r.nombre, r.cedula, r.email, r.phone_number, r.iglesia, r.red, r.event_id, r.registration_date, p.post_title AS event_name
              FROM {$table_name} r
              JOIN {$wpdb->posts} p ON r.event_id = p.ID
              ORDER BY r.registration_date DESC";

    $all_registrations = $wpdb->get_results($query);
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>Aquí se listan todos los correos electrónicos registrados para cada evento.</p>

        <table id="miTabla" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Nombre</th>
                    <th>cedula</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Iglesia</th>
                    <th>Red</th>
                    <th style="width:15%;">Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($all_registrations)) : ?>
                    <tr>
                        <td colspan="5">No hay registros todavía.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($all_registrations as $registration) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo get_edit_post_link($registration->event_id); ?>">
                                    <?php echo esc_html($registration->event_name); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($registration->nombre); ?></td>
                            <td><?php echo esc_html($registration->cedula); ?></td>
                            <td><?php echo esc_html($registration->email); ?></td>
                            <td><?php echo esc_html($registration->phone_number); ?></td>
                            <td><?php echo esc_html($registration->iglesia); ?></td>
                            <td><?php echo esc_html($registration->red); ?></td>
                            <td><?php echo esc_html($registration->registration_date); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}


/**
 * --------------------------------------------------------------------------
 * PARTE C: LÓGICA DE REGISTRO (AJAX) Y CARGA DE SCRIPTS
 * --------------------------------------------------------------------------
 */
// 1. Cargar el archivo JavaScript
function enqueue_event_registration_scripts()
{
    if (is_singular('events')) {
        wp_enqueue_script(
            'event-registration-ajax',
            get_stylesheet_directory_uri() . '/resources/scripts/event-registration.js',
            ['jquery'],
            '1.1', // Versión actualizada
            true
        );
        wp_localize_script('event-registration-ajax', 'event_reg_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('event_registration_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_event_registration_scripts');

// 2. Manejar la petición de registro y guardarla en la BD
function handle_event_registration()
{
    global $wpdb;
    check_ajax_referer('event_registration_nonce', 'nonce');
    $recaptcha_secret_key = '6LePAbwrAAAAAGT4G4s6FngmaTEK3O0UdPqGfOfT'; // ¡IMPORTANTE: Reemplaza esto!
    $recaptcha_token = sanitize_text_field($_POST['recaptcha_response']);

    $verification_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => $recaptcha_secret_key,
            'response' => $recaptcha_token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]
    ]);

    if (is_wp_error($verification_response)) {
        wp_send_json_error(['message' => 'No se pudo conectar con el servicio de verificación.']);
        return;
    }

    $response_data = json_decode(wp_remote_retrieve_body($verification_response));

    // ⚠️ INICIO DE LA MODIFICACIÓN PARA DESARROLLO LOCAL ⚠️

    // Primero, verificamos si la comunicación con Google fue exitosa en general.
    if (!$response_data || !$response_data->success) {
        // Esto es un error grave (ej. clave secreta incorrecta) y debe detener todo siempre.
        wp_send_json_error(['message' => 'Error de comunicación con el servicio reCAPTCHA.']);
        return;
    }

    // Ahora, definimos si estamos en un entorno local.
    // '127.0.0.1' (para IPv4) y '::1' (para IPv6) son las IPs de localhost.
    $is_local_environment = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    // La validación del SCORE solo se aplica si NO estamos en el entorno local.
    if (!$is_local_environment && $response_data->score < 0.5) {
        // Si un usuario real (no local) tiene un score bajo, lo bloqueamos.
        wp_send_json_error(['message' => 'Falló la verificación de humanidad. Intenta de nuevo.']);
        return;
    }
    if (empty($_POST['recaptcha_response'])) {
        wp_send_json_error(['message' => 'El token de verificación no se recibió.']);
        return;
    }
    // NUEVA VALIDACIÓN: Cédula
    if (empty($_POST['cedula'])) {
        wp_send_json_error(['message' => 'Por favor, introduce tu Cédula.']);
        return;
    }
    if (empty($_POST['phone_number'])) {
        wp_send_json_error(['message' => 'El número de teléfono es obligatorio.']);
        return;
    }
    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_send_json_error(['message' => 'Por favor, introduce un correo electrónico válido.']);
        return;
    }
    // NUEVA VALIDACIÓN: Iglesia
    if (empty($_POST['iglesia'])) {
        wp_send_json_error(['message' => 'Por favor, selecciona una iglesia.']);
        return;
    }
    // NUEVA VALIDACIÓN: Red condicional
    if ($_POST['iglesia'] === 'ERJPF' && empty($_POST['red'])) {
        wp_send_json_error(['message' => 'Por favor, selecciona tu red.']);
        return;
    }
    if (empty($_POST['event_id']) || !absint($_POST['event_id'])) {
        wp_send_json_error(['message' => 'ID de evento no válido.']);
        return;
    }

    $table_name = $wpdb->prefix . 'event_registrations';
    $nombre       = sanitize_text_field($_POST['nombre']);
    $cedula       = sanitize_text_field($_POST['cedula']);
    $email        = sanitize_email($_POST['email']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $iglesia      = sanitize_text_field($_POST['iglesia']);
    // El campo 'red' puede no existir si la iglesia no es ERJPF, así que lo comprobamos.
    $red          = isset($_POST['red']) ? sanitize_text_field($_POST['red']) : '';
    $event_id     = absint($_POST['event_id']);

    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE email = %s AND event_id = %d",
        $email,
        $event_id
    ));

    if ($existing) {
        wp_send_json_error(['message' => 'Este correo ya está registrado para el evento.']);
        return;
    }

    // --- INICIO DE INSERCIÓN EN LA BASE DE DATOS ---
    $result = $wpdb->insert($table_name, [
        'event_id'          => $event_id,
        'nombre'            => $nombre,
        'cedula'            => $cedula,
        'email'             => $email,
        'phone_number'      => $phone_number,
        'iglesia'           => $iglesia,
        'red'               => $red,
        'registration_date' => current_time('mysql'),
    ], [
        '%d', // event_id
        '%s', // nombre
        '%s', // cedula
        '%s', // email
        '%s', // phone_number
        '%s', // iglesia
        '%s', // red
        '%s'  // registration_date
    ]);
    // --- FIN DE INSERCIÓN ---

    if ($result) {
        // --- INICIO: CÓDIGO PARA ENVIAR CORREO DE CONFIRMACIÓN ---

        // 1. Obtenemos el título del evento usando su ID.
        $event_title = get_the_title($event_id);
        $event_date = get_field('fecha_del_evento', $event_id);
        $event_lugar = get_field('lugar_de_evento_titulo', $event_id);
        $event_descripcion = get_field('descripcion_del_evento_', $event_id);
        $event_url = get_the_permalink($event_id);
        // 2. Definimos el asunto del correo.
        $subject = '¡Confirmación de registro para ' . $event_title . '!';
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        // 3. Preparamos las variables para la plantilla.
        $logo_url     = get_field('logo_en_negro', 'option'); // Asegúrate de que esta URL es correcta.
        $url_inicio   = esc_url(home_url('/'));
        $titulo_sitio = esc_attr(get_bloginfo('name'));
        $enlace_html  = '<a href="' . $url_inicio . '" title="' . $titulo_sitio . '" style="color: #ffffff; text-decoration: none;">' . $titulo_sitio . '</a>';

        // 4. Creamos el cuerpo del correo usando la plantilla HTML.
        $body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$subject</title>
    </head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Georgia, 'Times New Roman', Times, serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td align="center" style="padding: 40px 0;">
                            <img src="$logo_url" alt="Logo del Sitio" width="120" style="display: block; border: 0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 30px 30px 30px; text-align: center;">
                            <h1 style="color: #333333; margin: 0; font-weight: normal; font-size: 28px;">Hola, $nombre</h1>
                            <p style="color: #555555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                ¡Gracias por registrarte en nuestro evento: <strong>$event_title</strong>! <br>Nos alegra mucho que nos acompañes.
                            </p>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="left" style="background-color: #f9f9f9; padding: 20px; border-left: 4px solid #bda071; text-align: left;">
                                        <p style="margin: 0 0 10px 0; color: #333333; font-size: 16px;"><strong>Lugar:</strong> $event_lugar</p>
                                        <p style="margin: 0 0 10px 0; color: #333333; font-size: 16px;"><strong>Fecha:</strong> $event_date</p>
                                        <p style="margin: 0; color: #555555; font-size: 16px; line-height: 1.5;">$event_descripcion</p>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellspacing="0" cellpadding="0" style="margin: 30px auto;">
                                <tr>
                                    <td align="center" class="button-td" style="border-radius: 25px; background: #1520A6;">
                                        <a href="$event_url" target="_blank" class="button-a" style="font-size: 16px; font-family: Arial, Helvetica, sans-serif; color: #ffffff; text-decoration: none; border-radius: 25px; padding: 12px 25px; border: 1px solid #1520A6; display: inline-block; font-weight: bold;">Ver más detalles</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #555555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                Recuerda que Dios está contigo:
                            </p>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center" style="padding: 20px;">
                                        <p style="color: #555555; font-size: 17px; line-height: 1.7; font-style: italic; margin: 0;">
                                            "¡Levántate y resplandece que tu luz ha llegado!<br>¡La gloria del Señor brilla sobre ti!"<br>
                                            <span style="display: block; margin-top: 10px; font-style: normal; color: #333333;">- Isaías 60:1-2</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#1520A6" style="padding: 25px 30px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                            <p style="color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; text-align: center; margin: 0;">
                                En Su amor y servicio<br>
                                <strong style="display: block; margin-top: 8px;"><br>$enlace_html</strong>
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

        // 5. Enviamos el correo.
        wp_mail($email, $subject, $body, $headers);

        // --- FIN: CÓDIGO PARA ENVIAR CORREO ---

        // Finalmente, enviamos la respuesta de éxito al usuario.
        wp_send_json_success(['message' => '¡Gracias! Te has registrado correctamente. Pronto recibirás un correo de confirmación.']);
    } else {
        wp_send_json_error(['message' => 'Hubo un error al procesar tu registro.']);
    }
}
// Los hooks no cambian
add_action('wp_ajax_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
add_action('wp_ajax_nopriv_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
