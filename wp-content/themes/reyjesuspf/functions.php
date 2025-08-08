<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

if (! function_exists('\Roots\bootloader')) {
    wp_die(
        __('You need to install Acorn to use this theme.', 'sage'),
        '',
        [
            'link_url' => 'https://roots.io/acorn/docs/installation/',
            'link_text' => __('Acorn Docs: Installation', 'sage'),
        ]
    );
}

\Roots\bootloader()->boot();

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
            );
        }
    });


// En tu archivo functions.php

/**
 * Función para registrar nuestro script de AJAX y pasarle la URL del endpoint de WordPress.
 */
function registrar_mis_scripts_de_eventos() {
    // Registra tu archivo de JavaScript. Asegúrate de que la ruta sea correcta.
    // Usualmente está en una carpeta como /assets/js/main.js o similar.
    wp_enqueue_script(
        'eventos-ajax-filter', 
        get_template_directory_uri() . '/resources/scripts/eventos-filter.js', // ¡Ajusta esta ruta a tu archivo JS!
        ['jquery'], // Dependencia
        '1.0', 
        true // Cargar en el footer
    );

    // Pasamos variables de PHP a JavaScript de forma segura.
    // La más importante es la URL para las llamadas AJAX.
    wp_localize_script(
        'eventos-ajax-filter',
        'eventos_ajax_obj', // Este será el objeto JS que contendrá nuestras variables
        [
            'ajax_url' => admin_url('admin-ajax.php')
        ]
    );
}
add_action('wp_enqueue_scripts', 'registrar_mis_scripts_de_eventos');




function filtrar_eventos_por_categoria() {
    // 1. Obtener los datos del AJAX: categoría Y número de posts a mostrar
    $categoria_slug = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
    // --> CAMBIO CLAVE 1: Obtenemos el número de posts que pide el JavaScript.
    // Usamos intval() para asegurar que sea un número. 6 es el valor por defecto.
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 3;

    // 2. Preparar los argumentos para WP_Query
    $args = [
        'post_type'      => 'events',
        // --> CAMBIO CLAVE 2: Usamos la variable en lugar de un número fijo.
        // Será 6 para "Mostrar menos" y -1 para "Ver más".
        'posts_per_page' => $posts_per_page,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ];

    if ($categoria_slug !== 'all') {
        $args['tax_query'] = [
            [
                'taxonomy' => 'tipo_evento',
                'field'    => 'slug',
                'terms'    => $categoria_slug,
            ],
        ];
    }

    $query = new WP_Query($args);

    // --> CAMBIO CLAVE 3: Para saber el total de posts que coinciden (sin límite), usamos found_posts
    $total_posts = $query->found_posts;

    // --> CAMBIO CLAVE 4: Usamos un buffer de salida para capturar todo el HTML en una variable
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Tu código de tarjeta de evento (sin cambios, está perfecto)
            ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <a href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <img class="aspect-video w-full object-cover" src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php else : ?>
                        <img class="aspect-video w-full object-cover" src="<?php echo get_template_directory_uri() . '/assets/images/placeholder.jpg'; ?>" alt="Imagen no disponible">
                    <?php endif; ?>
                </a>
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <?php
                        $event_terms = get_the_terms(get_the_ID(), 'tipo_evento');
                        if ($event_terms && !is_wp_error($event_terms)) {
                            $term = array_shift($event_terms);
                            echo '<span class="text-xs font-semibold px-2.5 py-0.5 bg-purple-100 text-purple-800 rounded-full">' . esc_html($term->name) . '</span>';
                        }
                        ?>
                        <span class="text-sm text-gray-500"><?php echo get_the_date('j F Y'); ?></span>
                    </div>
                    <h3 class="mt-2 text-xl font-bold text-gray-800">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <a href="<?php the_permalink(); ?>" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                        Ver detalles
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="col-span-full text-center">No se encontraron eventos en esta categoría.</p>';
    }
    
    wp_reset_postdata();

    // --> CAMBIO CLAVE 5: Guardamos el HTML capturado en una variable
    $html = ob_get_clean();

    // --> CAMBIO CLAVE 6: Enviamos una respuesta JSON estructurada que el JavaScript pueda entender
    wp_send_json_success([
        'html'        => $html,
        'total_posts' => $total_posts,
    ]);

    // wp_die() ya no es necesario aquí, wp_send_json_success se encarga de terminar la ejecución.
}

