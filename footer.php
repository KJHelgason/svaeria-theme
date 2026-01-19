<?php
/**
 * Footer Template - Overrides Kadence footer
 * Uses custom Nordic jewelry footer design
 */

// Close Kadence content wrapper if needed
?>
</div><!-- #content -->

<footer id="jj-footer" class="jj-footer">
    
    <!-- Newsletter Section - 75% background split -->
    <section class="jj-footer-newsletter">
        <div class="jj-newsletter-bg-wrapper"></div>
        <div class="jj-newsletter-container">
            <div class="jj-newsletter-content">
                <p class="jj-newsletter-subtitle">SIGN UP FOR</p>
                <h3 class="jj-newsletter-title">Our newsletter</h3>
                <p class="jj-newsletter-text">Sign up for our newsletter, and you'll be the first to know when we are about to release new products like jewelry, prints and paintings.</p>
                
                <?php 
                // Check if Mailchimp for WordPress is active
                if (function_exists('mc4wp_show_form')) {
                    mc4wp_show_form();
                } else {
                ?>
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
                <?php } ?>
                <p class="jj-newsletter-spam-notice">Please check your spam folder and add us to your contacts to ensure you receive our emails.</p>
            </div>
            <div class="jj-newsletter-image-col">
                <div class="jj-newsletter-image">
                    <img src="<?php echo esc_url(get_theme_mod('jj_footer_newsletter_image', 'https://jonnajintonsweden.com/wp-content/uploads/2022/10/jewelry-jonna-jinton-bison-silverchain-12-L.jpg')); ?>" alt="Newsletter" />
                </div>
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
                    <span>Pay safe with Card, PayPal or Klarna</span>
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
            <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/PayPal-1.svg" alt="PayPal" />
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
                } else {
                ?>
                    <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Logo-1.svg" alt="<?php echo get_bloginfo('name'); ?>" />
                <?php } ?>
            </div>
            
            <div class="jj-footer-menus">
                <div class="jj-footer-menu">
                    <h4>Products</h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-products',
                        'container' => false,
                        'fallback_cb' => function() {
                            echo '<ul>
                                <li><a href="/product-category/jewelry/">Jewelry</a></li>
                                <li><a href="/product-category/prints/">Photo Prints</a></li>
                                <li><a href="/product-category/music/">Music</a></li>
                            </ul>';
                        }
                    ));
                    ?>
                </div>
                
                <div class="jj-footer-menu">
                    <h4>Company</h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-company',
                        'container' => false,
                        'fallback_cb' => function() {
                            echo '<ul>
                                <li><a href="/about-us/">About us</a></li>
                                <li><a href="/social-media/">Social Media</a></li>
                            </ul>';
                        }
                    ));
                    ?>
                </div>
                
                <div class="jj-footer-menu">
                    <h4>Service</h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer-service',
                        'container' => false,
                        'fallback_cb' => function() {
                            echo '<ul>
                                <li><a href="/faq/">FAQ</a></li>
                                <li><a href="/customer-service/">Support</a></li>
                                <li><a href="/shipping-returns/">Shipping & return</a></li>
                                <li><a href="/jewelry-care/">Jewelry care</a></li>
                                <li><a href="/terms-conditions/">Terms & conditions</a></li>
                                <li><a href="/privacy-policy/">Privacy policy</a></li>
                                <li><a href="/cookies/">Cookies</a></li>
                            </ul>';
                        }
                    ));
                    ?>
                </div>
                
                <div class="jj-footer-menu jj-footer-contact">
                    <h4>Contact</h4>
                    <p>
                        <strong>Customer support</strong><br>
                        <a href="mailto:<?php echo antispambot(get_option('admin_email')); ?>"><?php echo antispambot(get_option('admin_email')); ?></a>
                    </p>
                    <p>
                        <strong>Business related</strong><br>
                        <a href="mailto:business@example.com">business@example.com</a>
                    </p>
                    <p class="jj-footer-address">
                        <strong>Ordinary mail</strong><br>
                        <?php echo get_bloginfo('name'); ?><br>
                        Street Address 123<br>
                        123 45 City<br>
                        Country
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Social Icons -->
        <div class="jj-footer-social">
            <a href="https://www.instagram.com/" target="_blank" rel="noopener" aria-label="Instagram">
                <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Insta-1.svg" alt="Instagram" />
            </a>
            <a href="https://www.facebook.com/" target="_blank" rel="noopener" aria-label="Facebook">
                <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Facebook-2.svg" alt="Facebook" />
            </a>
            <a href="https://www.pinterest.com/" target="_blank" rel="noopener" aria-label="Pinterest">
                <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Pinterest-1.svg" alt="Pinterest" />
            </a>
            <a href="https://www.youtube.com/" target="_blank" rel="noopener" aria-label="YouTube">
                <img src="https://jonnajintonsweden.com/wp-content/uploads/2025/05/Youtube-3.svg" alt="YouTube" />
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
