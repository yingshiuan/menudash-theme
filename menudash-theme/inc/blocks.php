<?php
/**
 * The theme's MenuDash blocks: the parts of the site that show what is entered in MenuDash
 * (restaurant details, menu, gift cards). They are drawn fresh on every page view, so
 * saving a page or the footer in the editor never freezes an old phone number or address
 * into it; in the editor they show a live preview (assets/js/blocks-editor.js). Texts
 * around them are normal blocks. Without MenuDash they print nothing.
 *
 *   menudash-theme/place        street · city, the welcome's small line
 *   menudash-theme/open         MenuDash's "open now" badge; for="order" for ordering times
 *   menudash-theme/buttons      which="hero": menu · reserve · order online; which="route": directions
 *   menudash-theme/contact      variant="visit": address and phone; variant="footer": address, country, phone, e-mail
 *   menudash-theme/directions   "Getting here", all lines or only the first (first=true)
 *   menudash-theme/social       variant="icons": Facebook and Instagram icons; variant="follow": the Instagram button
 *   menudash-theme/delivery     the whole delivery column; nothing without an order link
 *   menudash-theme/map          the Google Maps map, loaded on request
 *   menudash-theme/legal        "© Company · Privacy"
 *   menudash-theme/picks        the dishes marked Recommended in the menu, with photos
 *   menudash-theme/giftcard     the gift card teaser; only while gift cards can be ordered
 */

defined( 'ABSPATH' ) || exit;

/** The block names and their titles in the editor. */
function mdt_blocks() {
	return array(
		'place'      => __( 'Place (street · town)', 'menudash-theme' ),
		'open'       => __( 'Open now', 'menudash-theme' ),
		'buttons'    => __( 'Buttons (menu, reserve, order)', 'menudash-theme' ),
		'contact'    => __( 'Address and contact', 'menudash-theme' ),
		'directions' => __( 'Getting here', 'menudash-theme' ),
		'social'     => __( 'Instagram / Facebook', 'menudash-theme' ),
		'delivery'   => __( 'Delivery', 'menudash-theme' ),
		'map'        => __( 'Map', 'menudash-theme' ),
		'legal'      => __( '© and privacy', 'menudash-theme' ),
		'picks'      => __( 'Recommended dishes', 'menudash-theme' ),
		'giftcard'   => __( 'Gift card teaser', 'menudash-theme' ),
	);
}

add_action(
	'init',
	function () {
		$atts = array(
			'open'       => array( 'for' => array( 'type' => 'string', 'default' => 'open' ) ),
			'buttons'    => array( 'which' => array( 'type' => 'string', 'default' => 'hero' ) ),
			'contact'    => array( 'variant' => array( 'type' => 'string', 'default' => 'visit' ) ),
			'directions' => array( 'first' => array( 'type' => 'boolean', 'default' => false ) ),
			'social'     => array( 'variant' => array( 'type' => 'string', 'default' => 'icons' ) ),
		);
		foreach ( mdt_blocks() as $name => $title ) {
			register_block_type(
				"menudash-theme/$name",
				array(
					'title'           => $title,
					'category'        => 'theme',
					'attributes'      => ( isset( $atts[ $name ] ) ? $atts[ $name ] : array() ) + array( 'className' => array( 'type' => 'string', 'default' => '' ) ),
					'supports'        => array( 'html' => false ),
					'render_callback' => function ( $a ) use ( $name ) {
						return function_exists( 'mdash_detail' ) ? call_user_func( "mdt_block_$name", $a ) : '';
					},
				)
			);
		}
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_script( 'menudash-theme-blocks', get_theme_file_uri( 'assets/js/blocks-editor.js' ), array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ), wp_get_theme()->get( 'Version' ), true );
		wp_localize_script( 'menudash-theme-blocks', 'mdtBlocks', array( 'names' => mdt_blocks(), 'hint' => __( 'From MenuDash; change it there.', 'menudash-theme' ) ) );
	}
);

/** Block markup to HTML, with the shortcodes in it (the open badge, the hours) run. */
function mdt_render( $markup ) {
	return do_shortcode( do_blocks( $markup ) );
}

/** "de" on a German site, otherwise "en": the language for MenuDash's own texts. */
function mdt_lang() {
	return 0 === strpos( get_locale(), 'de' ) ? 'de' : 'en';
}

/** The small uppercase line above a heading. */
function mdt_eyebrow( $text ) {
	return '<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"primary"} --><p class="has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase">' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
}

/**
 * The open badge. In the editor's preview (a REST request) a sample, since the real text is
 * worked out in the visitor's browser.
 */
