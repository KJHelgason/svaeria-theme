<?php
/**
 * Widget Areas
 *
 * Register custom widget areas/sidebars.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom widget areas
 */
function kadence_child_widgets_init() {
    // Footer Column 1 - Products
    register_sidebar(array(
        'name'          => __('Footer - Products', 'kadence-child'),
        'id'            => 'footer-products',
        'description'   => __('Footer widget area for product links', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    // Footer Column 2 - Company
    register_sidebar(array(
        'name'          => __('Footer - Company', 'kadence-child'),
        'id'            => 'footer-company',
        'description'   => __('Footer widget area for company links', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    // Footer Column 3 - Service
    register_sidebar(array(
        'name'          => __('Footer - Service', 'kadence-child'),
        'id'            => 'footer-service',
        'description'   => __('Footer widget area for service links', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    // Footer Column 4 - Contact
    register_sidebar(array(
        'name'          => __('Footer - Contact', 'kadence-child'),
        'id'            => 'footer-contact',
        'description'   => __('Footer widget area for contact info', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    // Newsletter Section
    register_sidebar(array(
        'name'          => __('Newsletter Section', 'kadence-child'),
        'id'            => 'newsletter-section',
        'description'   => __('Widget area for newsletter signup', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget newsletter-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'kadence_child_widgets_init');
