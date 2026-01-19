<?php
/**
 * Custom Header Template Part
 * 
 * This can be used to override Kadence's default header
 * Copy to template-parts/header/custom-header.php if needed
 */

// Get theme mods
$announcement_text = get_theme_mod('jj_announcement_text', 'FREE SHIPPING | WORLDWIDE DELIVERY | SECURE PAYMENTS');
$show_announcement = get_theme_mod('jj_show_announcement', true);
?>

<?php if ($show_announcement && $announcement_text) : ?>
<!-- Announcement Bar -->
<div class="jj-announcement-bar">
    <div class="container">
        <?php echo wp_kses_post($announcement_text); ?>
    </div>
</div>
<?php endif; ?>

<!-- Main Header -->
<header id="masthead" class="site-header jj-header">
    <div class="jj-header-inner">
        <div class="container">
            <div class="jj-header-row">
                
                <!-- Mobile Menu Toggle (Left on mobile) -->
                <div class="jj-header-mobile-toggle">
                    <button class="jj-menu-toggle" aria-label="<?php esc_attr_e('Open Menu', 'kadence-child'); ?>">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>
                
                <!-- Logo / Site Title -->
                <div class="jj-header-logo">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title-link">
                            <span class="site-title"><?php bloginfo('name'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Primary Navigation -->
                <nav class="jj-header-nav" aria-label="<?php esc_attr_e('Primary Navigation', 'kadence-child'); ?>">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'jj-primary-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2,
                    ));
                    ?>
                </nav>
                
                <!-- Header Icons -->
                <div class="jj-header-icons">
                    
                    <!-- Currency Switcher (placeholder) -->
                    <div class="jj-currency-switcher">
                        <button class="jj-currency-btn" aria-label="<?php esc_attr_e('Change Currency', 'kadence-child'); ?>">
                            <span class="currency-flag">🇺🇸</span>
                            <span class="currency-code">USD</span>
                            <svg class="currency-arrow" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="jj-currency-dropdown">
                            <a href="#" data-currency="USD">
                                <span class="currency-flag">🇺🇸</span>
                                <span>USD $</span>
                            </a>
                            <a href="#" data-currency="EUR">
                                <span class="currency-flag">🇪🇺</span>
                                <span>EUR €</span>
                            </a>
                            <a href="#" data-currency="GBP">
                                <span class="currency-flag">🇬🇧</span>
                                <span>GBP £</span>
                            </a>
                            <a href="#" data-currency="SEK">
                                <span class="currency-flag">🇸🇪</span>
                                <span>SEK kr</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Search -->
                    <button class="jj-header-icon jj-search-toggle" aria-label="<?php esc_attr_e('Search', 'kadence-child'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                    </button>
                    
                    <!-- Account -->
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>" class="jj-header-icon jj-account-link" aria-label="<?php esc_attr_e('My Account', 'kadence-child'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </a>
                    
                    <!-- Cart -->
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="jj-header-icon jj-cart-link" aria-label="<?php esc_attr_e('Cart', 'kadence-child'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        <?php if (WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
                            <span class="jj-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        <?php endif; ?>
                    </a>
                    
                </div>
                
            </div>
        </div>
    </div>
</header>

<!-- Search Modal -->
<div class="jj-search-modal" aria-hidden="true">
    <div class="jj-search-modal-overlay"></div>
    <div class="jj-search-modal-content">
        <button class="jj-search-close" aria-label="<?php esc_attr_e('Close Search', 'kadence-child'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <form role="search" method="get" class="jj-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text"><?php esc_html_e('Search for:', 'kadence-child'); ?></label>
            <input type="search" class="jj-search-input" placeholder="<?php esc_attr_e('Search products...', 'kadence-child'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
            <input type="hidden" name="post_type" value="product" />
            <button type="submit" class="jj-search-submit">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Menu Drawer -->
<div class="jj-mobile-menu" aria-hidden="true">
    <div class="jj-mobile-menu-overlay"></div>
    <div class="jj-mobile-menu-drawer">
        <div class="jj-mobile-menu-header">
            <button class="jj-mobile-menu-close" aria-label="<?php esc_attr_e('Close Menu', 'kadence-child'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <nav class="jj-mobile-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'jj-mobile-menu-list',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 2,
            ));
            ?>
        </nav>
        <div class="jj-mobile-menu-footer">
            <!-- Mobile currency/language switcher -->
            <div class="jj-mobile-currency">
                <span class="jj-mobile-currency-label"><?php esc_html_e('Currency:', 'kadence-child'); ?></span>
                <select class="jj-mobile-currency-select">
                    <option value="USD">USD $</option>
                    <option value="EUR">EUR €</option>
                    <option value="GBP">GBP £</option>
                    <option value="SEK">SEK kr</option>
                </select>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Header Styles - Add to style.css or keep here */

