<?php
/**
 * Pattern: About Hero
 * Slug: sennen/about-hero
 * Categories: sennen-sections
 * Description: Full-width hero for the About page with heading, body, and organic image.
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section-gap","bottom":"var:preset|spacing|content-gap","left":"var:preset|spacing|container-padding","right":"var:preset|spacing|container-padding"}},"color":{"background":"#fcf9f2"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#fcf9f2;padding-top:var(--wp--preset--spacing--section-gap);padding-right:var(--wp--preset--spacing--container-padding);padding-bottom:var(--wp--preset--spacing--content-gap);padding-left:var(--wp--preset--spacing--container-padding)">
  <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|gutter"}}} -->
  <div class="wp-block-columns are-vertically-aligned-center">
    <!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
      <!-- wp:paragraph {"className":"is-style-label-caps","style":{"color":{"text":"#42493e"}}} -->
      <p class="is-style-label-caps has-text-color" style="color:#42493e">My Story</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"var(--wp--preset--font-size--display-xl)","lineHeight":"1.1"}}} -->
      <h1 class="wp-block-heading" style="font-size:var(--wp--preset--font-size--display-xl);line-height:1.1">Rooted in Grace, Wandering in Spirit</h1>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-lg)"},"color":{"text":"#42493e"}}} -->
      <p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg)">My journey began not with a destination, but with a profound desire to listen. Between the ancient temples of Thailand and the healing waters of Bali, I found the quiet voice that guides my practice today.</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
      <!-- wp:image {"sizeSlug":"large","className":"is-style-organic-shape-3"} -->
      <figure class="wp-block-image size-large is-style-organic-shape-3"><img src="" alt="Portrait of Sennen"/></figure>
      <!-- /wp:image -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
</div>
<!-- /wp:group -->
