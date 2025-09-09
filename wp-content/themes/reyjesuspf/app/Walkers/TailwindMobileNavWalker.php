<?php

namespace App\Walkers;

use Walker_Nav_Menu;

/**
 * Custom Tailwind CSS Nav Walker for Mobile Menus
 *
 * Extends Walker_Nav_Menu to apply Tailwind CSS classes
 * to mobile navigation menu items.
 */
class TailwindMobileNavWalker extends Walker_Nav_Menu
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

        // Default class for sub-menu
        $classes = ['sub-menu', 'mt-1', 'space-y-1']; // Added space-y-1 for sub-items

        $output .= "{$n}{$indent}<ul class=\"" . esc_attr(implode(' ', $classes)) . "\">{$n}";
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
        // ... (código inicial de la función es igual) ...
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
        $output .= $indent . '<li class="' . esc_attr(implode(' ', $li_classes)) . '">';
        $atts = [];
        $atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = ! empty($item->target)     ? $item->target     : '';
        if ('_blank' === $item->target && empty($item->xfn)) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $item->xfn;
        }
        $atts['href'] = ! empty($item->url) ? $item->url : '';
        $atts['aria-current'] = $item->current ? 'page' : '';
        $link_classes = ['block', 'px-3', 'py-2', 'rounded-md', 'text-base', 'font-medium'];
        if ($depth > 0) {
            $link_classes[] = 'pl-6';
            $link_classes[] = 'text-gray-600';
            $link_classes[] = 'hover:bg-gray-100';
            $link_classes[] = 'hover:text-indigo-500';
        } else {
            $link_classes[] = 'text-gray-700';
            $link_classes[] = 'hover:bg-gray-50';
            $link_classes[] = 'hover:text-indigo-600';
        }

        // =================================== INICIO DE LA SOLUCIÓN FINAL ===================================

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

        // Aplicamos las clases activas si la condición se cumple.
        if ($is_active) {
            $link_classes[] = 'bg-indigo-50';
            $link_classes[] = 'text-indigo-700';
        }

        // =================================== FIN DE LA SOLUCIÓN FINAL ===================================

        $atts['class'] = implode(' ', array_unique($link_classes));
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        // ... (El resto de la función sigue igual) ...
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
        $item_output .= '</a>';
        $item_output .= $args->after;
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @param string   &$output Used to append additional content (passed by reference).
     * @param WP_Post  $item    Page data object. Not used.
     * @param int      $depth   Depth of page. Not Used.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
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
}
