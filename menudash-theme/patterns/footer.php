<?php
/**
 * Title: Footer (address, getting here, opening hours)
 * Slug: menudash-theme/footer
 * Categories: footer, menudash-theme
 * Block Types: core/template-part/footer
 * Inserter: no
 */
?>
<!-- wp:group {"align":"full","className":"mdt-band","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40"}},"elements":{"link":{"color":{"text":"var:preset|color|cream"}},"heading":{"color":{"text":"var:preset|color|cream"}}}},"backgroundColor":"primary","textColor":"cream","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull mdt-band has-cream-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40)">
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
<!-- wp:separator {"align":"wide","style":{"color":{"background":"#ffffff40"}},"className":"is-style-wide"} -->
<hr class="wp-block-separator alignwide has-text-color has-alpha-channel-opacity has-background is-style-wide" style="background-color:#ffffff40;color:#ffffff40"/>
<!-- /wp:separator -->
<!-- wp:menudash-theme/legal /-->
</div>
<!-- /wp:group -->
