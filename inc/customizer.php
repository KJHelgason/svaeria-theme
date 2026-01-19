<?php
/**
 * Theme Customizer Settings
 *
 * WordPress Customizer additions for the theme.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customizer additions
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object
 */
function kadence_child_customizer($wp_customize) {
    // Add Nordic Jewelry section
    $wp_customize->add_section('jj_settings', array(
        'title'    => __('Nordic Jewelry Settings', 'kadence-child'),
        'priority' => 30,
    ));

    // Announcement bar text
    $wp_customize->add_setting('jj_announcement_text', array(
        'default'           => 'FREE SHIPPING | WORLDWIDE DELIVERY | SECURE PAYMENTS',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('jj_announcement_text', array(
        'label'    => __('Announcement Bar Text', 'kadence-child'),
        'section'  => 'jj_settings',
        'type'     => 'textarea',
    ));

    // Show/hide announcement bar
    $wp_customize->add_setting('jj_show_announcement', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('jj_show_announcement', array(
        'label'    => __('Show Announcement Bar', 'kadence-child'),
        'section'  => 'jj_settings',
        'type'     => 'checkbox',
    ));

    // Newsletter section title
    $wp_customize->add_setting('jj_newsletter_title', array(
        'default'           => 'Our newsletter',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('jj_newsletter_title', array(
        'label'    => __('Newsletter Section Title', 'kadence-child'),
        'section'  => 'jj_settings',
        'type'     => 'text',
    ));

    // Newsletter description
    $wp_customize->add_setting('jj_newsletter_description', array(
        'default'           => 'Sign up for our newsletter, and you\'ll be the first to know when we are about to release new products.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('jj_newsletter_description', array(
        'label'    => __('Newsletter Description', 'kadence-child'),
        'section'  => 'jj_settings',
        'type'     => 'textarea',
    ));
}
add_action('customize_register', 'kadence_child_customizer');
