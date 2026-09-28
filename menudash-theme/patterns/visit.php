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
<!-- wp:columns {"align":"wide","className":"mdt-two-ways"} -->
<div class="wp-block-columns alignwide mdt-two-ways">
<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"primary"} -->
<p class="has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase"><?php echo esc_html__( 'Come by', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Visit us', 'menudash-theme' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:menudash-theme/contact {"variant":"visit"} /-->
<!-- wp:menudash-theme/directions {"first":true} /-->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<?php echo mdt_bound_button( 'route', __( 'Get directions', 'menudash-theme' ) ); ?>
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%">
<!-- wp:menudash-theme/delivery /-->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:menudash-theme/map /-->
</div>
<!-- /wp:group -->
