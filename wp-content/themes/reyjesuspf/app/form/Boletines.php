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

    // 2. Limpiamos y sanitizamos los datos recibidos del formulario
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
    $boletines = $wpdb->get_results("SELECT * FROM $nombre_tabla_boletines ORDER BY fecha DESC");
    ?>
    <div class="wrap">
        <h1>Suscripciones al Boletín</h1>
        <table id="miTabla" class="widefat striped" style="width:100%">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($boletines)) : ?>
                    <tr>
                        <td colspan="3">No hay suscriptores todavía.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($boletines as $boletine) : ?>
                        <tr>
                            <td><?php echo esc_html($boletine->fecha); ?></td>
                            <td><?php echo esc_html($boletine->nombre); ?></td>
                            <td><?php echo esc_html($boletine->email); ?></td>
                            <td><?php echo esc_html($boletine->email); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>