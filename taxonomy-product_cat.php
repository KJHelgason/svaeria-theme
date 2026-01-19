<?php
/**
 * Custom Product Category Template
 * Uses the same styling as the New Arrivals section on the homepage
 */

get_header();

// Get current category
$term = get_queried_object();
$category_name = $term->name;
$category_description = $term->description;
$category_image_id = get_term_meta($term->term_id, 'thumbnail_id', true);
$category_image = $category_image_id ? wp_get_attachment_url($category_image_id) : '';

// Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$products_per_page = 12; // 3 rows of 4

// Query products in this category
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => $products_per_page,
    'paged'          => $paged,
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
    'post_status'    => 'publish',
);

// Handle sorting
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'menu_order';

switch ($orderby) {
    case 'date':
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        break;
    case 'price':
        $args['meta_key'] = '_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
        break;
    case 'price-desc':
        $args['meta_key'] = '_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        break;
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
    default:
        $args['orderby'] = 'menu_order title';
        $args['order'] = 'ASC';
        break;
}

$products = new WP_Query($args);
$total_products = $products->found_posts;
$total_pages = $products->max_num_pages;

// Get subcategories
$subcategories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'parent'     => $term->term_id,
    'hide_empty' => true,
));
?>

<main id="main" class="site-main jj-category-page">
    
    <!-- Category Header -->
    <section class="jj-category-header">
        <?php if ($category_image) : ?>
            <div class="jj-category-hero" style="background-image: url('<?php echo esc_url($category_image); ?>');">
                <div class="jj-category-hero-overlay"></div>
                <div class="jj-category-hero-content">
                    <h1 class="jj-category-title"><?php echo esc_html($category_name); ?></h1>
                    <?php if ($category_description) : ?>
                        <p class="jj-category-description"><?php echo wp_kses_post($category_description); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="jj-category-header-simple">
                <h1 class="jj-category-title"><?php echo esc_html($category_name); ?></h1>
                <?php if ($category_description) : ?>
                    <p class="jj-category-description"><?php echo wp_kses_post($category_description); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Breadcrumbs -->
    <nav class="jj-breadcrumbs">
        <div class="jj-breadcrumbs-inner">
            <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
            <span class="jj-breadcrumb-separator">/</span>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Shop</a>
            <?php
            // Show parent categories
            $ancestors = get_ancestors($term->term_id, 'product_cat');
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'product_cat');
                ?>
                <span class="jj-breadcrumb-separator">/</span>
                <a href="<?php echo esc_url(get_term_link($ancestor)); ?>"><?php echo esc_html($ancestor->name); ?></a>
                <?php
            }
            ?>
            <span class="jj-breadcrumb-separator">/</span>
            <span class="jj-breadcrumb-current"><?php echo esc_html($category_name); ?></span>
        </div>
    </nav>
    
    <!-- Subcategories (if any) -->
    <?php if (!empty($subcategories) && !is_wp_error($subcategories)) : ?>
        <section class="jj-subcategories">
            <div class="jj-subcategories-inner">
                <?php foreach ($subcategories as $subcat) : 
                    $subcat_image_id = get_term_meta($subcat->term_id, 'thumbnail_id', true);
                    $subcat_image = $subcat_image_id ? wp_get_attachment_url($subcat_image_id) : '';
                ?>
                    <a href="<?php echo esc_url(get_term_link($subcat)); ?>" class="jj-subcategory-card">
                        <div class="jj-subcategory-image">
                            <?php if ($subcat_image) : ?>
                                <img src="<?php echo esc_url($subcat_image); ?>" alt="<?php echo esc_attr($subcat->name); ?>">
                            <?php else : ?>
                                <div class="jj-subcategory-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <span class="jj-subcategory-name"><?php echo esc_html($subcat->name); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <!-- Toolbar (Count & Sorting) -->
    <div class="jj-category-toolbar">
        <div class="jj-category-toolbar-inner">
            <p class="jj-product-count">
                <?php echo esc_html($total_products); ?> <?php echo $total_products === 1 ? 'product' : 'products'; ?>
            </p>
            
            <form class="jj-sorting-form" method="get">
                <label for="jj-orderby">Sort by:</label>
                <select name="orderby" id="jj-orderby" onchange="this.form.submit()">
                    <option value="menu_order" <?php selected($orderby, 'menu_order'); ?>>Default</option>
                    <option value="date" <?php selected($orderby, 'date'); ?>>Newest</option>
                    <option value="price" <?php selected($orderby, 'price'); ?>>Price: Low to High</option>
                    <option value="price-desc" <?php selected($orderby, 'price-desc'); ?>>Price: High to Low</option>
                    <option value="popularity" <?php selected($orderby, 'popularity'); ?>>Popularity</option>
                    <option value="rating" <?php selected($orderby, 'rating'); ?>>Rating</option>
                </select>
            </form>
        </div>
    </div>
    
    <!-- Product Grid (Same as New Arrivals) -->
    <section class="jj-product-section jj-category-products">
        <?php if ($products->have_posts()) : ?>
            <div class="jj-product-grid jj-grid-4">
                <?php
                while ($products->have_posts()) :
                    $products->the_post();
                    global $product;
                    
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
            
            <!-- Pagination -->
            <?php if ($total_pages > 1) : ?>
                <nav class="jj-pagination">
                    <?php
                    echo paginate_links(array(
                        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'format'    => '?paged=%#%',
                        'current'   => max(1, $paged),
                        'total'     => $total_pages,
                        'prev_text' => '&larr; Previous',
                        'next_text' => 'Next &rarr;',
                        'type'      => 'list',
                    ));
                    ?>
                </nav>
            <?php endif; ?>
            
        <?php else : ?>
            <div class="jj-no-products">
                <p>No products found in this category.</p>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="jj-see-more-button">BROWSE ALL PRODUCTS</a>
            </div>
        <?php endif; ?>
    </section>
    
</main>

<?php
wp_reset_postdata();
get_footer();
