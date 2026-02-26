<?php

function crear_tabla_boletines_personalizada() {
    global $wpdb;
    $nombre_tabla_boletines = $wpdb->prefix . 'boletines_entradas';
    $charsert_collate = $wpdb->get_charset_collate();

    // Corregimos la sintaxis de la sentencia SQL agregando el ')'
    if ($wpdb->get_var("SHOW TABLES LIKE '$nombre_tabla_boletines'") != $nombre_tabla_boletines) {
        $sql = "CREATE TABLE $nombre_tabla_boletines (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            fecha datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            nombre varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            mensaje text NOT NULL,
            PRIMARY KEY (id)
        ) $charsert_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
// Ejecutamos la función. Recuerda comentarla o borrarla después del primer uso.
crear_tabla_boletines_personalizada();
}

// Corregimos el nombre de la función en el hook de 'nopriv'
add_action('admin_post_nopriv_procesar_formulario_boletines', __NAMESPACE__ . '\\mi_procesador_de_formularios_boletines');
add_action('admin_post_procesar_formulario_boletines', __NAMESPACE__ . '\\mi_procesador_de_formularios_boletines');

function mi_procesador_de_formularios_boletines() {
    global $wpdb;
    $nombre_tabla_boletines = $wpdb->prefix . 'boletines_entradas';

    // 1. Verificamos el Nonce de seguridad. El nombre de la acción debe ser el mismo que en el formulario.
    if ( !isset($_POST['mi_nonce']) || !wp_verify_nonce($_POST['mi_nonce'], 'mi_form_boletin_nonce') ) {
        wp_die('¡Falló la verificación de seguridad!');
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

    // 3. Limpiamos y sanitizamos los datos recibidos del formulario
    $nombre  = sanitize_text_field($_POST['nombre']);
    $email   = sanitize_email($_POST['email']);

    // 3. Insertamos los datos en nuestra tabla personalizada
    $wpdb->insert(
        $nombre_tabla_boletines,
        array(
            'fecha'   => current_time('mysql'),
            'nombre'  => $nombre,
            'email'   => $email,
        ),
        array(
            '%s', // Tipo de dato para 'fecha'
            '%s', // Tipo de dato para 'nombre'
            '%s'  // Tipo de dato para 'email'
        )
    );
    
    // Redirigimos al usuario para evitar la página en blanco
    wp_redirect(esc_url_raw($_POST['_wp_http_referer']));
    exit;
}

function mi_contenido_pagina_boletines() {
    global $wpdb;
    $nombre_tabla_boletines = $wpdb->prefix . 'boletines_entradas';

    // Estadísticas
    $total_suscriptores = $wpdb->get_var("SELECT COUNT(*) FROM $nombre_tabla_boletines");
    $suscriptores_hoy = $wpdb->get_var("SELECT COUNT(*) FROM $nombre_tabla_boletines WHERE DATE(fecha) = CURDATE()");

    $boletines = $wpdb->get_results("SELECT * FROM $nombre_tabla_boletines ORDER BY fecha DESC");
?>
    <div class="wrap siembras-premium-wrap">
        <div class="header-flex">
            <div>
                <h1 class="wp-heading-inline">Suscripciones al Boletín</h1>
                <p class="subtitle">Listado de personas interesadas en recibir actualizaciones</p>
            </div>
        </div>
        
        <hr class="wp-header-end">

        <!-- Tarjetas de Resumen Premium -->
        <div class="siembras-summary-grid">
            <div class="summary-card-premium total">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-id"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Total Suscriptores</span>
                    <span class="card-value"><?php echo $total_suscriptores ?: 0; ?></span>
                </div>
            </div>
            <div class="summary-card-premium usd">
                <div class="card-icon-wrap">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="card-info">
                    <span class="card-label">Hoy</span>
                    <span class="card-value"><?php echo $suscriptores_hoy ?: 0; ?></span>
                </div>
            </div>
        </div>

        <!-- Barra de Herramientas y Filtros Premium -->
        <div class="premium-toolbar">
            <div class="date-filter-group">
                <div class="filter-item">
                    <label>Suscriptor:</label>
                    <input type="text" id="search-suscriptor" class="premium-input" placeholder="Nombre o Correo...">
                </div>

                <div class="filter-item">
                    <label>Desde:</label>
                    <input type="date" id="min-date" class="premium-input">
                </div>
                <div class="filter-item">
                    <label>Hasta:</label>
                    <input type="date" id="max-date" class="premium-input">
                </div>
                
                <button type="button" id="clear-filters" class="button button-link">Limpiar</button>
            </div>
        </div>

        <div class="premium-table-container">
            <table id="miTablaBoletines" class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Correo Electrónico</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($boletines)) : ?>
                        <?php foreach ($boletines as $boletine) : ?>
                            <tr>
                                <td class="col-id">#<?php echo esc_html($boletine->id); ?></td>
                                <td class="col-date" data-order="<?php echo esc_attr(strtotime($boletine->fecha)); ?>">
                                    <span class="date-main"><?php echo date('d M, Y', strtotime($boletine->fecha)); ?></span>
                                    <span class="date-sub"><?php echo date('h:i A', strtotime($boletine->fecha)); ?></span>
                                </td>
                                <td><span class="user-name"><?php echo esc_html($boletine->nombre); ?></span></td>
                                <td><code class="premium-ref"><?php echo esc_html($boletine->email); ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reutilizamos los estilos premium -->
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

        .siembras-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card-premium { background: #fff; border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        .card-icon-wrap { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: var(--bg-body); color: var(--primary); }
        .card-icon-wrap .dashicons { font-size: 28px; width: 28px; height: 28px; }
        .card-label { display: block; font-size: 11px; font-weight: 700; color: var(--text-sub); text-transform: uppercase; margin-bottom: 4px; }
        .card-value { font-size: 26px; font-weight: 800; color: var(--text-main); }

        .premium-toolbar { background: #fff; padding: 20px; border-radius: 16px 16px 0 0; border: 1px solid #e2e8f0; border-bottom: none; display: flex; justify-content: space-between; align-items: center; }
        .date-filter-group { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .filter-item { display: flex; align-items: center; gap: 10px; }
        .filter-item label { font-weight: 600; color: var(--text-sub); font-size: 13px; }
        .premium-input { border: 1px solid #cbd5e1 !important; border-radius: 8px !important; padding: 6px 12px !important; font-size: 13px !important; }

        .premium-table-container { background: #fff; border-radius: 0 0 16px 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        #miTablaBoletines { border: none !important; width: 100% !important; border-collapse: collapse !important; }
        #miTablaBoletines thead th { background: #f8fafc !important; color: #64748b !important; font-weight: 700 !important; text-transform: uppercase !important; font-size: 11px !important; padding: 16px 20px !important; border-bottom: 2px solid #e2e8f0 !important; }
        #miTablaBoletines tbody td { padding: 14px 20px !important; vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; font-size: 14px; }
        
        .col-id { color: var(--text-sub); font-weight: 600; font-size: 12px; }
        .user-name { font-weight: 700; color: var(--text-main); }
        .premium-ref { background: #f8fafc; padding: 4px 8px; border-radius: 6px; color: var(--primary); font-family: monospace; font-size: 12px; border: 1px solid #e2e8f0; }
        .date-main { display: block; font-weight: 600; color: var(--text-main); }
        .date-sub { display: block; font-size: 11px; color: var(--text-sub); }

        .dt-top-flex { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; }
        .dt-button { background: var(--primary) !important; color: #fff !important; border: none !important; border-radius: 10px !important; padding: 10px 20px !important; font-weight: 700 !important; cursor: pointer !important; }
    </style>
<?php
}

/**
 * --------------------------------------------------------------------------
 * LÓGICA DE DATATABLES PARA BOLETINES
 * --------------------------------------------------------------------------
 */
function boletines_admin_scripts($hook)
{
    if ($hook !== 'contacto_page_boletines-slug') return;

    wp_enqueue_style('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');
    wp_enqueue_script('siembra-datatables-combined', 'https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.js', array('jquery'), '2.0.8', true);

    wp_add_inline_script('siembra-datatables-combined', '
    jQuery(document).ready(function($) {
        if (typeof $.fn.DataTable !== "function") return;

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
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

            const searchTerm = $("#search-suscriptor").val().toLowerCase().trim();
            if (searchTerm) {
                const nombre = data[2].toLowerCase();
                const email = data[3].toLowerCase();
                if (nombre.indexOf(searchTerm) === -1 && email.indexOf(searchTerm) === -1) return false;
            }
            return true;
        });

        const table = $("#miTablaBoletines").DataTable({
            dom: "<\"dt-top-flex\"Bf>lrtip",
            buttons: [{
                extend: "excelHtml5",
                text: "<span class=\"dashicons dashicons-download\"></span> Excel Suscriptores",
                title: "Suscriptores_Boletin_" + new Date().toISOString().split("T")[0],
                className: "dt-button"
            }],
            language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" },
            order: [[1, "desc"]],
            pageLength: 25
        });

        $("#min-date, #max-date, #search-suscriptor").on("keyup change", function() { table.draw(); });
        $("#clear-filters").on("click", function() { $("#min-date, #max-date, #search-suscriptor").val(""); table.draw(); });
    });
');
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\boletines_admin_scripts');
?>