<?php
/**
 * Title: Page not found
 * Slug: menudash-theme/404
 * Inserter: no
 * Description: The 404 page: a big "404" with the restaurant's icon as the zero (Settings → General → Site Icon, drawn round; the smiling drawing without one), a short note and buttons to the menu and the home page.
 */
?>
<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"mdt-404","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center mdt-404">
<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
<!-- wp:menudash-theme/not-found /-->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
<?php echo mdt_eyebrow( __( 'Error 404', 'menudash-theme' ) ); // phpcs:ignore -- escaped in mdt_eyebrow() ?>
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html__( 'This page is not on the menu', 'menudash-theme' ); ?></h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html__( 'Maybe the link is old, or the page has moved. Carry on here:', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( mdt_menu_url() ); ?>"><?php echo esc_html__( 'Menu', 'menudash-theme' ); ?></a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline","style":{"border":{"width":"2px"}},"borderColor":"primary","textColor":"primary"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-primary-color has-text-color has-border-color has-primary-border-color wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>" style="border-width:2px"><?php echo esc_html__( 'Home page', 'menudash-theme' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
