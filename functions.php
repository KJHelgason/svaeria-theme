<?php
/**
 * Kadence Child Theme - Nordic Jewelry
 * 
 * Functions and definitions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * CURRENCY CONVERTER SYSTEM
 * ============================================================================
 * 
 * A built-in multi-currency system for WooCommerce.
 * Base currency is SEK (Swedish Krona).
 */

// Currency configuration - exchange rates from ISK (Icelandic Króna)
// Update these rates periodically or integrate with an API
function jj_get_currency_config() {
    return array(
        'ISK' => array(
            'rate'     => 1,           // Base currency
            'symbol'   => 'kr',
            'position' => 'right_space', // Symbol on right with space: "100 kr"
            'decimals' => 0,
            'name'     => 'IS',
        ),
        'USD' => array(
            'rate'     => 0.0071,      // 1 ISK = 0.0071 USD (approx)
            'symbol'   => '$',
            'position' => 'left',      // Symbol on left: "$10"
            'decimals' => 2,
            'name'     => 'USA',
        ),
        'EUR' => array(
            'rate'     => 0.0067,      // 1 ISK = 0.0067 EUR (approx)
            'symbol'   => '€',
            'position' => 'left',
            'decimals' => 2,
            'name'     => 'EU',
        ),
        'GBP' => array(
            'rate'     => 0.0057,      // 1 ISK = 0.0057 GBP (approx)
            'symbol'   => '£',
            'position' => 'left',
            'decimals' => 2,
            'name'     => 'UK',
        ),
        'NOK' => array(
            'rate'     => 0.079,       // 1 ISK = 0.079 NOK (approx)
            'symbol'   => 'kr',
            'position' => 'right_space',
            'decimals' => 0,
            'name'     => 'NO',
        ),
        'DKK' => array(
            'rate'     => 0.050,       // 1 ISK = 0.050 DKK (approx)
            'symbol'   => 'kr',
            'position' => 'right_space',
            'decimals' => 0,
            'name'     => 'DK',
        ),
        'SEK' => array(
            'rate'     => 0.076,       // 1 ISK = 0.076 SEK (approx)
            'symbol'   => 'kr',
            'position' => 'right_space',
            'decimals' => 0,
            'name'     => 'SE',
        ),
    );
}

// Get current currency from cookie or default to ISK
function jj_get_current_currency() {
    if (isset($_COOKIE['jj_currency']) && !empty($_COOKIE['jj_currency'])) {
        $currency = sanitize_text_field($_COOKIE['jj_currency']);
        $currencies = jj_get_currency_config();
        if (isset($currencies[$currency])) {
            return $currency;
        }
    }
    return 'ISK'; // Default currency
}

// Set currency via AJAX
function jj_set_currency_ajax() {
    $currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : 'SEK';
    $currencies = jj_get_currency_config();
    
    if (isset($currencies[$currency])) {
        setcookie('jj_currency', $currency, time() + (86400 * 30), '/'); // 30 days
        wp_send_json_success(array('currency' => $currency));
    } else {
        wp_send_json_error(array('message' => 'Invalid currency'));
    }
    wp_die();
}
add_action('wp_ajax_jj_set_currency', 'jj_set_currency_ajax');
add_action('wp_ajax_nopriv_jj_set_currency', 'jj_set_currency_ajax');

// Convert price from SEK to selected currency
function jj_convert_price($price_in_sek) {
    $currency = jj_get_current_currency();
    $currencies = jj_get_currency_config();
    
    if (!isset($currencies[$currency])) {
        return $price_in_sek;
    }
    
    $config = $currencies[$currency];
    return $price_in_sek * $config['rate'];
}

// Format price with correct currency symbol and position
function jj_format_price($price, $currency_code = null) {
    if ($currency_code === null) {
        $currency_code = jj_get_current_currency();
    }
    
    $currencies = jj_get_currency_config();
    
    if (!isset($currencies[$currency_code])) {
        return wc_price($price);
    }
    
    $config = $currencies[$currency_code];
    $formatted_number = number_format($price, $config['decimals'], '.', ',');
    
    switch ($config['position']) {
        case 'left':
            return $config['symbol'] . $formatted_number;
        case 'left_space':
            return $config['symbol'] . ' ' . $formatted_number;
        case 'right':
            return $formatted_number . $config['symbol'];
        case 'right_space':
            return $formatted_number . ' ' . $config['symbol'];
        default:
            return $config['symbol'] . $formatted_number;
    }
}