// Tus actions están correctos, no necesitas cambiarlos.
add_action('wp_ajax_filtrar_eventos', __NAMESPACE__ . '\\filtrar_eventos_por_categoria');
add_action('wp_ajax_nopriv_filtrar_eventos', __NAMESPACE__ . '\\filtrar_eventos_por_categoria');


// 1. Encolar y localizar el script de AJAX
function mi_tema_multimedia_scripts() {
    // Asegúrate de que este script se cargue solo en la página que lo necesita.
    // Si esta sección está en una plantilla de página específica, puedes usar is_page('nombre-de-pagina').
    // Por ahora, lo encolaremos globalmente para el ejemplo.

    // El primer argumento 'mi-multimedia-ajax-script' es un nombre único (handle).
    // El segundo es la ruta a tu archivo JS. Aquí asumimos que lo crearás en /resources/scripts/multimedia-filter.js
    // El último argumento 'true' lo carga en el footer.
    wp_enqueue_script(
        'mi-multimedia-ajax-script',
        get_template_directory_uri() . '/resources/scripts/multimedia-filter.js',
        array(), // Dependencias, como 'jquery' si lo usaras
        '1.0',   // Versión
        true     // Cargar en el footer
    );

    // 2. Localizar el script: Pasamos datos de PHP a JavaScript de forma segura
    wp_localize_script(
        'mi-multimedia-ajax-script', // El mismo handle del script
        'multimedia_ajax_object',    // El nombre del objeto que usaremos en JS
        array(
            'ajax_url' => admin_url('admin-ajax.php'), // La URL estándar de AJAX en WordPress
            'nonce'    => wp_create_nonce('multimedia_filter_nonce') // Nonce de seguridad
        )
    );
}
add_action('wp_enqueue_scripts', 'mi_tema_multimedia_scripts');


// 3. La función que maneja la petición AJAX
function filtrar_multimedia_handler() {
    // Seguridad primero: Verificar el nonce
    if (!check_ajax_referer('multimedia_filter_nonce', 'nonce', false)) {
        wp_send_json_error('Nonce inválido.', 401);
        return;
    }

    // Limpiar la categoría recibida desde JS
    $category_slug = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';

    // Argumentos para la consulta
    $args = array(
        'post_type'      => 'multimedia',
        'posts_per_page' => 6, // Traemos todos para manejar "Ver más" con JS, o puedes paginar aquí.
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Si la categoría no es 'all', añadimos el filtro de taxonomía
    if ($category_slug !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'categoria_multimedia',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        );
    }

    $multimedia_query = new WP_Query($args);

    // Usamos un buffer de salida para capturar todo el HTML
    ob_start();

    if ($multimedia_query->have_posts()) :
        $counter = 0;
        while ($multimedia_query->have_posts()) : $multimedia_query->the_post();
            $counter++;
            $hidden_class = ($counter > 3) ? 'hidden' : ''; // Oculta a partir del 4to item

            // Extraer URL del iframe de ACF
            $iframe_code = get_field('iframe');
            $video_url = '';
            if ($iframe_code) {
                preg_match('/src="([^"]+)"/', $iframe_code, $matches);
                if (isset($matches[1])) {
                    $video_url = $matches[1];
                }
            }
            
            // Obtener imagen destacada
            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$thumbnail_url) {
                $thumbnail_url = get_template_directory_uri() . '/assets/images/default-thumbnail.jpg';
            }
            ?>
            <div class="event-item <?php echo $hidden_class; ?>">
                <div class="relative group cursor-pointer" data-video-src="<?php echo esc_url($video_url); ?>">
                    <div class="aspect-video w-full bg-gray-200 rounded-lg overflow-hidden">
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="font-semibold text-gray-800"><?php the_title(); ?></h3>
                </div>
            </div>
            <?php
        endwhile;
    else :
        echo '<p class="text-center col-span-full">No hay videos para mostrar en esta categoría.</p>';
    endif;
    
    wp_reset_postdata();

    // Capturamos el HTML del buffer y lo enviamos como respuesta JSON
    $html = ob_get_clean();
    wp_send_json_success(array('html' => $html));
}

