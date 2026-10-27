<?php
/**
 * The theme's MenuDash blocks: the parts of the site that show what is entered in MenuDash
 * (restaurant details, menu, gift cards). They are drawn fresh on every page view, so
 * saving a page or the footer in the editor never freezes an old phone number or address
 * into it; in the editor they show a live preview (assets/js/blocks-editor.js). Texts
 * around them are normal blocks. Without MenuDash they print nothing.
 *
 *   menudash-theme/place        only for pages saved with 1.0/1.1: now MenuDash's Contact block
 *   menudash-theme/open         only for pages saved with 1.0: now MenuDash's own "Open now" block
 *   menudash-theme/buttons      only for pages saved with 1.0: the patterns now use WordPress buttons
 *                               with MenuDash links (mdt_bound_button)
 *   menudash-theme/contact      only for pages saved with 1.0/1.1: now MenuDash's Contact block
 *   menudash-theme/directions   only for pages saved with 1.0/1.1: now MenuDash's Contact block
 *   menudash-theme/social       variant="icons": icons for the social links from MenuDash → Restaurant; variant="follow": the Instagram button
 *   menudash-theme/delivery     the whole delivery column; nothing without an order link
 *   menudash-theme/map          the Google Maps map, loaded on request
 *   menudash-theme/legal        "© Company · Legal notice · Privacy · meat origin" (each link when its page exists)
 *   menudash-theme/provider     who runs the website: company, address, phone, e-mail (legal notice)
 *   menudash-theme/picks        the dishes marked Recommended in the menu, with photos; with
 *                               MenuDash 2.3 its block menudash/picks, which the pattern now uses
 *   menudash-theme/giftcard     the gift card teaser; only while gift cards can be ordered
 *   menudash-theme/not-found    the big "404" of the 404 page, its 0 the Site Icon (or the smile,
 *                               or an own picture: picture / pictureId in the block's sidebar)
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
		'provider'   => __( 'Provider (legal notice)', 'menudash-theme' ),
		'picks'      => __( 'Recommended dishes', 'menudash-theme' ),
		'giftcard'   => __( 'Gift card teaser', 'menudash-theme' ),
		'giftcard-visit' => __( 'Gift card: at the restaurant', 'menudash-theme' ),
		'addon'      => __( 'Needs MenuDash Restaurant', 'menudash-theme' ),
		'not-found'  => __( '404 number', 'menudash-theme' ),
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
			'addon'      => array( 'part' => array( 'type' => 'string', 'default' => 'footer' ) ),
			'not-found'  => array(
				'picture'   => array( 'type' => 'string', 'default' => '' ),
				'pictureId' => array( 'type' => 'number', 'default' => 0 ),
			),
			// A section of its own: full width in the editor too.
			'giftcard'   => array( 'align' => array( 'type' => 'string', 'default' => 'full' ) ),
		);
		foreach ( mdt_blocks() as $name => $title ) {
			register_block_type(
				"menudash-theme/$name",
				array(
					'title'           => $title,
					'category'        => 'menudash-theme',
					'attributes'      => ( isset( $atts[ $name ] ) ? $atts[ $name ] : array() ) + array( 'className' => array( 'type' => 'string', 'default' => '' ) ),
					'supports'        => 'giftcard' === $name ? array( 'html' => false, 'align' => array( 'full' ) ) : array( 'html' => false ),
					'render_callback' => function ( $a ) use ( $name ) {
						// Recommended dishes, gift cards and © work with MenuDash alone; the rest needs Restaurant.
						$html = mdt_has_restaurant() || in_array( $name, array( 'picks', 'giftcard', 'giftcard-visit', 'legal', 'addon', 'not-found' ), true ) ? call_user_func( 'mdt_block_' . str_replace( '-', '_', $name ), $a ) : '';
						// Empty in the editor: say when it shows, instead of WordPress's grey "rendered as empty".
						return '' === $html ? mdt_empty_note( $name ) : $html;
					},
				)
			);
		}
	}
);

// The home page's two ways (visit / delivery): a column left empty, e.g. without an order
// link, is left out, so the other one can take the whole width.
add_filter(
	'render_block_core/columns',
	function ( $content, $block ) {
		$class = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
		return false === strpos( $class, 'mdt-two-ways' ) ? $content : preg_replace( '#<div class="wp-block-column(?: [^"]*)?"[^>]*>\s*</div>#', '', $content );
	},
	10,
	2
);

// The theme's blocks under their own heading in the inserter, not in WordPress's "Theme" list.
add_filter(
	'block_categories_all',
	function ( $categories ) {
		return array_merge( $categories, array( array( 'slug' => 'menudash-theme', 'title' => __( 'MenuDash Theme', 'menudash-theme' ), 'icon' => null ) ) );
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_script( 'menudash-theme-blocks', get_theme_file_uri( 'assets/js/blocks-editor.js' ), array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render', 'wp-hooks', 'wp-compose', 'wp-components', 'wp-data', 'wp-core-data' ), wp_get_theme()->get( 'Version' ), true );
		wp_localize_script(
			'menudash-theme-blocks',
			'mdtBlocks',
			array(
				'names' => mdt_blocks(),
				'hint'  => __( 'From MenuDash; change it there.', 'menudash-theme' ),
				// "Show only when …" (mdt_show_conditions()) and the 404 number's picture.
				'show'  => array(
					'title'  => __( 'Show only when', 'menudash-theme' ),
					'always' => __( 'Always', 'menudash-theme' ),
					'help'   => __( 'Guests see this block only then. The theme uses it where a part needs MenuDash Restaurant or a logo.', 'menudash-theme' ),
					'badge'  => __( 'Shown only when:', 'menudash-theme' ),
					'when'   => mdt_show_conditions(),
				),
				'zero'  => array(
					'title'  => __( 'The 0', 'menudash-theme' ),
					'auto'   => __( 'Site Icon (automatic)', 'menudash-theme' ),
					'autoTip' => __( 'The icon from MenuDash → Restaurant or Settings → General, drawn round; without one, the smile.', 'menudash-theme' ),
					'smile'  => __( 'Smile', 'menudash-theme' ),
					'own'    => __( 'Own picture', 'menudash-theme' ),
					'choose' => __( 'Choose a picture', 'menudash-theme' ),
					'other'  => __( 'Choose another picture', 'menudash-theme' ),
				),
				// The corner picture's panel on the header (mdt_mark()).
				'mark'  => array(
					'icon'    => has_site_icon() ? get_site_icon_url( 512 ) : '',
					'title'   => __( 'Corner picture', 'menudash-theme' ),
					'picture' => __( 'Picture', 'menudash-theme' ),
					'auto'    => __( 'Automatic', 'menudash-theme' ),
					'autoTip' => __( 'Automatic: the icon from MenuDash → Restaurant, otherwise the ring.', 'menudash-theme' ),
					'ring'    => __( 'Ring', 'menudash-theme' ),
					'own'     => __( 'Own picture', 'menudash-theme' ),
					'none'    => __( 'No picture', 'menudash-theme' ),
					'choose'  => __( 'Choose a picture', 'menudash-theme' ),
					'other'   => __( 'Choose another picture', 'menudash-theme' ),
					'opacity' => __( 'Strength (%)', 'menudash-theme' ),
					'opTip'   => __( 'How clearly the picture shows in the coloured band. Default: 8.', 'menudash-theme' ),
				),
			)
		);
	}
);

/*
 * The faint picture in the header band's right corner (class mdt-seal on the header's Group).
 * The owner chooses it on that Group (Site Editor → Header → "Corner picture"):
 *   ''      automatic: the icon from MenuDash → Restaurant (the Site Icon), else the ring
 *   'ring'  always the ring
 *   'image' an own picture from the media library (mdtMarkId)
 *   'none'  no picture
 * and how strong it shows (mdtMarkOpacity, in percent; 8 by default).
 */
