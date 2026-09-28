<?php
/**
 * Title: Page not found
 * Slug: menudash-theme/404
 * Inserter: no
 */
?>
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html__( 'Page not found', 'menudash-theme' ); ?></h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php echo wp_kses( sprintf( /* translators: %s: link to the home page */ __( 'This page does not exist (any more). Here is the %s.', 'menudash-theme' ), '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'home page', 'menudash-theme' ) . '</a>' ), array( 'a' => array( 'href' => true ) ) ); ?></p>
<!-- /wp:paragraph -->
