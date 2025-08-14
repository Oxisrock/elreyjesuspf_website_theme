<?php
function crear_tabla_contactos_personalizada() {
    global $wpdb;
    // El prefijo de la tabla de WordPress (ej. 'wp_') seguido del nombre de nuestra tabla.
    $nombre_tabla = $wpdb->prefix . 'contactos_entradas';
    $charset_collate = $wpdb->get_charset_collate();

    // Si la tabla no existe, la creamos.
    if($wpdb->get_var("SHOW TABLES LIKE '$nombre_tabla'") != $nombre_tabla) {
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

// Enganchamos nuestra función al 'action' que definimos en el formulario
// 'admin_post_nopriv_' es para usuarios no logueados
add_action('admin_post_nopriv_procesar_formulario_contacto', __NAMESPACE__ . '\\mi_procesador_de_formularios');
// 'admin_post_' es para usuarios logueados
add_action('admin_post_procesar_formulario_contacto', __NAMESPACE__ . '\\mi_procesador_de_formularios');

function mi_procesador_de_formularios() {
    global $wpdb;
    $nombre_tabla = $wpdb->prefix . 'contactos_entradas';

    // 1. Verificar el Nonce de seguridad
    if ( !isset($_POST['mi_nonce']) || !wp_verify_nonce($_POST['mi_nonce'], 'mi_form_contacto_nonce') ) {
        die('¡Falló la verificación de seguridad!');
    }

    // 2. Limpiar y sanitizar los datos recibidos del formulario
    $nombre  = sanitize_text_field($_POST['nombre']);
    $email   = sanitize_email($_POST['email']);
    $asunto  = sanitize_text_field($_POST['asunto']);
    $mensaje = sanitize_textarea_field($_POST['mensaje']);

    // 3. Insertar los datos en nuestra tabla personalizada
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

    // 4. Enviar correos

    // --- INICIO DE CAMBIOS Y CORRECCIONES ---

    // 4.1. Preparamos TODAS las variables que necesitamos para los correos
    
    // Para el correo del Administrador
    $admin_email = get_option('admin_email');
    $admin_asunto = "Nuevo mensaje de contacto de: $nombre";
    $admin_cuerpo = "Has recibido un nuevo mensaje a través del formulario de contacto:\n\n";
    $admin_cuerpo .= "Nombre: $nombre\n";
    $admin_cuerpo .= "Correo: $email\n";
    $admin_cuerpo .= "Asunto: $asunto\n";
    $admin_cuerpo .= "Mensaje:\n$mensaje\n";
    
    // Para el correo del Usuario (aquí integramos tu código)
    // Obtenemos la URL del logo usando get_field() porque estamos en PHP
    // Nota: Debes tener el plugin Advanced Custom Fields activo.
    $logo_url = get_field('logo_en_negro', 'option');

    // Aquí está el código que querías integrar, preparado para el correo
    $url_inicio = esc_url(home_url('/'));
    $titulo_sitio = esc_attr(get_bloginfo('name'));
    
    // Construimos el enlace con estilo para que se vea bien en el correo
    $enlace_html = '<a href="' . $url_inicio . '" title="' . $titulo_sitio . '" style="color: #ffffff; text-decoration: none;">' . $titulo_sitio . '</a>';

    // 4.2. Construimos el cuerpo del correo del usuario de forma limpia
    
    // Corregimos el Content-Type a UTF-8
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $usuario_asunto = "¡Hemos recibido tu mensaje!";

    // Usamos la sintaxis HEREDOC (<<<HTML) para escribir el HTML sin problemas con las comillas
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

    // --- FIN DE CAMBIOS Y CORRECCIONES ---

    // 4.3. Enviamos los correos
    wp_mail($email, $usuario_asunto, $usuario_cuerpo, $headers);

    // 5. Redirigir al usuario
    $url_contacto = home_url('/contacto'); // <-- ¡Asegúrate de que esta es la URL de tu página de contacto!
    $url_con_mensaje = add_query_arg('enviado', 'true', $url_contacto);
    wp_redirect($url_con_mensaje);
    exit;
}

// Añadir una nueva página al menú de administración
add_action('admin_menu', __NAMESPACE__ . '\\mi_menu_de_entradas_de_contacto');

function mi_menu_de_entradas_de_contacto() {
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
function mi_contenido_pagina_entradas() {
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

function mi_contenido_pagina_peticiones() {
    echo '<div class="wrap"><h2>Peticiones</h2><p>Contenido para el submenú de Peticiones aquí.</p></div>';
}