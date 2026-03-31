<?php
/**
 * Custom Footer Template for Nordic Jewelry Theme
 * Styled after Jonna Jinton Sweden
 */
?>
</div><!-- #content -->

<footer id="jj-footer" class="jj-footer">
    
    <!-- Newsletter Section -->
    <section class="jj-footer-newsletter">
        <div class="jj-newsletter-container">
            <div class="jj-newsletter-content">
                <p class="jj-newsletter-subtitle">SIGN UP FOR</p>
                <h3 class="jj-newsletter-title">Our newsletter</h3>
                <p class="jj-newsletter-text">Sign up for our newsletter, and you'll be the first to know when we are about to release new collections.</p>
                
                <form class="jj-newsletter-form" action="#" method="post">
                    <div class="jj-form-field">
                        <input type="text" name="name" placeholder="Your name" required>
                    </div>
                    <div class="jj-form-field">
                        <input type="email" name="email" placeholder="Your email address" required>
                    </div>
                    <div class="jj-form-checkbox">
                        <label>
                            <input type="checkbox" name="terms" required>
                            <span>I have read and agree to the terms & conditions</span>
                        </label>
                    </div>
                    <button type="submit" class="jj-newsletter-submit">Sign up</button>
                </form>
            </div>
            <div class="jj-newsletter-image">
                <img src="https://svaeria.is/wp-content/uploads/2026/03/IMG_8340-scaled-e1774020791822.jpg" alt="Newsletter" />
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="jj-footer-features">
        <div class="jj-features-container">
            <div class="jj-feature">
                <div class="jj-feature-icon">
                    <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Shipment-1.svg" alt="Free Shipping" />
                </div>
                <div class="jj-feature-text">
                    <strong>FREE SHIPPING ON ALL ORDERS</strong>
                    <span>We offer free worldwide shipping on all orders</span>
                </div>
            </div>
            <div class="jj-feature">
                <div class="jj-feature-icon">
                    <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Global-1.svg" alt="Worldwide Delivery" />
                </div>
                <div class="jj-feature-text">
                    <strong>WORLDWIDE DELIVERY</strong>
                    <span>With FedEx International Priority, we deliver to all corners of the world.</span>
                </div>
            </div>
            <div class="jj-feature">
                <div class="jj-feature-icon">
                    <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Payment-1.svg" alt="Safe Payments" />
                </div>
                <div class="jj-feature-text">
                    <strong>SAFE AND SECURE PAYMENTS</strong>
                    <span>Pay safe with Card, Teya or Klarna</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Icons -->
    <section class="jj-footer-payments">
        <div class="jj-payments-container">
            <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/FedEx-1.svg" alt="FedEx" />
            <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Visa-1.svg" alt="Visa" />
            <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Mastercard-1.svg" alt="Mastercard" />
            <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/AmericanExpress-1.svg" alt="American Express" />
            <img src="https://svaeria.is/wp-content/uploads/2026/03/Teya_idLF2SZofv_1.svg" alt="Teya" />
            <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Klarna-1.svg" alt="Klarna" />
        </div>
    </section>

    <!-- Main Footer -->
    <section class="jj-footer-main">
        <div class="jj-footer-container">
            <div class="jj-footer-logo">
                <?php
                $custom_logo_id = get_theme_mod('custom_logo');
                if ($custom_logo_id) {
                    echo wp_get_attachment_image($custom_logo_id, 'full');
                }
                ?>
            </div>
            
            <div class="jj-footer-menus">
                <div class="jj-footer-menu">
                    <h4>Products</h4>
                    <ul>
                        <?php
                        $footer_uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
                        $footer_exclude_ids = $footer_uncategorized ? array($footer_uncategorized->term_id) : array();
                        $footer_categories = get_terms(array(
                            'taxonomy'   => 'product_cat',
                            'hide_empty' => false,
                            'parent'     => 0,
                            'exclude'    => $footer_exclude_ids,
                        ));
                        if (!empty($footer_categories) && !is_wp_error($footer_categories)) :
                            foreach ($footer_categories as $footer_cat) :
                        ?>
                            <li><a href="<?php echo esc_url(get_term_link($footer_cat)); ?>"><?php echo esc_html($footer_cat->name); ?></a></li>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
                
                <div class="jj-footer-menu">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/social-media/">Social Media</a></li>
                    </ul>
                </div>
                
                <div class="jj-footer-menu">
                    <h4>Service</h4>
                    <ul>
                        <li><a href="/customer-service/">Support</a></li>
                        <li><a href="/shipping-returns/">Shipping & return</a></li>
                        <li><a href="/terms-conditions/">Terms & conditions</a></li>
                        <li><a href="/privacy-policy/">Privacy policy</a></li>
                        <li><a href="/cookies/">Cookies</a></li>
                    </ul>
                </div>
                
                <div class="jj-footer-menu jj-footer-contact">
                    <h4>Contact</h4>
                    <p>
                        <strong>Business Inquiries</strong><br>
                        <a href="mailto:svaeria.elsasolblom@gmail.com">svaeria.elsasolblom@gmail.com</a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Social Icons -->
        <div class="jj-footer-social">
            <a href="https://www.instagram.com/svaeria" target="_blank" rel="noopener" aria-label="Instagram">
                <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Insta-1.svg" alt="Instagram" />
            </a>
            <a href="https://www.tiktok.com/@svaeriaelsa" target="_blank" rel="noopener" aria-label="TikTok">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.7a8.18 8.18 0 0 0 4.76 1.52v-3.4a4.85 4.85 0 0 1-1-.13z"/></svg>
            </a>
        </div>
        
        <!-- Copyright -->
        <div class="jj-footer-copyright">
            <p>Copyright <?php echo date('Y'); ?> © <?php echo get_bloginfo('name'); ?></p>
        </div>
    </section>
    
    <!-- Back to Top -->
    <button type="button" id="jj-back-to-top" class="jj-back-to-top" aria-label="Back to top">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 15l-6-6-6 6"/>
        </svg>
    </button>
    
</footer>

<?php wp_footer(); ?>

<script>
// Back to top button
(function() {
    const backToTop = document.getElementById('jj-back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
</script>

</body>
</html>
