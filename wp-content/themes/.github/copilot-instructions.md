# Instrucciones para Agentes de Codificación AI para el Tema de WordPress El Rey Jesús PF

## Resumen del Proyecto
Este es un tema de WordPress construido sobre el framework [Sage](https://roots.io/sage/), integrando componentes de Laravel a través de [Acorn](https://github.com/roots/acorn). Utiliza herramientas frontend modernas con [Bud](https://bud.js.org/) para compilación de assets y [Tailwind CSS](https://tailwindcss.com/) para estilos.

## Arquitectura y Componentes Clave

### Estructura Central
- **`app/`**: Lógica de aplicación PHP con organización estilo Laravel
  - `setup.php`: Inicialización del tema, encolado de assets, registro de menús
  - `ajax.php`: Manejadores de endpoints AJAX para carga dinámica de contenido (ej. filtrado de eventos)
  - `filters.php`: Hooks de filtros de WordPress
  - `cpt/`: Definiciones de tipos de post personalizados (events, multimedia, fap) con `register_post_type()` y taxonomías asociadas
  - `enqueue/`: Lógica de encolado de assets con `wp_localize_script` para paso de datos a JS
  - `form/`: Manejadores de formularios personalizados con creación de tablas de base de datos vía `dbDelta()`
  - `Providers/`: Proveedores de servicios extendiendo SageServiceProvider
  - `View/Composers/`: Composers de vistas Blade de Laravel para inyección de datos
  - `Walkers/`: Walkers de menú personalizados

- **`resources/`**: Assets fuente y plantillas
  - `views/`: Plantillas Blade de Laravel con patrón `@yield('content')` e integración de campos ACF
  - `scripts/`: Archivos JavaScript (Alpine.js para interacciones, manejadores AJAX)
  - `styles/`: CSS/SCSS con clases Tailwind
  - `images/` & `fonts/`: Assets estáticos

- **`public/`**: Assets compilados con nombres de archivo hasheados (generados por Bud)

### Patrones y Convenciones Clave

#### Tipos de Post Personalizados
Registra CPTs en archivos `app/cpt/` con `register_post_type()` y taxonomías asociadas. Siempre incluye `'show_in_rest' => true` para compatibilidad con el editor de bloques.

**Ejemplo de `app/cpt/events.php`:**
```php
register_post_type('events', [
    'labels' => [...],
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'eventos'],
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    'show_in_rest' => true, // Esencial para Gutenberg
]);
```

#### Implementación AJAX
- Encola scripts en `app/enqueue/` usando `wp_enqueue_script()` y `wp_localize_script()`
- Pasa `admin_url('admin-ajax.php')` como `ajax_url` a JavaScript
- Maneja solicitudes en `app/ajax.php` con hooks `wp_ajax_` y `wp_ajax_nopriv_`
- Usa nonces para seguridad: `wp_create_nonce()` y `wp_verify_nonce()`
- Usa `ob_start()` para capturar salida HTML en respuestas AJAX

**Ejemplo de `app/enqueue/enqueue.php`:**
```php
wp_enqueue_script('eventos-ajax-filter', get_template_directory_uri() . '/resources/scripts/eventos-filter.js', ['jquery'], '1.0', true);
wp_localize_script('eventos-ajax-filter', 'eventos_ajax_obj', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('eventos_filter_nonce')
]);
```

#### Formularios y Base de Datos
- Formularios personalizados crean tablas usando `dbDelta()` en funciones de activación
- Procesa envíos vía hooks `admin_post_` en `app/form/`
- Sanitiza con `sanitize_text_field()`, `sanitize_email()`, `sanitize_textarea_field()`
- Almacena en tablas personalizadas con `$wpdb->insert()`
- Usa campos ACF extensivamente: `the_field()`, `get_field()`

**Ejemplo de acción de formulario:**
```php
add_action('admin_post_procesar_formulario_siembra', function() {
    check_admin_referer('mi_form_siembra_nonce', 'mi_nonce');
    // Procesa datos del formulario
    wp_redirect(add_query_arg('enviado', 'true', wp_get_referer()));
});
```

#### Composers de Vistas
- Extiende `Roots\Acorn\View\Composer` en `app/View/Composers/`
- Define array `$views` para plantillas aplicables
- Retorna array de datos desde método `with()`
- Disponible en plantillas Blade como variables

#### Gestión de Assets
- Usa aliases `@scripts/` y `@styles/` de Bud en archivos de entrada
- Compila con `npm run build` o `npm run dev` (modo watch)
- Encola bundles con `bundle('app')->enqueue()` en `app/setup.php`

#### Plantillas
- Layout principal en `resources/views/layouts/app.blade.php` con Alpine.js CDN
- Incluye secciones: `@include('sections.header')`, `@include('sections.footer')`
- Usa `@yield('content')` para contenido de página
- Alpine.js cargado vía CDN en head del layout para componentes reactivos

## Flujo de Desarrollo

### Configuración
```bash
composer install  # Instala dependencias PHP (Acorn, etc.)
npm install       # Instala dependencias Node (Bud, Tailwind)
npm run dev       # Inicia servidor de desarrollo con observación de archivos
```

### Comandos de Build
```bash
npm run build     # Build de producción con optimización de assets
npm run dev       # Build de desarrollo con observación y recarga en caliente
```

### Traducción
```bash
npm run translate:pot     # Genera archivo POT desde archivos del tema
npm run translate:update  # Actualiza archivos PO existentes
npm run translate:compile # Compila archivos MO para runtime
```

### Archivos Clave de Referencia
- `bud.config.js`: Configuración de build y puntos de entrada (bundles app, editor)
- `tailwind.config.js`: Personalización de Tailwind
- `composer.json`: Dependencias PHP (roots/acorn dev-main)
- `package.json`: Scripts Node y dependencias (@roots/bud, @roots/sage)
- `theme.json`: Configuración del editor de bloques (auto-generado por Bud)
- `app/setup.php`: Inicialización del tema y encolado de assets
- `resources/views/layouts/app.blade.php`: Layout Blade principal con Alpine.js

## Patrones Comunes

### Manejador AJAX con Salida HTML
```php
function filtrar_eventos_por_categoria() {
    $categoria_slug = sanitize_text_field($_POST['category']);
    $args = ['post_type' => 'events', 'posts_per_page' => intval($_POST['posts_per_page'])];
    // ... configuración de query
    $query = new WP_Query($args);
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Salida HTML aquí
        }
    }
    wp_send_json(['html' => ob_get_clean(), 'total' => $query->found_posts]);
}
add_action('wp_ajax_filtrar_eventos_por_categoria', 'filtrar_eventos_por_categoria');
add_action('wp_ajax_nopriv_filtrar_eventos_por_categoria', 'filtrar_eventos_por_categoria');
```

### Integración ACF en Plantillas
```blade
<h1><?php the_field('titulo_siembra_page', 'option'); ?></h1>
<img src="<?php the_field('logo', 'option'); ?>" alt="Logo">
```

### Procesamiento de Formulario con Redirección
```php
add_action('admin_post_procesar_formulario_siembra', function() {
    // Valida y procesa
    $success = process_siembra_form($_POST);
    wp_redirect(add_query_arg('enviado', $success ? 'true' : 'false', wp_get_referer()));
    exit;
});
```

Enfócate en mantener los patrones de arquitectura Sage/Acorn, uso adecuado de hooks de WordPress, prácticas de manejo seguro de datos e integración de campos ACF establecidos en este código base.</content>
<parameter name="filePath">\\wsl.localhost\Ubuntu\home\onyj\projects\proyects\elreyjesuspuntofijo\wp-content\themes\.github\copilot-instructions.md