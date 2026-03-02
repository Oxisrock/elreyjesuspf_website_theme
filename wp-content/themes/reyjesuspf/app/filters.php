<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Hide admin bar for subscribers.
 *
 * @param bool $show
 * @return bool
 */
add_filter('show_admin_bar', function ($show) {
    if (current_user_can('subscriber')) {
        return false;
    }
    return $show;
});
