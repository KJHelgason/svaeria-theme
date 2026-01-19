/**
 * Svaeria Nordic Jewelry Theme - Main JavaScript
 *
 * Handles header interactions, search, currency switcher, and mobile menu.
 *
 * @package Kadence_Child
 * @version 2.0.0
 */

(function($) {
    'use strict';

    /**
     * Initialize when DOM is ready
     */
    function init() {
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

        // ========================================
        // SEARCH TOGGLE
        // ========================================
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

        // ========================================
        // CURRENCY SWITCHER
        // ========================================
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

            // Use AJAX to set currency
            var ajaxUrl = (typeof jjAjax !== 'undefined') ? jjAjax.ajaxUrl : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
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

        // ========================================
        // CLOSE DROPDOWNS
        // ========================================
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

        // ========================================
        // MEGA MENU
        // ========================================
        $megaMenuItems.on('mouseenter.nordic', function() {
            $(this).addClass('mega-menu-open');
        }).on('mouseleave.nordic', function() {
            $(this).removeClass('mega-menu-open');
        });

        // ========================================
        // CART DROPDOWN
        // ========================================
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

        // Mobile currency select dropdown
        $('#jj-mobile-currency').on('change.nordic', function() {
            var currency = $(this).val();
            console.log('[Nordic] Mobile currency changed:', currency);

            var ajaxUrl = (typeof jjAjax !== 'undefined') ? jjAjax.ajaxUrl : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
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
            var ajaxUrl = (typeof jjAjax !== 'undefined') ? jjAjax.ajaxUrl : '/wp-admin/admin-ajax.php';

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: ajaxUrl,
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
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(init, 1);
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }

})(jQuery);
