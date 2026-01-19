<?php
/**
 * Content Shortcodes
 *
 * Shortcodes for content sections (trust badges, newsletter, headers, etc.).
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Trust Badges Shortcode
 * Usage: [trust_badges style="default"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_trust_badges_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'default',
    ), $atts);

    ob_start();
    ?>
    <div class="trust-badges jj-features">
        <div class="trust-badge jj-feature">
            <div class="trust-badge-icon jj-feature-icon">📦</div>
            <div class="trust-badge-title jj-feature-title">Free Shipping On All Orders</div>
            <div class="trust-badge-text jj-feature-text">We offer free worldwide shipping on all orders</div>
        </div>
        <div class="trust-badge jj-feature">
            <div class="trust-badge-icon jj-feature-icon">🌍</div>
            <div class="trust-badge-title jj-feature-title">Worldwide Delivery</div>
            <div class="trust-badge-text jj-feature-text">We deliver to all corners of the world with fast shipping</div>
        </div>
        <div class="trust-badge jj-feature">
            <div class="trust-badge-icon jj-feature-icon">🔒</div>
            <div class="trust-badge-title jj-feature-title">Safe And Secure Payments</div>
            <div class="trust-badge-text jj-feature-text">Pay safe with Card, PayPal or Klarna</div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('trust_badges', 'jj_trust_badges_shortcode');

/**
 * Newsletter Section Shortcode
 * Usage: [newsletter_section title="Our newsletter" description="..."]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_newsletter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'       => get_theme_mod('jj_newsletter_title', 'Our newsletter'),
        'description' => get_theme_mod('jj_newsletter_description', 'Sign up for our newsletter, and you\'ll be the first to know when we are about to release new products.'),
    ), $atts);

    ob_start();
    ?>
    <div class="newsletter-section jj-newsletter">
        <div class="newsletter-inner">
            <span class="section-subtitle">Sign Up For</span>
            <h2><?php echo esc_html($atts['title']); ?></h2>
            <p><?php echo wp_kses_post($atts['description']); ?></p>
            <?php
            // Display newsletter widget if available
            if (is_active_sidebar('newsletter-section')) {
                dynamic_sidebar('newsletter-section');
            } else {
                // Default form placeholder
                ?>
                <form class="newsletter-form" action="#" method="post">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" class="button">Sign Up</button>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('newsletter_section', 'jj_newsletter_shortcode');

/**
 * Section Header Shortcode
 * Usage: [section_header subtitle="New" title="Arrivals" text="Description text..."]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_section_header_shortcode($atts) {
    $atts = shortcode_atts(array(
        'subtitle' => '',
        'title'    => '',
        'text'     => '',
    ), $atts);

    ob_start();
    ?>
    <div class="section-header">
        <?php if ($atts['subtitle']) : ?>
            <span class="section-subtitle"><?php echo esc_html($atts['subtitle']); ?></span>
        <?php endif; ?>
        <?php if ($atts['title']) : ?>
            <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>
        <?php if ($atts['text']) : ?>
            <p class="section-description"><?php echo wp_kses_post($atts['text']); ?></p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('section_header', 'jj_section_header_shortcode');

/**
 * Love From The North Section Shortcode
 * Usage: [love_from_north subtitle="LOVE FROM THE NORTH" title="PURE AND HANDCRAFTED" text="..." stamp_image="URL" bg_color="#f2f2f2"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_love_from_north_shortcode($atts) {
    $atts = shortcode_atts(array(
        'subtitle'    => 'LOVE FROM THE NORTH',
        'title'       => 'PURE AND HANDCRAFTED',
        'text'        => 'We only use clean, nickel- and lead free sterling silver in our jewelry. The jewelry is crafted by us and our collaborators in Sweden and in Norway.',
        'stamp_image' => 'https://jonnajintonsweden.com/wp-content/uploads/2025/05/Stamp-1.svg',
        'bg_color'    => '#f2f2f2',
    ), $atts, 'love_from_north');

    $bg_style = !empty($atts['bg_color']) ? 'background-color: ' . esc_attr($atts['bg_color']) . ';' : '';

    ob_start();
    ?>
    <section class="jj-love-north-section" style="<?php echo $bg_style; ?>">
        <div class="jj-love-north-container">
            <?php if (!empty($atts['stamp_image'])) : ?>
                <div class="jj-love-north-stamp">
                    <img src="<?php echo esc_url($atts['stamp_image']); ?>" alt="Handcrafted stamp" />
                </div>
            <?php endif; ?>

            <div class="jj-love-north-content">
                <?php if (!empty($atts['subtitle'])) : ?>
                    <p class="jj-love-north-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
                <?php if (!empty($atts['title'])) : ?>
                    <h3 class="jj-love-north-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php endif; ?>
                <?php if (!empty($atts['text'])) : ?>
                    <p class="jj-love-north-text"><?php echo esc_html($atts['text']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('love_from_north', 'jj_love_from_north_shortcode');
