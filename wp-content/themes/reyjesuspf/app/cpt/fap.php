<?php
/**
 * Registrar un Custom Post Type para Preguntas Frecuentes (FAQ).
 */

// Se comprueba primero si la función ya existe para evitar errores fatales.
if ( ! function_exists( 'crear_cpt_faq' ) ) {

    function crear_cpt_faq() {

        $labels = array(
            'name'                  => _x( 'Preguntas Frecuentes', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Pregunta Frecuente', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'FAQs', 'text_domain' ),
            'name_admin_bar'        => __( 'Pregunta Frecuente', 'text_domain' ),
            'archives'              => __( 'Archivo de FAQs', 'text_domain' ),
            'attributes'            => __( 'Atributos de FAQ', 'text_domain' ),
            'parent_item_colon'     => __( 'FAQ Padre:', 'text_domain' ),
            'all_items'             => __( 'Todas las FAQs', 'text_domain' ),
            'add_new_item'          => __( 'Añadir Nueva FAQ', 'text_domain' ),
            'add_new'               => __( 'Añadir Nueva', 'text_domain' ),
            'new_item'              => __( 'Nueva FAQ', 'text_domain' ),
            'edit_item'             => __( 'Editar FAQ', 'text_domain' ),
            'update_item'           => __( 'Actualizar FAQ', 'text_domain' ),
            'view_item'             => __( 'Ver FAQ', 'text_domain' ),
            'view_items'            => __( 'Ver FAQs', 'text_domain' ),
            'search_items'          => __( 'Buscar FAQ', 'text_domain' ),
        );
        $args = array(
            'label'                 => __( 'Pregunta Frecuente', 'text_domain' ),
            'description'           => __( 'Repositorio de preguntas y respuestas frecuentes', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'revisions' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-editor-help',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );
        register_post_type( 'faq', $args );

    }

    add_action( 'init', 'crear_cpt_faq', 0 );

}