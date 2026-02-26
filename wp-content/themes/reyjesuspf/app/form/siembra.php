<?php

// Función para crear la tabla de siembras al activar el tema
function crear_tabla_siembras()
{
    global $wpdb;
    $tabla_siembras = $wpdb->prefix . 'siembras';

    $charset_collate = $wpdb->get_charset_collate();
    if ($wpdb->get_var("SHOW TABLES LIKE '$tabla_siembras'") != $tabla_siembras) {
        $sql = "CREATE TABLE $tabla_siembras (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            dia_pago datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            tipo_siembra varchar(50) DEFAULT '',
            metodo_de_pago varchar(50) NOT NULL,
            monto decimal(10, 2) NOT NULL,
            divisa varchar(10) DEFAULT 'USD',
            referencia varchar(100) DEFAULT '',
            nombre_completo varchar(255) DEFAULT '',
            telefono varchar(50) DEFAULT '',
            correo varchar(255) DEFAULT '',
            mensaje text DEFAULT '',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    } else {
        // Asegurarse de que la columna 'divisa' exista si la tabla ya fue creada
        $row = $wpdb->get_row("SELECT * FROM $tabla_siembras LIMIT 1");
        if (!isset($row->divisa)) {
            $wpdb->query("ALTER TABLE $tabla_siembras ADD divisa varchar(10) DEFAULT 'USD' AFTER monto");
        }
    }
}

// Crear tabla solo si no existe
crear_tabla_siembras();

