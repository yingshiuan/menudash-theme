<?php
/**
 * Title: Legal notice
 * Slug: menudash-theme/legal-notice
 * Categories: menudash-theme, text
 * Description: Who runs the website (from MenuDash → Restaurant), your company number and who made the website. For a page "Legal notice".
 */
?>
<!-- wp:heading {"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size"><?php echo esc_html__( 'Provider', 'menudash-theme' ); ?></h2>
<!-- /wp:heading -->

<?php if ( mdt_has_restaurant() ) : ?>
<!-- wp:menudash-theme/provider /-->
<?php else : ?>
<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Company name, street and number, postcode and town, phone and e-mail.', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->
<?php endif; ?>

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Commercial register: canton or court', 'menudash-theme' ); ?><br><?php echo esc_html__( 'Company number: replace with yours (in Switzerland the UID, CHE-…)', 'menudash-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size"><?php echo esc_html__( 'Website', 'menudash-theme' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php
/* translators: %s: link to insdash.ch */
printf( esc_html__( 'Design & development: %s', 'menudash-theme' ), '<a href="https://insdash.ch" target="_blank" rel="noreferrer noopener">insdash.ch</a>' );
?></p>
<!-- /wp:paragraph -->
