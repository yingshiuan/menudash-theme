<?php
/**
 * Title: Gift card page
 * Slug: menudash-theme/giftcard
 * Categories: menudash-theme
 * Description: An introduction and MenuDash's gift card order form ([menudash_giftcard]). Amounts, e-mail address, picture and terms are set under MenuDash → Gift cards.
 */
$lang = 0 === strpos( get_locale(), 'de' ) ? 'de' : 'en';
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html__( 'Give a meal: order a gift card.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Choose the amount and quantity and send us the form. We confirm your order by e-mail.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[menudash_giftcard lang="<?php echo esc_attr( $lang ); ?>"]
<!-- /wp:shortcode -->
