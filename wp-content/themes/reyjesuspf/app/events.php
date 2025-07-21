<?php

namespace App;

/**
 * Registra el Custom Post Type para los Eventos.
 */
add_action('init', function () {
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
        'has_archive' => true, /* Activa la vista de archivo en /eventos */
        'rewrite' => ['slug' => 'eventos'], /* La URL base para los eventos */
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'], /* Campos que soportará */
        'menu_position' => 5,
        'menu_icon' => 'dashicons-calendar-alt', /* Ícono en el menú de WP Admin */
        'show_in_rest' => true, /* Importante para el editor de bloques (Gutenberg) */
    ]);
});