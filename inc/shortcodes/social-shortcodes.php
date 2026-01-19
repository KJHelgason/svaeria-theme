<?php
/**
 * Social & Category Shortcodes
 *
 * Shortcodes for social media blocks, shop categories, and gallery features.
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Social Media Block Shortcode (50/50 split with text/button on one side, image on other)
 * Usage: [social_block title="My YouTube channel" text="Description text..." button_text="Go to my YouTube channel" button_url="https://youtube.com/..." button2_text="Subscribe" button2_url="..." image="URL" reverse="false"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_social_block_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'        => '',
        'text'         => '',
        'button_text'  => '',
        'button_url'   => '#',
        'button2_text' => '',
        'button2_url'  => '#',
        'image'        => '',
        'image_id'     => '', // WordPress media ID
        'reverse'      => 'false', // 'true' to put image on left, text on right
    ), $atts, 'social_block');

    $is_reverse = ($atts['reverse'] === 'true' || $atts['reverse'] === true);
    $reverse_class = $is_reverse ? 'jj-social-reverse' : '';

    // Get image URL
    $image_url = $atts['image'];
    if (empty($image_url) && !empty($atts['image_id'])) {
        $image_url = wp_get_attachment_image_url($atts['image_id'], 'large');
    }

    ob_start();
    ?>
    <section class="jj-social-block <?php echo esc_attr($reverse_class); ?>">
        <div class="jj-social-container">
            <div class="jj-social-content-col">
                <div class="jj-social-content-inner">
                    <?php if (!empty($atts['title'])) : ?>
                        <h2 class="jj-social-title"><?php echo wp_kses_post($atts['title']); ?></h2>
                    <?php endif; ?>
                    <?php if (!empty($atts['text'])) : ?>
                        <p class="jj-social-text"><?php echo wp_kses_post($atts['text']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($atts['button_text']) || !empty($atts['button2_text'])) : ?>
                        <div class="jj-social-buttons">
                            <?php if (!empty($atts['button_text'])) : ?>
                                <a href="<?php echo esc_url($atts['button_url']); ?>" target="_blank" rel="noopener" class="jj-social-button"><?php echo esc_html($atts['button_text']); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($atts['button2_text'])) : ?>
                                <a href="<?php echo esc_url($atts['button2_url']); ?>" target="_blank" rel="noopener" class="jj-social-button"><?php echo esc_html($atts['button2_text']); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="jj-social-image-col">
                <?php if (!empty($image_url)) : ?>
                    <div class="jj-social-image-wrapper">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(strip_tags($atts['title'])); ?>" class="jj-social-image" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('social_block', 'jj_social_block_shortcode');

/**
 * Shop Categories Grid Shortcode
 * Displays parent categories with images, names, and subcategories
 * Usage: [shop_categories columns="4" title="SHOP BY CATEGORY"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function jj_shop_categories_shortcode($atts) {
    $atts = shortcode_atts(array(
        'columns' => 4,
        'title'   => '',
    ), $atts, 'shop_categories');

    // Get the Uncategorized term ID to exclude it
    $uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
    $exclude_ids = $uncategorized ? array($uncategorized->term_id) : array();

    // Get parent product categories
    $categories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'exclude'    => $exclude_ids,
    ));

    if (empty($categories) || is_wp_error($categories)) {
        return '<p>No categories found.</p>';
    }

    ob_start();
    ?>
    <section class="jj-shop-categories">
        <?php if (!empty($atts['title'])) : ?>
            <h2 class="jj-shop-categories-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>

        <div class="jj-shop-categories-grid jj-grid-<?php echo esc_attr($atts['columns']); ?>">
            <?php foreach ($categories as $category) :
                // Get category image
                $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src('large');

                // Get subcategories
                $subcategories = get_terms(array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                    'parent'     => $category->term_id,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ));

                $category_link = get_term_link($category);
                ?>
                <div class="jj-shop-category-card">
                    <a href="<?php echo esc_url($category_link); ?>" class="jj-shop-category-image-link">
                        <div class="jj-shop-category-image">
                            <img src="<?php echo esc_url($image_url); ?>"
                                 alt="<?php echo esc_attr($category->name); ?>"
                                 loading="lazy" />
                        </div>
                    </a>
                    <div class="jj-shop-category-content">
                        <h3 class="jj-shop-category-name">
                            <a href="<?php echo esc_url($category_link); ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        </h3>
                        <?php if (!empty($subcategories) && !is_wp_error($subcategories)) : ?>
                            <ul class="jj-shop-subcategories">
                                <?php foreach ($subcategories as $subcategory) : ?>
                                    <li>
                                        <a href="<?php echo esc_url(get_term_link($subcategory)); ?>">
                                            <?php echo esc_html($subcategory->name); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('shop_categories', 'jj_shop_categories_shortcode');

/**
 * Add simple lightbox functionality for gallery pages
 */
function jj_gallery_lightbox_script() {
    if (is_page('gallery')) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create lightbox overlay
            const overlay = document.createElement('div');
            overlay.className = 'jj-lightbox-overlay';
            overlay.innerHTML = '<span class="jj-lightbox-close">&times;</span><img src="" alt="">';
            document.body.appendChild(overlay);

            const lightboxImg = overlay.querySelector('img');
            const closeBtn = overlay.querySelector('.jj-lightbox-close');

            // Get all gallery images
            const galleryImages = document.querySelectorAll('.wp-block-gallery img');

            galleryImages.forEach(function(img) {
                img.style.cursor = 'pointer';
                img.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Get the full size image URL
                    const fullSrc = this.src.replace(/-\d+x\d+\./, '.');
                    lightboxImg.src = fullSrc;
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            });

            // Close lightbox
            closeBtn.addEventListener('click', function() {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay.classList.contains('active')) {
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'jj_gallery_lightbox_script');