add_filter(
	'register_block_type_args',
	function ( $args, $name ) {
		if ( 'core/group' === $name ) {
			$args['attributes'] = ( isset( $args['attributes'] ) ? $args['attributes'] : array() ) + array(
				'mdtMark'        => array( 'type' => 'string', 'default' => '' ),
				'mdtMarkId'      => array( 'type' => 'number', 'default' => 0 ),
				'mdtMarkOpacity' => array( 'type' => 'number', 'default' => 8 ),
			);
		}
		return $args;
	},
	10,
	2
);

/** The corner picture for a header Group's settings: url ('' = the ring), round (an icon), off, opacity. */
function mdt_mark( $attrs = array() ) {
	$source  = isset( $attrs['mdtMark'] ) ? (string) $attrs['mdtMark'] : '';
	$opacity = isset( $attrs['mdtMarkOpacity'] ) ? max( 0, min( 100, (int) $attrs['mdtMarkOpacity'] ) ) / 100 : .08;
	$mark    = array( 'url' => '', 'round' => false, 'off' => 'none' === $source, 'opacity' => $opacity );
	if ( 'image' === $source && ! empty( $attrs['mdtMarkId'] ) ) {
		$mark['url'] = (string) wp_get_attachment_image_url( (int) $attrs['mdtMarkId'], 'large' );
	} elseif ( '' === $source && has_site_icon() ) {
		// The icon is usually a filled square: drawn as a circle, like the ring.
		$mark['url']   = get_site_icon_url( 512 );
		$mark['round'] = true;
	}
	return $mark;
}

add_filter(
	'render_block_core/group',
	function ( $content, $block ) {
		$class = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
		if ( ! preg_match( '/(^|\s)mdt-seal(\s|$)/', $class ) ) {
			return $content;
		}
		$mark = mdt_mark( $block['attrs'] );
		$tag  = new WP_HTML_Tag_Processor( $content );
		if ( ! $tag->next_tag() ) {
			return $content;
		}
		if ( $mark['off'] ) {
			$tag->add_class( 'mdt-seal-off' );
			return $tag->get_updated_html();
		}
		$vars = '';
		if ( '' !== $mark['url'] ) {
			$tag->add_class( 'mdt-seal-img' );
			$vars .= '--mdt-mark:url("' . esc_url( $mark['url'] ) . '");';
		}
		if ( $mark['round'] ) {
			$tag->add_class( 'mdt-seal-round' );
		}
		if ( abs( $mark['opacity'] - .08 ) > .001 ) {
			$vars .= '--mdt-mark-opacity:' . $mark['opacity'] . ';';
		}
		if ( '' !== $vars ) {
			$style = (string) $tag->get_attribute( 'style' );
			$tag->set_attribute( 'style', ( '' !== $style ? rtrim( $style, ';' ) . ';' : '' ) . $vars );
		}
		return $tag->get_updated_html();
	},
	10,
	2
);

