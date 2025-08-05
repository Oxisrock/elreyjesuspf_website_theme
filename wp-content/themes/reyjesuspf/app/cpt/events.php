<?php

namespace App;

/**
 * Registra el Custom Post Type para los Eventos y su taxonomía.
 */
add_action('init', function () {
    // REGISTRO DEL CUSTOM POST TYPE 'EVENTS'
    register_post_type('events', [
        'labels' => [
            'name' => __('Eventos', 'sage'),
            'singular_name' => __('Evento', 'sage'),
            'add_new_item' => __('Añadir Nuevo Evento', 'sage'),
            'edit_item' => __('Editar Evento', 'sage'),
            'new_item' => __('Nuevo Evento', 'sage'),
            'view_item' => __('Ver Evento', 'sage'),
            'search_items' => __('Buscar Eventos', 'sage'),
            'not_found' => __('No se encontraron eventos', 'sage'),
            'not_found_in_trash' => __('No se encontraron eventos en la papelera', 'sage'),
        ],
        'public' => true,
        'has_archive' => true, // Activa la vista de archivo en /eventos
        'rewrite' => ['slug' => 'eventos'], // La URL base para los eventos
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'], // Campos que soportará
        'menu_position' => 5,
        'menu_icon' => 'dashicons-calendar-alt', // Ícono en el menú de WP Admin
        'show_in_rest' => true, // Importante para el editor de bloques (Gutenberg)
    ]);

    // REGISTRO DE LA NUEVA TAXONOMÍA PARA 'EVENTS'
    register_taxonomy('tipo_evento', 'events', [ // El segundo parámetro 'events' la asocia al CPT
        'labels' => [
            'name' => __('Tipos de Evento', 'sage'),
            'singular_name' => __('Tipo de Evento', 'sage'),
            'search_items' => __('Buscar Tipos de Evento', 'sage'),
            'all_items' => __('Todos los Tipos', 'sage'),
            'parent_item' => __('Tipo de Evento Padre', 'sage'),
            'parent_item_colon' => __('Tipo de Evento Padre:', 'sage'),
            'edit_item' => __('Editar Tipo de Evento', 'sage'),
            'update_item' => __('Actualizar Tipo de Evento', 'sage'),
            'add_new_item' => __('Añadir Nuevo Tipo de Evento', 'sage'),
            'new_item_name' => __('Nombre del Nuevo Tipo de Evento', 'sage'),
            'menu_name' => __('Tipos de Evento', 'sage'),
        ],
        'hierarchical' => true, // True para que se comporte como categoría (con jerarquía)
        'public' => true,
        'show_in_rest' => true, // Habilita la taxonomía en el editor de bloques
        'rewrite' => ['slug' => 'tipo-de-evento'], // URL amigable para los archivos de la taxonomía
        'show_admin_column' => true, // Muestra la taxonomía en la lista de Eventos del admin
    ]);
});