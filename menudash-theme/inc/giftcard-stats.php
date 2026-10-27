<?php
/**
 * With MenuDash Statistics (1.6 or newer), without the MenuDash Gift Cards add-on: gift cards
 * are sold at the restaurant only, so the home page's gift card teaser has a button to the gift
 * card page, and that page shows the way there and the menu (menudash-theme/giftcard-visit). Counted, as
 * totals per day like everything else in Statistics (no cookies):
 *   gc:home   the teaser's "More about the gift card" button tapped,
 *   gc:page   the gift card page open for 2 seconds,
 *   gc:route  "Get directions" tapped there,
 *   gc:menu   "View the menu" tapped there.
 * The markup carries the keys (mdt_block_giftcard(), mdt_block_giftcard_visit());
 * Statistics counts only the keys listed here, and gets a card on its tab under Whole website.
 * Only without MenuDash Gift Cards: with it, guests order online and Statistics counts the
 * orders itself. Without Statistics nothing is counted and nothing here shows.
 */

defined( 'ABSPATH' ) || exit;

const MDT_GC_STATS = array( 'gc:home', 'gc:page', 'gc:route', 'gc:menu' );

add_filter( 'menudash_stats_extra_keys', 'mdt_gc_stats_keys' );

// A gift card page made for the add-on holds its form's shortcode. Without the add-on MenuDash
// quiets it (shows nothing, at init 99); the theme shows the "at the restaurant" part instead.
add_action(
	'init',
	function () {
		if ( ! shortcode_exists( 'menudash_giftcard' ) && ! function_exists( 'mdash_gc_open' ) ) {
			add_shortcode( 'menudash_giftcard', 'mdt_block_giftcard_visit' );
		}
	},
	98
);
add_action( 'menudash_stats_cards', 'mdt_gc_stats_card' );

/** The keys a guest's browser may send. */
function mdt_gc_stats_keys( $keys ) {
	return function_exists( 'mdash_gc_open' ) ? $keys : $keys + array_fill_keys( MDT_GC_STATS, true );
}

/** The gift card card on the Statistics tab, for the period chosen there. */
function mdt_gc_stats_card( $days ) {
	if ( ! function_exists( 'mdash_stats_extra_totals' ) || function_exists( 'mdash_gc_open' ) ) {
		return; // No Statistics, or the Gift Cards add-on (online orders, counted by Statistics).
	}
	$t = mdash_stats_extra_totals( 'gc:', $days );
	$n = function ( $k ) use ( $t ) {
		return isset( $t[ $k ] ) ? (int) $t[ $k ] : 0;
	};
	$page  = $n( 'gc:page' );
	$share = function ( $part ) use ( $page ) {
		return $page ? number_format_i18n( 100 * $part / $page ) . ' %' : '–';
	};
	$tiles = array(
		array( __( 'Gift card page opened', 'menudash-theme' ), $n( 'gc:page' ), '' ),
		array( __( 'From the home page button', 'menudash-theme' ), $n( 'gc:home' ), '' ),
		array( __( 'Then directions', 'menudash-theme' ), $n( 'gc:route' ), $share( $n( 'gc:route' ) ) ),
		array( __( 'Then the menu', 'menudash-theme' ), $n( 'gc:menu' ), $share( $n( 'gc:menu' ) ) ),
	);
	?>
	<section class="mdash-card mdash-stats mdt-gc-stats">
		<h2><?php esc_html_e( 'Gift card', 'menudash-theme' ); ?></h2>
		<p><?php esc_html_e( 'Gift cards are sold at the restaurant only. How often the gift card page was opened (and how often from the button on the home page), and how often guests then tapped Get directions or View the menu there. A tap shows interest; it is not yet a sale.', 'menudash-theme' ); ?></p>
		<dl class="mdash-stats-tiles">
			<?php foreach ( $tiles as $tile ) : ?>
				<div>
					<dt><?php echo esc_html( $tile[0] ); ?></dt>
					<dd><?php echo esc_html( number_format_i18n( $tile[1] ) ); ?>
						<?php if ( '' !== $tile[2] ) : ?>
							<small><?php /* translators: %s: e.g. "12 %" */ echo esc_html( sprintf( __( '%s of page visits', 'menudash-theme' ), $tile[2] ) ); ?></small>
						<?php endif; ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</section>
	<?php
}
