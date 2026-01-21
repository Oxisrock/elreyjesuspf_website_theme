<?php
/**
 * Archivo de logging para debuggear el formulario de siembra
 * Incluye este archivo en siembra.php para activar el logging
 */

// Función para escribir logs
function siembra_log($message, $data = null) {
    $log_file = WP_CONTENT_DIR . '/siembra_debug.log';

    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message";

    if ($data !== null) {
        $log_entry .= "\nData: " . print_r($data, true);
    }

    $log_entry .= "\n" . str_repeat('-', 50) . "\n";

    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Función para verificar si el logging está activo
function siembra_debug_active() {
    return defined('SIEMBRA_DEBUG') && SIEMBRA_DEBUG === true;
}

// Si el debug está activo, agregar logging automático
if (siembra_debug_active()) {
    // Log cuando se carga el archivo
    siembra_log("Archivo siembra.php cargado");

    // Log cuando se ejecuta la función
    add_action('admin_post_nopriv_procesar_formulario_siembra', function() {
        siembra_log("Hook admin_post_nopriv_procesar_formulario_siembra ejecutado");
    }, 1);

    add_action('admin_post_procesar_formulario_siembra', function() {
        siembra_log("Hook admin_post_procesar_formulario_siembra ejecutado");
    }, 1);
}
?>