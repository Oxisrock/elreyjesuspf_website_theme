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

    // 2. VERIFICACIÓN DE GOOGLE RECAPTCHA V3
    $recaptcha_secret_key = '6LfflFgsAAAAAJJanViKSxJVzrWo33zThlxu5KdO';
    $recaptcha_token = isset($_POST['recaptcha_response']) ? sanitize_text_field($_POST['recaptcha_response']) : '';

    if (empty($recaptcha_token)) {
        wp_die('Error de verificación de seguridad. Por favor, recarga la página e intenta de nuevo.');
    }

    $verification_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => $recaptcha_secret_key,
            'response' => $recaptcha_token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]
    ]);

    if (is_wp_error($verification_response)) {
        wp_die('No se pudo conectar con el servicio de verificación.');
    }

    $response_data = json_decode(wp_remote_retrieve_body($verification_response));

    // Verificar si la comunicación con Google fue exitosa
    if (!$response_data || !$response_data->success) {
        wp_die('Error de comunicación con el servicio reCAPTCHA.');
    }

    // Verificar si estamos en entorno local
    $is_local_environment = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    // La validación del SCORE solo se aplica si NO estamos en entorno local
    if (!$is_local_environment && $response_data->score < 0.5) {
        wp_die('Falló la verificación de humanidad. Intenta de nuevo.');
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
    
    // Estadísticas
    $total_contactos = $wpdb->get_var("SELECT COUNT(*) FROM $nombre_tabla");
    $contactos_hoy = $wpdb->get_var("SELECT COUNT(*) FROM $nombre_tabla WHERE DATE(fecha) = CURDATE()");

    $entradas = $wpdb->get_results("SELECT * FROM $nombre_tabla ORDER BY fecha DESC");
?>
    <div class="wrap siembras-premium-wrap">
        <div class="header-flex">
            <div>
                <h1 class="wp-heading-inline">Gestión de Contactos</h1>
                <p class="subtitle">Monitoreo de mensajes y peticiones recibidas</p>
            </div>
        </div>
        
        <hr class="wp-header-end">

        <!-- Tarjetas de Resumen Premium -->
        <div class="siembras-summary-grid">
            <div class="summary-card-premium total">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-email-alt"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Total Mensajes</span>
                    <span class="card-value"><?php echo $total_contactos ?: 0; ?></span>
                </div>
            </div>
            <div class="summary-card-premium usd">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Recibidos Hoy</span>
                    <span class="card-value"><?php echo $contactos_hoy ?: 0; ?></span>
                </div>
            </div>
        </div>

        <!-- Barra de Herramientas y Filtros Premium -->
        <div class="premium-toolbar">
            <div class="date-filter-group">
                <div class="filter-item">
                    <label>Participante:</label>
                    <input type="text" id="search-participante" class="premium-input" placeholder="Nombre, Correo o Mensaje...">
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
                        <th>Fecha</th>
                        <th>Contacto</th>
                        <th>Asunto</th>
                        <th>Mensaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entradas)) : ?>
                        <?php foreach ($entradas as $entrada) : ?>
                            <tr>
                                <td class="col-id">#<?php echo esc_html($entrada->id); ?></td>
                                <td class="col-date" data-order="<?php echo esc_attr(strtotime($entrada->fecha)); ?>">
                                    <span class="date-main"><?php echo date('d M, Y', strtotime($entrada->fecha)); ?></span>
                                    <span class="date-sub"><?php echo date('h:i A', strtotime($entrada->fecha)); ?></span>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name"><?php echo esc_html($entrada->nombre); ?></span>
                                        <span class="user-email"><?php echo esc_html($entrada->email); ?></span>
                                    </div>
                                </td>
                                <td><span class="premium-tag tag-iglesia"><?php echo esc_html($entrada->asunto); ?></span></td>
                                <td class="col-msg">
                                    <div class="message-preview">
                                        <?php echo wp_trim_words(esc_html($entrada->mensaje), 15, '...'); ?>
                                        <button type="button" class="view-more-btn" data-full-msg="<?php echo esc_attr($entrada->mensaje); ?>">Ver más</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para ver mensaje completo -->
    <div id="messageModal" class="premium-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalle del Mensaje</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p id="fullMessageText"></p>
            </div>
        </div>
    </div>

    <!-- Reutilizamos los estilos premium para consistencia -->
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
        .summary-card-premium.usd .card-icon-wrap { background: #fff7ed; color: #9a3412; }
        .summary-card-premium.total .card-icon-wrap { background: #f0f9ff; color: #075985; }
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
        .user-name { display: block; font-weight: 700; color: var(--text-main); }
        .user-email { display: block; font-size: 12px; color: var(--text-sub); }
        .premium-tag { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; white-space: nowrap; width: fit-content; }
        .tag-iglesia { background: #f0fdf4; color: #166534; }
        .date-main { display: block; font-weight: 600; color: var(--text-main); }
        .date-sub { display: block; font-size: 11px; color: var(--text-sub); margin-top: 2px; }
        .col-msg { max-width: 400px; color: var(--text-sub); font-size: 13px; line-height: 1.5; }
        .view-more-btn { background: none; border: none; color: var(--primary); font-weight: 600; font-size: 12px; cursor: pointer; padding: 0; margin-left: 5px; text-decoration: underline; }

        /* Modal Styles */
        .premium-modal { display: none; position: fixed; z-index: 999999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 0; border: none; border-radius: 20px; width: 500px; max-width: 90%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalIn 0.3s ease-out; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header { padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-radius: 20px 20px 0 0; }
        .modal-header h2 { margin: 0; font-size: 18px; color: var(--text-main); font-weight: 800; }
        .close-modal { color: var(--text-sub); font-size: 28px; font-weight: bold; cursor: pointer; transition: color 0.2s; }
        .close-modal:hover { color: var(--text-main); }
        .modal-body { padding: 30px; line-height: 1.7; color: var(--text-main); font-size: 15px; max-height: 400px; overflow-y: auto; }

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

function mi_contenido_pagina_peticiones()
{
    echo '<div class="wrap"><h2>Peticiones</h2><p>Contenido para el submenú de Peticiones aquí.</p></div>';
}

/**
 * --------------------------------------------------------------------------
 * PARTE D: CARGA DE DATATABLES Y LÓGICA DE CONTACTO
 * --------------------------------------------------------------------------
 */
function contact_entries_admin_scripts($hook)
{
    if ($hook !== 'toplevel_page_entradas-contacto-slug') return;

    // Bundle consolidado: DataTables 2.0.8, Buttons 3.0.2, HTML5 Export, JSZip 3.10.1
    wp_enqueue_style('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');
    wp_enqueue_script('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.js', array('jquery'), '2.0.8', true);

    wp_add_inline_script('siembra-datatables-combined', '
    jQuery(document).ready(function($) {
        if (typeof $.fn.DataTable !== "function") return;

        // Lógica de Filtrado Combinado (Fecha y Texto)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            // 1. Filtro de Fechas
            const min = $("#min-date").val();
            const max = $("#max-date").val();
            const timestamp = parseInt($(settings.aoData[dataIndex].anCells[1]).attr("data-order"));
            
            if (timestamp) {
                const rowDate = new Date(timestamp * 1000);
                rowDate.setHours(0,0,0,0);
                const minDate = min ? new Date(min + "T00:00:00") : null;
                const maxDate = max ? new Date(max + "T23:59:59") : null;
                if (minDate && rowDate < minDate) return false;
                if (maxDate && rowDate > maxDate) return false;
            }

            // 2. Buscador de Participante (Nombre, Correo, Asunto, Mensaje)
            const searchTerm = $("#search-participante").val().toLowerCase().trim();
            if (searchTerm) {
                const nombre = data[2].toLowerCase();
                const email = $(settings.aoData[dataIndex].anCells[2]).find(".user-email").text().toLowerCase();
                const asunto = data[3].toLowerCase();
                const mensaje = $(settings.aoData[dataIndex].anCells[4]).text().toLowerCase();
                
                const matches = nombre.indexOf(searchTerm) !== -1 || 
                                email.indexOf(searchTerm) !== -1 || 
                                asunto.indexOf(searchTerm) !== -1 || 
                                mensaje.indexOf(searchTerm) !== -1;
                
                if (!matches) return false;
            }
            
            return true;
        });

        const table = $("#miTabla").DataTable({
            dom: "<\"dt-top-flex\"Bf>lrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    text: "<span class=\"dashicons dashicons-download\"></span> Descargar Lista Excel",
                    title: "Contactos_" + new Date().toISOString().split("T")[0],
                    className: "dt-button"
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            order: [[1, "desc"]],
            pageLength: 25,
            autoWidth: false,
            responsive: true
        });

        // Eventos para redibujar
        $("#min-date, #max-date, #search-participante").on("keyup change", function() {
            table.draw();
        });

        $("#clear-filters").on("click", function() {
            $("#min-date, #max-date, #search-participante").val("");
            table.draw();
        });

        // Lógica del Modal para mensajes largos
        $(".view-more-btn").on("click", function() {
            const msg = $(this).attr("data-full-msg");
            $("#fullMessageText").text(msg);
            $("#messageModal").fadeIn(200);
        });

        $(".close-modal, .premium-modal").on("click", function(e) {
            if (e.target !== this && !$(e.target).hasClass("close-modal")) return;
            $("#messageModal").fadeOut(200);
        });
    });
');
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\contact_entries_admin_scripts');