/** Block markup to HTML, with the shortcodes in it (the open badge, the hours) run. */
function mdt_render( $markup ) {
	// Blocks only: the markup built here holds no shortcodes, and a second shortcode pass
	// would run text from the Restaurant tab ("Tel. [gallery]") as one.
	return do_blocks( $markup );
}

/** "de" on a German site, otherwise "en": the language for MenuDash's own texts. */
function mdt_lang() {
	return 0 === strpos( get_locale(), 'de' ) ? 'de' : 'en';
}

/** The small uppercase line above a heading. */
function mdt_eyebrow( $text, $id = '' ) {
	return '<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"primary"} --><p' . ( '' !== $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . ' class="has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase">' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
}

/** Old pages' menudash-theme/open, drawn by MenuDash's Open now block, which the theme uses now. */
function mdt_block_open( $atts ) {
	return do_blocks( '<!-- wp:menudash/open ' . serialize_block_attributes( array( 'for' => 'order' === $atts['for'] ? 'order' : 'open' ) ) . ' /-->' );
}

function mdt_block_place() {
	$text = implode( ' · ', array_filter( array( mdt_d( 'street' ), mdt_d( 'city' ) ) ) );
	return '' === $text ? '' : '<p class="mdt-place has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase">' . esc_html( $text ) . '</p>';
}

/** One button's block markup; $outline for the bordered style. */
function mdt_button( $url, $text, $outline = false, $external = false, $stat = '' ) {
	// $stat: a MenuDash Statistics key counted when the button is tapped (inc/giftcard-stats.php).
	$ext = ( $external ? ' target="_blank" rel="noopener"' : '' ) . ( '' !== $stat ? ' data-mdash-stat-tap="' . esc_attr( $stat ) . '"' : '' );
	if ( $outline ) {
		return '<!-- wp:button {"className":"is-style-outline","style":{"border":{"width":"2px"}},"borderColor":"primary","textColor":"primary"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-primary-color has-text-color has-border-color has-primary-border-color wp-element-button" href="' . esc_url( $url ) . '" style="border-width:2px"' . $ext . '>' . esc_html( $text ) . '</a></div><!-- /wp:button -->';
	}
	return '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '"' . $ext . '>' . esc_html( $text ) . '</a></div><!-- /wp:button -->';
}

/**
 * A WordPress Button whose link comes from MenuDash → Restaurant (block bindings, MenuDash 1.9):
 * $key reserve, order, call, route …; $label a text with the detail in it ("Reserve · %s"),
 * otherwise $text stays editable. The button isn't shown while the detail is empty.
 */
function mdt_bound_button( $key, $text, $outline = false, $label = '' ) {
	$bind = array( 'source' => 'menudash/detail', 'args' => array( 'key' => $key ) );
	$map  = array( 'url' => $bind, 'linkTarget' => $bind, 'rel' => $bind );
	if ( '' !== $label ) {
		$map['text'] = array( 'source' => 'menudash/detail', 'args' => array( 'key' => $key, 'label' => $label ) );
	}
	// Without MenuDash Restaurant the link has nothing to fill it: the button is left out.
	$attrs = array( 'metadata' => array( 'name' => $text . ' (MenuDash)', 'bindings' => $map ), 'mdtShow' => 'restaurant' );
	if ( $outline ) {
		$attrs = array( 'className' => 'is-style-outline', 'style' => array( 'border' => array( 'width' => '2px' ) ), 'borderColor' => 'primary', 'textColor' => 'primary' ) + $attrs;
		$a     = '<a class="wp-block-button__link has-primary-color has-text-color has-border-color has-primary-border-color wp-element-button" href="#" style="border-width:2px">';
	} else {
		$a = '<a class="wp-block-button__link wp-element-button" href="#">';
	}
	return '<!-- wp:button ' . serialize_block_attributes( $attrs ) . ' -->' . "\n"
		. '<div class="wp-block-button' . ( $outline ? ' is-style-outline' : '' ) . '">' . $a . esc_html( $text ) . '</a></div>' . "\n<!-- /wp:button -->\n";
}

/**
 * A note in the editor only, where the theme leaves out a part because the MenuDash
 * Restaurant add-on isn't installed: it says what would show there and where it comes from.
 * Guests see nothing. With the add-on it disappears, and the part itself shows instead.
 */
function mdt_block_addon( $a ) {
	if ( mdt_has_restaurant() || ! ( defined( 'REST_REQUEST' ) && REST_REQUEST && current_user_can( 'edit_posts' ) ) ) {
		return '';
	}
	$texts = array(
		'footer' => __( 'Here the address, getting here and opening hours appear once the MenuDash Restaurant add-on is installed.', 'menudash-theme' ),
		'hero'   => __( 'Here the "open now" badge, the address and the Reserve and Order online buttons appear once the MenuDash Restaurant add-on is installed.', 'menudash-theme' ),
		'visit'  => __( 'With the MenuDash Restaurant add-on this section fills itself: address, phone, getting here, directions, delivery and map, from MenuDash → Restaurant. Until then, write your address here.', 'menudash-theme' ),
		'follow' => __( 'Here the "Follow us on Instagram" button appears once the MenuDash Restaurant add-on is installed and an Instagram link is entered under MenuDash → Restaurant.', 'menudash-theme' ),
	);
	$part = isset( $texts[ $a['part'] ] ) ? $a['part'] : 'footer';
	return '<p class="mdt-addon-note">' . esc_html( $texts[ $part ] ) . ' <span>' . esc_html__( 'Only you see this note, in the editor.', 'menudash-theme' ) . '</span></p>';
}

/**
 * The big "404": its 0 is the Site Icon drawn round (picture ''), the theme's smile (picture
 * "smile", also without an icon) or an own picture (picture "image", pictureId).
 */
function mdt_block_not_found( $a ) {
	$picture = isset( $a['picture'] ) ? (string) $a['picture'] : '';
	$url     = '';
	if ( 'image' === $picture && ! empty( $a['pictureId'] ) ) {
		$url = (string) wp_get_attachment_image_url( (int) $a['pictureId'], 'medium' );
	} elseif ( '' === $picture && has_site_icon() ) {
		$url = get_site_icon_url( 512 );
	}
	$zero = new WP_HTML_Tag_Processor( '<span class="mdt-404-zero"></span>' );
	if ( '' !== $url && $zero->next_tag() ) {
		// Quoted, and the whole attribute escaped by the tag processor: a ")" in the address
		// can't end the url() and add CSS of its own.
		$zero->add_class( 'is-icon' );
		$zero->set_attribute( 'style', '--mdt-404-icon:url("' . esc_url_raw( $url ) . '")' );
	}
	return '<p class="mdt-404-num" aria-hidden="true">4' . $zero->get_updated_html() . '4</p>';
}

/** "Come by" and "Visit us", the start of the home page's visit section. */
function mdt_visit_eyebrow() {
	return '<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"primary"} -->' . "\n"
		. '<p class="has-primary-color has-text-color" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase">' . esc_html__( 'Come by', 'menudash-theme' ) . "</p>\n<!-- /wp:paragraph -->\n"
		. "<!-- wp:heading -->\n" . '<h2 class="wp-block-heading">' . esc_html__( 'Visit us', 'menudash-theme' ) . "</h2>\n<!-- /wp:heading -->\n";
}

/**
 * The visit section's content with MenuDash Restaurant: address, phone, getting here and
 * directions, delivery beside it, and the map below; all filled from MenuDash → Restaurant.
 */
function mdt_visit_markup() {
	/* translators: %s: the restaurant's phone number */
	$by_phone = trim( str_replace( '%s', '', __( 'Phone: %s', 'menudash-theme' ) ) );
	/* translators: %s: e-mail link */
	$by_mail = trim( str_replace( '%s', '', __( 'E-mail: %s', 'menudash-theme' ) ) );
	return '<!-- wp:columns {"align":"wide","className":"mdt-two-ways","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->' . "\n"
		. '<div class="wp-block-columns alignwide mdt-two-ways" style="margin-top:var(--wp--preset--spacing--40)">' . "\n"
		. '<!-- wp:column {"width":"50%"} -->' . "\n" . '<div class="wp-block-column" style="flex-basis:50%">' . "\n"
		. mdt_visit_eyebrow()
		. '<!-- wp:menudash/contact {"parts":["address"],"fontSize":"large","className":"mdt-visit-address"} /-->' . "\n"
		// Lines up with "Delivery hours" in the column beside it (style.css).
		. '<!-- wp:paragraph {"className":"mdt-contact-label"} -->' . "\n" . '<p class="mdt-contact-label"><strong>' . esc_html__( 'Reservations & contact', 'menudash-theme' ) . "</strong></p>\n<!-- /wp:paragraph -->\n"
		. '<!-- wp:menudash/contact ' . serialize_block_attributes( array( 'parts' => array( 'phone', 'email' ), 'phoneLabel' => $by_phone, 'emailLabel' => $by_mail, 'className' => 'mdt-visit-contact' ) ) . " /-->\n"
		. '<!-- wp:menudash/contact {"parts":["directions"],"directions":"first","className":"mdt-visit-directions"} /-->' . "\n"
		. "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">\n" . mdt_bound_button( 'route', __( 'Get directions', 'menudash-theme' ) ) . "</div>\n<!-- /wp:buttons -->\n"
		. "</div>\n<!-- /wp:column -->\n"
		. '<!-- wp:column {"width":"50%"} -->' . "\n" . '<div class="wp-block-column" style="flex-basis:50%">' . "\n"
		. "<!-- wp:menudash-theme/delivery /-->\n"
		. "</div>\n<!-- /wp:column -->\n"
		. "</div>\n<!-- /wp:columns -->\n"
		. "<!-- wp:menudash-theme/map /-->\n";
}

/*
 * The visit section's fill-in text (without MenuDash Restaurant) makes way for the filled-in
 * version once the add-on is there, also on a home page saved before, where the editor
 * stored the text in the page. What the owner typed there is replaced by the Restaurant tab.
 */
add_filter(
	'render_block_core/group',
	function ( $content, $block ) {
		$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
		if ( false === strpos( $class, 'mdt-visit-fallback' ) || ! mdt_has_restaurant() ) {
			return $content;
		}
		return do_blocks( mdt_visit_markup() );
	},
	10,
	2
);

/** In the editor only: what an empty block would show, and what it waits for. */
function mdt_empty_note( $name ) {
	if ( ! ( defined( 'REST_REQUEST' ) && REST_REQUEST && current_user_can( 'edit_posts' ) ) ) {
		return '';
	}
	$texts = array(
		'giftcard' => __( 'Gift card teaser: shows here while the MenuDash Gift Cards add-on takes orders and a page has the gift card form, or without the add-on while a gift card page is published.', 'menudash-theme' ),
		'delivery' => __( 'Delivery: shows here once an order link is entered under MenuDash → Restaurant (MenuDash Restaurant add-on).', 'menudash-theme' ),
		'map'      => __( 'Map: shows here once the address is entered under MenuDash → Restaurant (MenuDash Restaurant add-on).', 'menudash-theme' ),
		'social'   => __( 'Instagram / Facebook: show here once their links are entered under MenuDash → Restaurant (MenuDash Restaurant add-on).', 'menudash-theme' ),
		'provider' => __( 'Provider: the company name, address, phone and e-mail show here once they are entered under MenuDash → Restaurant (MenuDash Restaurant add-on).', 'menudash-theme' ),
	);
	if ( ! isset( $texts[ $name ] ) ) {
		return '';
	}
	$note = '<p class="mdt-addon-note">' . esc_html( $texts[ $name ] ) . ' <span>' . esc_html__( 'Only you see this note, in the editor.', 'menudash-theme' ) . '</span></p>';
	// The gift card teaser is a section of its own: its note gets the same band, so it
	// doesn't look like part of the section next to it.
	if ( 'giftcard' === $name ) {
		$note = '<div class="wp-block-group alignfull mdt-gc-home has-paper-background-color has-background is-layout-constrained"><div class="alignwide">'
			. '<p class="mdt-eyebrow" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--wp--preset--color--primary)">' . esc_html__( 'Gift card', 'menudash-theme' ) . '</p>'
			. $note . '</div></div>';
	}
	return $note;
}

/** The page with the menu ([menudash]), or /menu/. */
function mdt_menu_url() {
	$pages = function_exists( 'mdash_menu_pages_public' ) ? mdash_menu_pages_public() : array();
	if ( ! $pages ) {
		return home_url( '/menu/' );
	}
	// With Polylang: the menu page in the language of the page being shown.
	$id = function_exists( 'pll_get_post' ) && pll_get_post( $pages[0] ) && 'publish' === get_post_status( pll_get_post( $pages[0] ) ) ? pll_get_post( $pages[0] ) : $pages[0];
	return get_permalink( $id );
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

/**
 * The gift card page without the MenuDash Gift Cards add-on: sold at the restaurant only, so
 * the way there and the menu. With MenuDash Statistics the page counts as opened (gc:page) and
 * each button when tapped (gc:route, gc:menu). Empty while the add-on is installed: then the
 * page shows its order form.
 */
function mdt_block_giftcard_visit() {
	if ( function_exists( 'mdash_gc_open' ) ) {
		return '';
	}
	$route = function_exists( 'mdash_maps_url' ) ? mdash_maps_url( 'route' ) : '';
	$b     = ( '' !== $route ? mdt_button( $route, __( 'Get directions', 'menudash-theme' ), false, true, 'gc:route' ) : '' )
		. mdt_button( mdt_menu_url(), __( 'View the menu', 'menudash-theme' ), '' !== $route, false, 'gc:menu' );
	return mdt_render(
		'<!-- wp:paragraph --><p>' . esc_html__( 'Our gift cards are available only at the restaurant. Come by and ask us; we are happy to help.', 'menudash-theme' ) . '</p><!-- /wp:paragraph -->'
		. '<!-- wp:buttons --><div class="wp-block-buttons">' . $b . '</div><!-- /wp:buttons -->'
	) . '<span data-mdash-stat="gc:page" hidden></span>';
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
	$contact = array_filter( array( '' !== $tel ? sprintf( esc_html__( 'Reservations by phone: %s', 'menudash-theme' ), $tel ) : '', '' !== $mail ? sprintf( /* translators: %s: e-mail link */ esc_html__( 'E-mail: %s', 'menudash-theme' ), $mail ) : '' ) );
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

/**
 * WordPress's Social Icons have no Tripadvisor icon, so a Tripadvisor link is saved as the
 * plain link icon ("chain"); here it gets the owl instead. Also for links added by hand.
 * Icon: Simple Icons (simpleicons.org), CC0.
 */
function mdt_tripadvisor_icon( $content, $block ) {
	$url = isset( $block['attrs']['url'] ) ? (string) $block['attrs']['url'] : '';
	if ( 'chain' !== ( isset( $block['attrs']['service'] ) ? $block['attrs']['service'] : '' ) || ! preg_match( '#^(https?://)?([a-z0-9-]+\.)*tripadvisor\.[a-z.]+(/|$)#i', $url ) ) {
		return $content;
	}
	$svg = '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M12.006 4.295c-2.67 0-5.338.784-7.645 2.353H0l1.963 2.135a5.997 5.997 0 0 0 4.04 10.43 5.976 5.976 0 0 0 4.075-1.6L12 19.705l1.922-2.09a5.972 5.972 0 0 0 4.072 1.598 6 6 0 0 0 6-5.998 5.982 5.982 0 0 0-1.957-4.432L24 6.648h-4.35a13.573 13.573 0 0 0-7.644-2.353zM12 6.255c1.531 0 3.063.303 4.504.903C13.943 8.138 12 10.43 12 13.1c0-2.671-1.942-4.962-4.504-5.942A11.72 11.72 0 0 1 12 6.256zM6.002 9.157a4.059 4.059 0 1 1 0 8.118 4.059 4.059 0 0 1 0-8.118zm11.992.002a4.057 4.057 0 1 1 .003 8.115 4.057 4.057 0 0 1-.003-8.115zm-11.992 1.93a2.128 2.128 0 0 0 0 4.256 2.128 2.128 0 0 0 0-4.256zm11.992 0a2.128 2.128 0 0 0 0 4.256 2.128 2.128 0 0 0 0-4.256z"/></svg>';
	$content = preg_replace( '#<svg\b.*?</svg>#s', $svg, $content, 1 );
	return str_replace( 'wp-social-link-chain', 'wp-social-link-chain wp-social-link-tripadvisor', $content );
}
add_filter( 'render_block_core/social-link', 'mdt_tripadvisor_icon', 10, 2 );

function mdt_block_social( $atts ) {
	if ( 'follow' === $atts['variant'] ) {
		if ( '' === mdt_d( 'instagram' ) ) {
			return '';
		}
		$handle = ltrim( basename( untrailingslashit( mdt_d( 'instagram' ) ) ), '@' );
		/* translators: %s: the Instagram account name */
		$label = sprintf( __( 'Follow us on Instagram @%s', 'menudash-theme' ), $handle );
		return mdt_render( '<!-- wp:social-links {"iconColor":"white","iconColorValue":"#FFFFFF","iconBackgroundColor":"primary","showLabels":true,"className":"mdt-follow","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} --><ul class="wp-block-social-links has-visible-labels has-icon-color has-icon-background-color mdt-follow" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:social-link ' . serialize_block_attributes( array( 'url' => esc_url_raw( mdash_social_url( 'instagram', mdt_d( 'instagram' ) ) ), 'service' => 'instagram', 'label' => wp_strip_all_tags( $label ) ) ) . ' /--></ul><!-- /wp:social-links -->' );
	}
	$links = '';
	// Every link filled in under MenuDash → Restaurant (older add-ons: Instagram and Facebook).
	$social = array();
	if ( function_exists( 'mdash_social_links' ) ) {
		$social = mdash_social_links();
	} else {
		foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram' ) as $service => $label ) {
			if ( '' !== mdt_d( $service ) ) {
				$social[ $service ] = array( $label, mdash_social_url( $service, mdt_d( $service ) ) );
			}
		}
	}
	foreach ( $social as $service => $link ) {
		// WordPress has no Tripadvisor icon: saved as the link icon, drawn as the owl (mdt_tripadvisor_icon).
		$icon   = 'tripadvisor' === $service ? 'chain' : $service;
		$links .= '<!-- wp:social-link ' . serialize_block_attributes( array( 'url' => esc_url_raw( $link[1] ), 'service' => $icon, 'label' => $link[0] ) ) . ' /-->';
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
	// The delivery times can differ from the opening hours in the footer, so they say what they are.
	$times   = '<!-- wp:menudash/open {"for":"order"} /--><!-- wp:paragraph {"className":"mdt-delivery-label"} --><p class="mdt-delivery-label"><strong>' . esc_html__( 'Delivery hours', 'menudash-theme' ) . '</strong></p><!-- /wp:paragraph --><!-- wp:menudash/hours {"for":"delivery","className":"mdt-table mdt-delivery-hours"} /-->';
	/* translators: %s: the delivery service, e.g. "Uber Eats" */
	$label   = '' !== $service ? sprintf( __( 'Order with %s', 'menudash-theme' ), $service ) : __( 'Order online', 'menudash-theme' );
	$button  = '<!-- wp:buttons --><div class="wp-block-buttons">' . mdt_button( $order, $label, false, true ) . '</div><!-- /wp:buttons -->';
	// #delivery sits on the eyebrow: on wide screens the wrapper is display: contents (style.css).
	return '<div class="mdt-delivery">' . mdt_render( mdt_eyebrow( __( 'Delivery', 'menudash-theme' ), 'delivery' ) . $heading . $text . $times . $button ) . '</div>';
}

function mdt_block_map() {
	$src = mdash_maps_url( 'embed' );
	if ( '' === $src ) {
		return '';
	}
	// The iframe becomes the "Show map" placeholder in the render_block filter (functions.php).
	/* translators: %s: the restaurant's name and address */
	$title = sprintf( __( 'Map: %s', 'menudash-theme' ), trim( mdt_d( 'name' ) . ', ' . mdt_d( 'address' ), ', ' ) );
	return '<div class="mdt-map alignwide mdt-map-wide"><iframe src="' . esc_url( $src ) . '" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" title="' . esc_attr( $title ) . '" allowfullscreen></iframe></div>';
}

/** The published legal notice page, found by its address (German: impressum), or null. */
function mdt_legal_page() {
	/* translators: the legal notice page's web address, lower case without spaces (German: impressum) */
	foreach ( array_unique( array( sanitize_title( _x( 'legal-notice', 'page address', 'menudash-theme' ) ), 'legal-notice', 'impressum' ) ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === $page->post_status ) {
			return $page;
		}
	}
	return null;
}

/**
 * The published page that shows the meat and fish origin (MenuDash's [menudash_origin], e.g. a
 * "Fleisch-Deklaration" page), or null: linked in the footer's small print. Looked up once a
 * day, and again when a page is saved, published, unpublished or put in the bin.
 */
function mdt_origin_page() {
	$id = get_transient( 'mdt_origin_page' );
	if ( false === $id ) {
		$id = 0;
		foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 10, 's' => 'menudash_origin' ) ) as $p ) {
			if ( has_shortcode( $p->post_content, 'menudash_origin' ) ) {
				$id = $p->ID;
				break;
			}
		}
		set_transient( 'mdt_origin_page', $id, DAY_IN_SECONDS );
	}
	$page = $id ? get_post( (int) $id ) : null;
	return $page && 'publish' === $page->post_status ? $page : null;
}

