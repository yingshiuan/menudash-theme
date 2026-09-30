<?php
/**
 * MenuDash Theme. Most of it is in theme.json, the templates and the patterns; this file
 * loads the styles and translations, adds the theme's MenuDash blocks (inc/blocks.php), the
 * phone action bar, the details for search engines and chat apps, and the map on request.
 */

defined( 'ABSPATH' ) || exit;

require get_theme_file_path( 'inc/blocks.php' ); // The blocks that show what is entered in MenuDash.

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'menudash-theme', get_theme_file_path( 'languages' ) );
		add_editor_style( 'style.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'menudash-theme', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
		wp_enqueue_script( 'menudash-theme', get_theme_file_uri( 'assets/js/theme.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		// MenuDash's boxes (menu, specials, gift cards) in the theme's colours, unless the
		// owner chose colours under MenuDash → Design; then those win.
		if ( ! function_exists( 'mdash_colors_custom' ) || ! mdash_colors_custom() ) {
			wp_add_inline_style( 'menudash-theme', 'body .menudash,body .menudash-picks{--mdash-accent:var(--wp--preset--color--primary);--mdash-bg:var(--wp--preset--color--cream);--mdash-ink:var(--wp--preset--color--ink);--mdash-muted:var(--wp--preset--color--muted);--mdash-on-accent:var(--wp--preset--color--white)}' );
		}
	}
);

add_action(
	'init',
	function () {
		register_block_pattern_category( 'menudash-theme', array( 'label' => __( 'Restaurant (MenuDash)', 'menudash-theme' ) ) );
	}
);

/**
 * Whether the MenuDash Restaurant add-on is there (address, hours, "open now"). Without it
 * the theme leaves out what only it can fill: the footer shows the basics, the welcome has
 * no badge or reserve buttons, and the home page no "Visit us" section. Installing the
 * add-on brings them in by itself (patterns are read on every page view).
 */
function mdt_has_restaurant() {
	return function_exists( 'mdash_detail' );
}

/** A restaurant detail from MenuDash → Restaurant ("phone", "address", "city" …), or ''. */
function mdt_d( $key ) {
	return function_exists( 'mdash_detail' ) ? mdash_detail( $key ) : '';
}

/** The country's name in the site's language, from its two-letter code ("CH"). */
function mdt_country() {
	$names = array(
		'CH' => __( 'Switzerland', 'menudash-theme' ),
		'DE' => __( 'Germany', 'menudash-theme' ),
		'AT' => __( 'Austria', 'menudash-theme' ),
		'LI' => __( 'Liechtenstein', 'menudash-theme' ),
		'FR' => __( 'France', 'menudash-theme' ),
		'IT' => __( 'Italy', 'menudash-theme' ),
		'GB' => __( 'United Kingdom', 'menudash-theme' ),
		'US' => __( 'United States', 'menudash-theme' ),
	);
	$code  = mdt_d( 'country' );
	return isset( $names[ $code ] ) ? $names[ $code ] : $code;
}

/**
 * A page with the menu ([menudash]) gets the wide template by itself, so the menu has room
 * for its two columns even when nobody picked a template for the page.
 */
add_filter(
	'template_include',
	function ( $template ) {
		if ( is_page() && has_shortcode( (string) get_post_field( 'post_content', get_queried_object_id() ), 'menudash' ) && ! get_page_template_slug() ) {
			$wide = locate_block_template( get_theme_file_path( 'templates/page-wide.html' ), 'page-wide', array( 'page-wide' ) );
			if ( $wide ) {
				return $wide;
			}
		}
		return $template;
	}
);

/**
 * On activation, a "Home" page built from the theme's sections becomes the front page, so
 * the owner finds and edits it under Pages like any other. A site that already has a static
 * front page keeps it; the sections are then in the block inserter.
 */
function mdt_setup_home() {
	if ( 'page' === get_option( 'show_on_front' ) && get_post( (int) get_option( 'page_on_front' ) ) ) {
		return;
	}
	$sections = array( 'hero', 'picks', 'testimonial', 'about', 'giftcard-home', 'visit' );
	$content  = implode( "\n\n", array_map( function ( $s ) { return '<!-- wp:pattern {"slug":"menudash-theme/' . $s . '"} /-->'; }, $sections ) );
	$id       = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => __( 'Home', 'menudash-theme' ),
			'post_content' => $content,
			'meta_input'   => array( '_wp_page_template' => 'page-home' ),
		)
	);
	if ( $id && ! is_wp_error( $id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $id );
	}
}
add_action( 'after_switch_theme', 'mdt_setup_home' );

