<?php
/**
 * Title: Visit us page with the map
 * Slug: menudash-theme/visit-page
 * Categories: menudash-theme
 * Description: A whole "Come and see us" page: "Open now", the address (opens Google Maps), getting here, the Directions, Call and E-mail buttons and the opening hours, with the Google map below. Everything comes from MenuDash → Restaurant and Hours & holidays.
 */
?>
<!-- wp:menudash/open /-->
<!-- wp:columns {"className":"mdt-visit-page","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns mdt-visit-page" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html__( 'Address', 'menudash-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:menudash/contact {"parts":["name","address"],"mapLink":true,"fontSize":"large"} /-->
<!-- wp:pattern {"slug":"menudash-theme/directions"} /-->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html__( 'Opening hours', 'menudash-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:pattern {"slug":"menudash-theme/hours"} /-->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
<?php
echo mdt_bound_button( 'route', __( 'Get directions', 'menudash-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- block markup, escaped inside
echo mdt_bound_button( 'call', trim( str_replace( '· %s', '', __( 'Call · %s', 'menudash-theme' ) ) ), true, __( 'Call · %s', 'menudash-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
echo mdt_bound_button( 'email', __( 'Send an e-mail', 'menudash-theme' ), true ); // phpcs:ignore WordPress.Security.EscapeOutput
?>
</div>
<!-- /wp:buttons -->
<!-- wp:menudash-theme/map /-->