add_action(
	'transition_post_status', // every save, and a page published, unpublished or put in the bin
	function ( $new_status, $old_status, $post ) {
		if ( 'page' === $post->post_type ) {
			delete_transient( 'mdt_origin_page' );
		}
	},
	10,
	3
);

function mdt_block_legal() {
	$id    = (int) get_option( 'wp_page_for_privacy_policy' );
	$page  = $id ? get_post( $id ) : null; // get_post( 0 ) would be the current page.
	$link  = $page && 'publish' === $page->post_status ? '<a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html__( 'Privacy', 'menudash-theme' ) . '</a>' : '';
	$legal = mdt_legal_page();
	$legal = $legal ? '<a href="' . esc_url( get_permalink( $legal ) ) . '">' . esc_html__( 'Legal notice', 'menudash-theme' ) . '</a>' : '';
	$origin = mdt_origin_page();
	$origin = $origin ? '<a href="' . esc_url( get_permalink( $origin ) ) . '">' . esc_html( get_the_title( $origin ) ) . '</a>' : '';
	$owner  = '' !== mdt_d( 'company' ) ? mdt_d( 'company' ) : ( '' !== mdt_d( 'name' ) ? mdt_d( 'name' ) : get_bloginfo( 'name' ) );
	$parts  = array_filter( array( '' !== $owner ? '© ' . esc_html( $owner ) : '', $legal, $link, $origin ) );
	return $parts ? '<p class="alignwide" style="font-size:0.8125rem">' . implode( ' · ', $parts ) . '</p>' : '';
}