/**
 * On activation, a "Menu" page (German: "Speisekarte" at /speisekarte/) holding the menu, unless a
 * page with [menudash] exists already, in any status, so switching back and forth never
 * makes a second one. It gets the wide template by itself (see above). Needs MenuDash.
 */
function mdt_setup_menu() {
	/* translators: the menu page's web address, lower case without spaces (German: speisekarte) */
	$slug = sanitize_title( _x( 'menu', 'page address', 'menudash-theme' ) );
	if ( ! shortcode_exists( 'menudash' ) ) {
		return;
	}
	$pages = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'future', 'draft', 'pending', 'private' ), 'numberposts' => -1 ) );
	foreach ( $pages as $page ) {
		if ( has_shortcode( $page->post_content, 'menudash' ) ) {
			return;
		}
	}
	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => __( 'Menu', 'menudash-theme' ),
			'post_name'    => get_page_by_path( $slug ) ? '' : $slug,
			'post_content' => "<!-- wp:shortcode -->\n[menudash]\n<!-- /wp:shortcode -->",
		)
	);
}
add_action( 'after_switch_theme', 'mdt_setup_menu' );

/**
 * Shortcode blocks inside theme patterns (the header's holiday notice, the footer's hours)
 * are rendered through the Pattern block, after WordPress has run the shortcodes of the
 * template, so they would stay as plain "[menudash_closed]" text. This runs such a block,
 * but only while it still holds nothing but its unprocessed shortcode.
 * Security: the output's brackets are encoded, so text inside it (a dish called
 * "… [menudash_hours]" in an uploaded CSV) is never run as a shortcode by a later pass.
 */
add_filter(
	'render_block_core/shortcode',
	function ( $content, $block ) {
		$raw = trim( isset( $block['innerHTML'] ) ? $block['innerHTML'] : '' );
		if ( '' === $raw || trim( wp_strip_all_tags( $content ) ) !== $raw || ! preg_match( '/^\[[^\[\]]+\]$/', $raw ) ) {
			return $content;
		}
		return str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), do_shortcode( $raw ) );
	},
	10,
	2
);

/**
 * On phones, a bar fixed to the bottom of every page: call, directions, menu. Hidden on
 * larger screens (style.css). On the menu page, "Menu" jumps to the menu itself.
 */
add_action(
	'wp_footer',
	function () {
		$maps = function_exists( 'mdash_maps_url' ) ? mdash_maps_url( 'route' ) : '';
		$tel  = function_exists( 'mdash_tel' ) ? mdash_tel( mdt_d( 'phone' ) ) : '';
		$menu = mdt_menu_url();
		$icon = array(
			'call'  => '<path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/>',
			'route' => '<path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>',
			'menu'  => '<path d="M4 3h11a3 3 0 0 1 3 3v15l-4-2-4 2-4-2-2 1V3zm3 5v2h8V8H7zm0 4v2h6v-2H7z"/>',
		);
		$items = array_filter(
			array(
				'' !== $tel ? array( 'tel:' . $tel, __( 'Call', 'menudash-theme' ), 'call' ) : null,
				'' !== $maps ? array( $maps, __( 'Directions', 'menudash-theme' ), 'route' ) : null,
				array( untrailingslashit( get_permalink() ) === untrailingslashit( $menu ) ? '#the-menu' : $menu, __( 'Menu', 'menudash-theme' ), 'menu' ),
			)
		);
		echo '<nav class="mdt-actionbar" aria-label="' . esc_attr__( 'Quick links', 'menudash-theme' ) . '">';
		foreach ( $items as $i ) {
			$ext = 0 === strpos( $i[0], 'https://' ) ? ' target="_blank" rel="noopener"' : '';
			printf(
				'<a href="%1$s"%2$s><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">%3$s</svg><span>%4$s</span></a>',
				esc_url( $i[0] ),
				$ext, // phpcs:ignore -- fixed string
				$icon[ $i[2] ], // phpcs:ignore -- fixed SVG path
				esc_html( $i[1] )
			);
		}
		echo '</nav>';
	}
);

