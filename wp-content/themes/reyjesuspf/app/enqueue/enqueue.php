<?php
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
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\registrar_mis_scripts_de_eventos');
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
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\mi_tema_multimedia_scripts');