<?php
// Archivo temporal para verificar la base de datos de siembras
require_once('../../../wp-load.php');

global $wpdb;
$table = $wpdb->prefix . 'siembras';

echo "=== VERIFICACIÓN DE BASE DE DATOS DE SIEMBRAS ===\n\n";

$exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
echo "Tabla '$table' existe: " . ($exists ? 'SÍ' : 'NO') . "\n";

if ($exists) {
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    echo "Total de registros: $count\n\n";

    if ($count > 0) {
        echo "Últimos 5 registros:\n";
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC LIMIT 5");
        foreach ($results as $row) {
            echo "ID: {$row->id} | Fecha: {$row->dia_pago} | Nombre: {$row->nombre_completo} | Monto: {$row->monto}\n";
        }
    }

    echo "\nEstructura de la tabla:\n";
    $columns = $wpdb->get_results("DESCRIBE $table");
    foreach ($columns as $col) {
        echo "- {$col->Field}: {$col->Type}\n";
    }
} else {
    echo "La tabla no existe. Intentando crearla...\n";

    // Intentar crear la tabla
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table (
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
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $created = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    echo "Tabla creada: " . ($created ? 'SÍ' : 'NO') . "\n";
}

echo "\n=== FIN DE VERIFICACIÓN ===\n";