/** Who runs the website, for the legal notice: company and restaurant, address, phone, e-mail. */
function mdt_block_provider() {
	$who   = implode( ', ', array_unique( array_filter( array( mdt_d( 'company' ), mdt_d( 'name' ) ) ) ) );
	$addr  = implode( ', ', array_filter( array( mdt_d( 'street' ), trim( mdt_d( 'postcode' ) . ' ' . mdt_d( 'city' ) ), mdt_country() ) ) );
	$phone = mdt_d( 'phone' );
	$mail  = mdt_d( 'email' );
	$lines = array_filter(
		array(
			esc_html( $who ),
			esc_html( $addr ),
			/* translators: %s: phone number, as a link */
			'' !== $phone ? sprintf( esc_html__( 'Phone: %s', 'menudash-theme' ), '<a href="' . esc_url( 'tel:' . mdash_tel( $phone ) ) . '">' . esc_html( $phone ) . '</a>' ) : '',
			/* translators: %s: e-mail link */
			'' !== $mail ? sprintf( esc_html__( 'E-mail: %s', 'menudash-theme' ), '<a href="' . esc_url( 'mailto:' . $mail ) . '">' . esc_html( $mail ) . '</a>' ) : '',
		)
	);
	return $lines ? '<p>' . implode( '<br>', $lines ) . '</p>' : '';
}

