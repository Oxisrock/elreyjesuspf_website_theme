<?php

namespace App\Walkers;

use Walker_Nav_Menu;

/**
 * Custom Tailwind CSS Nav Walker for Desktop Menus
 *
 * Extends Walker_Nav_Menu to apply Tailwind CSS classes
 * to desktop navigation menu items.
 */
class TailwindNavWalker extends Walker_Nav_Menu
{
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @param string   &$output Used to append additional content (passed by reference).
     * @param int      $depth   Depth of menu item. Used for padding.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     */
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        if (is_array($args)) {
            $args = (object) $args;
        }
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);

        // Default class for sub-menu ul
        // Puedes agregar clases de Tailwind aquí si quieres estilizar el contenedor del submenú
        // ej: 'absolute mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10'
        $classes = ['sub-menu', 'origin-top-right', 'absolute', 'right-0', 'mt-2', 'w-56', 'rounded-md', 'shadow-lg', 'bg-white', 'ring-1', 'ring-black', 'ring-opacity-5', 'focus:outline-none', 'py-1'];

        // Ocultar submenú por defecto, se mostraría con JS/CSS (:hover, Alpine.js)
        // Si usas :hover en el <li> padre, no necesitarías 'hidden' aquí si el CSS lo maneja.
        // Para Alpine.js, controlarías la visibilidad.
        // $classes[] = 'hidden';


        $output .= "{$n}{$indent}<ul class=\"" . esc_attr(implode(' ', $classes)) . "\" role=\"menu\" aria-orientation=\"vertical\" aria-labelledby=\"menu-button\">{$n}";
    }


    /**
     * Starts the element output.
     *
     * @see Walker::start_el()
     *
     * @param string   &$output            Used to append additional content (passed by reference).
     * @param WP_Post  $item               Menu item data object.
     * @param int      $depth              Depth of menu item. Used for padding.
     * @param stdClass $args               An object of wp_nav_menu() arguments.
     * @param int      $id                 Current item ID.
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        if (is_array($args)) {
            $args = (object) $args;
        }
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $li_classes = ['menu-item', 'list-none'];
        if ($item->has_children) {
            $li_classes[] = 'relative group';
        }

        $output .= $indent . '<li class="' . esc_attr(implode(' ', $li_classes)) . '">';

        $atts = [];
        $atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = ! empty($item->target)     ? $item->target     : '';
        if ('_blank' === $item->target && empty($item->xfn)) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $item->xfn;
        }
        $atts['href'] = ! empty($item->url) ? $item->url : '#';
        $atts['aria-current'] = $item->current ? 'page' : '';

        $link_classes = [
            'nav-link',
            'font-semibold',
            'pb-1',
        ];

        // =================================== INICIO DE LA CORRECCIÓN ===================================

        // Por defecto, usamos la comprobación de WordPress.
        $is_active = in_array('current-menu-item', (array)$item->classes) || in_array('current_page_item', (array)$item->classes);

        // LÓGICA DEFINITIVA para la página de inicio
        if (is_front_page()) {
            // Un elemento es el enlace "real" a la home si su URL es "/" o la URL completa,
            // Y ADEMÁS, no contiene un ancla "#".
            $is_true_home_link = ($item->url === '/' || $item->url === home_url('/'));
            $is_anchor_link = strpos($item->url, '#') !== false;

            // El elemento solo será activo si es el enlace a la home y no un ancla.
            $is_active = ($is_true_home_link && !$is_anchor_link);
        }

        // Aplicamos las clases según el estado final de $is_active.
        if ($is_active) {
            $link_classes[] = 'text-blue-600 border-b-2 border-blue-600';
            $link_classes[] = 'active';
        } else {
            $link_classes[] = 'text-gray-700';
            $link_classes[] = 'hover:text-blue-600';
        }


        // =================================== FIN DE LA CORRECCIÓN ===================================

        $atts['class'] = implode(' ', array_unique($link_classes));

        if ($depth === 0 && $item->has_children) {
            // ...
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        if ($depth === 0 && $item->has_children) {
            $item_output .= ' <svg class="inline-block h-4 w-4 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>';
        }
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }


    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     */
    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @param string   &$output Used to append additional content (passed by reference).
     * @param int      $depth   Depth of menu item. Used for padding.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     */
    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent  = str_repeat($t, $depth);
        $output .= "$indent</ul>{$n}";
    }
}