/* Announcement Bar */
.jj-announcement-bar {
    background-color: var(--jj-black, #1a1a1a);
    color: var(--jj-white, #ffffff);
    text-align: center;
    padding: 10px 20px;
    font-size: 11px;
    font-weight: 400;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}

/* Main Header */
.jj-header {
    background-color: var(--jj-white, #ffffff);
    position: relative;
    z-index: 100;
    transition: all 0.3s ease;
}

.jj-header-inner {
    padding: 20px 0;
}

.jj-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
}

/* Logo */
.jj-header-logo {
    flex: 0 0 auto;
}

.jj-header-logo .site-title {
    font-family: var(--jj-font-heading, 'Cormorant Garamond', serif);
    font-size: 1.75rem;
    font-weight: 400;
    letter-spacing: 0.05em;
    color: var(--jj-black, #1a1a1a);
    margin: 0;
}

.jj-header-logo .custom-logo {
    max-height: 50px;
    width: auto;
}

/* Navigation */
.jj-header-nav {
    flex: 1;
    display: flex;
    justify-content: center;
}

.jj-primary-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 10px;
}

.jj-primary-menu li {
    position: relative;
}

.jj-primary-menu > li > a {
    display: block;
    padding: 10px 20px;
    font-size: 12px;
    font-weight: 400;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--jj-black, #1a1a1a);
    transition: color 0.3s ease;
}

.jj-primary-menu > li > a:hover {
    color: var(--jj-gray, #666666);
}

/* Dropdown */
.jj-primary-menu .sub-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: var(--jj-white, #ffffff);
    min-width: 220px;
    padding: 15px 0;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
    list-style: none;
}

.jj-primary-menu li:hover > .sub-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.jj-primary-menu .sub-menu a {
    display: block;
    padding: 10px 25px;
    font-size: 12px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--jj-dark-gray, #333333);
    transition: all 0.2s ease;
}

.jj-primary-menu .sub-menu a:hover {
    background-color: var(--jj-cream, #faf9f7);
    color: var(--jj-black, #1a1a1a);
}

/* Header Icons */
.jj-header-icons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.jj-header-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    color: var(--jj-black, #1a1a1a);
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.3s ease;
    position: relative;
}

.jj-header-icon:hover {
    color: var(--jj-gray, #666666);
}

/* Cart Count */
.jj-cart-count {
    position: absolute;
    top: 0;
    right: 0;
    background-color: var(--jj-black, #1a1a1a);
    color: var(--jj-white, #ffffff);
    font-size: 10px;
    font-weight: 500;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    text-align: center;
    border-radius: 50%;
}

/* Currency Switcher */
.jj-currency-switcher {
    position: relative;
}

.jj-currency-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 12px;
    letter-spacing: 0.05em;
    color: var(--jj-black, #1a1a1a);
    padding: 8px 10px;
}

.jj-currency-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--jj-white, #ffffff);
    min-width: 150px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
    z-index: 100;
}

.jj-currency-switcher:hover .jj-currency-dropdown,
.jj-currency-switcher.is-open .jj-currency-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.jj-currency-dropdown a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    font-size: 13px;
    color: var(--jj-dark-gray, #333333);
    transition: background-color 0.2s ease;
}

.jj-currency-dropdown a:hover {
    background-color: var(--jj-cream, #faf9f7);
}

/* Mobile Toggle */
.jj-header-mobile-toggle {
    display: none;
}

.jj-menu-toggle {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 40px;
    height: 40px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 10px;
}

.hamburger-line {
    width: 20px;
    height: 1.5px;
    background-color: var(--jj-black, #1a1a1a);
    transition: all 0.3s ease;
}

/* Search Modal */
.jj-search-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.jj-search-modal.is-open {
    opacity: 1;
    visibility: visible;
}

.jj-search-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
}

.jj-search-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 600px;
}

.jj-search-close {
    position: absolute;
    top: -60px;
    right: 0;
    background: none;
    border: none;
    color: var(--jj-white, #ffffff);
    cursor: pointer;
}

.jj-search-form {
    display: flex;
    background: var(--jj-white, #ffffff);
}

.jj-search-input {
    flex: 1;
    padding: 20px;
    font-size: 16px;
    border: none;
    outline: none;
}

.jj-search-submit {
    padding: 20px;
    background: var(--jj-black, #1a1a1a);
    color: var(--jj-white, #ffffff);
    border: none;
    cursor: pointer;
}

/* Mobile Menu Drawer */
.jj-mobile-menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.jj-mobile-menu.is-open {
    opacity: 1;
    visibility: visible;
}

.jj-mobile-menu-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.jj-mobile-menu-drawer {
    position: absolute;
    top: 0;
    left: 0;
    width: 320px;
    max-width: 85%;
    height: 100%;
    background: var(--jj-white, #ffffff);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
}

.jj-mobile-menu.is-open .jj-mobile-menu-drawer {
    transform: translateX(0);
}

.jj-mobile-menu-header {
    display: flex;
    justify-content: flex-end;
    padding: 15px 20px;
    border-bottom: 1px solid var(--jj-warm-beige, #e8e4df);
}

.jj-mobile-menu-close {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--jj-black, #1a1a1a);
}

.jj-mobile-nav {
    flex: 1;
    overflow-y: auto;
    padding: 20px 0;
}

.jj-mobile-menu-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.jj-mobile-menu-list a {
    display: block;
    padding: 15px 25px;
    font-size: 14px;
    font-weight: 400;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--jj-black, #1a1a1a);
    border-bottom: 1px solid var(--jj-warm-beige, #e8e4df);
}

.jj-mobile-menu-list .sub-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    background: var(--jj-cream, #faf9f7);
}

.jj-mobile-menu-list .sub-menu a {
    padding-left: 40px;
    font-size: 13px;
}

.jj-mobile-menu-footer {
    padding: 20px;
    border-top: 1px solid var(--jj-warm-beige, #e8e4df);
}

/* Transparent Header (for homepage) */
.transparent-header .jj-header {
    position: absolute;
    width: 100%;
    background: transparent;
}

.transparent-header .jj-header .site-title,
.transparent-header .jj-header .jj-primary-menu > li > a,
.transparent-header .jj-header .jj-header-icon,
.transparent-header .jj-header .jj-currency-btn {
    color: var(--jj-white, #ffffff);
}

.transparent-header .jj-header .hamburger-line {
    background-color: var(--jj-white, #ffffff);
}

.transparent-header .jj-header.header-scrolled {
    position: fixed;
    background: var(--jj-white, #ffffff);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
}

.transparent-header .jj-header.header-scrolled .site-title,
.transparent-header .jj-header.header-scrolled .jj-primary-menu > li > a,
.transparent-header .jj-header.header-scrolled .jj-header-icon,
.transparent-header .jj-header.header-scrolled .jj-currency-btn {
    color: var(--jj-black, #1a1a1a);
}

.transparent-header .jj-header.header-scrolled .hamburger-line {
    background-color: var(--jj-black, #1a1a1a);
}

/* Responsive */
@media (max-width: 991px) {
    .jj-header-nav {
        display: none;
    }
    
    .jj-header-mobile-toggle {
        display: block;
    }
    
    .jj-header-row {
        justify-content: space-between;
    }
    
    .jj-header-logo {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .jj-currency-switcher {
        display: none;
    }
}

@media (max-width: 480px) {
    .jj-header-logo .site-title {
        font-size: 1.25rem;
    }
    
    .jj-header-icons {
        gap: 5px;
    }
    
    .jj-header-icon {
        width: 35px;
        height: 35px;
    }
}
</style>

<script>
// Custom Header JavaScript
(function($) {
    $(document).ready(function() {
        // Search Modal
        $('.jj-search-toggle').on('click', function() {
            $('.jj-search-modal').addClass('is-open');
            $('.jj-search-input').focus();
        });
        
        $('.jj-search-close, .jj-search-modal-overlay').on('click', function() {
            $('.jj-search-modal').removeClass('is-open');
        });
        
        // Mobile Menu
        $('.jj-menu-toggle').on('click', function() {
            $('.jj-mobile-menu').addClass('is-open');
            $('body').addClass('mobile-menu-open');
        });
        
        $('.jj-mobile-menu-close, .jj-mobile-menu-overlay').on('click', function() {
            $('.jj-mobile-menu').removeClass('is-open');
            $('body').removeClass('mobile-menu-open');
        });
        
        // Close on escape
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape') {
                $('.jj-search-modal').removeClass('is-open');
                $('.jj-mobile-menu').removeClass('is-open');
                $('body').removeClass('mobile-menu-open');
            }
        });
        
        // Sticky Header
        var $header = $('.jj-header');
        var scrollThreshold = 100;
        
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > scrollThreshold) {
                $header.addClass('header-scrolled');
            } else {
                $header.removeClass('header-scrolled');
            }
        });
    });
})(jQuery);
</script>
