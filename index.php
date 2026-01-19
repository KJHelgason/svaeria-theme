<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Kadence_Child
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    if (have_posts()) :

        if (is_home() && !is_front_page()) :
            ?>
            <header class="page-header">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
            </header>
            <?php
        endif;

        /* Start the Loop */
        while (have_posts()) :
            the_post();

            /*
             * Include the Post-Type-specific template for the content.
             */
            get_template_part('template-parts/content/entry', get_post_type());

        endwhile;

        the_posts_navigation();

    else :

        get_template_part('template-parts/content/error');

    endif;
    ?>

</main><!-- #main -->

<?php
get_sidebar();
get_footer();
