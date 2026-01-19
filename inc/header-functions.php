<?php
/**
 * Header Functions
 *
 * Functions related to the custom header.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register footer menu locations
 */
function jj_register_footer_menus() {
    register_nav_menus(array(
        'footer-products' => __('Footer - Products', 'kadence-child'),
        'footer-company'  => __('Footer - Company', 'kadence-child'),
        'footer-service'  => __('Footer - Service', 'kadence-child'),
    ));
}
add_action('after_setup_theme', 'jj_register_footer_menus');

/**
 * Use custom footer template
 */
function jj_custom_footer() {
    // Remove Kadence's default footer
    remove_action('kadence_footer', 'Kadence\footer_markup');
}
add_action('wp', 'jj_custom_footer');

/**
 * Remove default Kadence header and add custom header
 */
function kadence_child_custom_header() {
    // Include our custom header template from child theme
    $header_file = get_stylesheet_directory() . '/template-parts/header/header-main.php';
    if (file_exists($header_file)) {
        include($header_file);
    }
}

/**
 * Output custom header before Kadence's header and hide Kadence's header with CSS
 */
function kadence_child_output_custom_header() {
    kadence_child_custom_header();
}
add_action('wp_body_open', 'kadence_child_output_custom_header', 5);

/**
 * Hide Kadence's default header with CSS
 */
function kadence_child_hide_kadence_header() {
    echo '<style>
        .site-header.kadence-header { display: none !important; }
        header#masthead.site-header { display: none !important; }
        .kadence-header { display: none !important; }
        /* But show our custom header */
        header#masthead.jj-main-header { display: block !important; }
    </style>';
}
add_action('wp_head', 'kadence_child_hide_kadence_header', 999);

/**
 * Add custom header via shortcode for flexibility
 */
function kadence_child_header_shortcode() {
    ob_start();
    get_template_part('template-parts/header/header', 'main');
    return ob_get_clean();
}
add_shortcode('jj_header', 'kadence_child_header_shortcode');
