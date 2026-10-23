<?php
/**
 * Title: Header (coloured band; slim glass bar while scrolling)
 * Slug: menudash-theme/header
 * Categories: header, menudash-theme
 * Block Types: core/template-part/header
 * Inserter: no
 */
// A navigation menu made in Appearance → Editor → Navigation, preferring one with real
// links over WordPress's automatic page list; without any, WordPress lists the pages.
$ref = '';
foreach ( get_posts( array( 'post_type' => 'wp_navigation', 'numberposts' => 20, 'orderby' => 'date', 'order' => 'ASC' ) ) as $nav ) {
	$ref = $ref ? $ref : '"ref":' . (int) $nav->ID . ',';
	if ( '<!-- wp:page-list /-->' !== trim( $nav->post_content ) ) {
		$ref = '"ref":' . (int) $nav->ID . ',';
		break;
	}
}
?>
<!-- wp:group {"align":"full","className":"mdt-band mdt-seal","style":{"spacing":{"padding":{"top":"1.1rem","bottom":"1.1rem"}},"elements":{"link":{"color":{"text":"var:preset|color|cream"}}}},"backgroundColor":"primary","textColor":"cream","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull mdt-band mdt-seal has-cream-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:1.1rem;padding-bottom:1.1rem">
<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide">
<!-- wp:group {"style":{"spacing":{"blockGap":"0.35rem"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group">
<!-- wp:site-logo {"width":132,"mdtShow":"logo"} /-->
<!-- wp:site-title {"mdtShow":"no-logo","level":0,"style":{"typography":{"fontSize":"2rem","fontWeight":"800","lineHeight":"1"}},"fontFamily":"display"} /-->
<!-- wp:site-tagline {"className":"mdt-tagline","style":{"typography":{"fontSize":"0.6875rem","fontWeight":"700","textTransform":"uppercase","lineHeight":"1"}}} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"mdt-head-end","style":{"spacing":{"blockGap":"1.4rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group mdt-head-end">
<!-- wp:navigation {<?php echo $ref; ?>"overlayBackgroundColor":"primary","overlayTextColor":"cream","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"1.6rem"}}} /-->
<?php // With MenuDash Restaurant: open now, and one button to book (wide screens; phones have the action bar). ?>
<!-- wp:menudash/open {"className":"mdt-head-open"} /-->
<!-- wp:buttons {"className":"mdt-head-cta"} -->
<div class="wp-block-buttons mdt-head-cta">
<?php echo mdt_bound_button( 'reserve', trim( str_replace( '· %s', '', __( 'Reserve · %s', 'menudash-theme' ) ) ) ); // phpcs:ignore -- block markup, escaped there ?>
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<?php if ( shortcode_exists( 'menudash_closed' ) ) : // MenuDash's holiday notice; empty when none is coming up. ?>
<!-- wp:shortcode -->
[menudash_closed]
<!-- /wp:shortcode -->
<?php endif; ?>