// Enganchamos la función a las acciones de AJAX de WordPress
add_action('wp_ajax_filtrar_multimedia', __NAMESPACE__ . '\\filtrar_multimedia_handler');       // Para usuarios logueados
add_action('wp_ajax_nopriv_filtrar_multimedia', __NAMESPACE__ . '\\filtrar_multimedia_handler'); // Para usuarios no logueados

// 1. FUNCIÓN PARA MOSTRAR EL CAMPO EN LA PÁGINA DE PERFIL
// Se ejecuta cuando se muestra el perfil de un usuario.
add_action( 'show_user_profile', __NAMESPACE__ . '\\jj_mostrar_campo_telefono_en_perfil' );
add_action( 'edit_user_profile', __NAMESPACE__ . '\\jj_mostrar_campo_telefono_en_perfil' );

function jj_mostrar_campo_telefono_en_perfil( $user ) {
    // Obtenemos el teléfono guardado para este usuario.
    $telefono = get_user_meta( $user->ID, 'phone', true );
    ?>
    <h3>Información Adicional</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone">Número de Teléfono</label></th>
            <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr( $telefono ); ?>" class="regular-text" />
                <p class="description">Número de contacto del usuario.</p>
            </td>
        </tr>
    </table>
    <?php
}

// 2. FUNCIÓN PARA GUARDAR EL CAMPO CUANDO SE ACTUALIZA EL PERFIL
// Se ejecuta cuando el usuario hace clic en "Actualizar perfil".
add_action( 'personal_options_update', __NAMESPACE__ . '\\jj_guardar_campo_telefono_del_perfil' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\jj_guardar_campo_telefono_del_perfil' );

function jj_guardar_campo_telefono_del_perfil( $user_id ) {
    // Verificación de seguridad: ¿el usuario actual tiene permiso?
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    // Si el campo 'phone' fue enviado, lo limpiamos y guardamos.
    if ( isset( $_POST['phone'] ) ) {
        $telefono_sanitizado = sanitize_text_field( $_POST['phone'] );
        update_user_meta( $user_id, 'phone', $telefono_sanitizado );
    }
}

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
        'Entradas Contacto',         // Título del menú
        'manage_options',            // Capacidad requerida para verla
        'entradas-contacto-slug',    // Slug único para la página
        'mi_contenido_pagina_entradas', // Función que renderiza el contenido
        'dashicons-email-alt',       // Ícono del menú
        25                           // Posición en el menú
    );
}

// La función que muestra el contenido de la página (la tabla)
function mi_contenido_pagina_entradas() {
    global $wpdb;
    $nombre_tabla = $wpdb->prefix . 'contactos_entradas';
    $entradas = $wpdb->get_results("SELECT * FROM $nombre_tabla ORDER BY fecha DESC");
    ?>
    <div class="wrap">
        <h1>Entradas del Formulario de Contacto</h1>
        <table class="wp-list-table widefat fixed striped">
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
// 1. AÑADIR LA PÁGINA DE SUBMENÚ PARA LOS REGISTROS
// ======================================================
function add_event_registrations_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=events',       // El slug del menú padre (tu CPT de eventos)
        'Registros de Eventos',            // Título de la página
        'Registros',                       // Título en el menú
        'manage_options',                  // Capacidad requerida para verla
        'event-registrations',             // Slug de esta página de menú
        'render_event_registrations_page'  // Función que mostrará el contenido de la página
    );
}
add_action('admin_menu', __NAMESPACE__ . '\\add_event_registrations_submenu_page');

