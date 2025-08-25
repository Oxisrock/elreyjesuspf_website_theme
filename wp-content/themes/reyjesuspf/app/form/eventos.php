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
    if (empty($_POST['nombre'])) {
        wp_send_json_error(['message' => 'Por favor, introduce tu Nombre Completo.']);
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
        wp_send_json_success(['message' => '¡Gracias! Te has registrado correctamente.']);
    } else {
        wp_send_json_error(['message' => 'Hubo un error al procesar tu registro.']);
    }
}
add_action('wp_ajax_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
add_action('wp_ajax_nopriv_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