/**
 * What search engines and chat apps read: a description (the site's tagline, or a page's
 * excerpt) and the site icon as the share picture. Skipped when an SEO plugin does this.
 * The restaurant's address and hours for Google come from MenuDash itself.
 */
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
			return;
		}
		$text = is_singular() && has_excerpt() ? get_the_excerpt() : get_bloginfo( 'description' );
		if ( '' !== trim( $text ) ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $text ) ) );
		}
		$og = array(
			'og:type'        => 'website',
			'og:site_name'   => get_bloginfo( 'name' ),
			'og:title'       => wp_get_document_title(),
			'og:description' => wp_strip_all_tags( $text ),
			'og:url'         => is_singular() ? get_permalink() : home_url( '/' ),
			'og:image'       => has_site_icon() ? get_site_icon_url( 512 ) : '',
		);
		foreach ( array_filter( $og ) as $k => $v ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $k ), esc_attr( $v ) );
		}
	}
);

/**
 * Google Maps only after a click: every Google Maps iframe on the site is sent without its
 * address, with a "Show map" button in its place, so Google learns nothing about a visit
 * until the guest asks for the map. assets/js/theme.js loads it on the click and remembers
 * the click in the browser; without JavaScript the "Open in Google Maps" link still works.
 */
add_filter(
	'render_block',
	function ( $content ) {
		if ( false === strpos( $content, '<iframe' ) || false === strpos( $content, 'google.' ) ) {
			return $content;
		}
		return preg_replace_callback(
			// "\s" before src, so an iframe already turned into data-src (render_block runs again
			// for every block around it) is left alone.
			'#<iframe\b([^>]*?\s)src="(https://(?:www\.|maps\.)?google\.[a-z.]+/maps[^"]*)"([^>]*)></iframe>#i',
			function ( $m ) {
				$open = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( trim( mdt_d( 'name' ) . ', ' . mdt_d( 'address' ), ', ' ) );
				return '<div class="mdt-map-slot"><iframe' . $m[1] . 'data-src="' . $m[2] . '"' . $m[3] . '></iframe>'
					. '<div class="mdt-map-off">'
					. '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>'
					. '<button type="button" class="mdt-map-load" hidden>' . esc_html__( 'Show map', 'menudash-theme' ) . '</button>'
					. '<p>' . esc_html__( 'The map comes from Google Maps; showing it sends your IP address to Google.', 'menudash-theme' ) . ' <a href="' . esc_url( $open ) . '" target="_blank" rel="noopener">' . esc_html__( 'Open in Google Maps', 'menudash-theme' ) . '</a></p>'
					. '</div></div>';
			},
			$content
		);
	}
);

/*
 * Two or more languages with Polylang (the free version is enough). The header's navigation
 * is one for all languages (Polylang translates Site Editor navigation only in its Pro
 * version), so the theme translates it while it is drawn: a link to a page goes to that
 * page's translation, under its translated title, and "DE | EN" is added at the end, unless
 * Polylang's own language switcher block is already in the navigation. Without Polylang
 * none of this runs.
 */
