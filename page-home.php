<?php
/**
 * Template Name: Homepage - Nordic Jewelry
 * 
 * Custom homepage template with Jonna Jinton-style sections
 */

get_header();
?>

<main id="primary" class="site-main jj-homepage">

    <!-- Hero Section -->
    <section class="jj-hero-section">
        <div class="jj-hero-background">
            <?php 
            // Use featured image or placeholder
            $hero_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            if (!$hero_image) {
                $hero_image = 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920';
            }
            ?>
            <img src="<?php echo esc_url($hero_image); ?>" alt="Hero Background" />
        </div>
        <div class="jj-hero-overlay"></div>
        <div class="jj-hero-content">
            <div class="jj-hero-notice">
                <h3>Holiday Information</h3>
                <p>Dear valued customer, to ensure delivery before Christmas, please place your order before December 14. We wish you happy holidays!</p>
            </div>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="jj-section jj-new-arrivals">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">New Arrivals</span>
                <h2 class="section-title">Latest Additions</h2>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="section-link">See More →</a>
            </div>
            
            <?php
            // Display newest products
            echo do_shortcode('[products limit="4" columns="4" orderby="date" order="DESC"]');
            ?>
        </div>
    </section>

    <!-- About Jewelry Section (Split) -->
    <section class="jj-split-section">
        <div class="jj-split-image">
            <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=800" alt="Jewelry crafting" />
        </div>
        <div class="jj-split-content">
            <span class="section-subtitle">Jonna Jinton Jewelry</span>
            <h2>Jewelry from the North</h2>
            <p>We see our jewelry as something more than just a beautiful accessory. Even though they are physical creations from natural materials, we also see them as extensions of ourselves. Symbols for something beyond what we can touch with our hands.</p>
            <p>They stand for our inner thoughts, hidden potentials and deepest dreams.</p>
            <a href="<?php echo esc_url(get_term_link('jewelry', 'product_cat')); ?>" class="button">Our Jewelry</a>
        </div>
    </section>

    <!-- Our Favorites Section -->
    <section class="jj-section jj-favorites bg-cream">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Our Favorites</span>
                <h2 class="section-title">Most Loved Pieces</h2>
            </div>
            
            <?php
            // Display featured products
            echo do_shortcode('[products limit="6" columns="3" visibility="featured"]');
            ?>
            
            <div class="section-footer">
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="button button-outline">See More</a>
            </div>
        </div>
    </section>

    <!-- Pure and Handcrafted Section -->
    <section class="jj-split-section reverse">
        <div class="jj-split-image">
            <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=800" alt="Handcrafted jewelry" />
        </div>
        <div class="jj-split-content">
            <span class="section-subtitle">Pure and Handcrafted</span>
            <h2>Crafted with Love</h2>
            <p>We only use clean, nickel- and lead free sterling silver in our jewelry. The jewelry is crafted by us and our collaborators in Sweden and in Norway.</p>
            <p>Each piece is made with passion, care, and attention to detail.</p>
        </div>
    </section>

    <!-- Collection Highlight -->
    <section class="jj-collection-highlight">
        <div class="jj-collection-bg">
            <img src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1920" alt="Collection background" />
        </div>
        <div class="jj-collection-overlay"></div>
        <div class="jj-collection-content">
            <span class="section-subtitle">Collection</span>
            <h2>Valkyria</h2>
            <p>Each silver feather in our Valkyria collection is a tribute to the wings of the Valkyries, the powerful figures who soared between worlds with strength and purpose.</p>
            <div class="jj-collection-buttons">
                <a href="#" class="button button-white">Necklace</a>
                <a href="#" class="button button-white">Earrings</a>
            </div>
        </div>
    </section>

    <!-- Quote Section -->
    <section class="jj-section jj-quote-section">
        <div class="container">
            <blockquote class="jj-quote">
                <p>"Only when you take the steps into your inner world, you will find your true self."</p>
                <cite>— Love from the North</cite>
            </blockquote>
        </div>
    </section>

    <!-- Trust Badges -->
    <?php echo do_shortcode('[trust_badges]'); ?>

    <!-- Newsletter Section -->
    <?php echo do_shortcode('[newsletter_section]'); ?>

</main>

<?php
get_footer();
?>

<style>
/* Homepage-specific styles */
.jj-homepage {
    overflow-x: hidden;
}

/* Hero Section */
.jj-hero-section {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.jj-hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.jj-hero-background img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.jj-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5));
    z-index: 1;
}

.jj-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: #fff;
    padding: 40px;
    max-width: 700px;
}

.jj-hero-notice {
    background: rgba(255,255,255,0.95);
    color: #1a1a1a;
    padding: 40px 50px;
}

.jj-hero-notice h3 {
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 15px;
}

.jj-hero-notice p {
    font-size: 15px;
    line-height: 1.8;
    color: #666;
    margin: 0;
}

/* Split Sections */
.jj-split-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 600px;
}

.jj-split-section.reverse {
    direction: rtl;
}

.jj-split-section.reverse > * {
    direction: ltr;
}

.jj-split-image {
    position: relative;
    overflow: hidden;
}

.jj-split-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.jj-split-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 80px;
    background-color: #faf9f7;
}

/* Collection Highlight */
.jj-collection-highlight {
    position: relative;
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.jj-collection-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.jj-collection-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.jj-collection-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
}

.jj-collection-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: #fff;
    max-width: 600px;
    padding: 40px;
}

.jj-collection-content h2 {
    color: #fff;
    font-size: 3rem;
    margin-bottom: 20px;
}

.jj-collection-content p {
    color: rgba(255,255,255,0.9);
    margin-bottom: 30px;
}

.jj-collection-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

/* Quote Section */
.jj-quote-section {
    text-align: center;
    padding: 100px 20px;
}

.jj-quote {
    font-family: var(--jj-font-heading, 'Cormorant Garamond', serif);
    font-size: 2rem;
    font-weight: 300;
    font-style: italic;
    color: #1a1a1a;
    max-width: 800px;
    margin: 0 auto;
    padding: 0;
    border: none;
}

.jj-quote cite {
    display: block;
    margin-top: 20px;
    font-family: var(--jj-font-body, 'Jost', sans-serif);
    font-size: 12px;
    font-style: normal;
    font-weight: 500;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #999;
}

/* Section Footer */
.section-footer {
    text-align: center;
    margin-top: 40px;
}

/* Responsive */
@media (max-width: 991px) {
    .jj-split-section {
        grid-template-columns: 1fr;
    }
    
    .jj-split-section.reverse {
        direction: ltr;
    }
    
    .jj-split-image {
        min-height: 400px;
    }
    
    .jj-split-content {
        padding: 60px 30px;
    }
}

@media (max-width: 768px) {
    .jj-hero-notice {
        padding: 30px;
    }
    
    .jj-collection-content h2 {
        font-size: 2rem;
    }
    
    .jj-quote {
        font-size: 1.5rem;
    }
}
</style>