function mdt_block_open( $atts ) {
	$order = 'order' === $atts['for'];
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return '<p class="menudash-open is-open">' . esc_html( $order ? __( 'Order now · until … (example)', 'menudash-theme' ) : __( 'Open now · until … (example)', 'menudash-theme' ) ) . '</p>';
	}
	return do_shortcode( '[menudash_open lang="' . mdt_lang() . '"' . ( $order ? ' for="order"' : '' ) . ']' );
}

function mdt_block_place() {
	$text = implode( ' · ', array_filter( array( mdt_d( 'street' ), mdt_d( 'city' ) ) ) );
	return '' === $text ? '' : '<p class="mdt-place has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase">' . esc_html( $text ) . '</p>';
}

/** One button's block markup; $outline for the bordered style. */
function mdt_button( $url, $text, $outline = false, $external = false ) {
	$ext = $external ? ' target="_blank" rel="noopener"' : '';
	if ( $outline ) {
		return '<!-- wp:button {"className":"is-style-outline","style":{"border":{"width":"2px"}},"borderColor":"primary","textColor":"primary"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-primary-color has-text-color has-border-color has-primary-border-color wp-element-button" href="' . esc_url( $url ) . '" style="border-width:2px"' . $ext . '>' . esc_html( $text ) . '</a></div><!-- /wp:button -->';
	}
	return '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '"' . $ext . '>' . esc_html( $text ) . '</a></div><!-- /wp:button -->';
}

/** The page with the menu ([menudash]), or /menu/. */
function mdt_menu_url() {
	$pages = function_exists( 'mdash_menu_pages_public' ) ? mdash_menu_pages_public() : array();
	return $pages ? get_permalink( $pages[0] ) : home_url( '/menu/' );
}

function mdt_block_buttons( $atts ) {
	$b = '';
	if ( 'route' === $atts['which'] ) {
		$route = mdash_maps_url( 'route' );
		$b     = '' !== $route ? mdt_button( $route, __( 'Get directions', 'menudash-theme' ), false, true ) : '';
	} else {
		$b .= mdt_button( mdt_menu_url(), __( 'Menu', 'menudash-theme' ) );
		if ( '' !== mdt_d( 'phone' ) ) {
			/* translators: %s: the restaurant's phone number */
			$b .= mdt_button( 'tel:' . mdash_tel( mdt_d( 'phone' ) ), sprintf( __( 'Reserve · %s', 'menudash-theme' ), mdt_d( 'phone' ) ), true );
		}
		if ( '' !== mdt_d( 'delivery_url' ) ) {
			$b .= mdt_button( '#delivery', __( 'Order online', 'menudash-theme' ), true );
		}
	}
	if ( '' === $b ) {
		return '';
	}
	$gap = 'route' === $atts['which'] ? '' : ' {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}}';
	$sty = 'route' === $atts['which'] ? '' : ' style="margin-top:var(--wp--preset--spacing--40)"';
	return mdt_render( '<!-- wp:buttons' . $gap . ' --><div class="wp-block-buttons"' . $sty . '>' . $b . '</div><!-- /wp:buttons -->' );
}

function mdt_block_contact( $atts ) {
	$street = esc_html( mdt_d( 'street' ) );
	$town   = esc_html( trim( mdt_d( 'postcode' ) . ' ' . mdt_d( 'city' ) ) );
	$phone  = mdt_d( 'phone' );
	$tel    = '' !== $phone ? '<a href="' . esc_url( 'tel:' . mdash_tel( $phone ) ) . '" style="white-space:nowrap">' . esc_html( $phone ) . '</a>' : '';
	$mail   = '' !== mdt_d( 'email' ) ? '<a href="' . esc_url( 'mailto:' . mdt_d( 'email' ) ) . '">' . esc_html( mdt_d( 'email' ) ) . '</a>' : '';
	if ( 'footer' === $atts['variant'] ) {
		$lines = array_filter( array( $street, $town, esc_html( mdt_country() ), $tel, $mail ) );
		return $lines ? '<p>' . implode( '<br>', $lines ) . '</p>' : '';
	}
	$html = '';
	if ( '' !== $street || '' !== $town ) {
		$html .= '<p class="has-large-font-size">' . implode( '<br>', array_filter( array( $street, $town ) ) ) . '</p>';
	}
	/* translators: %s: the restaurant's phone number, as a link */
	$contact = array_filter( array( '' !== $tel ? sprintf( __( 'Reservations by phone: %s', 'menudash-theme' ), $tel ) : '', '' !== $mail ? sprintf( /* translators: %s: e-mail link */ __( 'E-mail: %s', 'menudash-theme' ), $mail ) : '' ) );
	if ( $contact ) {
		$html .= '<p>' . implode( '<br>', $contact ) . '</p>';
	}
	return $html;
}

