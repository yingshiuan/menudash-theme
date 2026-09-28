<?php
/**
 * Title: Home: our recommendations
 * Slug: menudash-theme/picks
 * Categories: menudash-theme
 * Description: The dishes marked "Recommended" in the MenuDash menu, with their photos; they change with the menu by themselves.
 */
$menu = get_page_by_path( 'menu' );
$menu = $menu ? get_permalink( $menu ) : home_url( '/menu/' );
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"paper","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-paper-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php echo esc_html__( 'Our recommendations', 'menudash-theme' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"muted"} -->
<p class="has-text-align-center has-muted-color has-text-color"><?php echo esc_html__( 'Favourites from our kitchen', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:menudash-theme/picks /-->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $menu ); ?>"><?php echo esc_html__( 'See the whole menu', 'menudash-theme' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
