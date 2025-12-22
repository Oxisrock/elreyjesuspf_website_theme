<?php
function crear_tabla_contactos_personalizada()
{
    global $wpdb;
    // El prefijo de la tabla de WordPress (ej. 'wp_') seguido del nombre de nuestra tabla.
    $nombre_tabla = $wpdb->prefix . 'contactos_entradas';
    $charset_collate = $wpdb->get_charset_collate();

    // Si la tabla no existe, la creamos.
    if ($wpdb->get_var("SHOW TABLES LIKE '$nombre_tabla'") != $nombre_tabla) {
        $sql = "CREATE TABLE $nombre_tabla (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            fecha datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            nombre varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            asunto varchar(255) NOT NULL,
            mensaje text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Ejecutamos la función. Recuerda comentarla o borrarla después del primer uso.
crear_tabla_contactos_personalizada();

// Enganchamos la única función correcta para procesar el formulario.
add_action('admin_post_nopriv_procesar_formulario_contacto', __NAMESPACE__ . '\\handle_contact_form_submission');
add_action('admin_post_procesar_formulario_contacto', __NAMESPACE__ . '\\handle_contact_form_submission');

function handle_contact_form_submission()
{
    global $wpdb;
    $nombre_tabla = $wpdb->prefix . 'contactos_entradas';
    $contact_page_url = home_url('/contacto'); // <-- Ajusta el slug si es necesario.

    // 1. Verificamos el nonce de seguridad.
    if (!isset($_POST['mi_nonce']) || !wp_verify_nonce($_POST['mi_nonce'], 'mi_form_contacto_nonce')) {
        wp_die('Error de seguridad. Inténtalo de nuevo.');
    }


    // Si el código llega aquí, el reCAPTCHA es válido (ya sea por buen score o por estar en local).

    // ⚠️ FIN DE LA MODIFICACIÓN ⚠️

    // 3. Saneamos los datos del formulario.
    $nombre = sanitize_text_field($_POST['nombre']);
    $email = sanitize_email($_POST['email']);
    $asunto = sanitize_text_field($_POST['asunto']); // Usamos $asunto para consistencia
    $mensaje = sanitize_textarea_field($_POST['mensaje']);

    // 4. INSERTAR LOS DATOS EN LA BASE DE DATOS
    $wpdb->insert(
        $nombre_tabla,
        array(
            'fecha'   => current_time('mysql'),
            'nombre'  => $nombre,
            'email'   => $email,
            'asunto'  => $asunto,
            'mensaje' => $mensaje,
        )
    );

    // =======================================================================
    // 5. ENVIAR CORREOS (AQUÍ INTEGRAMOS TU CÓDIGO AVANZADO)
    // =======================================================================

    // --- CORREO PARA EL ADMINISTRADOR ---
    $admin_email = get_option('admin_email');
    $admin_asunto = "Nuevo mensaje de contacto de: $nombre";
    $admin_cuerpo = "Has recibido un nuevo mensaje a través del formulario de contacto:\n\n";
    $admin_cuerpo .= "Nombre: $nombre\n";
    $admin_cuerpo .= "Correo: $email\n";
    $admin_cuerpo .= "Asunto: $asunto\n";
    $admin_cuerpo .= "Mensaje:\n$mensaje\n";
    $admin_headers = 'From: ' . $nombre . ' <' . $email . '>';

    // Se envía el correo al administrador
    wp_mail($admin_email, $admin_asunto, $admin_cuerpo, $admin_headers);

    // --- CORREO DE CONFIRMACIÓN PARA EL USUARIO (HTML) ---
    $logo_url = get_field('logo_en_negro', 'option'); // Asegúrate de tener ACF activo
    $url_inicio = esc_url(home_url('/'));
    $titulo_sitio = esc_attr(get_bloginfo('name'));
    $enlace_html = '<a href="' . $url_inicio . '" title="' . $titulo_sitio . '" style="color: #ffffff; text-decoration: none;">' . $titulo_sitio . '</a>';

    $usuario_headers = array('Content-Type: text/html; charset=UTF-8');
    $usuario_asunto = "¡Hemos recibido tu mensaje!";

    // Usamos la sintaxis HEREDOC para el cuerpo del correo del usuario
    $usuario_cuerpo = <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head><title>$usuario_asunto</title></head>
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
                                <h1 style="color: #333; margin: 0; font-weight: normal;">Hola, $nombre</h1>
                                <p style="color: #555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                    Gracias por tu confianza al contactarnos. Hemos recibido tu mensaje y lo valoramos profundamente.
                                </p>
                                <p style="color: #555; font-size: 18px; line-height: 1.6; margin: 20px 0;">
                                    Mientras atendemos tu solicitud, queremos compartir contigo una promesa para recordarte que Dios está contigo:
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

    // Se envía el correo al usuario
    wp_mail($email, $usuario_asunto, $usuario_cuerpo, $usuario_headers);

    // =======================================================================
    // 6. REDIRIGIR AL USUARIO A LA PÁGINA DE ÉXITO
    // =======================================================================
    wp_safe_redirect(add_query_arg('enviado', 'true', $contact_page_url));
    exit;
}

// Añadir una nueva página al menú de administración
add_action('admin_menu', __NAMESPACE__ . '\\mi_menu_de_entradas_de_contacto');

function mi_menu_de_entradas_de_contacto()
{
    add_menu_page(
        'Entradas de Contacto',      // Título de la página
        'Contacto',         // Título del menú
        'manage_options',            // Capacidad requerida para verla
        'entradas-contacto-slug',    // Slug único para la página
        'mi_contenido_pagina_entradas', // Función que renderiza el contenido
        'dashicons-email-alt',       // Ícono del menú
        25                           // Posición en el menú
    );
    // 2. Submenú para Boletines
    add_submenu_page(
        'entradas-contacto-slug',               // Slug del menú padre
        'Boletines',                            // Título de la página del submenú
        'Boletines',                            // Título del submenú
        'manage_options',                       // Capacidad requerida
        'boletines-slug',                       // Slug único para este submenú
        'mi_contenido_pagina_boletines'         // Función que renderiza el contenido
    );

    // 3. Submenú para Peticiones
    /*add_submenu_page(
        'entradas-contacto-slug',               // Slug del menú padre
        'Peticiones',                           // Título de la página del submenú
        'Peticiones',                           // Título del submenú
        'manage_options',                       // Capacidad requerida
        'peticiones-slug',                      // Slug único para este submenú
        'mi_contenido_pagina_peticiones'        // Función que renderiza el contenido
    );*/
}


// La función que muestra el contenido de la página (la tabla)
function mi_contenido_pagina_entradas()
{
    global $wpdb;
    $nombre_tabla = $wpdb->prefix . 'contactos_entradas';
    $entradas = $wpdb->get_results("SELECT * FROM $nombre_tabla ORDER BY fecha DESC");
?>
    <div class="wrap">
        <h1>Peticiones</h1>
        <table id="miTabla" class="wp-list-table widefat fixed striped" style="width:100%">
            <thead>
                <tr>
                    <th style="width:150px;">Fecha</th>
                    <th style="width:150px;">Nombre</th>
                    <th style="width:200px;">Correo</th>
                    <th>Asunto</th>
                    <th>Mensaje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($entradas)) : ?>
                    <tr>
                        <td colspan="5">No hay entradas todavía.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($entradas as $entrada) : ?>
                        <tr>
                            <td><?php echo esc_html($entrada->fecha); ?></td>
                            <td><?php echo esc_html($entrada->nombre); ?></td>
                            <td><?php echo esc_html($entrada->email); ?></td>
                            <td><?php echo esc_html($entrada->asunto); ?></td>
                            <td><?php echo esc_html($entrada->mensaje); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}

function mi_contenido_pagina_peticiones()
{
    echo '<div class="wrap"><h2>Peticiones</h2><p>Contenido para el submenú de Peticiones aquí.</p></div>';
}