function mdt_block_directions( $atts ) {
	$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', mdt_d( 'directions' ) ) ) ) );
	if ( ! empty( $atts['first'] ) ) {
		$lines = array_slice( $lines, 0, 1 );
	}
	return function_exists( 'mdash_directions_html' ) ? mdash_directions_html( implode( "\n", $lines ) ) : '';
}

function mdt_block_social( $atts ) {
	if ( 'follow' === $atts['variant'] ) {
		if ( '' === mdt_d( 'instagram' ) ) {
			return '';
		}
		$handle = ltrim( basename( untrailingslashit( mdt_d( 'instagram' ) ) ), '@' );
		/* translators: %s: the Instagram account name */
		$label = sprintf( __( 'Follow us on Instagram @%s', 'menudash-theme' ), $handle );
		return mdt_render( '<!-- wp:social-links {"iconColor":"white","iconColorValue":"#FFFFFF","iconBackgroundColor":"primary","showLabels":true,"className":"mdt-follow","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} --><ul class="wp-block-social-links has-visible-labels has-icon-color has-icon-background-color mdt-follow" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:social-link {"url":"' . esc_url( mdash_social_url( 'instagram', mdt_d( 'instagram' ) ) ) . '","service":"instagram","label":"' . esc_attr( $label ) . '"} /--></ul><!-- /wp:social-links -->' );
	}
	$links = '';
	foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram' ) as $service => $label ) {
		if ( '' !== mdt_d( $service ) ) {
			$links .= '<!-- wp:social-link {"url":"' . esc_url( mdash_social_url( $service, mdt_d( $service ) ) ) . '","service":"' . $service . '","label":"' . $label . '"} /-->';
		}
	}
	return '' === $links ? '' : mdt_render( '<!-- wp:social-links {"iconColor":"cream","className":"is-style-logos-only"} --><ul class="wp-block-social-links has-icon-color is-style-logos-only">' . $links . '</ul><!-- /wp:social-links -->' );
}

function mdt_block_delivery() {
	$order = mdt_d( 'delivery_url' );
	if ( '' === $order ) {
		return '';
	}
	$service = mdt_d( 'delivery_name' );
	$heading = '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'Eating at home?', 'menudash-theme' ) . '</h2><!-- /wp:heading -->';
	/* translators: %s: the delivery service, e.g. "Uber Eats" */
	$line    = '' !== $service ? sprintf( __( 'No problem: order online with %s.', 'menudash-theme' ), $service ) : __( 'No problem: order online.', 'menudash-theme' );
	$text    = '<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html( $line ) . '</p><!-- /wp:paragraph -->';
	$times   = mdt_block_open( array( 'for' => 'order' ) ) . '<!-- wp:shortcode -->[menudash_hours for="delivery" lang="' . mdt_lang() . '" class="mdt-table mdt-delivery-hours"]<!-- /wp:shortcode -->';
	/* translators: %s: the delivery service, e.g. "Uber Eats" */
	$label   = '' !== $service ? sprintf( __( 'Order with %s', 'menudash-theme' ), $service ) : __( 'Order online', 'menudash-theme' );
	$button  = '<!-- wp:buttons --><div class="wp-block-buttons">' . mdt_button( $order, $label, false, true ) . '</div><!-- /wp:buttons -->';
	return '<div id="delivery" class="mdt-delivery">' . mdt_render( mdt_eyebrow( __( 'Delivery', 'menudash-theme' ) ) . $heading . $text . $times . $button ) . '</div>';
}