add_filter(
	'render_block_core/navigation-link',
	function ( $content, $block ) {
		if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_current_language' ) || ! pll_current_language() ) {
			return $content;
		}
		$a     = isset( $block['attrs'] ) ? $block['attrs'] : array();
		$url   = isset( $a['url'] ) ? (string) $a['url'] : '';
		$label = isset( $a['label'] ) ? wp_strip_all_tags( (string) $a['label'] ) : '';
		$id    = isset( $a['id'] ) && 'post-type' === ( isset( $a['kind'] ) ? $a['kind'] : '' ) ? (int) $a['id'] : 0;
		// A custom link to the home page counts as a link to the front page. (The saved address,
		// not home_url(), which Polylang changes to /en/ on English pages.)
		if ( ! $id && '' !== $url && untrailingslashit( $url ) === untrailingslashit( (string) get_option( 'home' ) ) && 'page' === get_option( 'show_on_front' ) ) {
			$id = (int) get_option( 'page_on_front' );
		}
		if ( ! $id ) {
			return $content;
		}
		$to    = pll_get_post( $id ) ? (int) pll_get_post( $id ) : $id;
		$front = 'page' === get_option( 'show_on_front' ) && ( (int) get_option( 'page_on_front' ) === $to || (int) get_option( 'page_on_front' ) === $id );
		$new   = $front && function_exists( 'pll_home_url' ) ? pll_home_url() : get_permalink( $to );
		$html  = $content;
		if ( '' !== $url && untrailingslashit( $url ) !== untrailingslashit( $new ) ) {
			$html = str_replace( 'href="' . esc_url( $url ) . '"', 'href="' . esc_url( $new ) . '"', $html );
		}
		// The label is swapped only when it is one of the page's titles (a label of your own stays).
		$titles = array();
		foreach ( function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $id ) : array( $id ) as $tid ) {
			$titles[] = get_post_field( 'post_title', $tid );
		}
		$title = get_post_field( 'post_title', $to );
		if ( '' !== $label && $label !== $title && in_array( $label, $titles, true ) ) {
			$html = str_replace( '>' . esc_html( $label ) . '<', '>' . esc_html( $title ) . '<', $html );
		}
		if ( get_queried_object_id() === $to && false === strpos( $html, 'current-menu-item' ) ) {
			$html = preg_replace( '/class="wp-block-navigation-item /', 'class="wp-block-navigation-item current-menu-item ', $html, 1 );
			$html = preg_replace( '/<a class="wp-block-navigation-item__content"/', '<a class="wp-block-navigation-item__content" aria-current="page"', $html, 1 );
		}
		return $html;
	},
	10,
	2
);

add_filter(
	'render_block_core/navigation',
	function ( $content ) {
		if ( ! function_exists( 'pll_the_languages' ) || false !== strpos( $content, 'polylang' ) || false !== strpos( $content, 'mdt-lang' ) ) {
			return $content;
		}
		$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0 ) );
		if ( ! is_array( $langs ) || count( $langs ) < 2 ) {
			return $content;
		}
		$items = '';
		foreach ( $langs as $l ) {
			$items .= sprintf(
				'<li class="wp-block-navigation-item mdt-lang%1$s"><a class="wp-block-navigation-item__content" href="%2$s" lang="%3$s" hreflang="%3$s"%4$s title="%5$s">%6$s</a></li>',
				$l['current_lang'] ? ' is-current' : '',
				esc_url( $l['url'] ),
				esc_attr( $l['locale'] ),
				$l['current_lang'] ? ' aria-current="true"' : '',
				esc_attr( $l['name'] ),
				esc_html( strtoupper( $l['slug'] ) )
			);
		}
		// At the end of the list of links (also in the phone menu, which is the same list).
		$pos = strrpos( $content, '</ul>' );
		return false === $pos ? $content : substr_replace( $content, $items . '</ul>', $pos, 5 );
	}
);
