<?php
/**
 * Title: Gift card page
 * Slug: menudash-theme/giftcard
 * Categories: menudash-theme
 * Description: An introduction and MenuDash's gift card order form ([menudash_giftcard]). Amounts, e-mail address, picture and terms are set under MenuDash → Gift cards. Without the MenuDash Gift Cards add-on: sold at the restaurant only, with buttons for directions and the menu.
 */
$lang = 0 === strpos( get_locale(), 'de' ) ? 'de' : 'en';
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php echo esc_html__( 'Give a meal: order a gift card.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"mdtShow":"giftcards"} -->
<p><?php echo esc_html__( 'Choose the amount and quantity and send us the form. We confirm your order by e-mail.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:menudash-theme/giftcard-visit {"mdtShow":"no-giftcards"} /-->
<!-- wp:shortcode {"mdtShow":"giftcards"} -->
[menudash_giftcard lang="<?php echo esc_attr( $lang ); ?>"]
<!-- /wp:shortcode -->
