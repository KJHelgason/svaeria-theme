<?php
/**
 * Main Header Template
 * 
 * Custom header with mega menu, search dropdown, and currency switcher
 */

// Get WooCommerce cart count
$cart_count = 0;
if (class_exists('WooCommerce') && WC()->cart) {
    $cart_count = WC()->cart->get_cart_contents_count();
}

// Get the Uncategorized term ID to exclude it
$uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
$exclude_ids = $uncategorized ? array($uncategorized->term_id) : array();

// Custom category order (parent categories)
$category_order = array('Clothing', 'Flowers', 'Accessories', 'Antiques');

// Custom subcategory order per parent category
$subcategory_order = array(
    'Clothing'    => array('Costumes', 'Easywear', 'Armour'),
    'Flowers'     => array('Boutiques', 'Individual'),
    'Accessories' => array('Belts', 'Hats', 'Gloves', 'Bags'),
    'Antiques'    => array('Furniture', 'Accessories', 'Home Decor', 'Kitchen'),
);

// Get product categories for mega menu (excluding Uncategorized)
$product_categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'parent'     => 0, // Only parent categories
    'exclude'    => $exclude_ids,
));

// Sort categories by custom order
if (!empty($product_categories) && !is_wp_error($product_categories)) {
    usort($product_categories, function($a, $b) use ($category_order) {
        $pos_a = array_search($a->name, $category_order);
        $pos_b = array_search($b->name, $category_order);
        // If not in custom order, place at end alphabetically
        if ($pos_a === false) $pos_a = 999;
        if ($pos_b === false) $pos_b = 999;
        if ($pos_a === $pos_b) return strcmp($a->name, $b->name);
        return $pos_a - $pos_b;
    });
}
?>

<!-- Sticky Header Wrapper -->
<div class="jj-sticky-header-wrap">

<!-- Top Bar -->
<div class="top-bar">
    <span>Free Shipping</span>
    <span>Worldwide Delivery</span>
    <span>Secure Payments</span>
</div>

