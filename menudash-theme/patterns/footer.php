<?php
/**
 * Title: Footer (address, getting here, opening hours; without MenuDash Restaurant: name and menu)
 * Slug: menudash-theme/footer
 * Categories: footer, menudash-theme
 * Block Types: core/template-part/footer
 * Inserter: no
 */
?>
<!-- wp:group {"align":"full","className":"mdt-band","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40"}},"elements":{"link":{"color":{"text":"var:preset|color|cream"}},"heading":{"color":{"text":"var:preset|color|cream"}}}},"backgroundColor":"primary","textColor":"cream","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull mdt-band has-cream-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40)">
<?php if ( mdt_has_restaurant() ) : // Address, getting here and hours: MenuDash Restaurant. ?>
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:site-title {"level":0,"style":{"typography":{"fontSize":"1.75rem","fontWeight":"800","lineHeight":"1"}},"fontFamily":"display"} /-->
<!-- wp:menudash/contact {"parts":["address","country","phone","email"]} /-->
<!-- wp:menudash-theme/social {"variant":"icons"} /-->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:pattern {"slug":"menudash-theme/directions"} /-->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html__( 'Opening hours', 'menudash-theme' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:pattern {"slug":"menudash-theme/hours"} /-->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<?php else : // MenuDash alone: the name, the tagline and the way to the menu. ?>
<!-- wp:columns {"align":"wide","verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:site-title {"level":0,"style":{"typography":{"fontSize":"1.75rem","fontWeight":"800","lineHeight":"1"}},"fontFamily":"display"} /-->
<!-- wp:site-tagline /-->
<!-- wp:menudash-theme/addon {"part":"footer"} /-->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-outline","textColor":"cream"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-cream-color has-text-color wp-element-button" href="<?php echo esc_url( mdt_menu_url() ); ?>"><?php echo esc_html__( 'Menu', 'menudash-theme' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<?php endif; ?>
<!-- wp:separator {"align":"wide","style":{"color":{"background":"#ffffff40"}},"className":"is-style-wide"} -->
<hr class="wp-block-separator alignwide has-text-color has-alpha-channel-opacity has-background is-style-wide" style="background-color:#ffffff40;color:#ffffff40"/>
<!-- /wp:separator -->
<!-- wp:menudash-theme/legal /-->
</div>
<!-- /wp:group -->
