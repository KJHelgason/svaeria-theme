<?php
/**
 * Template Name: Gallery Page
 * 
 * Masonry-style gallery layout inspired by Jonna Jinton
 * 
 * HOW TO USE:
 * 1. Edit this page in WordPress admin
 * 2. Add a "Gallery" block
 * 3. Select images from your Media Library
 * 4. The images will automatically display in the masonry layout below
 * 
 * Or use the Featured Image and page content for custom layouts
 */

get_header();

// Get the page content
while (have_posts()) : the_post();
    
    $page_title = get_the_title();
    $page_content = get_the_content();
    
    // Get gallery images from page content
    $gallery_images = array();
    
    // Check for WordPress Gallery block or shortcode
    if (has_block('gallery')) {
        $blocks = parse_blocks($page_content);
        foreach ($blocks as $block) {
            if ($block['blockName'] === 'core/gallery') {
                if (!empty($block['innerBlocks'])) {
                    foreach ($block['innerBlocks'] as $image_block) {
                        if ($image_block['blockName'] === 'core/image' && !empty($image_block['attrs']['id'])) {
                            $gallery_images[] = $image_block['attrs']['id'];
                        }
                    }
                }
            }
        }
    }
    
    // Fallback: Get attached images
    if (empty($gallery_images)) {
        $attachments = get_attached_media('image', get_the_ID());
        foreach ($attachments as $attachment) {
            $gallery_images[] = $attachment->ID;
        }
    }
    
    // Fallback: Get images from Media Library (last 12 uploaded)
    if (empty($gallery_images)) {
        $media_query = new WP_Query(array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));
        
        if ($media_query->have_posts()) {
            while ($media_query->have_posts()) {
                $media_query->the_post();
                $gallery_images[] = get_the_ID();
            }
            wp_reset_postdata();
        }
    }
    
    // Define grid pattern for masonry layout (repeats as needed for all images)
    $grid_pattern_base = array(
        'text',        // Text block (only shown once at start)
        'medium-v',    // Medium vertical
        'large',       // Large square
        'medium-h',    // Medium horizontal
        'large',       // Large square
        'medium',      // Medium square
        'large',       // Large square
        'medium-v',    // Medium vertical
        'medium-h',    // Medium horizontal
        'large',       // Large square
        'medium',      // Medium square
        'large',       // Large square
    );

    // Build pattern to accommodate all images
    $total_images = count($gallery_images);
    $grid_pattern = array('text'); // Start with text
    $image_patterns = array('medium-v', 'large', 'medium-h', 'large', 'medium', 'large', 'medium-v', 'medium-h', 'large', 'medium', 'large');

    // Add enough pattern items for all images
    $pattern_index = 0;
    for ($i = 0; $i < $total_images; $i++) {
        $grid_pattern[] = $image_patterns[$pattern_index % count($image_patterns)];
        $pattern_index++;
    }
    
    ?>
    
    <main id="main" class="site-main jj-gallery-page">
        
        <!-- Masonry Gallery Grid -->
        <section class="jj-masonry-gallery">
            <div class="jj-masonry-grid">
                
                <?php
                $image_index = 0;
                
                foreach ($grid_pattern as $index => $grid_class) {
                    
                    // Text block
                    if ($grid_class === 'text') {
                        ?>
                        <div class="jj-masonry-item jj-masonry-text jj-grid-small">
                            <div class="jj-text-content">
                                <h2>Inspired by Nature</h2>
                                <p>Each piece in our collection tells a story of the Nordic wilderness, crafted with love and care in the heart of Iceland.</p>
                            </div>
                        </div>
                        <?php
                        continue;
                    }
                    
                    // Image blocks
                    if (isset($gallery_images[$image_index])) {
                        $image_id = $gallery_images[$image_index];
                        $image_url = wp_get_attachment_image_url($image_id, 'large');
                        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                        $image_title = get_the_title($image_id);
                        
                        if ($image_url) {
                            ?>
                            <div class="jj-masonry-item jj-masonry-image jj-grid-<?php echo esc_attr($grid_class); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     alt="<?php echo esc_attr($image_alt ? $image_alt : $image_title); ?>" 
                                     loading="lazy">
                            </div>
                            <?php
                        }
                        $image_index++;
                    }
                }
                
                // If no images found, show a message
                if (empty($gallery_images)) {
                    ?>
                    <div class="jj-masonry-item jj-masonry-text jj-grid-large" style="grid-column: span 2;">
                        <div class="jj-text-content">
                            <h2>Add Your Images</h2>
                            <p>Edit this page and add a Gallery block to display your images here. Select images from your Media Library and they will automatically appear in this beautiful masonry layout.</p>
                        </div>
                    </div>
                    <?php
                }
                ?>
                
            </div>
        </section>
        
    </main>
    
    <?php
endwhile;

get_footer();