// Filter WooCommerce price HTML
function jj_filter_woocommerce_price($price_html, $product) {
    $currency = jj_get_current_currency();
    
    // If SEK (base currency), return original
    if ($currency === 'SEK') {
        return $price_html;
    }
    
    // Get original price(s) in SEK
    if ($product->is_type('variable')) {
        $min_price = $product->get_variation_price('min', true);
        $max_price = $product->get_variation_price('max', true);
        
        $min_converted = jj_convert_price($min_price);
        $max_converted = jj_convert_price($max_price);
        
        if ($min_price === $max_price) {
            return jj_format_price($min_converted);
        } else {
            return jj_format_price($min_converted) . ' – ' . jj_format_price($max_converted);
        }
    } else {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        
        if ($sale_price && $sale_price < $regular_price) {
            $regular_converted = jj_convert_price($regular_price);
            $sale_converted = jj_convert_price($sale_price);
            return '<del>' . jj_format_price($regular_converted) . '</del> <ins>' . jj_format_price($sale_converted) . '</ins>';
        } else {
            $price = $product->get_price();
            $converted = jj_convert_price($price);
            return jj_format_price($converted);
        }
    }
}
add_filter('woocommerce_get_price_html', 'jj_filter_woocommerce_price', 100, 2);

// Filter cart item price
function jj_filter_cart_item_price($price_html, $cart_item, $cart_item_key) {
    $currency = jj_get_current_currency();
    
    if ($currency === 'SEK') {
        return $price_html;
    }
    
    $product = $cart_item['data'];
    $price = $product->get_price();
    $converted = jj_convert_price($price);
    
    return jj_format_price($converted);
}
add_filter('woocommerce_cart_item_price', 'jj_filter_cart_item_price', 100, 3);

// Filter cart subtotal
function jj_filter_cart_subtotal($subtotal, $compound, $cart) {
    $currency = jj_get_current_currency();
    
    if ($currency === 'SEK') {
        return $subtotal;
    }
    
    $total = $cart->get_subtotal();
    $converted = jj_convert_price($total);
    
    return jj_format_price($converted);
}
add_filter('woocommerce_cart_subtotal', 'jj_filter_cart_subtotal', 100, 3);

// Filter cart total
function jj_filter_cart_total($total) {
    $currency = jj_get_current_currency();
    
    if ($currency === 'SEK') {
        return $total;
    }
    
    // Remove currency symbol and convert
    $numeric_total = floatval(preg_replace('/[^0-9.]/', '', $total));
    $converted = jj_convert_price($numeric_total);
    
    return jj_format_price($converted);
}
add_filter('woocommerce_cart_total', 'jj_filter_cart_total', 100);

// Also filter the raw price amount for mini-cart
function jj_filter_formatted_price($formatted_price, $price, $decimals, $decimal_separator, $thousand_separator) {
    $currency = jj_get_current_currency();
    
    if ($currency === 'SEK') {
        return $formatted_price;
    }
    
    $converted = jj_convert_price(floatval($price));
    $currencies = jj_get_currency_config();
    $config = $currencies[$currency];
    
    return number_format($converted, $config['decimals'], $decimal_separator, $thousand_separator);
}
// Note: This filter can cause issues, so we'll use a different approach

// End of Currency Converter System
// ============================================================================

/**
 * Enqueue parent and child theme styles
 */
