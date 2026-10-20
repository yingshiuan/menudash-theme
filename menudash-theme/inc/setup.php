<?php
/**
 * Getting MenuDash: while the free MenuDash plugin isn't active, the Dashboard, Themes and
 * Plugins screens say what it brings, with one button that installs it (from its latest
 * GitHub release) and activates it. When MenuDash is activated, by this button or under
 * Plugins, the theme makes the menu page it would have made on activation. The paid add-ons
 * can't be downloaded from here (they are private), only activated once uploaded.
 *
 * Appearance → Plugins & Layout shows all of this in one place: MenuDash and its add-ons,
 * and buttons that bring back the theme's default layout (home page, menu page, header,
 * footer and templates).
 */

defined( 'ABSPATH' ) || exit;

/** The latest MenuDash release; the zip holds the folder menudash/. */
const MDT_MENUDASH_ZIP  = 'https://github.com/yingshiuan/menudash/releases/latest/download/menudash.zip';
const MDT_MENUDASH_PAGE = 'https://github.com/yingshiuan/menudash';

/** The plugin file of an installed plugin, found by its name ("MenuDash" → "menudash/menudash.php"), else ''. */
function mdt_plugin_file( $name = 'MenuDash' ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	foreach ( get_plugins() as $file => $data ) {
		if ( $name === $data['Name'] ) {
			return $file;
		}
	}
	return '';
}

/** Where the page Appearance → Plugins & Layout is. */
function mdt_setup_url() {
	return admin_url( 'themes.php?page=menudash-theme-setup' );
}

/** A back address on the screen the button was pressed on, with the result added. */
function mdt_menudash_back( $args ) {
	$back = wp_get_referer();
	$back = $back ? remove_query_arg( array( 'mdt-menudash', 'mdt-done' ), $back ) : admin_url( 'themes.php' );
	wp_safe_redirect( add_query_arg( $args, $back ) );
	exit;
}

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'plugins', 'appearance_page_menudash-theme-setup' ), true ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification -- only picks which message to show
		$done = isset( $_GET['mdt-menudash'] ) ? sanitize_key( $_GET['mdt-menudash'] ) : '';
		if ( 'ready' === $done && defined( 'MENUDASH_FILE' ) ) {
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'MenuDash is installed and active. Your menu page is ready.', 'menudash-theme' ),
				esc_url( admin_url( 'admin.php?page=menudash' ) ),
				esc_html__( 'Upload your menu', 'menudash-theme' )
			);
			return;
		}
		// The reason is kept on the server for this user (never read from the address), so a
		// made-up link can't show an admin a message of its own.
		$why = 'error' === $done ? get_transient( 'mdt_install_error_' . get_current_user_id() ) : false;
		if ( false !== $why ) {
			delete_transient( 'mdt_install_error_' . get_current_user_id() );
			printf(
				'<div class="notice notice-error"><p>%s %s</p><p>%s</p></div>',
				esc_html__( 'MenuDash couldn\'t be installed.', 'menudash-theme' ),
				esc_html( $why ),
				wp_kses(
					sprintf(
						/* translators: %s: link to MenuDash on GitHub */
						__( 'Download menudash.zip from %s and upload it under Plugins → Add New → Upload Plugin.', 'menudash-theme' ),
						'<a href="' . esc_url( MDT_MENUDASH_PAGE . '/releases/latest' ) . '" target="_blank" rel="noopener">GitHub</a>'
					),
					array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) )
				)
			);
			return;
		}
		// On Plugins & Layout the page itself says it.
		if ( defined( 'MENUDASH_FILE' ) || 'appearance_page_menudash-theme-setup' === $screen->id || ! current_user_can( 'activate_plugins' ) || get_user_meta( get_current_user_id(), 'mdt_menudash_later', true ) ) {
			return;
		}
		$file = mdt_plugin_file();
		if ( $file ) {
			$label = __( 'Activate MenuDash', 'menudash-theme' );
			$url   = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ), 'activate-plugin_' . $file );
		} elseif ( current_user_can( 'install_plugins' ) ) {
			$label = __( 'Install and activate MenuDash', 'menudash-theme' );
			$url   = wp_nonce_url( admin_url( 'admin-post.php?action=mdt_install_menudash' ), 'mdt_install_menudash' );
		} else {
			$label = '';
			$url   = '';
		}
		echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'MenuDash Theme works with the free MenuDash plugin', 'menudash-theme' ) . '</strong></p>';
		echo '<p>' . esc_html__( 'Your menu, today\'s specials, the dishes on the home page and the holiday notice come from MenuDash. Once it is active, the theme makes your menu page.', 'menudash-theme' ) . '</p><p>';
		if ( $url ) {
			printf( '<a class="button button-primary" href="%s">%s</a> ', esc_url( $url ), esc_html( $label ) );
		} else {
			echo esc_html__( 'Ask your site\'s administrator to install it.', 'menudash-theme' ) . ' ';
		}
		printf(
			'<a class="button-link" href="%s">%s</a> · <a href="%s">%s</a> · <a href="%s" target="_blank" rel="noopener">%s</a></p></div>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mdt_menudash_later' ), 'mdt_menudash_later' ) ),
			esc_html__( 'Not now', 'menudash-theme' ),
			esc_url( mdt_setup_url() ),
			esc_html__( 'Plugins & Layout', 'menudash-theme' ),
			esc_url( MDT_MENUDASH_PAGE ),
			esc_html__( 'About MenuDash', 'menudash-theme' )
		);
	}
);