/**
 * The dishes marked Recommended in the MenuDash menu, up to 12, with their photos
 * (dishes without a photo come last). Each links to the menu.
 */
function mdt_block_picks() {
	// MenuDash 2.3 has its own Recommended dishes block; this one then shows the same, and
	// stays for pages saved with it (their "whole menu" button is a block of its own).
	if ( function_exists( 'mdash_picks_render' ) ) {
		return mdash_picks_render( array( 'button' => false ) );
	}
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
		if ( ! $photo ) { // No photo yet: a plate with the smile drawing, so the row stays even.
			$html .= '<figure class="mdt-dish mdt-dish-none" aria-hidden="true"><span></span></figure>';
		} else {
			$html .= '<figure class="mdt-dish' . ( $photo['alpha'] ? '' : ' mdt-dish-round' ) . '"><img src="' . esc_url( mdash_photo_url( $photo, 400 ) ) . '" alt="' . esc_attr( $name ) . '" width="400" height="400" loading="lazy"></figure>';
		}
		$html .= ( '' !== (string) $d['no'] ? '<span class="mdt-no">' . esc_html( sprintf( /* translators: %s: dish number */ __( 'No. %s', 'menudash-theme' ), $d['no'] ) ) . '</span>' : '' )
			. '<strong>' . esc_html( $name ) . '</strong>' . ( '' !== $second ? '<span class="mdt-zh">' . esc_html( $second ) . '</span>' : '' ) . '</a>';
	}
	return $html . '</div>';
}

