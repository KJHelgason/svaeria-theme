<?php
/**
 * Helper Functions
 *
 * Utility functions, theme support, and AJAX handlers.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add support for custom logo
 */
function kadence_child_theme_support() {
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
}
add_action('after_setup_theme', 'kadence_child_theme_support');

/**
 * Remove Emoji scripts
 */
function kadence_child_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'kadence_child_disable_emojis');

/**
 * Add SVG support
 */
function kadence_child_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'kadence_child_mime_types');

/**
 * Add custom body classes
 */
function kadence_child_body_classes($classes) {
    // Add transparent header class for homepage
    if (is_front_page()) {
        $classes[] = 'transparent-header';
    }

    // Add WooCommerce-specific classes
    if (class_exists('WooCommerce')) {
        if (is_shop() || is_product_category() || is_product_tag()) {
            $classes[] = 'shop-page';
        }
        if (is_product()) {
            $classes[] = 'single-product-page';
        }
    }

    return $classes;
}
add_filter('body_class', 'kadence_child_body_classes');

/**
 * AJAX Live Product Search
 */
function jj_live_product_search() {
    $search_term = sanitize_text_field($_POST['search_term']);

    if (strlen($search_term) < 2) {
        wp_send_json_success(array('html' => ''));
        wp_die();
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 8,
        's'              => $search_term,
        'post_status'    => 'publish',
    );

    $products = new WP_Query($args);

    ob_start();

    if ($products->have_posts()) {
        echo '<div class="jj-search-results-list">';
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            $product_id = get_the_ID();
            $product_title = get_the_title();
            $product_price = $product->get_price_html();
            $product_link = get_permalink();
            $product_image = get_the_post_thumbnail_url($product_id, 'thumbnail');
            ?>
            <a href="<?php echo esc_url($product_link); ?>" class="jj-search-result-item">
                <div class="jj-search-result-image">
                    <?php if ($product_image) : ?>
                        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>" />
                    <?php else : ?>
                        <div class="jj-search-result-placeholder"></div>
                    <?php endif; ?>
                </div>
                <div class="jj-search-result-info">
                    <span class="jj-search-result-title"><?php echo esc_html($product_title); ?></span>
                    <span class="jj-search-result-price"><?php echo $product_price; ?></span>
                </div>
            </a>
            <?php
        }
        echo '</div>';

        // View all results link
        $shop_url = add_query_arg(array('s' => $search_term, 'post_type' => 'product'), home_url('/'));
        echo '<a href="' . esc_url($shop_url) . '" class="jj-search-view-all">View all results</a>';
    } else {
        echo '<div class="jj-search-no-results">No products found for "' . esc_html($search_term) . '"</div>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
    wp_die();
}
add_action('wp_ajax_jj_live_search', 'jj_live_product_search');
add_action('wp_ajax_nopriv_jj_live_search', 'jj_live_product_search');
