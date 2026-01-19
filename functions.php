<?php
/**
 * Kadence Child Theme - Nordic Jewelry (Svaeria)
 *
 * Functions and definitions - Bootstrap loader
 *
 * @package Kadence_Child
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define theme constants
 */
define('KADENCE_CHILD_VERSION', '2.0.0');
define('KADENCE_CHILD_DIR', get_stylesheet_directory());
define('KADENCE_CHILD_URI', get_stylesheet_directory_uri());

/**
 * Load theme modules
 *
 * All functionality is organized into separate files for maintainability.
 */

// Core functionality
require_once KADENCE_CHILD_DIR . '/inc/class-currency-converter.php';  // Multi-currency system
require_once KADENCE_CHILD_DIR . '/inc/enqueues.php';                   // Scripts & styles
require_once KADENCE_CHILD_DIR . '/inc/helpers.php';                    // Utility functions & AJAX
require_once KADENCE_CHILD_DIR . '/inc/header-functions.php';           // Header & footer logic
require_once KADENCE_CHILD_DIR . '/inc/widgets.php';                    // Widget areas
require_once KADENCE_CHILD_DIR . '/inc/customizer.php';                 // Customizer settings

// WooCommerce (only if active)
if (class_exists('WooCommerce')) {
    require_once KADENCE_CHILD_DIR . '/inc/woocommerce.php';
}

// Shortcodes
require_once KADENCE_CHILD_DIR . '/inc/shortcodes/product-shortcodes.php';   // [new_arrivals], [our_favorites]
require_once KADENCE_CHILD_DIR . '/inc/shortcodes/layout-shortcodes.php';    // [collection_banner], [split_content], [hero_image]
require_once KADENCE_CHILD_DIR . '/inc/shortcodes/content-shortcodes.php';   // [trust_badges], [newsletter_section], [section_header], [love_from_north]
require_once KADENCE_CHILD_DIR . '/inc/shortcodes/social-shortcodes.php';    // [social_block], [shop_categories], gallery lightbox