function kadence_child_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style(
        'kadence-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('kadence')->get('Version')
    );
    
    // Child theme style
    wp_enqueue_style(
        'kadence-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('kadence-parent-style'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'kadence_child_enqueue_styles');

/**
 * Register footer menu locations
 */
function jj_register_footer_menus() {
    register_nav_menus(array(
        'footer-products' => __('Footer - Products', 'kadence-child'),
        'footer-company'  => __('Footer - Company', 'kadence-child'),
        'footer-service'  => __('Footer - Service', 'kadence-child'),
    ));
}
add_action('after_setup_theme', 'jj_register_footer_menus');

/**
 * Use custom footer template
 */
function jj_custom_footer() {
    // Remove Kadence's default footer
    remove_action('kadence_footer', 'Kadence\footer_markup');
}
add_action('wp', 'jj_custom_footer');

/**
 * Load our custom footer
 */
function jj_load_custom_footer() {
    get_template_part('footer', 'custom');
    exit; // Prevent default footer from loading
}

/**
 * Override get_footer to use our custom footer
 */
function jj_override_footer($name) {
    if ($name === null || $name === '') {
        // Load our custom footer instead
        include(get_stylesheet_directory() . '/footer-custom.php');
        return true; // Prevents the default footer from loading
    }
    return false;
}

/**
 * Add inline JavaScript for header functionality (single consolidated script)
 */
function kadence_child_inline_scripts() {
    ?>
    <script type="text/javascript">
    (function() {
        'use strict';
        
        // Wait for DOM to be fully loaded
        function init() {
            var $ = jQuery;
            
            console.log('[Nordic] Initializing header scripts...');
            
            // Cache DOM elements
            var $searchWrapper = $('.jj-search-wrapper');
            var $searchToggle = $('.jj-search-toggle');
            var $searchInput = $('.jj-search-dropdown .search-field');
            var $currencySwitcher = $('.jj-currency-switcher');
            var $currencyBtn = $('.jj-currency-btn');
            var $cartWrapper = $('.jj-cart-wrapper');
            var $megaMenuItems = $('.jj-has-mega-menu');
            
            console.log('[Nordic] Elements found - Search:', $searchToggle.length, 'Currency:', $currencyBtn.length);
            
            // Remove any existing handlers to prevent duplicates
            $searchToggle.off('click.nordic');
            $currencyBtn.off('click.nordic');
            $(document).off('click.nordic keyup.nordic');
            
            // Search Toggle - use namespaced events
            $searchToggle.on('click.nordic', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('[Nordic] Search toggle clicked');
                
                // Close other dropdowns
                $currencySwitcher.removeClass('is-open');
                
                // Toggle search
                $searchWrapper.toggleClass('search-active');
                
                // Focus input when opening
                if ($searchWrapper.hasClass('search-active')) {
                    console.log('[Nordic] Search opened, focusing input');
                    setTimeout(function() {
                        $searchInput.focus();
                    }, 150);
                }
            });
            
            // Currency Toggle
            $currencyBtn.on('click.nordic', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('[Nordic] Currency button clicked');
                
                // Close other dropdowns
                $searchWrapper.removeClass('search-active');
                
                // Toggle currency
                $currencySwitcher.toggleClass('is-open');
            });
            
            // Currency Selection - Custom Currency Converter
            $('.jj-currency-option').on('click.nordic', function(e) {
                e.preventDefault();
                var $this = $(this);
                var currency = $this.data('currency');
                var displayText = $this.data('display') || currency;
                
                console.log('[Nordic] Currency selected:', currency);
                
                // Update button text
                $currencyBtn.find('.currency-text').text(displayText);
                
                // Update active state
                $('.jj-currency-option').removeClass('active');
                $this.addClass('active');
                
                // Close dropdown
                $currencySwitcher.removeClass('is-open');
                
                // Use our custom currency converter AJAX
                $.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'jj_set_currency',
                        currency: currency
                    },
                    success: function(response) {
                        console.log('[Nordic] Currency set:', response);
                        location.reload();
                    },
                    error: function() {
                        // Fallback: set cookie directly and reload
                        document.cookie = 'jj_currency=' + currency + ';path=/;max-age=' + (86400 * 30);
                        location.reload();
                    }
                });
            });
            
            // Close dropdowns on outside click
            $(document).on('click.nordic', function(e) {
                if (!$(e.target).closest('.jj-search-wrapper').length) {
                    $searchWrapper.removeClass('search-active');
                }
                if (!$(e.target).closest('.jj-currency-switcher').length) {
                    $currencySwitcher.removeClass('is-open');
                }
            });
            
            // Escape key closes dropdowns
            $(document).on('keyup.nordic', function(e) {
                if (e.keyCode === 27 || e.key === 'Escape') {
                    $searchWrapper.removeClass('search-active');
                    $currencySwitcher.removeClass('is-open');
                    $mobileDrawer.removeClass('is-open');
                }
            });
            
            // Mega menu hover
            $megaMenuItems.on('mouseenter.nordic', function() {
                $(this).addClass('mega-menu-open');
            }).on('mouseleave.nordic', function() {
                $(this).removeClass('mega-menu-open');
            });
            
            // Cart dropdown hover
            $cartWrapper.on('mouseenter.nordic', function() {
                $searchWrapper.removeClass('search-active');
                $currencySwitcher.removeClass('is-open');
                $(this).addClass('is-open');
            }).on('mouseleave.nordic', function() {
                $(this).removeClass('is-open');
            });
            
            // ========================================
            // MOBILE MENU
            // ========================================
            var $mobileDrawer = $('.jj-mobile-drawer');
            var $mobileToggle = $('.jj-mobile-toggle');
            var $mobileClose = $('.jj-mobile-drawer-close');
            var $mobileOverlay = $('.jj-mobile-drawer-overlay');
            var $mobileCurrencyToggle = $('.jj-mobile-currency-toggle');
            var $mobileSubmenuToggles = $('.jj-mobile-submenu-toggle');
            
            // Open mobile drawer
            $mobileToggle.on('click.nordic', function(e) {
                e.preventDefault();
                console.log('[Nordic] Mobile menu toggle clicked');
                $mobileDrawer.addClass('is-open');
                $('body').addClass('mobile-menu-open');
            });
            
            // Close mobile drawer
            $mobileClose.on('click.nordic', function(e) {
                e.preventDefault();
                $mobileDrawer.removeClass('is-open');
                $('body').removeClass('mobile-menu-open');
            });
            
            $mobileOverlay.on('click.nordic', function() {
                $mobileDrawer.removeClass('is-open');
                $('body').removeClass('mobile-menu-open');
            });
            
            // Mobile currency toggle - opens the currency dropdown on desktop-style
            $mobileCurrencyToggle.on('click.nordic', function(e) {
                e.preventDefault();
                console.log('[Nordic] Mobile currency toggle clicked');
                $currencySwitcher.toggleClass('is-open');
            });
            
            // Mobile currency select dropdown
            $('#jj-mobile-currency').on('change.nordic', function() {
                var currency = $(this).val();
                console.log('[Nordic] Mobile currency changed:', currency);
                
                $.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: {
                        action: 'jj_set_currency',
                        currency: currency
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        document.cookie = 'jj_currency=' + currency + ';path=/;max-age=' + (86400 * 30);
                        location.reload();
                    }
                });
            });
            
            // Mobile submenu toggles
            $mobileSubmenuToggles.on('click.nordic', function(e) {
                e.preventDefault();
                var $parent = $(this).closest('.jj-mobile-has-submenu');
                $parent.toggleClass('submenu-open');
                $parent.find('.jj-mobile-submenu').slideToggle(200);
            });
            
            // ========================================
            // LIVE PRODUCT SEARCH
            // ========================================
            var $searchResults = $('#jj-search-results');
            var searchTimeout = null;
            
            $searchInput.on('input.nordic', function() {
                var searchTerm = $(this).val();
                
                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Hide results if less than 2 characters
                if (searchTerm.length < 2) {
                    $searchResults.html('').removeClass('has-results');
                    return;
                }
                
                // Show loading state
                $searchResults.html('<div class="jj-search-loading">Searching...</div>').addClass('has-results');
                
                // Debounce the search (wait 300ms after user stops typing)
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'jj_live_search',
                            search_term: searchTerm
                        },
                        success: function(response) {
                            if (response.success && response.data.html) {
                                $searchResults.html(response.data.html).addClass('has-results');
                            } else {
                                $searchResults.html('').removeClass('has-results');
                            }
                        },
                        error: function() {
                            $searchResults.html('<div class="jj-search-no-results">Search error. Please try again.</div>').addClass('has-results');
                        }
                    });
                }, 300);
            });
            
            // Clear results when search dropdown closes
            $searchToggle.on('click.nordic-search', function() {
                if ($searchWrapper.hasClass('search-active')) {
                    // Closing - clear results after animation
                    setTimeout(function() {
                        $searchResults.html('').removeClass('has-results');
                        $searchInput.val('');
                    }, 300);
                }
            });
            
            console.log('[Nordic] All handlers attached successfully!');
        }
        
        // Initialize when jQuery is ready
        if (typeof jQuery !== 'undefined') {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(init, 1);
            } else {
                document.addEventListener('DOMContentLoaded', init);
            }
        } else {
            console.error('[Nordic] jQuery not found!');
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'kadence_child_inline_scripts', 100);

