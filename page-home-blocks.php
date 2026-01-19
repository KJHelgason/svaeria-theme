<?php
/**
 * Template Name: Homepage - Block Editor
 * 
 * Clean homepage template that lets the WordPress block editor control all content.
 * Use this template for full control over homepage sections.
 */

get_header();
?>

<main id="primary" class="site-main jj-homepage jj-block-homepage">
    <?php
    while (have_posts()) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
?>

<style>
/* Block Homepage Styles */
.jj-block-homepage {
    overflow-x: hidden;
    padding: 0;
    margin: 0;
}

/* Remove default content padding/margins */
.jj-block-homepage > * {
    max-width: 100%;
}

/* Ensure full-width blocks work properly */
.jj-block-homepage .alignfull {
    width: 100vw;
    max-width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
}
</style>
