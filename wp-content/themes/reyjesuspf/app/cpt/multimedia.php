<?php

namespace App;
function crear_cpt_multimedia() {

    // ETIQUETAS PARA EL CUSTOM POST TYPE 'MULTIMEDIA'
    $labels_cpt = array(
        'name'                  => _x( 'Multimedia', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Multimedia', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Multimedia', 'text_domain' ),
        'name_admin_bar'        => __( 'Multimedia', 'text_domain' ),
        'archives'              => __( 'Archivos de Multimedia', 'text_domain' ),
        'attributes'            => __( 'Atributos de Multimedia', 'text_domain' ),
        'parent_item_colon'     => __( 'Multimedia Padre:', 'text_domain' ),
        'all_items'             => __( 'Todo el Multimedia', 'text_domain' ),
        'add_new_item'          => __( 'Añadir Nuevo Multimedia', 'text_domain' ),
        'add_new'               => __( 'Añadir Nuevo', 'text_domain' ),
        'new_item'              => __( 'Nuevo Multimedia', 'text_domain' ),
        'edit_item'             => __( 'Editar Multimedia', 'text_domain' ),
        'update_item'           => __( 'Actualizar Multimedia', 'text_domain' ),
        'view_item'             => __( 'Ver Multimedia', 'text_domain' ),
        'view_items'            => __( 'Ver Multimedia', 'text_domain' ),
        'search_items'          => __( 'Buscar Multimedia', 'text_domain' ),
        'not_found'             => __( 'No encontrado', 'text_domain' ),
        'not_found_in_trash'    => __( 'No encontrado en la Papelera', 'text_domain' ),
        'featured_image'        => __( 'Imagen Destacada', 'text_domain' ),
        'set_featured_image'    => __( 'Establecer Imagen Destacada', 'text_domain' ),
        'remove_featured_image' => __( 'Quitar Imagen Destacada', 'text_domain' ),
        'use_featured_image'    => __( 'Usar como Imagen Destacada', 'text_domain' ),
        'insert_into_item'      => __( 'Insertar en Multimedia', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Subido a este elemento de Multimedia', 'text_domain' ),
        'items_list'            => __( 'Lista de Multimedia', 'text_domain' ),
        'items_list_navigation' => __( 'Navegación de la lista de Multimedia', 'text_domain' ),
        'filter_items_list'     => __( 'Filtrar lista de Multimedia', 'text_domain' ),
    );

    // ARGUMENTOS PARA EL CUSTOM POST TYPE 'MULTIMEDIA'
    $args_cpt = array(
        'label'                 => __( 'Multimedia', 'text_domain' ),
        'description'           => __( 'Contenido para audios, videos, galerías, etc.', 'text_domain' ),
        'labels'                => $labels_cpt,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-video', // Ícono de WordPress
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Habilitar el editor de bloques (Gutenberg)
    );
    // REGISTRAR EL CUSTOM POST TYPE
    register_post_type( 'multimedia', $args_cpt );


    // ETIQUETAS PARA LA TAXONOMÍA 'CATEGORÍA DE MULTIMEDIA'
    $labels_tax = array(
        'name'              => _x( 'Categorías de Multimedia', 'taxonomy general name', 'text_domain' ),
        'singular_name'     => _x( 'Categoría de Multimedia', 'taxonomy singular name', 'text_domain' ),
        'search_items'      => __( 'Buscar Categorías', 'text_domain' ),
        'all_items'         => __( 'Todas las Categorías', 'text_domain' ),
        'parent_item'       => __( 'Categoría Padre', 'text_domain' ),
        'parent_item_colon' => __( 'Categoría Padre:', 'text_domain' ),
        'edit_item'         => __( 'Editar Categoría', 'text_domain' ),
        'update_item'       => __( 'Actualizar Categoría', 'text_domain' ),
        'add_new_item'      => __( 'Añadir Nueva Categoría', 'text_domain' ),
        'new_item_name'     => __( 'Nombre de Nueva Categoría', 'text_domain' ),
        'menu_name'         => __( 'Categorías', 'text_domain' ),
    );

    // ARGUMENTOS PARA LA TAXONOMÍA 'CATEGORÍA DE MULTIMEDIA'
    $args_tax = array(
        'hierarchical'      => true, // Esto la hace funcionar como una categoría
        'labels'            => $labels_tax,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'categoria-multimedia' ), // URL amigable
        'show_in_rest'      => true, // Habilitar en el editor de bloques
    );
    // REGISTRAR LA TAXONOMÍA Y ASOCIARLA AL CPT 'MULTIMEDIA'
    register_taxonomy( 'categoria_multimedia', array( 'multimedia' ), $args_tax );

}
// ENGANCHAR LA FUNCIÓN AL HOOK 'init' DE WORDPRESS
add_action( 'init', __NAMESPACE__ . '\\crear_cpt_multimedia', 0 );

/**
 * Actualizar los permalinks al activar el tema para evitar errores 404.
 */
function flush_rewrite_rules_on_activate() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'flush_rewrite_rules_on_activate' );