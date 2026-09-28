<?php
/**
 * Title: Home: what guests say
 * Slug: menudash-theme/testimonial
 * Categories: menudash-theme, testimonials
 * Description: One quote from a guest, a review or an article, with its source; on a coloured band.
 */
?>
<!-- wp:group {"align":"full","className":"mdt-band mdt-press","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"elements":{"link":{"color":{"text":"var:preset|color|cream"}}}},"backgroundColor":"primary","textColor":"cream","layout":{"type":"constrained","contentSize":"820px"}} -->
<div class="wp-block-group alignfull mdt-band mdt-press has-cream-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.8125rem","fontWeight":"700","textTransform":"uppercase","letterSpacing":"0.18em"}}} -->
<p class="has-text-align-center" style="font-size:0.8125rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase"><?php echo esc_html__( 'What guests say', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:quote {"className":"mdt-press-quote"} -->
<blockquote class="wp-block-quote mdt-press-quote"><!-- wp:paragraph -->
<p><?php echo esc_html__( 'Put a short quote from a guest, a review or an article here.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph --><cite><?php echo esc_html__( 'Name, where it was published', 'menudash-theme' ); ?></cite></blockquote>
<!-- /wp:quote -->
</div>
<!-- /wp:group -->
