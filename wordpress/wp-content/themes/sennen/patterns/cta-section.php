<?php
/**
 * Pattern: CTA Section
 * Slug: sennen/cta-section
 * Categories: sennen-sections
 * Description: Call-to-action section with heading, body, and button.
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section-gap","bottom":"var:preset|spacing|section-gap","left":"var:preset|spacing|container-padding","right":"var:preset|spacing|container-padding"}},"color":{"background":"#2d5a27"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#2d5a27;padding-top:var(--wp--preset--spacing--section-gap);padding-right:var(--wp--preset--spacing--container-padding);padding-bottom:var(--wp--preset--spacing--section-gap);padding-left:var(--wp--preset--spacing--container-padding)">
  <!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"var(--wp--preset--font-size--headline-lg)"},"color":{"text":"#ffffff"}}} -->
  <h2 class="wp-block-heading has-text-align-center has-text-color" style="color:#ffffff;font-size:var(--wp--preset--font-size--headline-lg)">Take the first breath.</h2>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-lg)","lineHeight":"1.6"},"color":{"text":"#bcf0ae"}}} -->
  <p class="has-text-align-center has-text-color" style="color:#bcf0ae;font-size:var(--wp--preset--font-size--body-lg);line-height:1.6">Your journey inward begins with a single step. I'd be honored to walk alongside you.</p>
  <!-- /wp:paragraph -->

  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|gutter"}}}} -->
  <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--gutter)">
    <!-- wp:button {"backgroundColor":"on-primary","textColor":"primary","style":{"typography":{"fontWeight":"600"}}} -->
    <div class="wp-block-button"><a class="wp-block-button__link has-primary-color has-on-primary-background-color has-text-color has-background wp-element-button" href="/booking" style="font-weight:600">Book a Session</a></div>
    <!-- /wp:button -->
  </div>
  <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
