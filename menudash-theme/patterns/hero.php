<?php
/**
 * Title: Home: welcome
 * Slug: menudash-theme/hero
 * Categories: menudash-theme
 * Description: The restaurant's name line, a big headline, the "open now" badge and the buttons (menu, reserve, order online; each can be deleted or changed, reserve and order take their links from MenuDash → Restaurant), with a bowl drawing beside it.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:menudash/open /-->
<!-- wp:menudash/contact {"parts":["address"],"oneLine":true,"textColor":"primary","style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}},"className":"mdt-place"} /-->
<!-- wp:heading {"level":1,"style":{"typography":{"lineHeight":"0.9"}}} -->
<h1 class="wp-block-heading" style="line-height:0.9"><?php echo esc_html__( 'Home cooking, made fresh every day', 'menudash-theme' ); ?></h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html__( 'Tell your guests in one or two lines what makes your kitchen special.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( mdt_menu_url() ); ?>"><?php echo esc_html__( 'Menu', 'menudash-theme' ); ?></a></div>
<!-- /wp:button -->
<?php
/* translators: %s: the restaurant's phone number */
echo mdt_bound_button( 'reserve', trim( str_replace( '· %s', '', __( 'Reserve · %s', 'menudash-theme' ) ) ), true, __( 'Reserve · %s', 'menudash-theme' ) );
echo mdt_bound_button( 'order', __( 'Order online', 'menudash-theme' ), true );
?>
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
<!-- wp:group {"className":"mdt-hero-bowl","layout":{"type":"default"}} -->
<div class="wp-block-group mdt-hero-bowl"></div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