/** "Not now": the notice stays away for this user until the theme is activated again. */
add_action(
	'admin_post_mdt_menudash_later',
	function () {
		check_admin_referer( 'mdt_menudash_later' );
		update_user_meta( get_current_user_id(), 'mdt_menudash_later', 1 );
		mdt_menudash_back( array() );
	}
);
add_action(
	'after_switch_theme',
	function () {
		delete_metadata( 'user', 0, 'mdt_menudash_later', '', true );
	}
);

/** Back to the screen with "couldn't be installed" and the reason (kept for 5 minutes). */
function mdt_install_failed( $why ) {
	set_transient( 'mdt_install_error_' . get_current_user_id(), wp_strip_all_tags( (string) $why ), 5 * MINUTE_IN_SECONDS );
	mdt_menudash_back( array( 'mdt-menudash' => 'error' ) );
}

/** The button: download the latest release, install it, activate it. */
add_action(
	'admin_post_mdt_install_menudash',
	function () {
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to install plugins on this site.', 'menudash-theme' ), 403 );
		}
		check_admin_referer( 'mdt_install_menudash' );
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$file = mdt_plugin_file();
		if ( ! $file ) {
			// A quiet skin: it collects messages instead of printing them, so the redirect works.
			$skin   = new WP_Ajax_Upgrader_Skin();
			$result = ( new Plugin_Upgrader( $skin ) )->install( MDT_MENUDASH_ZIP );
			if ( is_wp_error( $result ) || ! $result || $skin->get_errors()->has_errors() ) {
				$error = is_wp_error( $result ) ? $result : $skin->get_errors();
				$why   = $error->has_errors() ? $error->get_error_message() : __( 'The site can\'t write to its plugins folder.', 'menudash-theme' );
				mdt_install_failed( $why );
			}
			wp_clean_plugins_cache();
			$file = mdt_plugin_file();
		}
		$active = $file ? activate_plugin( $file ) : new WP_Error( 'mdt_missing', __( 'The download didn\'t contain MenuDash.', 'menudash-theme' ) );
		if ( is_wp_error( $active ) ) {
			mdt_install_failed( $active->get_error_message() );
		}
		mdt_menudash_back( array( 'mdt-menudash' => 'ready' ) );
	}
);

/**
 * MenuDash was just activated (its file is loaded, so [menudash] exists now): make the menu
 * page, as on the theme's activation. The theme's "Menu" button already points to it.
 */
add_action(
	'activated_plugin',
	function ( $plugin ) {
		if ( defined( 'MENUDASH_FILE' ) && plugin_basename( MENUDASH_FILE ) === $plugin ) {
			mdt_setup_menu();
		}
	}
);

/** The first page holding the menu ([menudash]), in any status but the bin, or null. */
function mdt_menu_page() {
	$pages = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'future', 'draft', 'pending', 'private' ), 'numberposts' => -1 ) );
	foreach ( $pages as $page ) {
		if ( has_shortcode( $page->post_content, 'menudash' ) ) {
			return $page;
		}
	}
	return null;
}