/**
 * Enqueue WooCommerce add-to-cart scripts
 */
function jj_enqueue_wc_cart_scripts() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-add-to-cart');
    }
}
add_action('wp_enqueue_scripts', 'jj_enqueue_wc_cart_scripts');

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

/**
 * Add Google Fonts - Inter + Spectral (Jonna Jinton Style)
 */
function kadence_child_google_fonts() {
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Spectral:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'kadence_child_google_fonts');

/**
 * Remove default Kadence header and add custom header
 */
function kadence_child_custom_header() {
    // Include our custom header template from child theme
    $header_file = get_stylesheet_directory() . '/template-parts/header/header-main.php';
    if (file_exists($header_file)) {
        include($header_file);
    }
}

/**
 * Output custom header before Kadence's header and hide Kadence's header with CSS
 */
function kadence_child_output_custom_header() {
    kadence_child_custom_header();
}
add_action('wp_body_open', 'kadence_child_output_custom_header', 5);

/**
 * Hide Kadence's default header with CSS
 */
function kadence_child_hide_kadence_header() {
    echo '<style>
        .site-header.kadence-header { display: none !important; }
        header#masthead.site-header { display: none !important; }
        .kadence-header { display: none !important; }
        /* But show our custom header */
        header#masthead.jj-main-header { display: block !important; }
    </style>';
}
add_action('wp_head', 'kadence_child_hide_kadence_header', 999);

