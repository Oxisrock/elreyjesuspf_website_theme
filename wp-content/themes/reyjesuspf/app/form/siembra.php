<?php
// Función para crear la tabla de siembras al activar el tema
function crear_tabla_siembras()
{
    global $wpdb;
    $tabla_siembras = $wpdb->prefix . 'siembras'; // El prefijo por defecto es wp_

    $charset_collate = $wpdb->get_charset_collate();
    if ($wpdb->get_var("SHOW TABLES LIKE '$tabla_siembras'") != $tabla_siembras) {
        $sql = "CREATE TABLE $tabla_siembras (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            dia_pago datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            metodo_de_pago varchar(50) NOT NULL,
            monto decimal(10, 2) NOT NULL,
            nombre_completo varchar(255) DEFAULT '',
            correo varchar(255) DEFAULT '',
            PRIMARY KEY  (id)
        )   $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
// Ejecutamos la función. Recuerda comentarla o borrarla después del primer uso.
crear_tabla_siembras();

// Función para procesar el formulario y guardar los datos
function procesar_formulario_siembra()
{

    // Verificar el nonce de seguridad
    if (! isset($_POST['mi_nonce']) || ! wp_verify_nonce($_POST['mi_nonce'], 'mi_form_siembra_nonce')) {
        wp_die('Error de seguridad, por favor inténtalo de nuevo.');
    }

    // Verificar que el formulario fue enviado
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return;
    }

    global $wpdb;
    $tabla_siembras = $wpdb->prefix . 'siembras';

    // Obtener y sanitizar los datos del formulario
    $metodo_de_pago = sanitize_text_field($_POST['metodo_de_pago']);
    $monto = floatval($_POST['monto']);
    $nombre = sanitize_text_field($_POST['nombre']);
    $email = sanitize_email($_POST['email']);
    $dia_pago = current_time('mysql');

    // Insertar los datos en la tabla
    $wpdb->insert(
        $tabla_siembras,
        array(
            'dia_pago' => $dia_pago,
            'metodo_de_pago' => $metodo_de_pago,
            'monto' => $monto,
            'nombre_completo' => $nombre,
            'correo' => $email,
        ),
        array('%s', '%s', '%f', '%s', '%s')
    );

    // Redirigir al usuario con un mensaje de éxito
    $redirect_url = add_query_arg('enviado', 'true', wp_get_referer());
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_nopriv_procesar_formulario_siembra', __NAMESPACE__ . '\\procesar_formulario_siembra');
add_action('admin_post_procesar_formulario_siembra', __NAMESPACE__ . '\\procesar_formulario_siembra');
// Añade un submenú en el panel de administración
function anadir_menu_siembras()
{

    add_menu_page(
        'Registro de Siembras', // Título de la página
        'Siembras',             // Título del menú
        'manage_options',       // Capacidad requerida para ver el menú
        'siembras',             // Slug del menú
        'mostrar_siembras_page', // Función que muestra el contenido de la página
        'dashicons-money-alt',  // Icono
        6                      // Posición en el menú
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
                    <th>Metodo de Pago</th>
                    <th>Monto</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (! empty($siembras)) {
                    foreach ($siembras as $siembra) {
                        echo '<tr>';
                        echo '<td>' . esc_html($siembra->id) . '</td>';
                        echo '<td>' . esc_html($siembra->dia_pago) . '</td>';
                        echo '<td>' . esc_html($siembra->metodo_de_pago) . '</td>';
                        echo '<td>' . esc_html($siembra->monto) . '</td>';
                        echo '<td>' . esc_html($siembra->nombre_completo) . '</td>';
                        echo '<td>' . esc_html($siembra->correo) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">No se han registrado siembras.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
}

function mi_datatable_enqueue_scripts($hook)
{
    /*if ($hook != 'settings_page_mi-datatable') {
        return;
    }*/
    
    // Estilos
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/v/dt/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.css');

    // Scripts
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/v/dt/dt-2.0.8/b-3.0.2/b-html5-3.0.2/datatables.min.js', array('jquery'), null, true);
    // Carga las dependencias en el orden correcto
    wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array(), null, true);
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_script('datatables-buttons', 'https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js', array('datatables-js', 'jszip'), null, true);
    wp_enqueue_script('datatables-buttons-html5', 'https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js', array('datatables-buttons'), null, true);
    // Tu script de inicialización
    // Tu script de inicialización con input de texto
    wp_add_inline_script('datatables-js', '
    jQuery(document).ready(function($) {
        var table = $("#miTabla").DataTable({
            dom: "Bfrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    text: "Exportar a Excel"
                }
            ],
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    
                    // Creamos el input de texto en lugar del select
                    var input = $(\'<input type="text" placeholder="Filtrar..." />\')
                        .appendTo( $(column.header()) )
                        .on( "keyup change", function () {
                            if ( column.search() !== this.value ) {
                                column
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                } );
            }
        });
    });
');
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\mi_datatable_enqueue_scripts');
