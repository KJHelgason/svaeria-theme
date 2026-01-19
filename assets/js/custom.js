/**
 * Nordic Jewelry Theme - Custom JavaScript
 * Fonts: Inter + Spectral (matching Jonna Jinton Sweden)
 */

jQuery(document).ready(function($) {
    'use strict';
    
    console.log('[Nordic] Initializing scripts...');

    // ========================================
    // SEARCH TOGGLE
    // ========================================
    var $searchWrapper = $('.jj-search-wrapper');
    var $searchToggle = $('.jj-search-toggle');
    var $searchDropdown = $('.jj-search-dropdown');
    var $searchInput = $('.jj-search-dropdown-input');
    
    console.log('[Nordic] Search toggle found:', $searchToggle.length);
    
    $searchToggle.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('[Nordic] Search toggle clicked!');
        
        $searchWrapper.toggleClass('search-active');
        
        if ($searchWrapper.hasClass('search-active')) {
            setTimeout(function() {
                $searchInput.focus();
            }, 100);
        }
    });
    
    // Close search when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.jj-search-wrapper').length) {
            $searchWrapper.removeClass('search-active');
        }
    });
    
    // Close on Escape key
    $(document).on('keyup', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            $searchWrapper.removeClass('search-active');
            $('.jj-currency-switcher').removeClass('is-open');
        }
    });

    // ========================================
    // CURRENCY SWITCHER
    // ========================================
    var $currencySwitcher = $('.jj-currency-switcher');
    var $currencyBtn = $('.jj-currency-btn');
    var $currencyDropdown = $('.jj-currency-dropdown');
    
    console.log('[Nordic] Currency button found:', $currencyBtn.length);
    
    $currencyBtn.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('[Nordic] Currency button clicked!');
        
        $currencySwitcher.toggleClass('is-open');
    });
    
    // Handle currency selection
    $currencyDropdown.on('click', 'a', function(e) {
        e.preventDefault();
        var $this = $(this);
        var currency = $this.data('currency');
        var currencyText = $this.text();
        
        console.log('[Nordic] Currency selected:', currency);
        
        // Update button text
        $currencyBtn.find('.currency-text').text(currencyText);
        
        // Update active state
        $currencyDropdown.find('a').removeClass('active');
        $this.addClass('active');
        
        // Close dropdown
        $currencySwitcher.removeClass('is-open');
        
        // Store in localStorage
        localStorage.setItem('jj_currency', currency);
        
        // If WOOCS plugin is active, redirect to change currency
        if (typeof woocs_current_currency !== 'undefined' || $('body').hasClass('woocommerce')) {
            var url = window.location.href.split('?')[0];
            window.location.href = url + '?currency=' + currency;
        }
    });
    
    // Close currency dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.jj-currency-switcher').length) {
            $currencySwitcher.removeClass('is-open');
        }
    });
    
    // Load saved currency
    var savedCurrency = localStorage.getItem('jj_currency');
    if (savedCurrency) {
        var $savedOption = $currencyDropdown.find('a[data-currency="' + savedCurrency + '"]');
        if ($savedOption.length) {
            $currencyBtn.find('.currency-text').text($savedOption.text());
            $currencyDropdown.find('a').removeClass('active');
            $savedOption.addClass('active');
        }
    }

    // ========================================
    // MEGA MENU
    // ========================================
    var $megaMenuItems = $('.jj-has-mega-menu');
    var megaMenuTimeout;
    
    $megaMenuItems.on('mouseenter', function() {
        var $this = $(this);
        clearTimeout(megaMenuTimeout);
        $megaMenuItems.not($this).removeClass('mega-menu-open');
        $this.addClass('mega-menu-open');
    });
    
    $megaMenuItems.on('mouseleave', function() {
        var $this = $(this);
        megaMenuTimeout = setTimeout(function() {
            $this.removeClass('mega-menu-open');
        }, 150);
    });
    
    $('.jj-mega-menu').on('mouseenter', function() {
        clearTimeout(megaMenuTimeout);
        $(this).closest('.jj-has-mega-menu').addClass('mega-menu-open');
    });
    
    $('.jj-mega-menu').on('mouseleave', function() {
        var $parent = $(this).closest('.jj-has-mega-menu');
        megaMenuTimeout = setTimeout(function() {
            $parent.removeClass('mega-menu-open');
        }, 150);
    });

    // ========================================
    // CART DROPDOWN
    // ========================================
    var $cartWrapper = $('.jj-cart-wrapper');
    var cartTimeout;
    
    $cartWrapper.on('mouseenter', function() {
        clearTimeout(cartTimeout);
        $(this).addClass('is-open');
    });
    
    $cartWrapper.on('mouseleave', function() {
        var $this = $(this);
        cartTimeout = setTimeout(function() {
            $this.removeClass('is-open');
        }, 300);
    });

    // ========================================
    // MOBILE DRAWER
    // ========================================
    var $mobileToggle = $('.jj-mobile-toggle');
    var $mobileDrawer = $('.jj-mobile-drawer');
    var $mobileClose = $('.jj-mobile-drawer-close');
    var $mobileOverlay = $('.jj-mobile-drawer-overlay');
    
    $mobileToggle.on('click', function(e) {
        e.preventDefault();
        $mobileDrawer.addClass('is-open');
        $('body').addClass('mobile-menu-open');
    });
    
    function closeMobileMenu() {
        $mobileDrawer.removeClass('is-open');
        $('body').removeClass('mobile-menu-open');
    }
    
    $mobileClose.on('click', closeMobileMenu);
    $mobileOverlay.on('click', closeMobileMenu);
    
    // Mobile submenu toggle
    $('.jj-mobile-submenu-toggle').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).closest('.jj-mobile-has-submenu');
        $parent.toggleClass('submenu-open');
        $parent.find('.jj-mobile-submenu').slideToggle(200);
    });

    // ========================================
    // STICKY HEADER
    // ========================================
    var $header = $('.jj-main-header');
    var $topBar = $('.top-bar');
    var scrollThreshold = $topBar.length ? $topBar.outerHeight() + 20 : 50;
    
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > scrollThreshold) {
            $header.addClass('header-scrolled');
            $('body').addClass('header-is-sticky');
        } else {
            $header.removeClass('header-scrolled');
            $('body').removeClass('header-is-sticky');
        }
    });

    console.log('[Nordic] All scripts initialized successfully!');
});