<!-- Main Header -->
<header id="masthead" class="jj-main-header">
    <div class="jj-header-inner">
        <div class="jj-header-container">
            
            <!-- Mobile Left Section (Burger + Currency) - Only visible on mobile -->
            <div class="jj-mobile-left">
                <!-- Mobile Menu Toggle -->
                <button class="jj-header-icon jj-mobile-toggle" aria-label="<?php esc_attr_e('Open Menu', 'kadence-child'); ?>" type="button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                
                <!-- Mobile Currency (simplified) -->
                <button class="jj-header-icon jj-mobile-currency-toggle" aria-label="<?php esc_attr_e('Change Currency', 'kadence-child'); ?>" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </button>
            </div>
            
            <!-- Logo -->
            <div class="jj-header-logo">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title-link">
                        <span class="site-title"><?php bloginfo('name'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Main Navigation -->
            <nav class="jj-header-nav" aria-label="<?php esc_attr_e('Primary Navigation', 'kadence-child'); ?>">
                <ul class="jj-nav-menu">
                    <!-- Home -->
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                    </li>
                    
                    <!-- Shop with Mega Menu -->
                    <li class="jj-has-mega-menu">
                        <a href="<?php echo esc_url(home_url('/shop')); ?>">Shop</a>
                        
                        <!-- Mega Menu Dropdown -->
                        <div class="jj-mega-menu">
                            <div class="jj-mega-menu-inner">
                                <?php 
                                if (!empty($product_categories) && !is_wp_error($product_categories)) :
                                    foreach ($product_categories as $category) :
                                        // Get category image
                                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                        $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src('medium');
                                        
                                        // Get subcategories
                                        $subcategories = get_terms(array(
                                            'taxonomy'   => 'product_cat',
                                            'hide_empty' => false,
                                            'parent'     => $category->term_id,
                                        ));

                                        // Sort subcategories by custom order
                                        if (!empty($subcategories) && !is_wp_error($subcategories) && isset($subcategory_order[$category->name])) {
                                            $sub_order = $subcategory_order[$category->name];
                                            usort($subcategories, function($a, $b) use ($sub_order) {
                                                $pos_a = array_search($a->name, $sub_order);
                                                $pos_b = array_search($b->name, $sub_order);
                                                if ($pos_a === false) $pos_a = 999;
                                                if ($pos_b === false) $pos_b = 999;
                                                if ($pos_a === $pos_b) return strcmp($a->name, $b->name);
                                                return $pos_a - $pos_b;
                                            });
                                        }
                                        
                                        $category_link = get_term_link($category);
                                        ?>
                                        <div class="jj-mega-category">
                                            <!-- Category Image -->
                                            <div class="jj-mega-category-image">
                                                <a href="<?php echo esc_url($category_link); ?>">
                                                    <img src="<?php echo esc_url($image_url); ?>" 
                                                         alt="<?php echo esc_attr($category->name); ?>"
                                                         width="250" 
                                                         height="376"
                                                         loading="lazy" />
                                                </a>
                                            </div>
                                            
                                            <!-- Category Content - On the right of image -->
                                            <div class="jj-mega-category-content">
                                                <h3 class="jj-mega-category-title">
                                                    <a href="<?php echo esc_url($category_link); ?>">
                                                        <?php echo esc_html($category->name); ?>
                                                    </a>
                                                </h3>
                                                
                                                <?php if (!empty($subcategories) && !is_wp_error($subcategories)) : ?>
                                                    <ul class="jj-mega-subcategories">
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
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    </li>
                    
                    <!-- Gallery -->
                    <li>
                        <a href="<?php echo esc_url(home_url('/gallery')); ?>">Gallery</a>
                    </li>
                    
                    <!-- Social Media -->
                    <li>
                        <a href="<?php echo esc_url(home_url('/social-media')); ?>">Social Media</a>
                    </li>
                    
                    <!-- About Us -->
                    <li>
                        <a href="<?php echo esc_url(home_url('/about-us')); ?>">About Us</a>
                    </li>
                </ul>
            </nav>
            
            <!-- Header Actions (Icons) -->
            <div class="jj-header-actions">
                
                <!-- Search Toggle -->
                <div class="jj-search-wrapper" id="jj-search-wrapper">
                    <button class="jj-header-icon jj-search-toggle" id="jj-search-toggle" aria-label="<?php esc_attr_e('Toggle Search', 'kadence-child'); ?>" type="button">
                        <!-- Search Icon (shown when closed) -->
                        <svg class="jj-search-icon-open" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <!-- X Icon (shown when open) -->
                        <svg class="jj-search-icon-close" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                    
                    <!-- Search Dropdown - Full Width -->
                    <div class="jj-search-dropdown" id="jj-search-dropdown">
                        <h3 class="jj-dropdown-title">SEARCH</h3>
                        <form role="search" method="get" class="jj-search-dropdown-form" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="flex-row the-search">
                                <div class="flex-col search-icon-col">
                                    <i class="search-icon">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="M21 21l-4.35-4.35"/>
                                        </svg>
                                    </i>
                                </div>
                                <div class="flex-col flex-grow">
                                    <input type="search" 
                                           class="search-field" 
                                           id="jj-product-search"
                                           placeholder="<?php esc_attr_e('Search…', 'kadence-child'); ?>" 
                                           value="<?php echo get_search_query(); ?>" 
                                           name="s" 
                                           autocomplete="off" />
                                    <input type="hidden" name="post_type" value="product" />
                                </div>
                                <div class="flex-col submit-col">
                                    <button type="submit" class="ux-search-submit submit-button" aria-label="Submit">
                                        <i class="arrow-icon">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M5 12h14M12 5l7 7-7 7"/>
                                            </svg>
                                        </i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- Live Search Results -->
                        <div class="jj-search-results" id="jj-search-results"></div>
                    </div>
                </div>
                
                <!-- Account Icon -->
                <a href="<?php echo esc_url(home_url('/my-account')); ?>" class="jj-header-icon jj-account-link" aria-label="<?php esc_attr_e('My Account', 'kadence-child'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </a>
                
                <!-- Cart Icon with Dropdown -->
                <div class="jj-cart-wrapper">
                    <a href="<?php echo esc_url(home_url('/cart')); ?>" class="jj-header-icon jj-cart-link" aria-label="<?php esc_attr_e('Cart', 'kadence-child'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        <?php if ($cart_count > 0) : ?>
                            <span class="jj-cart-count"><?php echo esc_html($cart_count); ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Cart Dropdown -->
                    <div class="jj-cart-dropdown">
                        <?php if (class_exists('WooCommerce') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
                            <!-- Cart has items -->
                            <div class="jj-cart-dropdown-header">
                                Shopping Cart (<?php echo WC()->cart->get_cart_contents_count(); ?>)
                            </div>
                            <div class="jj-cart-dropdown-items">
                                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : 
                                    $product = $cart_item['data'];
                                    $product_id = $cart_item['product_id'];
                                    $product_name = $product->get_name();
                                    $product_price = WC()->cart->get_product_price($product);
                                    $product_qty = $cart_item['quantity'];
                                    $product_permalink = $product->get_permalink();
                                    $thumbnail = $product->get_image('thumbnail');
                                ?>
                                    <div class="jj-cart-dropdown-item">
                                        <div class="jj-cart-dropdown-item-image">
                                            <a href="<?php echo esc_url($product_permalink); ?>">
                                                <?php echo $thumbnail; ?>
                                            </a>
                                        </div>
                                        <div class="jj-cart-dropdown-item-details">
                                            <a href="<?php echo esc_url($product_permalink); ?>" class="jj-cart-dropdown-item-name">
                                                <?php echo esc_html($product_name); ?>
                                            </a>
                                            <span class="jj-cart-dropdown-item-meta">
                                                <?php echo $product_qty; ?> × <?php echo $product_price; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="jj-cart-dropdown-footer">
                                <div class="jj-cart-dropdown-subtotal">
                                    <span class="jj-cart-dropdown-subtotal-label">Subtotal:</span>
                                    <span class="jj-cart-dropdown-subtotal-value"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                                </div>
                                <div class="jj-cart-dropdown-buttons">
                                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="button button-outline">View Cart</a>
                                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button">Checkout</a>
                                </div>
                            </div>
                        <?php else : ?>
                            <!-- Cart is empty -->
                            <div class="jj-cart-dropdown-empty">
                                <div class="jj-cart-dropdown-empty-icon">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                        <line x1="3" y1="6" x2="21" y2="6"/>
                                        <path d="M16 10a4 4 0 0 1-8 0"/>
                                    </svg>
                                </div>
                                <p class="jj-cart-dropdown-empty-text">No products in the cart.</p>
                                <a href="<?php echo esc_url(home_url('/shop')); ?>" class="button">Return to Shop</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Currency Switcher -->
                <?php
                // Get currencies from our custom system
                $currencies = jj_get_currency_config();
                $current_code = jj_get_current_currency();
                $current_config = $currencies[$current_code];
                $current_display = $current_config['name'];
                ?>
                <div class="jj-currency-switcher" id="jj-currency-switcher">
                    <button class="jj-currency-btn" id="jj-currency-btn" type="button" aria-label="<?php esc_attr_e('Change Currency', 'kadence-child'); ?>">
                        <span class="currency-text"><?php echo esc_html($current_display . ' / ' . $current_code); ?></span>
                    </button>
                    <!-- Currency Dropdown - Full Width -->
                    <div class="jj-currency-dropdown" id="jj-currency-dropdown">
                        <h3 class="jj-dropdown-title">CURRENCY</h3>
                        <div class="jj-currency-options">
                            <?php
                            foreach ($currencies as $code => $config) {
                                $active_class = ($code === $current_code) ? 'active' : '';
                                $display_name = $config['name'];
                                echo '<a href="#" data-currency="' . esc_attr($code) . '" data-display="' . esc_attr($display_name . ' / ' . $code) . '" class="jj-currency-option ' . $active_class . '">';
                                echo esc_html($display_name . ' / ' . $code);
                                echo '</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</header>

<!-- Mobile Menu Drawer -->
<div class="jj-mobile-drawer" aria-hidden="true">
    <div class="jj-mobile-drawer-overlay"></div>
    <div class="jj-mobile-drawer-content">
        <div class="jj-mobile-drawer-header">
            <button class="jj-mobile-drawer-close" aria-label="<?php esc_attr_e('Close Menu', 'kadence-child'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <nav class="jj-mobile-drawer-nav">
            <ul class="jj-mobile-menu">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li class="jj-mobile-has-submenu">
                    <a href="<?php echo esc_url(home_url('/shop')); ?>">Shop</a>
                    <button class="jj-mobile-submenu-toggle" aria-label="Toggle submenu">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 4.5L6 7.5L9 4.5"/>
                        </svg>
                    </button>
                    <ul class="jj-mobile-submenu">
                        <?php 
                        if (!empty($product_categories) && !is_wp_error($product_categories)) :
                            foreach ($product_categories as $category) :
                                ?>
                                <li>
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                        <?php echo esc_html($category->name); ?>
                                    </a>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </li>
                <li><a href="<?php echo esc_url(home_url('/gallery')); ?>">Gallery</a></li>
                <li><a href="<?php echo esc_url(home_url('/social-media')); ?>">Social Media</a></li>
                <li><a href="<?php echo esc_url(home_url('/about-us')); ?>">About Us</a></li>
                <li><a href="<?php echo esc_url(home_url('/my-account')); ?>">My Account</a></li>
            </ul>
        </nav>
        <div class="jj-mobile-drawer-footer">
            <div class="jj-mobile-currency-select">
                <label>Currency</label>
                <select id="jj-mobile-currency">
                    <?php
                    $currencies = jj_get_currency_config();
                    $current_code = jj_get_current_currency();
                    foreach ($currencies as $code => $config) {
                        $selected = ($code === $current_code) ? 'selected' : '';
                        echo '<option value="' . esc_attr($code) . '" ' . $selected . '>' . esc_html($config['name'] . ' / ' . $code) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
</div>

</div><!-- End Sticky Header Wrapper -->