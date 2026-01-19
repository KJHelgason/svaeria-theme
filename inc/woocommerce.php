<?php
/**
 * WooCommerce Customizations
 *
 * WooCommerce-specific filters and functions.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Only run if WooCommerce is active
if (!class_exists('WooCommerce')) {
    return;
}

/**
 * Change number of products per row
 */
add_filter('loop_shop_columns', function() {
    return 4; // 4 products per row
});

/**
 * Change number of products per page
 */
add_filter('loop_shop_per_page', function() {
    return 16; // 16 products per page
});

/**
 * Add product short description to loop
 */
function kadence_child_product_excerpt() {
    global $product;
    $excerpt = $product->get_short_description();
    if ($excerpt) {
        echo '<div class="product-excerpt">' . wp_trim_words($excerpt, 20, '...') . '</div>';
    }
}
add_action('woocommerce_after_shop_loop_item_title', 'kadence_child_product_excerpt', 15);

/**
 * Change "Add to Cart" button text
 */
add_filter('woocommerce_product_single_add_to_cart_text', function() {
    return __('ADD TO CART', 'kadence-child');
});

add_filter('woocommerce_product_add_to_cart_text', function($text, $product) {
    if ($product->is_type('variable')) {
        return __('SELECT OPTIONS', 'kadence-child');
    }
    return __('ADD TO CART', 'kadence-child');
}, 10, 2);

/**
 * Customize product gallery thumbnails
 */
add_filter('woocommerce_gallery_thumbnail_size', function() {
    return array(150, 150);
});

/**
 * Add trust badges after checkout
 */
function kadence_child_checkout_trust_badges() {
    ?>
    <div class="checkout-trust-badges">
        <div class="trust-badge">
            <span class="trust-badge-icon">🔒</span>
            <span class="trust-badge-text">Secure Checkout</span>
        </div>
        <div class="trust-badge">
            <span class="trust-badge-icon">🚚</span>
            <span class="trust-badge-text">Free Worldwide Shipping</span>
        </div>
    </div>
    <?php
}
add_action('woocommerce_review_order_after_submit', 'kadence_child_checkout_trust_badges');