// Función para procesar el formulario y guardar los datos
function procesar_formulario_siembra()
{
    // 1. Verificación de reCAPTCHA o Nonce
    $is_logged_in = is_user_logged_in();
    $nonce_valid = (isset($_POST['mi_nonce']) && wp_verify_nonce($_POST['mi_nonce'], 'mi_form_siembra_nonce'));
    
    // Si no es un nonce válido, requerimos reCAPTCHA (especialmente para invitados donde el nonce suele fallar por caché)
    if (!$nonce_valid) {
        $recaptcha_secret_key = '6LfflFgsAAAAAJJanViKSxJVzrWo33zThlxu5KdO';
        $recaptcha_token = isset($_POST['recaptcha_response']) ? sanitize_text_field($_POST['recaptcha_response']) : '';

        if (empty($recaptcha_token)) {
            wp_die('Error de seguridad. Por favor, intenta de nuevo desde la página.');
        }

        $verification_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $recaptcha_secret_key,
                'response' => $recaptcha_token,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]
        ]);

        if (is_wp_error($verification_response)) {
            wp_die('No se pudo conectar con el servicio de verificación de seguridad.');
        }

        $response_data = json_decode(wp_remote_retrieve_body($verification_response));

        if (!$response_data || !$response_data->success || $response_data->score < 0.5) {
            // Permitir falla de score en local para desarrollo
            $is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
            if (!$is_local) {
                wp_die('Error de verificación de seguridad (reCAPTCHA failed).');
            }
        }
    }

    // Verificar que el formulario fue enviado por POST
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return;
    }

    // URL de retorno
    $siembra_page_url = wp_get_referer() ? wp_get_referer() : home_url();

    global $wpdb;
    $tabla_siembras = $wpdb->prefix . 'siembras';

    // Obtener y sanitizar los datos del formulario
    $tipo_siembra = isset($_POST['tipo_siembra']) ? sanitize_text_field($_POST['tipo_siembra']) : '';
    $metodo_de_pago = isset($_POST['metodo_de_pago']) ? sanitize_text_field($_POST['metodo_de_pago']) : '';
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $divisa = isset($_POST['divisa']) ? sanitize_text_field($_POST['divisa']) : 'USD';
    $referencia = isset($_POST['referencia']) ? sanitize_text_field($_POST['referencia']) : '';
    $nombre = isset($_POST['nombre']) ? sanitize_text_field($_POST['nombre']) : '';
    $telefono = isset($_POST['telefono']) ? sanitize_text_field($_POST['telefono']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $mensaje = isset($_POST['mensaje']) ? sanitize_textarea_field($_POST['mensaje']) : '';

    // Validar campos requeridos
    if (empty($tipo_siembra) || empty($metodo_de_pago) || empty($monto) || empty($referencia) || empty($nombre) || empty($telefono) || empty($email)) {
        wp_redirect(add_query_arg('enviado', 'false', $siembra_page_url));
        exit;
    }

    // Insertar datos en la base de datos
    $resultado = $wpdb->insert(
        $tabla_siembras,
        [
            'tipo_siembra' => $tipo_siembra,
            'metodo_de_pago' => $metodo_de_pago,
            'monto' => $monto,
            'divisa' => $divisa,
            'referencia' => $referencia,
            'nombre_completo' => $nombre,
            'telefono' => $telefono,
            'correo' => $email,
            'mensaje' => $mensaje,
        ],
        [
            '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s'
        ]
    );

    if ($resultado !== false) {
        // ENVIAR CORREO DE CONFIRMACIÓN
        $logo_url = get_field('logo_en_negro', 'option');
        if (!$logo_url) {
            $logo_url = get_field('logo', 'option');
        }
        $url_inicio = esc_url(home_url('/'));
        $titulo_sitio = esc_attr(get_bloginfo('name'));
        $enlace_html = '<a href="' . $url_inicio . '" title="' . $titulo_sitio . '" style="color: #ffffff; text-decoration: none;">' . $titulo_sitio . '</a>';

        $usuario_headers = array('Content-Type: text/html; charset=UTF-8');
        $usuario_asunto = "¡Gracias por tu Siembra!";

        $simbolo_monedad = ($divisa === 'BS') ? 'Bs' : '$';

        $body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><title>$usuario_asunto</title></head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Georgia', serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td align="center" style="padding: 40px 0; background-color: #ffffff;">
                            <img src="$logo_url" alt="Logo de la Iglesia" width="100" style="display: block;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 40px 40px 40px; text-align: center;">
                            <h1 style="color: #1e293b; margin: 0; font-size: 24px; font-weight: bold;">¡Gracias, $nombre!</h1>
                            <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin: 16px 0;">
                                Hemos recibido el registro de tu siembra correctamente. Tu generosidad permite que sigamos expandiendo el Reino de Dios.
                            </p>

                            <!-- Resumen de Siembra -->
                            <div style="background-color: #f1f5f9; border-radius: 12px; padding: 24px; margin: 32px 0; text-align: left;">
                                <h2 style="color: #334155; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 16px 0;">Resumen de tu Siembra</h2>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px;">Propósito:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: bold; text-align: right; text-transform: capitalize;">$tipo_siembra</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px;">Monto:</td>
                                        <td style="padding: 8px 0; color: #2563eb; font-size: 18px; font-weight: bold; text-align: right;">$simbolo_monedad $monto</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px;">Método:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: bold; text-align: right;">$metodo_de_pago</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px;">Referencia:</td>
                                        <td style="padding: 8px 0; color: #1e293b; font-size: 14px; font-weight: bold; text-align: right; font-family: monospace;">$referencia</td>
                                    </tr>
                                </table>
                            </div>

                            <p style="color: #64748b; font-size: 16px; line-height: 1.6; font-style: italic; margin-top: 32px;">
                                "Cada uno dé como propuso en su corazón: no con tristeza, ni por necesidad, porque Dios ama al dador alegre."<br>
                                <span style="display: block; margin-top: 8px; font-style: normal; font-weight: bold; color: #334155;">- 2 Corintios 9:7</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#1520A6" style="padding: 32px; text-align: center;">
                            <p style="color: #ffffff; font-family: sans-serif; font-size: 14px; margin: 0;">
                                Con amor,<br>
                                <strong style="display: block; margin-top: 8px;">$enlace_html</strong>
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

        // Enviar el correo al usuario
        wp_mail($email, $usuario_asunto, $body, $usuario_headers);

        // Éxito
        wp_redirect(add_query_arg('enviado', 'true', $siembra_page_url));
        exit;
    } else {
        // Error en la base de datos
        wp_redirect(add_query_arg('enviado', 'false', $siembra_page_url));
        exit;
    }
}

// Registrar los hooks
add_action('admin_post_nopriv_procesar_formulario_siembra', 'procesar_formulario_siembra');
add_action('admin_post_procesar_formulario_siembra', 'procesar_formulario_siembra');

// Añade un submenú en el panel de administración
function anadir_menu_siembras()
{
    add_menu_page(
        'Registro de Siembras',
        'Siembras',
        'manage_options',
        'siembras',
        'mostrar_siembras_page',
        'dashicons-money-alt',
        6
    );
}
add_action('admin_menu', 'anadir_menu_siembras');