/**
 * The published page about gift cards when they are sold at the restaurant only (no MenuDash
 * Gift Cards add-on): the one holding the gift card pattern, its "at the restaurant" block or
 * the form's shortcode. 0 when there is none.
 */
function mdt_giftcard_page() {
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 50, 'orderby' => 'ID', 'order' => 'ASC' ) ) as $p ) {
		foreach ( array( '[menudash_giftcard', 'wp:menudash-theme/giftcard-visit', '"slug":"menudash-theme/giftcard"' ) as $mark ) {
			if ( false !== strpos( $p->post_content, $mark ) ) {
				return $p->ID;
			}
		}
	}
	return 0;
}

/**
 * The gift card teaser: while MenuDash takes gift card orders and a page has the form. Without
 * the Gift Cards add-on, when a gift card page is published: sold at the restaurant, with a
 * button to that page (counted by MenuDash Statistics as gc:home).
 */
function mdt_block_giftcard() {
	if ( ! function_exists( 'mdash_gc_open' ) ) {
		$id = mdt_giftcard_page();
		if ( ! $id ) {
			return '';
		}
		$col1 = mdt_eyebrow( __( 'Gift card', 'menudash-theme' ) )
			. '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'Give a meal', 'menudash-theme' ) . '</h2><!-- /wp:heading -->'
			. '<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html__( 'A gift card for a meal with us: available at the restaurant. Come by and ask us.', 'menudash-theme' ) . '</p><!-- /wp:paragraph -->'
			. '<!-- wp:buttons --><div class="wp-block-buttons">' . mdt_button( get_permalink( $id ), __( 'More about the gift card', 'menudash-theme' ), false, false, 'gc:home' ) . '</div><!-- /wp:buttons -->';
		return mdt_render( '<!-- wp:group {"align":"full","className":"mdt-gc-home","backgroundColor":"paper","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull mdt-gc-home has-paper-background-color has-background"><!-- wp:group {"align":"wide","layout":{"type":"default"}} --><div class="wp-block-group alignwide">' . $col1 . '</div><!-- /wp:group --></div><!-- /wp:group -->' );
	}
	if ( ! mdash_gc_open() ) {
		return '';
	}
	// The page that really has the form (a page that only mentions the word doesn't count).
	$page = array();
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 20, 's' => 'menudash_giftcard' ) ) as $p ) {
		if ( has_shortcode( $p->post_content, 'menudash_giftcard' ) ) {
			$page = array( $p->ID );
			break;
		}
	}
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

