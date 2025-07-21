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
        $value = '';

        // Attempt to extract original classes and preserve them
        $css_classes = empty($item->classes) ? [] : (array) $item->classes;
        // Filter out 'current-menu-item' and 'current-menu-parent' if you want to style them differently or not at all via Walker
        // $css_classes = array_filter($css_classes, function($class) {
        //     return !in_array($class, ['current-menu-item', 'current-menu-parent', 'current_page_item']);
        // });

        // Add 'active' class for current menu item for potential custom styling not via Tailwind active selectors
        if (in_array('current-menu-item', $item->classes) || in_array('current_page_item', $item->classes)) {
            // $li_classes[] = 'active-mobile-item'; // Example: if you want a specific class
        }


        $output .= $indent . '<li' . $value . ($li_classes ? ' class="' . esc_attr(implode(' ', $li_classes)) . '"' : '') . '>';

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

        // Tailwind classes for the <a> tag in a mobile menu
        // Adjust these classes to match your desired mobile menu item appearance
        $link_classes = [
            'block',
            'px-3', // Horizontal padding
            'py-2', // Vertical padding
            'rounded-md', // Rounded corners
            'text-base', // Base text size
            'font-medium', // Medium font weight
        ];

        if ($depth > 0) { // Sub-menu item
            $link_classes[] = 'pl-6'; // Indent sub-menu items more
            $link_classes[] = 'text-gray-600'; // Slightly different color for sub-items
            $link_classes[] = 'hover:bg-gray-100';
            $link_classes[] = 'hover:text-indigo-500';
        } else { // Top-level item
            $link_classes[] = 'text-gray-700'; // Default text color
            $link_classes[] = 'hover:bg-gray-50'; // Background on hover
            $link_classes[] = 'hover:text-indigo-600'; // Text color on hover (matching your desktop log in)
        }

        // Add active class styling directly if desired
         if (in_array('current-menu-item', $item->classes) || in_array('current_page_item', $item->classes)) {
            $link_classes[] = 'bg-indigo-50'; // Example active background
            $link_classes[] = 'text-indigo-700'; // Example active text
         }


        $atts['class'] = implode(' ', array_unique($link_classes));


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