// 2. FUNCIÓN PARA MOSTRAR EL CONTENIDO DE LA PÁGINA DE REGISTROS
// =================================================================
function render_event_registrations_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>Aquí se listan todos los correos electrónicos registrados para cada evento.</p>

        <?php
        // Preparamos una consulta para obtener todos los eventos
        $events_query = new WP_Query(array(
            'post_type' => 'events',
            'posts_per_page' => -1, // Obtener todos los eventos
            'post_status' => 'publish',
        ));

        if ($events_query->have_posts()) :
            while ($events_query->have_posts()) : $events_query->the_post();
                // Por cada evento, obtenemos los correos registrados en su meta-campo
                $registrations = get_post_meta(get_the_ID(), '_event_registrations', true);

                // Solo mostramos el evento si tiene al menos un registro
                if (!empty($registrations) && is_array($registrations)) {
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php the_title(); ?></span></h2>
                        <div class="inside">
                            <p><strong>Total de registrados: <?php echo count($registrations); ?></strong></p>
                            <ul>
                                <?php foreach ($registrations as $email) : ?>
                                    <li><?php echo esc_html($email); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                }
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>No hay eventos con registros todavía.</p>';
        endif;
        ?>
    </div>
    <?php
}


// 3. ENCOLAR SCRIPTS Y PASAR VARIABLES A JAVASCRIPT
// ====================================================
function enqueue_event_registration_scripts() {
    // Solo cargar este script en las páginas individuales del CPT 'events'
    if (is_singular('events')) {
        // Registra el script. Asegúrate de que la ruta sea correcta.
        // Crea una carpeta 'js' en tu tema y dentro el archivo 'event-registration.js'
        wp_enqueue_script(
            'event-registration-ajax',
            get_stylesheet_directory_uri() . '/resources/scripts/event-registration.js',
            array('jquery'), // Dependencia
            '1.0',           // Versión
            true             // Cargar en el footer
        );

        // Pasamos datos de PHP a JavaScript de forma segura
        wp_localize_script('event-registration-ajax', 'event_reg_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('event_registration_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_event_registration_scripts');


// 4. MANEJADOR AJAX PARA PROCESAR EL REGISTRO
// ===============================================
function handle_event_registration() {
    // 1. Seguridad: Verificar el nonce
    check_ajax_referer('event_registration_nonce', 'nonce');

    // 2. Validar y sanitizar los datos de entrada
    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_send_json_error(array('message' => 'Por favor, introduce un correo electrónico válido.'));
        return;
    }
    if (!isset($_POST['event_id']) || !absint($_POST['event_id'])) {
        wp_send_json_error(array('message' => 'ID de evento no válido.'));
        return;
    }

    $email = sanitize_email($_POST['email']);
    $event_id = absint($_POST['event_id']);

    // 3. Obtener los registros existentes para este evento
    $registrations = get_post_meta($event_id, '_event_registrations', true);
    if (empty($registrations) || !is_array($registrations)) {
        $registrations = array();
    }

    // 4. Comprobar si el correo ya está registrado para evitar duplicados
    if (in_array($email, $registrations)) {
        wp_send_json_error(array('message' => 'Este correo ya está registrado para el evento.'));
        return;
    }

    // 5. Añadir el nuevo correo y actualizar el campo meta
    $registrations[] = $email;
    $success = update_post_meta($event_id, '_event_registrations', $registrations);

    if ($success) {
        wp_send_json_success(array('message' => '¡Gracias! Te has registrado correctamente.'));
    } else {
        wp_send_json_error(array('message' => 'Hubo un error al procesar tu registro. Inténtalo de nuevo.'));
    }
}
// Enganchar la función para usuarios logueados y no logueados
add_action('wp_ajax_register_to_event', __NAMESPACE__ . '\\handle_event_registration');
add_action('wp_ajax_nopriv_register_to_event', __NAMESPACE__ . '\\handle_event_registration');

