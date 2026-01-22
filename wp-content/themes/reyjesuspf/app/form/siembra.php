<?php
// Incluir sistema de logging avanzado
require_once __DIR__ . '/siembra_debug.php';

// Función para crear la tabla de siembras al activar el tema
function crear_tabla_siembras()
{
    siembra_log("=== CREANDO TABLA DE SIEMBRAS ===", 'SYSTEM');

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
            referencia varchar(100) DEFAULT '',
            nombre_completo varchar(255) DEFAULT '',
            telefono varchar(50) DEFAULT '',
            correo varchar(255) DEFAULT '',
            mensaje text DEFAULT '',
            PRIMARY KEY  (id)
        )   $charset_collate;";

        siembra_log("Ejecutando SQL: " . substr($sql, 0, 100) . "...", 'DEBUG');

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $table_created = $wpdb->get_var("SHOW TABLES LIKE '$tabla_siembras'") == $tabla_siembras;
        siembra_log("Tabla creada: " . ($table_created ? "SÍ" : "NO"), $table_created ? 'SUCCESS' : 'ERROR');
    } else {
        siembra_log("Tabla ya existe", 'INFO');
    }
}
crear_tabla_siembras();

// Función para procesar el formulario y guardar los datos
function procesar_formulario_siembra()
{
    $start_time = microtime(true);
    siembra_log("=== INICIO PROCESAMIENTO DE SIEMBRA ===", 'START');

    try {
        // Verificar el nonce de seguridad
        if (! isset($_POST['mi_nonce']) || ! wp_verify_nonce($_POST['mi_nonce'], 'mi_form_siembra_nonce')) {
            siembra_log_error("ERROR: Nonce de seguridad inválido o faltante", null, [
                'nonce_present' => isset($_POST['mi_nonce']),
                'nonce_value' => isset($_POST['mi_nonce']) ? substr($_POST['mi_nonce'], 0, 10) . '...' : 'N/A'
            ]);
            wp_die('Error de seguridad, por favor inténtalo de nuevo.');
        }
        siembra_log("✅ Nonce verificado correctamente", 'SUCCESS');

        // VERIFICACIÓN DE GOOGLE RECAPTCHA V3
        $recaptcha_secret_key = '6LePAbwrAAAAAGT4G4s6FngmaTEK3O0UdPqGfOfT';
        $recaptcha_token = isset($_POST['recaptcha_response']) ? sanitize_text_field($_POST['recaptcha_response']) : '';

        siembra_log("🔐 Verificando reCAPTCHA - Token presente: " . (!empty($recaptcha_token) ? 'SÍ' : 'NO'), 'DEBUG');

        if (empty($recaptcha_token)) {
            siembra_log_error("ERROR: Token de reCAPTCHA faltante", null, [
                'post_data' => $_POST,
                'server_data' => [
                    'remote_addr' => $_SERVER['REMOTE_ADDR'],
                    'request_method' => $_SERVER['REQUEST_METHOD']
                ]
            ]);
            wp_die('Error de verificación de seguridad. Por favor, recarga la página e intenta de nuevo.');
        }

        siembra_log("📡 Enviando verificación reCAPTCHA a Google", 'DEBUG');
        $recaptcha_start = microtime(true);

        $verification_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $recaptcha_secret_key,
                'response' => $recaptcha_token,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ]
        ]);

        siembra_log_performance("reCAPTCHA verification", $recaptcha_start);

        if (is_wp_error($verification_response)) {
            siembra_log_error("Error de conexión con reCAPTCHA", null, [
                'error' => $verification_response->get_error_message(),
                'wp_debug' => defined('WP_DEBUG') && WP_DEBUG
            ]);
            // En desarrollo, permitir continuar si hay error de conexión
            if (WP_DEBUG) {
                siembra_log("Modo debug: Continuando sin verificación reCAPTCHA debido a error de conexión", 'WARNING');
            } else {
                wp_die('No se pudo conectar con el servicio de verificación.');
            }
        } else {
            $response_data = json_decode(wp_remote_retrieve_body($verification_response));

            siembra_log("📥 Respuesta reCAPTCHA recibida", 'DEBUG', $response_data);

            // Verificar si la comunicación con Google fue exitosa
            if (!$response_data || !$response_data->success) {
                siembra_log_error("reCAPTCHA falló", null, [
                    'response_data' => $response_data,
                    'wp_debug' => defined('WP_DEBUG') && WP_DEBUG
                ]);
                // En desarrollo, permitir continuar
                if (WP_DEBUG) {
                    siembra_log("Modo debug: Continuando sin verificación reCAPTCHA", 'WARNING');
                } else {
                    wp_die('Error de comunicación con el servicio reCAPTCHA.');
                }
            } else {
                // Verificar si estamos en entorno local
                $is_local_environment = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
                $score = $response_data->score ?? null;

                siembra_log("✅ reCAPTCHA exitoso - Score: " . ($score ?? 'N/A') . " - Local: " . ($is_local_environment ? 'SÍ' : 'NO'), 'SUCCESS');

                // La validación del SCORE solo se aplica si NO estamos en entorno local
                if (!$is_local_environment && $score !== null && $score < 0.5) {
                    siembra_log_error("reCAPTCHA score demasiado bajo", null, ['score' => $score]);
                    wp_die('Falló la verificación de humanidad. Intenta de nuevo.');
                }
            }
        }
    }

        // Verificar que el formulario fue enviado por POST
        if ('POST' !== $_SERVER['REQUEST_METHOD']) {
            siembra_log_error("ERROR: Método de solicitud no es POST", null, [
                'method' => $_SERVER['REQUEST_METHOD'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
            ]);
            return;
        }
        siembra_log("✅ Método POST confirmado", 'SUCCESS');

        // URL de retorno
        $siembra_page_url = wp_get_referer() ? wp_get_referer() : home_url();
        siembra_log("📍 URL de retorno: $siembra_page_url", 'INFO');

        global $wpdb;
        $tabla_siembras = $wpdb->prefix . 'siembras';

        // Obtener y sanitizar los datos del formulario
        $tipo_siembra   = isset($_POST['tipo_siembra']) ? sanitize_text_field($_POST['tipo_siembra']) : '';
        $metodo_de_pago = isset($_POST['metodo_de_pago']) ? sanitize_text_field($_POST['metodo_de_pago']) : '';
        $monto          = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
        $referencia     = isset($_POST['referencia']) ? sanitize_text_field($_POST['referencia']) : '';
        $nombre         = isset($_POST['nombre']) ? sanitize_text_field($_POST['nombre']) : '';
        $telefono       = isset($_POST['telefono']) ? sanitize_text_field($_POST['telefono']) : '';
        $email          = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $mensaje        = isset($_POST['mensaje']) ? sanitize_textarea_field($_POST['mensaje']) : '';

        siembra_log_data([
            'tipo_siembra' => $tipo_siembra,
            'metodo_de_pago' => $metodo_de_pago,
            'monto' => $monto,
            'referencia' => $referencia,
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'mensaje_length' => strlen($mensaje)
        ], "📝 Datos del formulario recibidos");

        // Validaciones básicas
        $validation_errors = [];
        if (empty($tipo_siembra)) $validation_errors[] = 'tipo_siembra';
        if (empty($metodo_de_pago)) $validation_errors[] = 'metodo_de_pago';
        if ($monto <= 0) $validation_errors[] = 'monto';
        if (empty($nombre)) $validation_errors[] = 'nombre';
        if (empty($email)) $validation_errors[] = 'email';
        if (empty($mensaje)) $validation_errors[] = 'mensaje';

        if (!empty($validation_errors)) {
            siembra_log_error("ERROR: Campos obligatorios faltantes", null, [
                'missing_fields' => $validation_errors,
                'form_data' => $_POST
            ]);
            wp_die('Por favor, completa todos los campos obligatorios.');
        }

        if (!is_email($email)) {
            siembra_log_error("ERROR: Email inválido", null, ['email' => $email]);
            wp_die('Por favor, ingresa un correo electrónico válido.');
        }

        siembra_log("✅ Validaciones de formulario pasaron", 'SUCCESS');

        $dia_pago = current_time('mysql');

        siembra_log("💾 Preparando inserción en base de datos...", 'DEBUG');

        $db_start = microtime(true);

        // Insertar los datos en la tabla
        $insert_data = array(
            'dia_pago'        => $dia_pago,
            'tipo_siembra'    => $tipo_siembra,
            'metodo_de_pago'  => $metodo_de_pago,
            'monto'           => $monto,
            'referencia'      => $referencia,
            'nombre_completo' => $nombre,
            'telefono'        => $telefono,
            'correo'          => $email,
            'mensaje'         => $mensaje,
        );

        $insert_result = $wpdb->insert(
            $tabla_siembras,
            $insert_data,
            array('%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s')
        );

        siembra_log_performance("Database insertion", $db_start);

        // Verificar si la inserción fue exitosa
        if ($insert_result === false) {
            siembra_log_error("Error al insertar en la base de datos", null, [
                'wpdb_error' => $wpdb->last_error,
                'wpdb_errno' => $wpdb->last_errno,
                'insert_data' => $insert_data,
                'table' => $tabla_siembras
            ]);
            wp_die('Error al guardar los datos. Por favor, intenta de nuevo.');
        }

        $siembra_id = $wpdb->insert_id;
        siembra_log("✅ Siembra insertada correctamente con ID: $siembra_id", 'SUCCESS');

        // Enviar email de confirmación al usuario
        siembra_log("📧 Preparando envío de emails...", 'DEBUG');

        $email_start = microtime(true);

        $asunto_usuario = 'Confirmación de tu Siembra - El Rey Jesús Punto Fijo';
        $mensaje_usuario = "
        <html>
        <head>
            <title>Confirmación de Siembra</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f8f9fa; padding: 10px; text-align: center; font-size: 12px; }
                .highlight { background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>El Rey Jesús Punto Fijo</h1>
                <h2>Confirmación de tu Siembra</h2>
            </div>
            <div class='content'>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>¡Gracias por tu siembra! Hemos recibido tu donación y la estamos procesando.</p>

                <div class='highlight'>
                    <h3>Detalles de tu Siembra:</h3>
                    <p><strong>ID de Transacción:</strong> {$siembra_id}</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i', strtotime($dia_pago)) . "</p>
                    <p><strong>Tipo de Siembra:</strong> {$tipo_siembra}</p>
                    <p><strong>Método de Pago:</strong> {$metodo_de_pago}</p>
                    <p><strong>Monto:</strong> $" . number_format($monto, 2) . "</p>
                    " . (!empty($referencia) ? "<p><strong>Referencia:</strong> {$referencia}</p>" : "") . "
                </div>

                <p><strong>Tu mensaje de oración:</strong></p>
                <blockquote style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff;'>
                    " . nl2br(esc_html($mensaje)) . "
                </blockquote>

                <p>Estamos orando por ti y agradecemos tu generosidad. Tu contribución nos ayuda a continuar con la obra de Dios en nuestra comunidad.</p>

                <p>Si tienes alguna pregunta, puedes contactarnos respondiendo a este email.</p>

                <p>Dios te bendiga abundantemente,</p>
                <p><strong>Iglesia El Rey Jesús Punto Fijo</strong></p>
            </div>
        <div class='footer'>
            <p>Este es un mensaje automático, por favor no respondas directamente a este email.</p>
        </div>
    </body>
    </html>
    ";

        $headers_usuario = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Iglesia El Rey Jesús Punto Fijo <no-reply@elreyjesuspuntofijo.com>'
        );

        siembra_log("📧 Enviando email de confirmación al usuario: $email", 'DEBUG');

        // Enviar email al usuario
        $email_usuario_enviado = wp_mail($email, $asunto_usuario, $mensaje_usuario, $headers_usuario);

        if (!$email_usuario_enviado) {
            siembra_log_error("Error al enviar email al usuario", null, [
                'to' => $email,
                'subject' => $asunto_usuario,
                'wp_mail_error' => error_get_last()
            ]);
        } else {
            siembra_log("✅ Email de confirmación enviado al usuario: $email", 'SUCCESS');
        }

        // Enviar email de notificación a la iglesia
        $admin_email = get_option('admin_email');
        siembra_log("📧 Enviando notificación al administrador: $admin_email", 'DEBUG');

        $asunto_admin = 'Nueva Siembra Recibida - ID: ' . $siembra_id;
        $mensaje_admin = "
        <html>
        <head>
            <title>Nueva Siembra Recibida</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .info-box { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Nueva Siembra Recibida</h1>
            </div>
            <div class='content'>
                <div class='info-box'>
                    <h3>Detalles de la Siembra:</h3>
                    <p><strong>ID:</strong> {$siembra_id}</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i', strtotime($dia_pago)) . "</p>
                    <p><strong>Nombre:</strong> {$nombre}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Teléfono:</strong> {$telefono}</p>
                    <p><strong>Tipo de Siembra:</strong> {$tipo_siembra}</p>
                    <p><strong>Método de Pago:</strong> {$metodo_de_pago}</p>
                    <p><strong>Monto:</strong> $" . number_format($monto, 2) . "</p>
                    " . (!empty($referencia) ? "<p><strong>Referencia:</strong> {$referencia}</p>" : "") . "
                </div>

                <div class='info-box'>
                    <h3>Mensaje de Oración:</h3>
                    <blockquote>
                        " . nl2br(esc_html($mensaje)) . "
                    </blockquote>
                </div>

                <p><a href='" . admin_url('admin.php?page=siembras') . "'>Ver todas las siembras en el panel de administración</a></p>
            </div>
        </body>
        </html>
        ";

        $headers_admin = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Sistema de Siembras <no-reply@elreyjesuspuntofijo.com>'
        );

        // Enviar email al administrador
        $email_admin_enviado = wp_mail($admin_email, $asunto_admin, $mensaje_admin, $headers_admin);

        if (!$email_admin_enviado) {
            siembra_log_error("Error al enviar email al administrador", null, [
                'to' => $admin_email,
                'subject' => $asunto_admin,
                'wp_mail_error' => error_get_last()
            ]);
        } else {
            siembra_log("✅ Email de notificación enviado al administrador: $admin_email", 'SUCCESS');
        }

        siembra_log_performance("Email sending", $email_start);

        // Log de envío de emails
        siembra_log("📊 Resumen de envío de emails - Usuario: " . ($email_usuario_enviado ? 'ENVIADO' : 'FALLÓ') . " - Admin: " . ($email_admin_enviado ? 'ENVIADO' : 'FALLÓ'), 'INFO');

        siembra_log("🎉 PROCESAMIENTO DE SIEMBRA COMPLETADO EXITOSAMENTE", 'SUCCESS');
        siembra_log_performance("Total processing time", $start_time);

        // Redirigir al usuario con un mensaje de éxito
        $redirect_url = add_query_arg('enviado', 'true', $siembra_page_url);
        siembra_log("🔄 Redirigiendo a: $redirect_url", 'INFO');
        wp_redirect($redirect_url);
        exit;

    } catch (Exception $e) {
        siembra_log_error("EXCEPCIÓN CRÍTICA durante el procesamiento de siembra", $e, [
            'post_data' => $_POST,
            'server_data' => $_SERVER
        ]);
        wp_die('Ha ocurrido un error inesperado. Por favor, intenta de nuevo más tarde.');
    }
}
add_action('admin_post_nopriv_procesar_formulario_siembra', __NAMESPACE__ . '\\procesar_formulario_siembra');
add_action('admin_post_procesar_formulario_siembra', __NAMESPACE__ . '\\procesar_formulario_siembra');

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
add_action('admin_menu', __NAMESPACE__ . '\\anadir_menu_siembras');

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
                        echo '<tr>';
                        echo '<td>' . esc_html($siembra->id) . '</td>';
                        echo '<td>' . esc_html($siembra->dia_pago) . '</td>';
                        echo '<td>' . esc_html($siembra->tipo_siembra) . '</td>';
                        echo '<td>' . esc_html($siembra->metodo_de_pago) . '</td>';
                        echo '<td>' . esc_html($siembra->monto) . '</td>';
                        echo '<td>' . esc_html($siembra->referencia) . '</td>';
                        echo '<td>' . esc_html($siembra->nombre_completo) . '</td>';
                        echo '<td>' . esc_html($siembra->telefono) . '</td>';
                        echo '<td>' . esc_html($siembra->correo) . '</td>';
                        echo '<td>' . esc_html($siembra->mensaje) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8">No se han registrado siembras.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
}

function mi_datatable_enqueue_scripts($hook)
{
    // Estilos y Scripts de DataTables
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/v/dt/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');
    wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array(), null, true);
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_script('datatables-buttons', 'https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js', array('datatables-js', 'jszip'), null, true);
    wp_enqueue_script('datatables-buttons-html5', 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js', array('datatables-buttons'), null, true);
    
    wp_add_inline_script('datatables-js', '
    jQuery(document).ready(function($) {
        var table = $("#miTabla").DataTable({
            dom: "Bfrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    text: "Exportar a Excel"
                }
            ]
        });
    });
');
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\mi_datatable_enqueue_scripts');