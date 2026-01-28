<?php
/**
 * Sistema de Logging Avanzado para Formulario de Siembra
 * Captura todos los eventos críticos del proceso de envío
 */

// Configuración del logging
define('SIEMBRA_DEBUG_ACTIVE', true);
define('SIEMBRA_LOG_FILE', WP_CONTENT_DIR . '/siembra-debug.log');
define('SIEMBRA_MAX_LOG_SIZE', 5 * 1024 * 1024); // 5MB

// Función principal de logging
if (!function_exists('siembra_log')) {
    function siembra_log($message, $level = 'INFO', $context = null) {
        if (!SIEMBRA_DEBUG_ACTIVE) {
            return false;
        }

        // Crear directorio si no existe
        $log_dir = dirname(SIEMBRA_LOG_FILE);
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }

        // Rotar log si es muy grande
        if (file_exists(SIEMBRA_LOG_FILE) && filesize(SIEMBRA_LOG_FILE) > SIEMBRA_MAX_LOG_SIZE) {
            $backup_file = SIEMBRA_LOG_FILE . '.' . date('Y-m-d-H-i-s') . '.bak';
            rename(SIEMBRA_LOG_FILE, $backup_file);
        }

        // Crear archivo si no existe
        if (!file_exists(SIEMBRA_LOG_FILE)) {
            touch(SIEMBRA_LOG_FILE);
            if (function_exists('chmod')) {
                chmod(SIEMBRA_LOG_FILE, 0666);
            }
        }

        $timestamp = current_time('Y-m-d H:i:s');
        $microtime = microtime(true);
        $memory_usage = memory_get_usage(true) / 1024 / 1024; // MB

        $log_entry = sprintf(
            "[%s] [%.4f] [%s] [MEM: %.2fMB] %s\n",
            $timestamp,
            $microtime,
            str_pad($level, 8),
            $memory_usage,
            $message
        );

        // Agregar contexto si existe
        if ($context !== null) {
            if (is_array($context) || is_object($context)) {
                $log_entry .= "CONTEXT: " . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                $log_entry .= "CONTEXT: $context\n";
            }
        }

        $log_entry .= str_repeat('=', 80) . "\n";

        // Escribir al archivo
        $result = file_put_contents(SIEMBRA_LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);

        // También escribir a error_log de PHP para errores críticos
        if ($level === 'ERROR' || $level === 'CRITICAL') {
            error_log("SIEMBRA $level: $message");
        }

        return $result !== false;
    }
}

// Función para logging de datos estructurado
if (!function_exists('siembra_log_data')) {
    function siembra_log_data($data, $label = 'DATA') {
        if (is_array($data) || is_object($data)) {
            siembra_log("$label received", 'DEBUG', $data);
        } else {
            siembra_log("$label: " . (string)$data, 'DEBUG');
        }
    }
}

// Función para logging de errores con stack trace
if (!function_exists('siembra_log_error')) {
    function siembra_log_error($message, $exception = null, $context = null) {
        $error_data = [
            'message' => $message,
            'file' => __FILE__,
            'line' => __LINE__,
        ];

        if ($exception) {
            $error_data['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        if ($context) {
            $error_data['context'] = $context;
        }

        siembra_log($message, 'ERROR', $error_data);

        // Enviar alerta por email para errores críticos
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $admin_email = get_option('admin_email');
            $subject = '🚨 ERROR CRÍTICO - Formulario de Siembra';
            $body = "Se ha producido un error crítico en el formulario de siembra:\n\n";
            $body .= "Mensaje: $message\n";
            $body .= "Archivo: " . __FILE__ . "\n";
            $body .= "Línea: " . __LINE__ . "\n";
            $body .= "Timestamp: " . current_time('Y-m-d H:i:s') . "\n\n";

            if ($exception) {
                $body .= "Excepción: " . $exception->getMessage() . "\n";
                $body .= "Stack Trace:\n" . $exception->getTraceAsString() . "\n\n";
            }

            if ($context) {
                $body .= "Contexto:\n" . print_r($context, true) . "\n";
            }

            wp_mail($admin_email, $subject, $body);
        }
    }
}

// Función para verificar estado del sistema
if (!function_exists('siembra_log_system_status')) {
    function siembra_log_system_status() {
        siembra_log("=== VERIFICACIÓN DEL SISTEMA ===", 'SYSTEM');

        // Verificar conexión a base de datos
        global $wpdb;
        try {
            $db_connected = $wpdb->check_connection();
            siembra_log("Base de datos: " . ($db_connected ? "CONECTADA ✓" : "DESCONECTADA ✗"), 'SYSTEM');
        } catch (Exception $e) {
            siembra_log("Base de datos: ERROR - " . $e->getMessage(), 'ERROR');
        }

        // Verificar tabla siembras
        try {
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}siembras'");
            siembra_log("Tabla siembras: " . ($table_exists ? "EXISTE ✓" : "NO EXISTE ✗"), 'SYSTEM');

            if ($table_exists) {
                $row_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}siembras");
                siembra_log("Registros en tabla: $row_count", 'SYSTEM');
            }
        } catch (Exception $e) {
            siembra_log("Tabla siembras: ERROR - " . $e->getMessage(), 'ERROR');
        }

        // Verificar permisos de escritura
        $can_write = is_writable(SIEMBRA_LOG_FILE) ||
                    (!file_exists(SIEMBRA_LOG_FILE) && is_writable(dirname(SIEMBRA_LOG_FILE)));
        siembra_log("Permisos de escritura: " . ($can_write ? "OK ✓" : "ERROR ✗"), 'SYSTEM');

        // Verificar configuración de email
        $admin_email = get_option('admin_email');
        siembra_log("Email admin configurado: " . ($admin_email ? "SÍ ($admin_email)" : "NO"), 'SYSTEM');

        // Verificar PHP y WordPress
        siembra_log("PHP Version: " . PHP_VERSION, 'SYSTEM');
        siembra_log("WordPress Version: " . get_bloginfo('version'), 'SYSTEM');
        siembra_log("WP_DEBUG: " . (defined('WP_DEBUG') && WP_DEBUG ? "ACTIVADO" : "DESACTIVADO"), 'SYSTEM');

        // Verificar reCAPTCHA
        $recaptcha_secret = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '6LfflFgsAAAAAJJanViKSxJVzrWo33zThlxu5KdO';
        siembra_log("reCAPTCHA configurado: " . (!empty($recaptcha_secret) ? "SÍ" : "NO"), 'SYSTEM');

        siembra_log("=== FIN VERIFICACIÓN DEL SISTEMA ===", 'SYSTEM');
    }
}