/**
 * The theme's templates and template parts (header, footer) that were changed in the Editor.
 * Templates the owner made from scratch are left out: they have no default to go back to.
 */
function mdt_changed_templates() {
	$changed = array();
	foreach ( array( 'wp_template', 'wp_template_part' ) as $type ) {
		foreach ( get_block_templates( array(), $type ) as $t ) {
			if ( 'custom' === $t->source && $t->has_theme_file && $t->wp_id && get_stylesheet() === $t->theme ) {
				$changed[] = $t;
			}
		}
	}
	return $changed;
}

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Plugins & Layout', 'menudash-theme' ), __( 'Plugins & Layout', 'menudash-theme' ), 'edit_theme_options', 'menudash-theme-setup', 'mdt_setup_page' );
		// Fonts are managed in the Editor (Styles → Typography), like everything else, so
		// WordPress 7's separate Appearance → Fonts entry is left out. The screen still works.
		remove_submenu_page( 'themes.php', 'font-library.php' );
	},
	20
);

/** Appearance → Plugins & Layout. */
function mdt_setup_page() {
	// phpcs:ignore WordPress.Security.NonceVerification -- only picks which message to show
	$done = isset( $_GET['mdt-done'] ) ? sanitize_key( $_GET['mdt-done'] ) : '';
	$msgs = array(
		'home'      => __( 'A new home page with the default layout is now your front page. The old one is still under Pages.', 'menudash-theme' ),
		'menu'      => __( 'The menu page is made.', 'menudash-theme' ),
		'templates' => __( 'The header, footer and templates are back to the theme\'s default.', 'menudash-theme' ),
	);
	$post_button = function ( $action, $label, $confirm = '', $primary = false ) {
		return sprintf(
			'<form method="post" action="%1$s" style="display:inline"%2$s><input type="hidden" name="action" value="%3$s">%4$s<button type="submit" class="button%5$s">%6$s</button></form>',
			esc_url( admin_url( 'admin-post.php' ) ),
			'' !== $confirm ? ' onsubmit="return confirm(' . esc_attr( wp_json_encode( $confirm ) ) . ')"' : '',
			esc_attr( $action ),
			wp_nonce_field( $action, '_wpnonce', true, false ),
			$primary ? ' button-primary' : '',
			esc_html( $label )
		);
	};
	$yes = '<span style="color:#008a20">✓</span> ';
	echo '<div class="wrap"><h1>' . esc_html__( 'Plugins & Layout', 'menudash-theme' ) . '</h1>';
	if ( isset( $msgs[ $done ] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msgs[ $done ] ) . '</p></div>';
	}

	// Plugins: MenuDash, then its add-ons.
	echo '<h2>' . esc_html__( 'Plugins', 'menudash-theme' ) . '</h2>';
	echo '<p>' . esc_html__( 'The theme shows what you enter in MenuDash. The free MenuDash plugin is needed; each add-on brings more sections.', 'menudash-theme' ) . '</p>';
	$plugins = array(
		'MenuDash'             => __( 'The menu, the recommended dishes on the home page, colours. Free.', 'menudash-theme' ),
		'MenuDash Restaurant'  => __( 'Address, opening hours, "open now", holiday notice, contact buttons, map.', 'menudash-theme' ),
		'MenuDash Specials'    => __( 'Today\'s specials and the lunch menu.', 'menudash-theme' ),
		'MenuDash Gift Cards'  => __( 'Gift card orders.', 'menudash-theme' ),
		'MenuDash Statistics'  => __( 'Which dishes guests look at.', 'menudash-theme' ),
	);
	echo '<table class="widefat striped" style="max-width:960px"><tbody>';
	foreach ( $plugins as $name => $what ) {
		$file   = mdt_plugin_file( $name );
		$active = $file && is_plugin_active( $file );
		if ( $active ) {
			$state  = $yes . esc_html__( 'Active', 'menudash-theme' );
			$action = '';
		} elseif ( $file ) {
			$state  = esc_html__( 'Installed, not active', 'menudash-theme' );
			$action = current_user_can( 'activate_plugins' ) ? sprintf( '<a class="button button-primary" href="%s">%s</a>', esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ), 'activate-plugin_' . $file ) ), esc_html__( 'Activate', 'menudash-theme' ) ) : '';
		} else {
			$state = esc_html__( 'Not installed', 'menudash-theme' );
			if ( 'MenuDash' === $name ) {
				$action = current_user_can( 'install_plugins' ) ? sprintf( '<a class="button button-primary" href="%s">%s</a>', esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mdt_install_menudash' ), 'mdt_install_menudash' ) ), esc_html__( 'Install and activate', 'menudash-theme' ) ) : '';
			} else {
				$action = sprintf( '<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url( MDT_MENUDASH_PAGE . '#add-ons' ), esc_html__( 'How to get it', 'menudash-theme' ) );
			}
		}
		printf( '<tr><td style="width:24%%"><strong>%s</strong></td><td>%s</td><td style="width:20%%">%s</td><td style="width:22%%">%s</td></tr>', esc_html( $name ), esc_html( $what ), $state, $action ); // phpcs:ignore -- built from escaped parts above
	}
	echo '</tbody></table>';
	echo '<p class="description">' . esc_html__( 'Add-ons are uploaded under Plugins → Add New → Upload Plugin.', 'menudash-theme' ) . '</p>';

	// The default layout.
	echo '<h2 style="margin-top:2em">' . esc_html__( 'Default layout', 'menudash-theme' ) . '</h2>';
	echo '<p>' . esc_html__( 'Bring back what the theme made when it was activated. Your own pages are never deleted.', 'menudash-theme' ) . '</p>';
	echo '<table class="widefat striped" style="max-width:960px"><tbody>';

	$front = 'page' === get_option( 'show_on_front' ) ? get_post( (int) get_option( 'page_on_front' ) ) : null;
	$state = $front ? $yes . sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $front->ID ) ), esc_html( get_the_title( $front ) ) ) : esc_html__( 'No home page: the site shows the latest posts.', 'menudash-theme' );
	printf(
		'<tr><td style="width:24%%"><strong>%s</strong></td><td>%s</td><td style="width:42%%">%s</td></tr>',
		esc_html__( 'Home page', 'menudash-theme' ),
		$state, // phpcs:ignore -- escaped above
		$post_button( 'mdt_make_home', __( 'Make a new home page', 'menudash-theme' ), $front ? __( 'Make a new home page with the default layout and show it instead of the current one? The current one stays under Pages.', 'menudash-theme' ) : '', ! $front ) // phpcs:ignore -- escaped in $post_button
	);

	$menu = mdt_menu_page();
	if ( $menu ) {
		$state  = $yes . sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $menu->ID ) ), esc_html( get_the_title( $menu ) ) );
		$button = '';
	} elseif ( shortcode_exists( 'menudash' ) ) {
		$state  = esc_html__( 'Missing', 'menudash-theme' );
		$button = $post_button( 'mdt_make_menu', __( 'Make the menu page', 'menudash-theme' ), '', true );
	} else {
		$state  = esc_html__( 'Needs MenuDash', 'menudash-theme' );
		$button = '';
	}
	printf( '<tr><td><strong>%s</strong></td><td>%s</td><td>%s</td></tr>', esc_html__( 'Menu page', 'menudash-theme' ), $state, $button ); // phpcs:ignore -- escaped above

	$changed = mdt_changed_templates();
	if ( $changed ) {
		$names  = implode( ', ', array_map( function ( $t ) { return $t->title ? $t->title : $t->slug; }, $changed ) );
		/* translators: %s: names of the changed templates, e.g. "Header, Front Page" */
		$state  = esc_html( sprintf( __( 'Changed in the Editor: %s', 'menudash-theme' ), $names ) );
		$button = $post_button( 'mdt_reset_templates', __( 'Back to the default', 'menudash-theme' ), __( 'Undo all changes to the header, footer and templates? Your pages, menu and colours stay as they are.', 'menudash-theme' ) );
	} else {
		$state  = $yes . esc_html__( 'As the theme made them', 'menudash-theme' );
		$button = '';
	}
	printf( '<tr><td><strong>%s</strong></td><td>%s</td><td>%s</td></tr>', esc_html__( 'Header, footer and templates', 'menudash-theme' ), $state, $button ); // phpcs:ignore -- escaped above
	echo '</tbody></table>';

	// Where things are: in a block theme almost all of it is in Appearance → Editor (no
	// Customize, Menus, Widgets, Header or Background screens as in older themes).
	echo '<h2 style="margin-top:2em">' . esc_html__( 'Where to change what', 'menudash-theme' ) . '</h2>';
	echo '<p>' . esc_html__( 'Almost everything is in Appearance → Editor. These links open the right place.', 'menudash-theme' ) . '</p>';
	$editor = function ( $path, $edit = false ) {
		return admin_url( 'site-editor.php?p=' . rawurlencode( $path ) . ( $edit ? '&canvas=edit' : '' ) );
	};
	$theme  = get_stylesheet();
	$links  = array(
		array( $editor( '/wp_template_part/' . $theme . '//header', true ), __( 'Header', 'menudash-theme' ), __( 'Logo, top menu, corner picture, holiday notice.', 'menudash-theme' ) ),
		array( $editor( '/navigation' ), __( 'Top menu', 'menudash-theme' ), __( 'The links in the header (older themes: Appearance → Menus).', 'menudash-theme' ) ),
		array( $editor( '/styles' ), __( 'Colours and fonts', 'menudash-theme' ), __( 'Colours, fonts, sizes and spacing for the whole site (older themes: Customize, Background).', 'menudash-theme' ) ),
		array( admin_url( 'site-editor.php?p=%2Fstyles&section=%2Ftypography' ), __( 'Fonts', 'menudash-theme' ), __( 'Styles → Typography: the theme\'s two fonts; the button next to "Fonts" adds or uploads others.', 'menudash-theme' ) ),
		array( $front ? get_edit_post_link( $front->ID, 'raw' ) : '', __( 'Home page', 'menudash-theme' ), __( 'Texts, pictures and the order of the sections.', 'menudash-theme' ) ),
		array( $editor( '/wp_template_part/' . $theme . '//footer', true ), __( 'Footer', 'menudash-theme' ), __( 'Address, opening hours, links at the bottom (older themes: Widgets).', 'menudash-theme' ) ),
		array( $editor( '/pattern' ), __( 'Sections', 'menudash-theme' ), __( 'The ready-made sections to add to any page (Patterns).', 'menudash-theme' ) ),
		array( $editor( '/template' ), __( 'Page layouts', 'menudash-theme' ), __( 'How each kind of page is built (Templates).', 'menudash-theme' ) ),
		array( admin_url( 'admin.php?page=menudash' ), __( 'Menu, hours and restaurant details', 'menudash-theme' ), __( 'Entered in MenuDash; the theme shows them everywhere.', 'menudash-theme' ) ),
	);
	echo '<table class="widefat striped" style="max-width:960px"><tbody>';
	foreach ( $links as $l ) {
		if ( '' === (string) $l[0] || ( 'admin.php?page=menudash' === substr( $l[0], -23 ) && ! defined( 'MENUDASH_FILE' ) ) ) {
			continue;
		}
		printf( '<tr><td style="width:24%%"><a href="%s"><strong>%s</strong></a></td><td>%s</td></tr>', esc_url( $l[0] ), esc_html( $l[1] ), esc_html( $l[2] ) );
	}
	echo '</tbody></table>';
	echo '</div>';
}

/** The page's three buttons. Each needs the right to change the theme's layout. */
foreach ( array( 'mdt_make_home', 'mdt_make_menu', 'mdt_reset_templates' ) as $mdt_action ) {
	add_action(
		'admin_post_' . $mdt_action,
		function () use ( $mdt_action ) {
			if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_pages' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to change the layout.', 'menudash-theme' ), 403 );
			}
			check_admin_referer( $mdt_action );
			if ( 'mdt_make_home' === $mdt_action ) {
				mdt_make_home();
				$done = 'home';
			} elseif ( 'mdt_make_menu' === $mdt_action ) {
				mdt_setup_menu();
				$done = 'menu';
			} else {
				// Deleted changes go to the bin (as a page would), so they can still be found there.
				foreach ( mdt_changed_templates() as $t ) {
					wp_trash_post( $t->wp_id );
				}
				$done = 'templates';
			}
			wp_safe_redirect( add_query_arg( 'mdt-done', $done, mdt_setup_url() ) );
			exit;
		}
	);
}