// Función para mostrar la tabla con los datos de las siembras
function mostrar_siembras_page()
{
    global $wpdb;
    $tabla_siembras = $wpdb->prefix . 'siembras';
    $siembras = $wpdb->get_results("SELECT * FROM $tabla_siembras ORDER BY dia_pago DESC");

    // Calcular totales
    $total_usd = $wpdb->get_var("SELECT SUM(monto) FROM $tabla_siembras WHERE divisa = 'USD'");
    $total_bs = $wpdb->get_var("SELECT SUM(monto) FROM $tabla_siembras WHERE divisa = 'BS'");
    $total_siembras = count($siembras);

    // Obtener valores únicos para los filtros
    $propositos = $wpdb->get_col("SELECT DISTINCT tipo_siembra FROM $tabla_siembras WHERE tipo_siembra != '' ORDER BY tipo_siembra ASC");
    $metodos = $wpdb->get_col("SELECT DISTINCT metodo_de_pago FROM $tabla_siembras WHERE metodo_de_pago != '' ORDER BY metodo_de_pago ASC");
?>
    <div class="wrap siembras-premium-wrap">
        <div class="header-flex">
            <div>
                <h1 class="wp-heading-inline">Gestión de Siembras</h1>
                <p class="subtitle">Monitoreo y administración de contribuciones en tiempo real</p>
            </div>
        </div>
        
        <hr class="wp-header-end">

        <!-- Tarjetas de Resumen Premium -->
        <div class="siembras-summary-grid">
            <div class="summary-card-premium usd">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Total Recaudado (USD)</span>
                    <span class="card-value">$<?php echo number_format($total_usd ?: 0, 2); ?></span>
                </div>
            </div>
            <div class="summary-card-premium ves">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-chart-area"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Total Recaudado (VES)</span>
                    <span class="card-value">Bs <?php echo number_format($total_bs ?: 0, 2); ?></span>
                </div>
            </div>
            <div class="summary-card-premium total">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Siembras Registradas</span>
                    <span class="card-value"><?php echo $total_siembras; ?></span>
                </div>
            </div>
        </div>

        <!-- Barra de Herramientas y Filtros Premium -->
        <div class="premium-toolbar">
            <div class="date-filter-group">
                <div class="filter-item">
                    <label>Desde:</label>
                    <input type="date" id="min-date" class="premium-input">
                </div>
                <div class="filter-item">
                    <label>Hasta:</label>
                    <input type="date" id="max-date" class="premium-input">
                </div>
                
                <div class="filter-item">
                    <label>Propósito:</label>
                    <select id="filter-proposito" class="premium-input">
                        <option value="">Todos</option>
                        <?php foreach($propositos as $p): ?>
                            <option value="<?php echo esc_attr($p); ?>"><?php echo esc_html($p); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Método:</label>
                    <select id="filter-metodo" class="premium-input">
                        <option value="">Todos</option>
                        <?php foreach($metodos as $m): ?>
                            <option value="<?php echo esc_attr($m); ?>"><?php echo esc_html($m); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" id="clear-filters" class="button button-link">Limpiar Todo</button>
            </div>
        </div>

        <div class="premium-table-container">
            <table id="miTabla" class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha y Hora</th>
                        <th>Propósito</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Referencia</th>
                        <th>Sembrador</th>
                        <th>Contacto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (! empty($siembras)) {
                        foreach ($siembras as $siembra) {
                            $simbolo = (isset($siembra->divisa) && $siembra->divisa === 'BS') ? 'Bs' : '$';
                            // Sanitizar tipo para clase CSS (eliminar tildes)
                            $tipo_low = strtr(mb_strtolower($siembra->tipo_siembra, 'UTF-8'), 'áéíóúñ', 'aeioun');
                            $tipo_class = str_replace([' ', '_'], '-', $tipo_low);
                            echo '<tr>';
                            echo '<td class="col-id">#' . esc_html($siembra->id) . '</td>';
                            echo '<td class="col-date" data-order="' . esc_attr(strtotime($siembra->dia_pago)) . '">
                                    <span class="date-main">' . date('d M, Y', strtotime($siembra->dia_pago)) . '</span>
                                    <span class="date-sub">' . date('h:i A', strtotime($siembra->dia_pago)) . '</span>
                                  </td>';
                            echo '<td><span class="premium-tag tag-' . $tipo_class . '">' . esc_html($siembra->tipo_siembra) . '</span></td>';
                            echo '<td><span class="payment-method"><span class="dashicons dashicons-id-alt"></span> ' . esc_html($siembra->metodo_de_pago) . '</span></td>';
                            echo '<td><span class="amount-badge">' . $simbolo . ' ' . number_format($siembra->monto, 2) . '</span></td>';
                            echo '<td><code class="premium-ref">' . esc_html($siembra->referencia) . '</code></td>';
                            echo '<td>
                                    <div class="user-info">
                                        <span class="user-name">' . esc_html($siembra->nombre_completo) . '</span>
                                        <span class="user-email">' . esc_html($siembra->correo) . '</span>
                                    </div>
                                  </td>';
                            echo '<td class="col-tel">' . esc_html($siembra->telefono) . '</td>';
                            echo '<td><button type="button" class="button view-msg-btn" onclick="mostrarMensaje(`' . esc_js($siembra->mensaje) . '`)"><span class="dashicons dashicons-testimonial"></span></button></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Mensajes -->
    <div id="msgModal" class="premium-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-header">
                <h3>Petición / Mensaje</h3>
            </div>
            <div class="modal-body">
                <p id="modalMsgText"></p>
            </div>
        </div>
    </div>

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
        .summary-card-premium:hover { transform: translateY(-2px); }
        .card-icon-wrap { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: var(--bg-body); color: var(--primary); }
        .card-icon-wrap .dashicons { font-size: 28px; width: 28px; height: 28px; }
        .summary-card-premium.usd .card-icon-wrap { background: #dcfce7; color: #166534; }
        .summary-card-premium.ves .card-icon-wrap { background: #fef2f2; color: #991b1b; }
        .summary-card-premium.total .card-icon-wrap { background: #eff6ff; color: #1e40af; }
        .card-label { display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .card-value { font-size: 26px; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; }

        /* Toolbar & Filters */
        .premium-toolbar { background: #fff; padding: 20px; border-radius: 16px 16px 0 0; border: 1px solid #e2e8f0; border-bottom: none; display: flex; justify-content: space-between; align-items: center; }
        .date-filter-group { display: flex; align-items: center; gap: 15px; }
        .filter-item { display: flex; align-items: center; gap: 10px; }
        .filter-item label { font-weight: 600; color: var(--text-sub); font-size: 13px; }
        .premium-input { border: 1px solid #cbd5e1 !important; border-radius: 8px !important; padding: 6px 12px !important; color: var(--text-main) !important; font-size: 13px !important; }

        /* Table Design */
        .premium-table-container { background: #fff; border-radius: 0 0 16px 16px; border: 1px solid #e2e8f0; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        #miTabla { border: none !important; width: 100% !important; border-collapse: collapse !important; }
        #miTabla thead th { background: #f8fafc !important; color: #64748b !important; font-weight: 700 !important; text-transform: uppercase !important; font-size: 11px !important; letter-spacing: 0.05em !important; padding: 16px 20px !important; border-bottom: 2px solid #e2e8f0 !important; }
        #miTabla tbody td { padding: 14px 20px !important; vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; color: var(--text-main); font-size: 14px; }
        #miTabla tbody tr:hover { background-color: #fbfcfe !important; }

        /* Column Styles */
        .col-id { color: var(--text-sub); font-weight: 600; font-size: 12px; }
        .date-main { display: block; font-weight: 600; color: var(--text-main); }
        .date-sub { display: block; font-size: 11px; color: var(--text-sub); margin-top: 2px; }
        .premium-tag { padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #f1f5f9; color: #475569; display: inline-block; white-space: nowrap; }
        .tag-diezmo { background: #dcfce7; color: #15803d; }
        .tag-ofrenda { background: #dbeafe; color: #1d4ed8; }
        .tag-pacto { background: #fef3c7; color: #b45309; }
        .tag-primicia { background: #ffedd5; color: #c2410c; }
        .tag-yo-construyo { background: #ede9fe; color: #6d28d9; }
        
        .payment-method { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-sub); font-weight: 600; text-transform: uppercase; }
        .payment-method .dashicons { font-size: 16px; width: 16px; height: 16px; color: #cbd5e1; }
        .amount-badge { font-weight: 800; color: var(--text-main); font-size: 16px; }
        .premium-ref { background: #f8fafc; padding: 4px 8px; border-radius: 6px; color: var(--primary); font-family: "JetBrains Mono", monospace; font-size: 12px; border: 1px solid #e2e8f0; }
        .user-name { display: block; font-weight: 700; color: var(--text-main); }
        .user-email { display: block; font-size: 12px; color: var(--text-sub); }
        .col-tel { font-family: monospace; font-weight: 600; color: var(--text-sub); font-size: 12px; }
        .view-msg-btn { color: #cbd5e1 !important; transition: all 0.2s !important; border: 1px solid #e2e8f0 !important; background: transparent !important; border-radius: 8px !important; cursor: pointer; }
        .view-msg-btn:hover { color: var(--primary) !important; border-color: var(--primary) !important; background: #eff6ff !important; }

        /* Modal Styles */
        .premium-modal { display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }
        .modal-content { background: #fff; margin: 15vh auto; padding: 0; border-radius: 20px; width: 450px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; overflow: hidden; animation: modalFadeIn 0.3s ease-out; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .close-modal { position: absolute; right: 20px; top: 15px; font-size: 28px; cursor: pointer; color: var(--text-sub); z-index: 2; line-height: 1; }
        .modal-header { padding: 25px 30px; background: #f8fafc; border-bottom: 2px solid #f1f5f9; }
        .modal-header h3 { margin: 0; color: var(--text-main); font-weight: 800; font-size: 18px; }
        .modal-body { padding: 30px; }
        .modal-body p { line-height: 1.7; color: var(--text-main); white-space: pre-wrap; margin: 0; font-size: 15px; }

        /* DataTables Controls */
        .dt-top-flex { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; }
        .dt-button { background: var(--primary) !important; color: #fff !important; border: none !important; border-radius: 10px !important; padding: 10px 20px !important; font-weight: 700 !important; font-size: 13px !important; transition: all 0.2s !important; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1) !important; cursor: pointer !important; }
        .dt-button:hover { background: #1d4ed8 !important; transform: translateY(-1px) !important; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2) !important; }
        
        .dataTables_filter { margin-top: 0 !important; }
        .dataTables_filter input { border: 1px solid #e2e8f0 !important; border-radius: 10px !important; padding: 8px 15px !important; width: 280px !important; margin-left: 15px !important; background: #f8fafc !important; }
        .dataTables_length { padding: 15px 20px !important; font-size: 12px !important; color: var(--text-sub) !important; font-weight: 600 !important; }
        .dataTables_length select { border: 1px solid #e2e8f0 !important; border-radius: 8px !important; padding: 2px 24px 2px 8px !important; }
        .dataTables_info { padding: 20px !important; color: var(--text-sub) !important; font-weight: 600 !important; font-size: 12px !important; text-transform: uppercase; letter-spacing: 0.05em; }
        .dataTables_paginate { padding: 15px 20px !important; }
        .paginate_button { border-radius: 10px !important; border: 1px solid #e2e8f0 !important; margin: 0 2px !important; font-weight: 700 !important; font-size: 13px !important; padding: 5px 12px !important; }
        .paginate_button.current { background: var(--primary) !important; color: #fff !important; border-color: var(--primary) !important; }
    </style>

    <script>
        function mostrarMensaje(text) {
            const modal = document.getElementById('msgModal');
            document.getElementById('modalMsgText').innerText = text || 'Sin petición específica.';
            modal.style.display = 'block';
        }

        document.querySelector('.close-modal').onclick = function() {
            document.getElementById('msgModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('msgModal')) {
                document.getElementById('msgModal').style.display = 'none';
            }
        }
    </script>
<?php
}

function mi_datatable_enqueue_scripts($hook)
{
    if ($hook !== 'toplevel_page_siembras') return;

    // Bundle consolidado: DataTables 2.0.8, Buttons 3.0.2, HTML5 Export, JSZip 3.10.1
    wp_enqueue_style('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');
    wp_enqueue_script('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.js', array('jquery'), '2.0.8', true);

    wp_add_inline_script('siembra-datatables-combined', '
    jQuery(document).ready(function($) {
        if (typeof $.fn.DataTable !== "function") return;

        // Lógica de Filtrado Combinado (Fecha, Propósito, Método)
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

            // 2. Filtro de Propósito (Columna 2)
            const selProposito = $("#filter-proposito").val();
            const rowProposito = data[2]; 
            if (selProposito && rowProposito !== selProposito) return false;

            // 3. Filtro de Método (Columna 3)
            const selMetodo = $("#filter-metodo").val();
            const rowMetodo = $(settings.aoData[dataIndex].anCells[3]).text().trim(); // Usar text() por iconos
            if (selMetodo && rowMetodo !== selMetodo) return false;
            
            return true;
        });

        const table = $("#miTabla").DataTable({
            dom: "<\"dt-top-flex\"Bf>lrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    text: "<span class=\"dashicons dashicons-download\"></span> Descargar Reporte Excel",
                    title: "Reporte_Siembras_" + new Date().toISOString().split("T")[0],
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

        // Eventos para disparar el redibujado de la tabla
        $("#min-date, #max-date, #filter-proposito, #filter-metodo").on("change", function() {
            table.draw();
        });

        $("#clear-filters").on("click", function() {
            $("#min-date, #max-date, #filter-proposito, #filter-metodo").val("");
            table.draw();
        });
    });
');
}
add_action('admin_enqueue_scripts', 'mi_datatable_enqueue_scripts');