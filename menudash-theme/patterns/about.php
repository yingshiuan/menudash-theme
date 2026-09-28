<?php
/**
 * Title: Home: about us
 * Slug: menudash-theme/about
 * Categories: menudash-theme, about
 * Description: Your story in a few lines, with the Instagram button from MenuDash → Restaurant.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"primary"} -->
<p class="has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase"><?php echo esc_html__( 'About us', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Our story', 'menudash-theme' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html__( 'Who cooks here, and since when? Where do the recipes come from? A few sentences are enough.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Replace this text in the editor. Photos of your kitchen or your team fit well next to it.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:menudash-theme/social {"variant":"follow"} /-->
</div>
<!-- /wp:group -->