/**
 * Add custom header via shortcode for flexibility
 */
function kadence_child_header_shortcode() {
    ob_start();
    get_template_part('template-parts/header/header', 'main');
    return ob_get_clean();
}
add_shortcode('jj_header', 'kadence_child_header_shortcode');

/**
 * Register custom widget areas
 */
function kadence_child_widgets_init() {
    // Footer Column 1 - Products
    register_sidebar(array(
        'name'          => __('Footer - Products', 'kadence-child'),
        'id'            => 'footer-products',
        'description'   => __('Footer widget area for product links', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Column 2 - Company
    register_sidebar(array(
        'name'          => __('Footer - Company', 'kadence-child'),
        'id'            => 'footer-company',
        'description'   => __('Footer widget area for company links', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Column 3 - Service
    register_sidebar(array(
        'name'          => __('Footer - Service', 'kadence-child'),
        'id'            => 'footer-service',
        'description'   => __('Footer widget area for service links', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Column 4 - Contact
    register_sidebar(array(
        'name'          => __('Footer - Contact', 'kadence-child'),
        'id'            => 'footer-contact',
        'description'   => __('Footer widget area for contact info', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Newsletter Section
    register_sidebar(array(
        'name'          => __('Newsletter Section', 'kadence-child'),
        'id'            => 'newsletter-section',
        'description'   => __('Widget area for newsletter signup', 'kadence-child'),
        'before_widget' => '<div id="%1$s" class="widget newsletter-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'kadence_child_widgets_init');

/**
 * Customize WooCommerce
 */
if (class_exists('WooCommerce')) {
    
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
     * Remove default WooCommerce breadcrumb
     */
    // remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    
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
     * Add currency display before header icons
     */
    function kadence_child_currency_display() {
        // This is a placeholder - you'll need a currency switcher plugin
        // like WOOCS or WPML for actual functionality
        echo '<div class="header-currency-switcher">';
        echo '<span class="currency-current">USD $</span>';
        echo '</div>';
    }
    // add_action('kadence_header_column', 'kadence_child_currency_display', 5);
    
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
}

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
 * Customizer additions
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

/**
 * Shortcode for Trust Badges
 */
function kadence_child_trust_badges_shortcode($atts) {
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
add_shortcode('trust_badges', 'kadence_child_trust_badges_shortcode');

/**
 * Shortcode for Newsletter Section
 */
function kadence_child_newsletter_shortcode($atts) {
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
add_shortcode('newsletter_section', 'kadence_child_newsletter_shortcode');

/**
 * Shortcode for Section Header
 */
function kadence_child_section_header_shortcode($atts) {
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
add_shortcode('section_header', 'kadence_child_section_header_shortcode');

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
 * New Arrivals Product Grid Shortcode
 * Usage: [new_arrivals title="NEW ARRIVALS" count="8" columns="4" link_text="SEE MORE"]
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

/**
 * Love From The North Section Shortcode
 * Usage: [love_from_north subtitle="LOVE FROM THE NORTH" title="PURE AND HANDCRAFTED" text="..." stamp_image="URL" bg_color="#f2f2f2"]
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

/**
 * Collection Banner Shortcode (50/50 image and text with colored background)
 * Usage: [collection_banner subtitle="COLLECTION" title="Valkyria" image="URL" bg_color="grey" button1_text="NECKLACE" button1_url="/product/necklace/"]Your text content here[/collection_banner]
 * bg_color options: "grey" (default, uses --jj-section-grey), "blue" (uses --jj-svaeria-blue)
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

/**
 * Social Media Block Shortcode (50/50 split with text/button on one side, image on other)
 * Usage: [social_block title="My YouTube channel" text="Description text..." button_text="Go to my YouTube channel" button_url="https://youtube.com/..." button2_text="Subscribe" button2_url="..." image="URL" reverse="false"]
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