// Función para log de rendimiento
if (!function_exists('siembra_log_performance')) {
    function siembra_log_performance($operation, $start_time, $end_time = null) {
        if ($end_time === null) {
            $end_time = microtime(true);
        }

        $duration = ($end_time - $start_time) * 1000; // Convertir a milisegundos
        siembra_log("PERFORMANCE: $operation tomó " . number_format($duration, 2) . "ms", 'PERF');
    }
}

// Función para log de requests HTTP
if (!function_exists('siembra_log_http_request')) {
    function siembra_log_http_request($url, $method = 'GET', $response_code = null, $error = null) {
        $status = $response_code ? "HTTP $response_code" : ($error ? "ERROR: $error" : "UNKNOWN");
        siembra_log("HTTP REQUEST: $method $url - $status", $error ? 'ERROR' : 'DEBUG');
    }
}

// Inicialización del logging
siembra_log("=== SISTEMA DE LOGGING INICIALIZADO ===", 'SYSTEM');
siembra_log("Archivo: " . __FILE__, 'SYSTEM');
siembra_log("Timestamp: " . current_time('Y-m-d H:i:s T'), 'SYSTEM');
siembra_log_system_status();

// Función para mostrar logs en el navegador (solo para administradores)
if (!function_exists('siembra_show_logs_page')) {
    function siembra_show_logs_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Acceso denegado');
        }

        $logs = siembra_get_recent_logs(100);
        $log_file = SIEMBRA_LOG_FILE;
        $file_exists = file_exists($log_file);
        $file_size = $file_exists ? filesize($log_file) : 0;
        $file_size_mb = round($file_size / 1024 / 1024, 2);

        ?>
        <div class="wrap">
            <h1>Logs del Formulario de Siembra</h1>

            <div class="notice notice-info">
                <p><strong>Archivo de logs:</strong> <?php echo esc_html($log_file); ?></p>
                <p><strong>Tamaño:</strong> <?php echo $file_size_mb; ?> MB</p>
                <p><strong>Estado:</strong> <?php echo $file_exists ? '✅ Archivo existe' : '❌ Archivo no encontrado'; ?></p>
            </div>

            <div style="margin: 20px 0;">
                <a href="<?php echo admin_url('admin.php?page=siembra-logs&action=clear'); ?>" class="button button-secondary"
                   onclick="return confirm('¿Estás seguro de que quieres limpiar los logs?')">Limpiar Logs Antiguos</a>

                <a href="<?php echo admin_url('admin.php?page=siembra-logs&action=download'); ?>" class="button button-primary">Descargar Logs</a>

                <a href="<?php echo admin_url('admin.php?page=siembra-logs&action=system-check'); ?>" class="button button-info">Verificar Sistema</a>
            </div>

            <?php if (!empty($logs)): ?>
                <h2>Últimos 100 Registros</h2>
                <div style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-height: 600px; overflow-y: auto; font-family: monospace; font-size: 12px; white-space: pre-wrap;">
                    <?php echo esc_html($logs); ?>
                </div>
            <?php else: ?>
                <div class="notice notice-warning">
                    <p>No hay logs disponibles o el archivo está vacío.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Función para manejar acciones de logs
if (!function_exists('siembra_handle_log_actions')) {
    function siembra_handle_log_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['page']) && $_GET['page'] === 'siembra-logs' && isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'clear':
                    $deleted = siembra_clean_old_logs(1); // Limpiar logs de más de 1 día
                    add_action('admin_notices', function() use ($deleted) {
                        echo '<div class="notice notice-success"><p>Se eliminaron ' . $deleted . ' archivos de log antiguos.</p></div>';
                    });
                    wp_redirect(admin_url('admin.php?page=siembra-logs'));
                    exit;

                case 'download':
                    if (file_exists(SIEMBRA_LOG_FILE)) {
                        header('Content-Type: text/plain');
                        header('Content-Disposition: attachment; filename="siembra-debug-' . date('Y-m-d-H-i-s') . '.log"');
                        header('Content-Length: ' . filesize(SIEMBRA_LOG_FILE));
                        readfile(SIEMBRA_LOG_FILE);
                        exit;
                    }
                    break;

                case 'system-check':
                    siembra_log_system_status();
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-success"><p>Verificación del sistema completada. Revisa los logs para ver los resultados.</p></div>';
                    });
                    wp_redirect(admin_url('admin.php?page=siembra-logs'));
                    exit;
            }
        }
    }
    add_action('admin_init', 'siembra_handle_log_actions');
}

// Agregar menú de logs
if (!function_exists('siembra_add_logs_menu')) {
    function siembra_add_logs_menu() {
        add_submenu_page(
            'siembras',
            'Logs de Siembra',
            'Ver Logs',
            'manage_options',
            'siembra-logs',
            'siembra_show_logs_page'
        );
    }
    add_action('admin_menu', 'siembra_add_logs_menu');
}