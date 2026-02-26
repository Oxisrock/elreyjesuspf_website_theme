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

    // Calcular estadísticas
    $total_registros = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $registros_24h = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");

    // Obtener lista de eventos únicos para el filtro
    $eventos_query = "SELECT DISTINCT r.event_id, p.post_title AS event_name 
                      FROM {$table_name} r 
                      JOIN {$wpdb->posts} p ON r.event_id = p.ID 
                      ORDER BY event_name ASC";
    $eventos_list = $wpdb->get_results($eventos_query);

    $query = "SELECT r.id, r.nombre, r.cedula, r.email, r.phone_number, r.iglesia, r.red, r.event_id, r.registration_date, p.post_title AS event_name
              FROM {$table_name} r
              JOIN {$wpdb->posts} p ON r.event_id = p.ID
              ORDER BY r.registration_date DESC";

    $all_registrations = $wpdb->get_results($query);
?>
    <div class="wrap siembras-premium-wrap">
        <div class="header-flex">
            <div>
                <h1 class="wp-heading-inline">Registros de Eventos</h1>
                <p class="subtitle">Gestión y monitoreo de asistentes registrados</p>
            </div>
        </div>
        
        <hr class="wp-header-end">

        <!-- Tarjetas de Resumen Premium -->
        <div class="siembras-summary-grid">
            <div class="summary-card-premium total">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Total Registrados</span>
                    <span class="card-value"><?php echo $total_registros ?: 0; ?></span>
                </div>
            </div>
            <div class="summary-card-premium usd">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Últimas 24 Horas</span>
                    <span class="card-value"><?php echo $registros_24h ?: 0; ?></span>
                </div>
            </div>
        </div>

        <!-- Barra de Herramientas y Filtros Premium -->
        <div class="premium-toolbar">
            <div class="date-filter-group">
                <div class="filter-item">
                    <label>Evento:</label>
                    <select id="filter-evento" class="premium-input">
                        <option value="">Todos los Eventos</option>
                        <?php foreach($eventos_list as $ev): ?>
                            <option value="<?php echo esc_attr($ev->event_name); ?>"><?php echo esc_html($ev->event_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Desde:</label>
                    <input type="date" id="min-date" class="premium-input">
                </div>
                <div class="filter-item">
                    <label>Hasta:</label>
                    <input type="date" id="max-date" class="premium-input">
                </div>
                
                <button type="button" id="clear-filters" class="button button-link">Limpiar Todo</button>
            </div>
        </div>

        <div class="premium-table-container">
            <table id="miTabla" class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Evento</th>
                        <th>Participante</th>
                        <th>Identificación</th>
                        <th>Contacto</th>
                        <th>Organización</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($all_registrations)) : ?>
                        <?php foreach ($all_registrations as $registration) : ?>
                            <tr>
                                <td class="col-id">#<?php echo esc_html($registration->id); ?></td>
                                <td>
                                    <span class="event-badge">
                                        <a href="<?php echo get_edit_post_link($registration->event_id); ?>">
                                            <?php echo esc_html($registration->event_name); ?>
                                        </a>
                                    </span>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name"><?php echo esc_html($registration->nombre); ?></span>
                                        <span class="user-email"><?php echo esc_html($registration->email); ?></span>
                                    </div>
                                </td>
                                <td><code class="premium-ref"><?php echo esc_html($registration->cedula ?: 'N/A'); ?></code></td>
                                <td class="col-tel"><?php echo esc_html($registration->phone_number); ?></td>
                                <td>
                                    <div class="org-info">
                                        <span class="premium-tag tag-iglesia"><?php echo esc_html($registration->iglesia); ?></span>
                                        <?php if($registration->red): ?>
                                            <span class="premium-tag tag-red"><?php echo esc_html($registration->red); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="col-date" data-order="<?php echo esc_attr(strtotime($registration->registration_date)); ?>">
                                    <span class="date-main"><?php echo date('d M, Y', strtotime($registration->registration_date)); ?></span>
                                    <span class="date-sub"><?php echo date('h:i A', strtotime($registration->registration_date)); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reutilizamos los estilos premium de Siembras para consistencia -->
    <style>
        :root {
            --primary: #2563eb;
            --success: #10b981;
            --bg-body: #f1f5f9;
            --text-main: #1e293b;
            --text-sub: #64748b;
        }

        .siembras-premium-wrap { margin: 20px 20px 20px 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
        .header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; }
        .subtitle { color: var(--text-sub); margin: 5px 0 0; font-size: 14px; }

        /* Summary Cards */
        .siembras-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card-premium { background: #fff; border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid rgba(226, 232, 240, 0.8); transition: transform 0.2s; }
        .card-icon-wrap { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: var(--bg-body); color: var(--primary); }
        .card-icon-wrap .dashicons { font-size: 28px; width: 28px; height: 28px; }
        .summary-card-premium.usd .card-icon-wrap { background: #fef2f2; color: #991b1b; }
        .summary-card-premium.total .card-icon-wrap { background: #eff6ff; color: #1e40af; }
        .card-label { display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .card-value { font-size: 26px; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; }

        /* Toolbar & Filters */
        .premium-toolbar { background: #fff; padding: 20px; border-radius: 16px 16px 0 0; border: 1px solid #e2e8f0; border-bottom: none; display: flex; justify-content: space-between; align-items: center; }
        .date-filter-group { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .filter-item { display: flex; align-items: center; gap: 10px; }
        .filter-item label { font-weight: 600; color: var(--text-sub); font-size: 13px; }
        .premium-input { border: 1px solid #cbd5e1 !important; border-radius: 8px !important; padding: 6px 12px !important; color: var(--text-main) !important; font-size: 13px !important; }

        /* Table Design */
        .premium-table-container { background: #fff; border-radius: 0 0 16px 16px; border: 1px solid #e2e8f0; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        #miTabla { border: none !important; width: 100% !important; border-collapse: collapse !important; }
        #miTabla thead th { background: #f8fafc !important; color: #64748b !important; font-weight: 700 !important; text-transform: uppercase !important; font-size: 11px !important; letter-spacing: 0.05em !important; padding: 16px 20px !important; border-bottom: 2px solid #e2e8f0 !important; }
        #miTabla tbody td { padding: 14px 20px !important; vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; color: var(--text-main); font-size: 14px; }
        
        .col-id { color: var(--text-sub); font-weight: 600; font-size: 12px; }
        .event-badge a { font-weight: 700; color: var(--primary); text-decoration: none; }
        .user-name { display: block; font-weight: 700; color: var(--text-main); }
        .user-email { display: block; font-size: 12px; color: var(--text-sub); }
        .premium-ref { background: #f8fafc; padding: 4px 8px; border-radius: 6px; color: var(--text-main); font-family: monospace; font-size: 12px; border: 1px solid #e2e8f0; }
        .col-tel { font-family: monospace; font-weight: 600; color: var(--text-sub); font-size: 12px; }
        .org-info { display: flex; flex-direction: column; gap: 4px; }
        .premium-tag { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; white-space: nowrap; width: fit-content; }
        .tag-iglesia { background: #eff6ff; color: #1e40af; }
        .tag-red { background: #fefce8; color: #854d0e; }
        .date-main { display: block; font-weight: 600; color: var(--text-main); }
        .date-sub { display: block; font-size: 11px; color: var(--text-sub); margin-top: 2px; }

        /* DataTables Controls Reused Styles */
        .dt-top-flex { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; }
        .dt-button { background: var(--primary) !important; color: #fff !important; border: none !important; border-radius: 10px !important; padding: 10px 20px !important; font-weight: 700 !important; font-size: 13px !important; transition: all 0.2s !important; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1) !important; cursor: pointer !important; }
        .dt-button:hover { background: #1d4ed8 !important; transform: translateY(-1px) !important; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2) !important; }
        .dataTables_filter input { border: 1px solid #e2e8f0 !important; border-radius: 10px !important; padding: 8px 15px !important; width: 280px !important; margin-left: 15px !important; background: #f8fafc !important; }
        .dataTables_paginate { padding: 15px 20px !important; }
        .paginate_button { border-radius: 10px !important; border: 1px solid #e2e8f0 !important; margin: 0 2px !important; font-weight: 700 !important; font-size: 13px !important; padding: 5px 12px !important; }
        .paginate_button.current { background: var(--primary) !important; color: #fff !important; border-color: var(--primary) !important; }
    </style>
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
    $recaptcha_secret_key = '6LfflFgsAAAAAJJanViKSxJVzrWo33zThlxu5KdO'; // ¡IMPORTANTE: Reemplaza esto!
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
/**
 * --------------------------------------------------------------------------
 * PARTE D: CARGA DE DATATABLES PARA LA TABLA DE ADMINISTRACIÓN
 * --------------------------------------------------------------------------
 */
function event_registrations_admin_scripts($hook)
{
    if ($hook !== 'events_page_event-registrations') return;

    // Bundle consolidado: DataTables 2.0.8, Buttons 3.0.2, HTML5 Export, JSZip 3.10.1
    wp_enqueue_style('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');
    wp_enqueue_script('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.js', array('jquery'), '2.0.8', true);

    wp_add_inline_script('siembra-datatables-combined', '
    jQuery(document).ready(function($) {
        if (typeof $.fn.DataTable !== "function") return;

        // Lógica de Filtrado Combinado (Fecha y Evento)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            // 1. Filtro de Fechas
            const min = $("#min-date").val();
            const max = $("#max-date").val();
            const timestamp = parseInt($(settings.aoData[dataIndex].anCells[6]).attr("data-order"));
            
            if (timestamp) {
                const rowDate = new Date(timestamp * 1000);
                rowDate.setHours(0,0,0,0);
                const minDate = min ? new Date(min + "T00:00:00") : null;
                const maxDate = max ? new Date(max + "T23:59:59") : null;
                if (minDate && rowDate < minDate) return false;
                if (maxDate && rowDate > maxDate) return false;
            }

            // 2. Filtro de Evento (Columna 1)
            const selEvento = $("#filter-evento").val();
            const rowEvento = $(settings.aoData[dataIndex].anCells[1]).text().trim();
            if (selEvento && rowEvento !== selEvento) return false;
            
            return true;
        });

        const table = $("#miTabla").DataTable({
            dom: "<\"dt-top-flex\"Bf>lrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    text: "<span class=\"dashicons dashicons-download\"></span> Descargar Lista Excel",
                    title: "Registros_Eventos_" + new Date().toISOString().split("T")[0],
                    className: "dt-button"
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            order: [[6, "desc"]],
            pageLength: 25,
            autoWidth: false,
            responsive: true
        });

        // Eventos para redibujar
        $("#min-date, #max-date, #filter-evento").on("change", function() {
            table.draw();
        });

        $("#clear-filters").on("click", function() {
            $("#min-date, #max-date, #filter-evento").val("");
            table.draw();
        });
    });
');
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\event_registrations_admin_scripts');

// Los hooks para el registro AJAX
add_action('wp_ajax_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
add_action('wp_ajax_nopriv_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
