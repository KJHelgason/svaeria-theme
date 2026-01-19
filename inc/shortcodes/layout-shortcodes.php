<?php
/**
 * Layout Shortcodes
 *
 * Shortcodes for page layout sections (banners, split content, hero images).
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Collection Banner Shortcode (50/50 image and text with colored background)
 * Usage: [collection_banner subtitle="COLLECTION" title="Valkyria" image="URL" bg_color="grey" button1_text="NECKLACE" button1_url="/product/necklace/"]Your text content here[/collection_banner]
 * bg_color options: "grey" (default, uses --jj-section-grey), "blue" (uses --jj-svaeria-blue)
 *
 * @param array $atts Shortcode attributes
 * @param string|null $content Content between shortcode tags
 * @return string HTML output
 */
function jj_collection_banner_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'subtitle'     => 'COLLECTION',
        'title'        => '',
        'image'        => '',
        'bg_color'     => 'grey', // 'grey' (default) or 'blue' - colors defined in CSS variables
        'text_color'   => 'dark', // 'dark' or 'light'
        'button1_text' => '',
        'button1_url'  => '#',
        'button2_text' => '',
        'button2_url'  => '#',
        'reverse'      => 'false', // 'true' to put image on left
    ), $atts, 'collection_banner');

    $is_reverse = ($atts['reverse'] === 'true' || $atts['reverse'] === true);
    $reverse_class = $is_reverse ? 'jj-collection-reverse' : '';
    $text_class = ($atts['text_color'] === 'light') ? 'jj-collection-light' : '';
    $bg_class = ($atts['bg_color'] === 'blue') ? 'jj-collection-blue' : 'jj-collection-grey';

    // Get text from content between shortcode tags
    $text_content = !empty($content) ? trim($content) : '';

    ob_start();
    ?>
    <section class="jj-collection-banner <?php echo esc_attr($reverse_class); ?> <?php echo esc_attr($text_class); ?> <?php echo esc_attr($bg_class); ?>">
        <div class="jj-collection-container">
            <div class="jj-collection-content">
                <div class="jj-collection-content-inner">
                    <?php if (!empty($atts['subtitle'])) : ?>
                        <p class="jj-collection-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($atts['title'])) : ?>
                        <h3 class="jj-collection-title"><?php echo esc_html($atts['title']); ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($text_content)) : ?>
                        <p class="jj-collection-text"><?php echo wp_kses_post($text_content); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($atts['button1_text']) || !empty($atts['button2_text'])) : ?>
                        <div class="jj-collection-buttons">
                            <?php if (!empty($atts['button1_text'])) : ?>
                                <a href="<?php echo esc_url($atts['button1_url']); ?>" class="jj-collection-button"><?php echo esc_html($atts['button1_text']); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($atts['button2_text'])) : ?>
                                <a href="<?php echo esc_url($atts['button2_url']); ?>" class="jj-collection-button"><?php echo esc_html($atts['button2_text']); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="jj-collection-image">
                <?php if (!empty($atts['image'])) : ?>
                    <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" />
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('collection_banner', 'jj_collection_banner_shortcode');

