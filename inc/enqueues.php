<?php
/**
 * Scripts and Styles Enqueuing
 *
 * Handles enqueuing of parent/child theme styles, scripts, and fonts.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function kadence_child_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style(
        'kadence-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('kadence')->get('Version')
    );

    // Child theme style
    wp_enqueue_style(
        'kadence-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('kadence-parent-style'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'kadence_child_enqueue_styles');

/**
 * Add Google Fonts - Inter + Spectral (Jonna Jinton Style)
 */
function kadence_child_google_fonts() {
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Spectral:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'kadence_child_google_fonts');

/**
 * Enqueue WooCommerce add-to-cart scripts
 */
function jj_enqueue_wc_cart_scripts() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-add-to-cart');
    }
}
add_action('wp_enqueue_scripts', 'jj_enqueue_wc_cart_scripts');

/**
 * Enqueue main JavaScript file
 */
function kadence_child_enqueue_scripts() {
    wp_enqueue_script(
        'kadence-child-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );

    // Localize script with AJAX URL
    wp_localize_script('kadence-child-main', 'jjAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('jj_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'kadence_child_enqueue_scripts');