/*
 * "Show only when …" on any block (Advanced, in the block's sidebar). Where a part depends on
 * MenuDash Restaurant or on a logo, the theme's patterns hold both versions, each marked, and
 * every page view shows the one that fits. The mark is stored in the block, so it survives
 * saving a page or template in the editor; a PHP "if" in a pattern would be decided only once,
 * when the editor first opened it. Several, separated by commas, must all be true.
 */
function mdt_show_conditions() {
	return array(
		'restaurant'    => __( 'MenuDash Restaurant is active', 'menudash-theme' ),
		'no-restaurant' => __( 'MenuDash Restaurant is not installed', 'menudash-theme' ),
		'logo'          => __( 'A logo is set', 'menudash-theme' ),
		'no-logo'       => __( 'No logo is set', 'menudash-theme' ),
		'giftcards'     => __( 'MenuDash Gift Cards is active', 'menudash-theme' ),
		'no-giftcards'  => __( 'MenuDash Gift Cards is not installed', 'menudash-theme' ),
	);
}

/** Whether a block marked "Show only when $show" shows now. */
function mdt_show_ok( $show ) {
	foreach ( array_filter( array_map( 'trim', explode( ',', (string) $show ) ) ) as $when ) {
		$ok = array(
			'restaurant'    => mdt_has_restaurant(),
			'no-restaurant' => ! mdt_has_restaurant(),
			'logo'          => has_custom_logo(),
			'no-logo'       => ! has_custom_logo(),
			'giftcards'     => function_exists( 'mdash_gc_open' ),
			'no-giftcards'  => ! function_exists( 'mdash_gc_open' ),
		);
		if ( isset( $ok[ $when ] ) && ! $ok[ $when ] ) {
			return false;
		}
	}
	return true;
}

/**
 * Whether this request is the editor's live preview of a block (the block-renderer REST
 * route, only for people who can edit). Other REST requests, such as a page's rendered
 * content, get what guests see.
 */
function mdt_is_block_preview() {
	return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $GLOBALS['wp']->query_vars['rest_route'] )
		&& 0 === strpos( (string) $GLOBALS['wp']->query_vars['rest_route'], '/wp/v2/block-renderer/' )
		&& current_user_can( 'edit_posts' );
}

// Known to every block, so the editor keeps it and live previews accept it.
add_filter(
	'register_block_type_args',
	function ( $args ) {
		$args['attributes'] = ( isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array() ) + array( 'mdtShow' => array( 'type' => 'string', 'default' => '' ) );
		return $args;
	}
);

add_filter(
	'render_block',
	function ( $content, $block ) {
		$show = isset( $block['attrs']['mdtShow'] ) && is_string( $block['attrs']['mdtShow'] ) ? $block['attrs']['mdtShow'] : '';
		// The editor's live previews show both versions; the editor marks which is which.
		if ( '' === $show || mdt_is_block_preview() ) {
			return $content;
		}
		return mdt_show_ok( $show ) ? $content : '';
	},
	5,
	2
);