/**
 * Split Content Block Shortcode (75% background with image extending past)
 * Usage: [split_content subtitle="JONNA JINTON JEWELRY" title="Jewelry from the north" text="..." button_text="Our jewelry" button_url="/product-category/jewelry/" image="URL" reverse="false" bg_color="grey"]
 * bg_color options: "grey" (default, uses --jj-section-grey), "blue" (uses --jj-svaeria-blue)
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_split_content_shortcode($atts) {
    $atts = shortcode_atts(array(
        'subtitle'    => '',
        'title'       => '',
        'text'        => '',
        'button_text' => '',
        'button_url'  => '#',
        'image'       => '',
        'image_id'    => '', // WordPress media ID
        'reverse'     => 'false', // 'true' to flip layout (image left, text right)
        'bg_color'    => 'grey', // 'grey' (default) or 'blue' - colors defined in CSS variables
    ), $atts, 'split_content');

    $is_reverse = ($atts['reverse'] === 'true' || $atts['reverse'] === true);
    $reverse_class = $is_reverse ? 'jj-split-reverse' : '';
    $bg_class = ($atts['bg_color'] === 'blue') ? 'jj-split-blue' : '';

    // Get image URL
    $image_url = $atts['image'];
    if (empty($image_url) && !empty($atts['image_id'])) {
        $image_url = wp_get_attachment_image_url($atts['image_id'], 'large');
    }

    ob_start();
    ?>
    <section class="jj-split-block <?php echo esc_attr($reverse_class); ?> <?php echo esc_attr($bg_class); ?>">
        <div class="jj-split-bg-wrapper">
            <div class="jj-split-bg"></div>
        </div>
        <div class="jj-split-container">
            <div class="jj-split-content-col">
                <div class="jj-split-content-inner">
                    <?php if (!empty($atts['subtitle'])) : ?>
                        <p class="jj-split-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($atts['title'])) : ?>
                        <h3 class="jj-split-title"><?php echo esc_html($atts['title']); ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($atts['text'])) : ?>
                        <div class="jj-split-text"><?php echo wp_kses_post($atts['text']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($atts['button_text'])) : ?>
                        <a href="<?php echo esc_url($atts['button_url']); ?>" class="jj-split-button"><?php echo esc_html($atts['button_text']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="jj-split-image-col">
                <?php if (!empty($image_url)) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($atts['title']); ?>" class="jj-split-image" />
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('split_content', 'jj_split_content_shortcode');

/**
 * Hero Image Banner Shortcode (full-width hero image with optional overlay text)
 * Usage: [hero_image image="URL" height="600" title="" subtitle="" button_text="" button_url="#" overlay="true" overlay_opacity="0.3"]
 * height: Height in pixels (default 600)
 * overlay: "true" or "false" - adds dark gradient overlay for text readability
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_hero_image_shortcode($atts) {
    $atts = shortcode_atts(array(
        'image'           => '',
        'image_id'        => '', // WordPress media ID
        'height'          => '600', // Height in pixels
        'title'           => '',
        'subtitle'        => '',
        'button_text'     => '',
        'button_url'      => '#',
        'overlay'         => 'true', // 'true' or 'false'
        'overlay_opacity' => '0.3', // 0 to 1
    ), $atts, 'hero_image');

    // Get image URL
    $image_url = $atts['image'];
    if (empty($image_url) && !empty($atts['image_id'])) {
        $image_url = wp_get_attachment_image_url($atts['image_id'], 'full');
    }

    $has_overlay = ($atts['overlay'] === 'true' || $atts['overlay'] === true);
    $has_content = !empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['button_text']);
    $height = intval($atts['height']);
    $overlay_opacity = floatval($atts['overlay_opacity']);

    ob_start();
    ?>
    <section class="jj-hero-banner" style="height: <?php echo esc_attr($height); ?>px;">
        <?php if (!empty($image_url)) : ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($atts['title']); ?>" class="jj-hero-banner-image" />
        <?php endif; ?>

        <?php if ($has_overlay) : ?>
            <div class="jj-hero-banner-overlay" style="background: linear-gradient(to bottom, rgba(0,0,0,<?php echo esc_attr($overlay_opacity); ?>) 0%, rgba(0,0,0,<?php echo esc_attr($overlay_opacity + 0.2); ?>) 100%);"></div>
        <?php endif; ?>

        <?php if ($has_content) : ?>
            <div class="jj-hero-banner-content">
                <?php if (!empty($atts['subtitle'])) : ?>
                    <p class="jj-hero-banner-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
                <?php if (!empty($atts['title'])) : ?>
                    <h1 class="jj-hero-banner-title"><?php echo esc_html($atts['title']); ?></h1>
                <?php endif; ?>
                <?php if (!empty($atts['button_text'])) : ?>
                    <a href="<?php echo esc_url($atts['button_url']); ?>" class="jj-hero-banner-button"><?php echo esc_html($atts['button_text']); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('hero_image', 'jj_hero_image_shortcode');
