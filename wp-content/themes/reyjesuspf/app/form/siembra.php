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
?>
    <div class="wrap">
        <h2>Registro de Siembras</h2>
        <table id="miTabla" class="widefat striped" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Metodo de Pago</th>
                    <th>Monto</th>
                    <th>Divisa</th>
                    <th>Referencia</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Petición de Oración</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (! empty($siembras)) {
                    foreach ($siembras as $siembra) {
                        $simbolo = (isset($siembra->divisa) && $siembra->divisa === 'BS') ? 'Bs' : '$';
                        echo '<tr>';
                        echo '<td>' . esc_html($siembra->id) . '</td>';
                        echo '<td>' . esc_html($siembra->dia_pago) . '</td>';
                        echo '<td style="text-transform: capitalize;">' . esc_html($siembra->tipo_siembra) . '</td>';
                        echo '<td>' . esc_html($siembra->metodo_de_pago) . '</td>';
                        echo '<td><strong>' . $simbolo . ' ' . number_format($siembra->monto, 2) . '</strong></td>';
                        echo '<td>' . esc_html(isset($siembra->divisa) ? $siembra->divisa : 'USD') . '</td>';
                        echo '<td><code style="background:#f0f0f0;padding:2px 5px;border-radius:4px;">' . esc_html($siembra->referencia) . '</code></td>';
                        echo '<td>' . esc_html($siembra->nombre_completo) . '</td>';
                        echo '<td>' . esc_html($siembra->telefono) . '</td>';
                        echo '<td>' . esc_html($siembra->correo) . '</td>';
                        echo '<td>' . esc_html($siembra->mensaje) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="11">No se han registrado siembras.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
}

function mi_datatable_enqueue_scripts($hook)
{
    // Solo cargar en la página de siembras
    if ($hook !== 'toplevel_page_siembras') {
        return;
    }

    // Estilos y Scripts de DataTables
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/v/dt/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');
    wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array(), null, true);
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_script('datatables-buttons', 'https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js', array('datatables-js', 'jszip'), null, true);
    wp_enqueue_script('datatables-buttons-html5', 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js', array('datatables-buttons'), null, true);

    wp_add_inline_script('datatables-js', '
    jQuery(document).ready(function($) {
        $("#miTabla").DataTable({
            dom: "Bfrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    text: "Exportar a Excel",
                    title: "Registro_de_Siembras_" + new Date().toISOString().split("T")[0]
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            order: [[1, "desc"]], // Ordenar por fecha descendente
            pageLength: 25
        });
    });
');
}
add_action('admin_enqueue_scripts', 'mi_datatable_enqueue_scripts');