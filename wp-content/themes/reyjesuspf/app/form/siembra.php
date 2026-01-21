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
            referencia_pago varchar(100) DEFAULT '',
            nombre_completo varchar(255) DEFAULT '',
            telefono varchar(50) DEFAULT '',
            correo varchar(255) DEFAULT '',
            peticion_oracion text DEFAULT '',
            PRIMARY KEY  (id)
        )   $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
crear_tabla_siembras();

// Función para procesar el formulario y guardar los datos
function procesar_formulario_siembra()
{
    // Verificar el nonce de seguridad
    if (! isset($_POST['mi_nonce']) || ! wp_verify_nonce($_POST['mi_nonce'], 'mi_form_siembra_nonce')) {
        wp_die('Error de seguridad, por favor inténtalo de nuevo.');
    }

    // VERIFICACIÓN DE GOOGLE RECAPTCHA V3
    $recaptcha_secret_key = '6LePAbwrAAAAAGT4G4s6FngmaTEK3O0UdPqGfOfT';
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

    // Verificar que el formulario fue enviado por POST
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return;
    }

    // URL de retorno
    $siembra_page_url = wp_get_referer() ? wp_get_referer() : home_url();

    global $wpdb;
    $tabla_siembras = $wpdb->prefix . 'siembras';

    // Obtener y sanitizar los datos del formulario
    $tipo_siembra   = isset($_POST['tipo_siembra']) ? sanitize_text_field($_POST['tipo_siembra']) : '';
    $metodo_de_pago = isset($_POST['metodo_de_pago']) ? sanitize_text_field($_POST['metodo_de_pago']) : '';
    $monto          = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $referencia_pago = isset($_POST['referencia']) ? sanitize_text_field($_POST['referencia']) : '';
    $nombre         = isset($_POST['nombre']) ? sanitize_text_field($_POST['nombre']) : '';
    $telefono       = isset($_POST['telefono']) ? sanitize_text_field($_POST['telefono']) : '';
    $email          = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $peticion_oracion = isset($_POST['mensaje']) ? sanitize_textarea_field($_POST['mensaje']) : '';

    $dia_pago       = current_time('mysql');

    // Insertar los datos en la tabla
    $wpdb->insert(
        $tabla_siembras,
        array(
            'dia_pago'        => $dia_pago,
            'tipo_siembra'    => $tipo_siembra,
            'metodo_de_pago'  => $metodo_de_pago,
            'monto'           => $monto,
            'referencia_pago' => $referencia_pago,  
            'nombre_completo' => $nombre,
            'telefono'        => $telefono,
            'correo'          => $email,
            'peticion_oracion'=> $peticion_oracion,
        ),
        array('%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s')
    );

    // Redirigir al usuario con un mensaje de éxito
    $redirect_url = add_query_arg('enviado', 'true', $siembra_page_url);
    wp_redirect($redirect_url);
    exit;
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
                        echo '<td>' . esc_html($siembra->referencia_pago) . '</td>';
                        echo '<td>' . esc_html($siembra->nombre_completo) . '</td>';
                        echo '<td>' . esc_html($siembra->telefono) . '</td>';
                        echo '<td>' . esc_html($siembra->correo) . '</td>';
                        echo '<td>' . esc_html($siembra->peticion_oracion) . '</td>';
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