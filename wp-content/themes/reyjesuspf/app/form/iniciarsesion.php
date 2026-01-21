<?php
// Enganchamos nuestra función al gestor de acciones POST de WordPress.
add_action('admin_post_nopriv_custom_login', __NAMESPACE__ . '\\handle_custom_login_form');
add_action('admin_post_custom_login', __NAMESPACE__ . '\\handle_custom_login_form');

function handle_custom_login_form()
{
    // Primero, verificamos que la petición viene de nuestro formulario.
    if (!isset($_POST['login_submit'])) {
        wp_safe_redirect(home_url('/')); // Redirige si se accede directamente.
        exit;
    }

    // Verificar nonce
    if (!wp_verify_nonce($_POST['login_nonce'], 'custom_login_nonce')) {
        wp_safe_redirect(add_query_arg('login_error', 'nonce_fail', home_url('/login')));
        exit;
    }

    // ======================================================
    // 1. VERIFICACIÓN DE GOOGLE RECAPTCHA V3
    // ======================================================
    $recaptcha_secret_key = '6LePAbwrAAAAAGT4G4s6FngmaTEK3O0UdPqGfOfT'; // Tu clave secreta está segura aquí.
    $recaptcha_token = isset($_POST['recaptcha_response']) ? $_POST['recaptcha_response'] : '';
    $login_page_url = home_url('/login'); // URL de tu página de login, ajústala si es diferente.

    if (empty($recaptcha_token)) {
        wp_safe_redirect(add_query_arg('login_error', 'recaptcha_token', $login_page_url));
        exit;
    }

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = ['secret' => $recaptcha_secret_key, 'response' => $recaptcha_token];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $response_json = file_get_contents($url, false, $context);
    $response_data = json_decode($response_json);

    // ⚠️ INICIO DE LA MODIFICACIÓN PARA DESARROLLO LOCAL ⚠️

    // Primero, verificamos si la comunicación con Google fue exitosa en general.
    if (!$response_data || !$response_data->success) {
        // Si la comunicación falla, redirigimos con un error.
        wp_safe_redirect(add_query_arg('login_error', 'recaptcha_fail', $login_page_url));
        exit;
    }

    // Ahora, definimos si estamos en un entorno local.
    $is_local_environment = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    // La validación del SCORE solo se aplica si NO estamos en el entorno local.
    if (!$is_local_environment && $response_data->score < 0.5) {
        // Si un usuario real (no local) tiene un score bajo, lo redirigimos con error.
        wp_safe_redirect(add_query_arg('login_error', 'recaptcha_fail', $login_page_url));
        exit;
    }

    // Si el código llega aquí, el reCAPTCHA es válido.

    // ⚠️ FIN DE LA MODIFICACIÓN ⚠️

    // ======================================================
    // 2. LÓGICA DE LOGIN DE WORDPRESS
    // ======================================================
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        wp_safe_redirect(add_query_arg('login_error', 'empty_fields', $login_page_url));
        exit;
    }

    $creds = [
        'user_login'    => $email,
        'user_password' => $password,
        'remember'      => true,
    ];

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error_code = $user->get_error_code();
        // Redirigimos de vuelta con el código de error específico.
        wp_safe_redirect(add_query_arg('login_error', $error_code, $login_page_url));
        exit;
    } else {
        // Si el inicio de sesión es exitoso, redirigimos al homepage donde se mostrará el usuario logueado.
        wp_safe_redirect(home_url('/'));
        exit;
    }
}
