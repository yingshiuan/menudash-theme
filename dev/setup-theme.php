<?php
/**
 * Demo for dev/serve.sh, after MenuDash's own demo (sample menu, photos, specials, hours,
 * details, gift cards and their pages): the theme's home page and a top menu: Home · Menu ·
 * Gift card. The site language comes from the blueprint (dev/serve.sh … de: German).
 */

require_once '/wordpress/wp-load.php';

update_option( 'timezone_string', 'Europe/Berlin' );

// Recommended dishes need the "Recommended" mark; the sample menu has some.
mdt_setup_home();
mdt_setup_menu();

// The top menu as a block navigation: Home · Menu · Gift card.
// (WordPress may already have made an automatic one that only lists the pages.)
if ( ! get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1 ) ) ) {
	$links = '<!-- wp:navigation-link {"label":"' . esc_attr__( 'Home', 'menudash-theme' ) . '","url":"' . home_url( '/' ) . '","kind":"custom"} /-->';
	foreach ( array( 'menu', 'gift-card' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$links .= sprintf( '<!-- wp:navigation-link {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type"} /-->', esc_attr( get_the_title( $page ) ), $page->ID, get_permalink( $page ) );
		}
	}
	wp_insert_post( array( 'post_type' => 'wp_navigation', 'post_status' => 'publish', 'post_title' => 'Main menu', 'post_content' => $links ) );
}
