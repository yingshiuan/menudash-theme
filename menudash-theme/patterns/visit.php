<?php
/**
 * Title: Home: visit us / delivery (with map)
 * Slug: menudash-theme/visit
 * Categories: menudash-theme
 * Description: Two ways to your food: come by (address, phone, getting here, directions) or order online (ordering times with "order now"), with the map below. Everything comes from MenuDash → Restaurant; without an order link the delivery column goes.
 */

?>
<!-- wp:group {"align":"full","className":"is-style-mdt-on-cream","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-mdt-on-cream" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"className":"mdt-visit-bowl","layout":{"type":"default"}} -->
<div class="wp-block-group mdt-visit-bowl"></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<?php if ( mdt_has_restaurant() ) : ?>
<?php echo mdt_visit_markup(); // phpcs:ignore -- block markup, escaped inside ?>
<?php else : // MenuDash alone: text to fill in. Marked mdt-visit-fallback, so once MenuDash Restaurant is installed the theme shows the filled-in version instead, even on a saved page. ?>
<!-- wp:group {"align":"wide","className":"mdt-visit-fallback","layout":{"type":"constrained","contentSize":"640px","justifyContent":"left"}} -->
<div class="wp-block-group alignwide mdt-visit-fallback">
<?php echo mdt_visit_eyebrow(); // phpcs:ignore -- escaped inside ?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html__( 'Street and number', 'menudash-theme' ); ?><br><?php echo esc_html__( 'Postcode and town', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><?php echo esc_html__( 'How to get here by tram, bus or car, and when you are open. Replace this text in the editor.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:menudash-theme/addon {"part":"visit"} /-->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( mdt_menu_url() ); ?>"><?php echo esc_html__( 'Menu', 'menudash-theme' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
<?php endif; ?>
</div>
<!-- /wp:group -->
