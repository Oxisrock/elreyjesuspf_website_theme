<?php

/**
 * Theme setup.
 */

namespace App;

use function Roots\bundle;

/**
 * Register the theme assets.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    bundle('app')->enqueue();
}, 100);

/**
 * Register the theme assets with the block editor.
 *
 * @return void
 */
add_action('enqueue_block_editor_assets', function () {
    bundle('editor')->enqueue();
}, 100);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'primary_mobile_navigation' => __('Primary Mobile Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});
/**
 * Cargar archivos personalizados.
 */
require_once get_template_directory() . '/app/cpt/events.php';
require_once get_template_directory() . '/app/cpt/multimedia.php';
require_once get_template_directory() . '/app/cpt/fap.php';


function handle_ajax_custom_registration() {
    // 1. Verificar Nonce de seguridad
    if (!check_ajax_referer('custom_register_nonce', 'security_nonce', false)) {
        wp_send_json_error(['errors' => ['form' => 'Error de seguridad. Por favor, recarga la página.']]);
    }

    // 2. Sanitizar y validar los datos de entrada
    $full_name = sanitize_text_field($_POST['full_name'] ?? '');
    if (empty($full_name)) {
        wp_send_json_error(['errors' => ['full_name' => 'El nombre completo es obligatorio.']]);
    }

    $phone = sanitize_text_field($_POST['phone'] ?? '');

    $email = sanitize_email($_POST['email'] ?? '');
    if (empty($email)) {
        wp_send_json_error(['errors' => ['email' => 'El correo electrónico es obligatorio.']]);
    }
    if (!is_email($email)) {
        wp_send_json_error(['errors' => ['email' => 'El formato del correo no es válido.']]);
    }
    if (email_exists($email)) {
        $login_url = home_url('/login');
        $recovery_url = home_url('/recuperar-clave');
        $error_message = sprintf(
            '<a href="%s" class="font-semibold underline">Inicia sesión</a> o <a href="%s" class="font-semibold underline">recupera tu contraseña</a>.',
            esc_url($login_url),
            esc_url($recovery_url)
        );
        wp_send_json_error([
            'error_type' => 'duplicate_email',
            'message' => $error_message
        ]);
    }

    $password = $_POST['password'] ?? '';
    if (strlen($password) < 8) {
        wp_send_json_error(['errors' => ['password' => 'La contraseña debe tener al menos 8 caracteres.']]);
    }

    $password_confirm = $_POST['password_confirm'] ?? '';
    if ($password !== $password_confirm) {
        wp_send_json_error(['errors' => ['password_confirm' => 'Las contraseñas no coinciden.']]);
    }

    if (!isset($_POST['terms'])) {
        wp_send_json_error(['errors' => ['terms' => 'Debes aceptar los Términos y Condiciones.']]);
    }

    // =================================================================
    // INICIO DE LOS CAMBIOS MÁS IMPORTANTES
    // =================================================================

    // 3. Preparar los datos del usuario a partir del nombre completo
    $name_parts = explode(' ', trim($full_name));
    $first_name = array_shift($name_parts); // Extrae el primer nombre: "Jonil"
    $last_name = !empty($name_parts) ? implode(' ', $name_parts) : ''; // El resto es el apellido: "Josue Peña Garcia"

    // 4. Crear el usuario USANDO EL EMAIL como nombre de usuario
    // Esta era tu lógica original para la creación, y la restauramos.
    $user_id = wp_create_user($email, $password, $email);

    if (is_wp_error($user_id)) {
        // En este caso, el error podría ser "nombre de usuario ya existe", que es lo mismo que "email ya existe".
        // La validación previa con email_exists() ya debería haber manejado esto.
        wp_send_json_error(['errors' => ['form' => $user_id->get_error_message()]]);
    }

    // 5. Actualizar los campos del perfil (Nombre, Apellidos, Alias)
    // Esta parte se mantiene para que el perfil se vea como deseas.
    wp_update_user([
        'ID'           => $user_id,
        'first_name'   => $first_name,       // Llena el campo "Nombre"
        'last_name'    => $last_name,        // Llena el campo "Apellidos"
        'nickname'     => $first_name,       // Llena el campo "Alias (obligatorio)"
        'display_name' => $full_name,        // Nombre a mostrar públicamente
    ]);
    
    // Añadir metadatos extra como el teléfono
    update_user_meta($user_id, 'phone', $phone);
    update_user_meta($user_id, 'full_name', $full_name);
    

    // =================================================================
    // FIN DE LOS CAMBIOS
    // =================================================================

    // 6. Si todo fue bien, enviar una respuesta de éxito.
    wp_send_json_success(['message' => '¡Registro completado con éxito!']);
}

// Los hooks se mantienen igual
add_action('wp_ajax_nopriv_ajax_custom_register', __NAMESPACE__ . '\\handle_ajax_custom_registration');
add_action('wp_ajax_ajax_custom_register', __NAMESPACE__ . '\\handle_ajax_custom_registration');