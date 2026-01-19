<?php
/**
 * Product Shortcodes
 *
 * Shortcodes for displaying WooCommerce products.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * New Arrivals Product Grid Shortcode
 * Usage: [new_arrivals title="NEW ARRIVALS" count="8" columns="4" link_text="SEE MORE"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_new_arrivals_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'     => 'NEW ARRIVALS',
        'count'     => 8,
        'columns'   => 4,
        'link_text' => 'SEE MORE',
        'link_url'  => '', // Leave empty for auto-detect
    ), $atts, 'new_arrivals');

    // Query newest products
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => intval($atts['count']),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    );

    $products = new WP_Query($args);

    if (!$products->have_posts()) {
        return '<p>No products found.</p>';
    }

    // Track categories to find most common
    $category_counts = array();

    ob_start();
    ?>
    <section class="jj-product-section jj-new-arrivals-section">
        <h2 class="jj-section-title"><?php echo esc_html($atts['title']); ?></h2>

        <div class="jj-product-grid jj-grid-<?php echo esc_attr($atts['columns']); ?>">
            <?php
            while ($products->have_posts()) :
                $products->the_post();
                global $product;

                // Track categories
                $terms = get_the_terms(get_the_ID(), 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        if ($term->slug !== 'uncategorized') {
                            if (!isset($category_counts[$term->term_id])) {
                                $category_counts[$term->term_id] = array(
                                    'count' => 0,
                                    'name'  => $term->name,
                                    'url'   => get_term_link($term),
                                );
                            }
                            $category_counts[$term->term_id]['count']++;
                        }
                    }
                }

                // Get product data
                $product_id    = get_the_ID();
                $product_title = get_the_title();
                $product_price = $product->get_price_html();
                $product_link  = get_permalink();
                $product_image = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
                $product_type  = $product->get_type();
                $short_desc    = $product->get_short_description();

                // Truncate description
                if (strlen($short_desc) > 60) {
                    $short_desc = substr($short_desc, 0, 60) . '...';
                }

                // Check if product is already in cart
                $in_cart = false;
                if (WC()->cart) {
                    foreach (WC()->cart->get_cart() as $cart_item) {
                        if ($cart_item['product_id'] == $product_id) {
                            $in_cart = true;
                            break;
                        }
                    }
                }

                // Button text and attributes based on product type and cart status
                $cart_url = wc_get_cart_url();
                ?>
                <div class="jj-product-card">
                    <a href="<?php echo esc_url($product_link); ?>" class="jj-product-image-link">
                        <div class="jj-product-image">
                            <?php if ($product_image) : ?>
                                <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>" />
                            <?php else : ?>
                                <div class="jj-product-placeholder"></div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="jj-product-info">
                        <h3 class="jj-product-title">
                            <a href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product_title); ?></a>
                        </h3>
                        <div class="jj-product-price"><?php echo $product_price; ?></div>
                        <?php if ($short_desc) : ?>
                            <p class="jj-product-desc"><?php echo esc_html(wp_strip_all_tags($short_desc)); ?></p>
                        <?php endif; ?>
                        <?php if ($product_type === 'variable') : ?>
                            <a href="<?php echo esc_url($product_link); ?>" class="jj-product-button">SELECT OPTIONS</a>
                        <?php elseif ($in_cart) : ?>
                            <a href="<?php echo esc_url($cart_url); ?>" class="jj-product-button in-cart">VIEW CART</a>
                        <?php else : ?>
                            <a href="?add-to-cart=<?php echo esc_attr($product_id); ?>"
                               class="jj-product-button add_to_cart_button ajax_add_to_cart"
                               data-product_id="<?php echo esc_attr($product_id); ?>"
                               data-quantity="1"
                               aria-label="Add <?php echo esc_attr($product_title); ?> to cart">ADD TO CART</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php
        // Determine "See More" link
        $see_more_url = $atts['link_url'];
        if (empty($see_more_url) && !empty($category_counts)) {
            // Find most common category
            usort($category_counts, function($a, $b) {
                return $b['count'] - $a['count'];
            });
            $top_category = reset($category_counts);
            $see_more_url = $top_category['url'];
        }
        if (empty($see_more_url)) {
            $see_more_url = get_permalink(wc_get_page_id('shop'));
        }
        ?>

        <div class="jj-section-footer">
            <a href="<?php echo esc_url($see_more_url); ?>" class="jj-see-more-button"><?php echo esc_html($atts['link_text']); ?></a>
        </div>
    </section>
    <?php

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('new_arrivals', 'jj_new_arrivals_shortcode');

/**
 * Our Favorites Carousel Shortcode (8 products, 4 visible at a time with arrows)
 * Usage: [our_favorites title="OUR FAVORITES" count="8" link_text="See more" link_url="/shop/"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_our_favorites_shortcode($atts) {
    if (!class_exists('WooCommerce')) {
        return '<p>WooCommerce is required for this shortcode.</p>';
    }

    $atts = shortcode_atts(array(
        'title'      => 'OUR FAVORITES',
        'count'      => 8,
        'link_text'  => 'See more',
        'link_url'   => '',
        'category'   => '', // Optional: filter by category slug
        'orderby'    => 'popularity', // popularity, rating, date, rand
    ), $atts, 'our_favorites');

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => intval($atts['count']),
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => '_stock_status',
                'value'   => 'instock',
                'compare' => '=',
            ),
        ),
    );

    // Handle orderby
    switch ($atts['orderby']) {
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
        case 'rand':
            $args['orderby'] = 'rand';
            break;
    }

    // Filter by category if specified
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    $products = new WP_Query($args);

    if (!$products->have_posts()) {
        return '<p>No products found.</p>';
    }

    ob_start();
    ?>
    <section class="jj-favorites-section">
        <h2 class="jj-favorites-title"><?php echo esc_html($atts['title']); ?></h2>

        <div class="jj-favorites-carousel-wrapper">
            <button class="jj-favorites-arrow jj-favorites-prev" aria-label="Previous products">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div class="jj-favorites-carousel">
                <div class="jj-favorites-track">
                    <?php while ($products->have_posts()) : $products->the_post();
                        global $product;
                        $product_id = $product->get_id();
                        $product_title = $product->get_name();
                        $product_price = $product->get_price_html();
                        $product_link = get_permalink($product_id);
                        $product_image = wp_get_attachment_image_url($product->get_image_id(), 'medium');
                        $product_type = $product->get_type();
                        $short_desc = $product->get_short_description();

                        // Truncate description
                        if (strlen($short_desc) > 60) {
                            $short_desc = substr($short_desc, 0, 60) . '...';
                        }

                        // Check if product is already in cart
                        $in_cart = false;
                        $cart_url = wc_get_cart_url();
                        if (WC()->cart) {
                            foreach (WC()->cart->get_cart() as $cart_item) {
                                if ($cart_item['product_id'] == $product_id) {
                                    $in_cart = true;
                                    break;
                                }
                            }
                        }
                        ?>
                        <div class="jj-favorites-item">
                            <a href="<?php echo esc_url($product_link); ?>" class="jj-favorites-image-link">
                                <div class="jj-favorites-image">
                                    <?php if ($product_image) : ?>
                                        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_title); ?>" />
                                    <?php else : ?>
                                        <div class="jj-favorites-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="jj-favorites-info">
                                <h3 class="jj-favorites-item-title">
                                    <a href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product_title); ?></a>
                                </h3>
                                <div class="jj-favorites-price"><?php echo $product_price; ?></div>
                                <?php if ($short_desc) : ?>
                                    <p class="jj-favorites-desc"><?php echo esc_html(wp_strip_all_tags($short_desc)); ?></p>
                                <?php endif; ?>
                                <?php if ($product_type === 'variable') : ?>
                                    <a href="<?php echo esc_url($product_link); ?>" class="jj-product-button">SELECT OPTIONS</a>
                                <?php elseif ($in_cart) : ?>
                                    <a href="<?php echo esc_url($cart_url); ?>" class="jj-product-button in-cart">VIEW CART</a>
                                <?php else : ?>
                                    <a href="?add-to-cart=<?php echo esc_attr($product_id); ?>"
                                       class="jj-product-button add_to_cart_button ajax_add_to_cart"
                                       data-product_id="<?php echo esc_attr($product_id); ?>"
                                       data-quantity="1"
                                       aria-label="Add <?php echo esc_attr($product_title); ?> to cart">ADD TO CART</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <button class="jj-favorites-arrow jj-favorites-next" aria-label="Next products">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <?php
        $see_more_url = !empty($atts['link_url']) ? $atts['link_url'] : get_permalink(wc_get_page_id('shop'));
        ?>
        <div class="jj-favorites-footer">
            <a href="<?php echo esc_url($see_more_url); ?>" class="jj-favorites-button"><?php echo esc_html($atts['link_text']); ?></a>
        </div>
    </section>

    <script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.jj-favorites-carousel');
            const track = document.querySelector('.jj-favorites-track');
            const prevBtn = document.querySelector('.jj-favorites-prev');
            const nextBtn = document.querySelector('.jj-favorites-next');

            if (!carousel || !track || !prevBtn || !nextBtn) return;

            let currentSlide = 0;
            const totalItems = track.children.length;
            const itemsPerView = 4;
            const maxSlide = Math.max(0, totalItems - itemsPerView);

            function updateCarousel() {
                const itemWidth = track.children[0].offsetWidth;
                const gap = 30; // Match CSS gap
                const offset = currentSlide * (itemWidth + gap);
                track.style.transform = 'translateX(-' + offset + 'px)';

                // Update button states
                prevBtn.style.opacity = currentSlide === 0 ? '0.3' : '1';
                prevBtn.style.pointerEvents = currentSlide === 0 ? 'none' : 'auto';
                nextBtn.style.opacity = currentSlide >= maxSlide ? '0.3' : '1';
                nextBtn.style.pointerEvents = currentSlide >= maxSlide ? 'none' : 'auto';
            }

            prevBtn.addEventListener('click', function() {
                if (currentSlide > 0) {
                    currentSlide -= itemsPerView;
                    if (currentSlide < 0) currentSlide = 0;
                    updateCarousel();
                }
            });

            nextBtn.addEventListener('click', function() {
                if (currentSlide < maxSlide) {
                    currentSlide += itemsPerView;
                    if (currentSlide > maxSlide) currentSlide = maxSlide;
                    updateCarousel();
                }
            });

            // Initialize
            updateCarousel();

            // Update on resize
            window.addEventListener('resize', updateCarousel);
        });
    })();
    </script>
    <?php

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('our_favorites', 'jj_our_favorites_shortcode');