function mdt_block_map() {
	$src = mdash_maps_url( 'embed' );
	if ( '' === $src ) {
		return '';
	}
	// The iframe becomes the "Show map" placeholder in the render_block filter (functions.php).
	/* translators: %s: the restaurant's name and address */
	$title = sprintf( __( 'Map: %s', 'menudash-theme' ), trim( mdt_d( 'name' ) . ', ' . mdt_d( 'address' ), ', ' ) );
	return '<div class="mdt-map alignwide mdt-map-wide"><iframe src="' . esc_url( $src ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="' . esc_attr( $title ) . '" allowfullscreen></iframe></div>';
}

function mdt_block_legal() {
	$id    = (int) get_option( 'wp_page_for_privacy_policy' );
	$page  = $id ? get_post( $id ) : null; // get_post( 0 ) would be the current page.
	$link  = $page && 'publish' === $page->post_status ? '<a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html__( 'Privacy', 'menudash-theme' ) . '</a>' : '';
	$owner = '' !== mdt_d( 'company' ) ? mdt_d( 'company' ) : mdt_d( 'name' );
	$parts = array_filter( array( '' !== $owner ? '© ' . esc_html( $owner ) : '', $link ) );
	return $parts ? '<p class="alignwide" style="font-size:0.8125rem">' . implode( ' · ', $parts ) . '</p>' : '';
}

/**
 * The dishes marked Recommended in the MenuDash menu, up to 12, with their photos
 * (dishes without a photo come last). Each links to the menu.
 */
function mdt_block_picks() {
	if ( ! function_exists( 'mdash_get_menu' ) || ! ( $menu = mdash_get_menu() ) ) { // phpcs:ignore
		return '';
	}
	$photos = mdash_photo_index();
	$match  = mdash_photo_matches( $menu, $photos );
	$lang   = mdt_lang();
	$other  = 'de' === $lang ? 'en' : 'de';
	$with   = array();
	$without = array();
	foreach ( $menu['sections'] as $sec ) {
		foreach ( $sec['dishes'] as $d ) {
			if ( ! in_array( 'pick', (array) $d['flags'], true ) ) {
				continue;
			}
			$photo = isset( $match['dish'][ $d['key'] ], $photos[ $match['dish'][ $d['key'] ] ] ) ? $photos[ $match['dish'][ $d['key'] ] ] : null;
			$name  = ! empty( $d['name'][ $lang ] ) ? $d['name'][ $lang ] : ( ! empty( $d['name'][ $other ] ) ? $d['name'][ $other ] : reset( $d['name'] ) );
			$item  = array( $d, $photo, $name );
			if ( $photo ) {
				$with[] = $item;
			} else {
				$without[] = $item;
			}
		}
	}
	$items = array_slice( array_merge( $with, $without ), 0, 12 );
	if ( ! $items ) {
		return '';
	}
	$url  = mdt_menu_url();
	$html = '<div class="mdt-picks mdt-picks-grid">';
	foreach ( $items as $it ) {
		list( $d, $photo, $name ) = $it;
		$second = ! empty( $d['name']['zh'] ) ? $d['name']['zh'] : '';
		$html  .= '<a class="mdt-pick" href="' . esc_url( $url ) . '">';
		if ( ! $photo ) { // No photo yet: a plate with the bowl drawing, so the row stays even.
			$html .= '<figure class="mdt-dish mdt-dish-none" aria-hidden="true"><span></span></figure>';
		} else {
			$html .= '<figure class="mdt-dish' . ( $photo['alpha'] ? '' : ' mdt-dish-round' ) . '"><img src="' . esc_url( mdash_photo_url( $photo, 400 ) ) . '" alt="' . esc_attr( $name ) . '" width="400" height="400" loading="lazy"></figure>';
		}
		$html .= ( '' !== (string) $d['no'] ? '<span class="mdt-no">' . esc_html( sprintf( /* translators: %s: dish number */ __( 'No. %s', 'menudash-theme' ), $d['no'] ) ) . '</span>' : '' )
			. '<strong>' . esc_html( $name ) . '</strong>' . ( '' !== $second ? '<span class="mdt-zh">' . esc_html( $second ) . '</span>' : '' ) . '</a>';
	}
	return $html . '</div>';
}

/** The gift card teaser: only while MenuDash takes gift card orders and a page has the form. */
function mdt_block_giftcard() {
	if ( ! function_exists( 'mdash_gc_open' ) || ! mdash_gc_open() ) {
		return '';
	}
	$page = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 's' => 'menudash_giftcard', 'fields' => 'ids' ) );
	if ( ! $page ) {
		return '';
	}
	$gc  = mdash_gc_settings();
	$pic = $gc['picture'] ? '<figure class="wp-block-image aligncenter mdt-gc-card"><a href="' . esc_url( get_permalink( $page[0] ) ) . '"><img src="' . esc_url( mdash_url( 'giftcard/' . $gc['picture']['file'] ) ) . '" alt="' . esc_attr__( 'Gift card', 'menudash-theme' ) . '" width="' . (int) $gc['picture']['w'] . '" height="' . (int) $gc['picture']['h'] . '"></a></figure>' : '';
	$col1 = mdt_eyebrow( __( 'Gift card', 'menudash-theme' ) )
		. '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'Give a meal', 'menudash-theme' ) . '</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html__( 'A gift card for a meal with us: ordered online in a minute.', 'menudash-theme' ) . '</p><!-- /wp:paragraph -->'
		. '<!-- wp:buttons --><div class="wp-block-buttons">' . mdt_button( get_permalink( $page[0] ), __( 'Order a gift card', 'menudash-theme' ) ) . '</div><!-- /wp:buttons -->';
	return mdt_render(
		'<!-- wp:group {"align":"full","className":"mdt-gc-home","backgroundColor":"paper","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull mdt-gc-home has-paper-background-color has-background"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} --><div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"55%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">' . $col1 . '</div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"45%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">' . $pic . '</div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->'
	);
}
