<?php
/**
 * Pattern: Philosophy Section
 * Slug: sennen/philosophy-section
 * Categories: sennen-sections
 * Description: Text and image section with heading, body, and organic image.
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|section-gap","bottom":"var:preset|spacing|section-gap","left":"var:preset|spacing|container-padding","right":"var:preset|spacing|container-padding"}},"color":{"background":"#fcf9f2"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-background" style="background-color:#fcf9f2;padding-top:var(--wp--preset--spacing--section-gap);padding-right:var(--wp--preset--spacing--container-padding);padding-bottom:var(--wp--preset--spacing--section-gap);padding-left:var(--wp--preset--spacing--container-padding)">
  <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|content-gap"}}} -->
  <div class="wp-block-columns are-vertically-aligned-center">
    <!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
      <!-- wp:heading {"level":2} -->
      <h2 class="wp-block-heading">The Art of Slowing Down</h2>
      <!-- /wp:heading -->

      <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|gutter"}},"layout":{"type":"constrained"}} -->
      <div class="wp-block-group">
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-lg)"},"color":{"text":"#42493e"}}} -->
        <p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg)">In a world that constantly demands more, we offer a space to simply be. Our philosophy is rooted in the earth, drawing inspiration from the tactile textures of nature and the gentle rhythm of the tides.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-md)"},"color":{"text":"#42493e"}}} -->
        <p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-md)">Here, every breath is intentional, every movement is unhurried. We believe true luxury is found in connection—to oneself, to the environment, and to the present moment.</p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
      <!-- wp:image {"sizeSlug":"large","className":"is-style-organic-shape-2"} -->
      <figure class="wp-block-image size-large is-style-organic-shape-2"><img src="" alt="Tranquil nature scene"/></figure>
      <!-- /wp:image -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
</div>
<!-- /wp:group -->
