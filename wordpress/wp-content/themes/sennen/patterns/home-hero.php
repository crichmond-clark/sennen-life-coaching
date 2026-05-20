<?php
/**
 * Pattern: Home Hero
 * Slug: sennen/home-hero
 * Categories: sennen-sections
 * Description: Full-width hero with heading, subtitle, CTA, and organic image.
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|section-gap","bottom":"var:preset|spacing|content-gap","left":"var:preset|spacing|container-padding","right":"var:preset|spacing|container-padding"}},"color":{"background":"#fcf9f2"}},"layout":{"type":"constrained","wideSize":"1200px"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#fcf9f2;padding-top:var(--wp--preset--spacing--section-gap);padding-right:var(--wp--preset--spacing--container-padding);padding-bottom:var(--wp--preset--spacing--content-gap);padding-left:var(--wp--preset--spacing--container-padding)">
  <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|gutter"}}} -->
  <div class="wp-block-columns are-vertically-aligned-center">
    <!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
      <!-- wp:paragraph {"className":"is-style-label-caps","style":{"color":{"text":"#42493e"}}} -->
      <p class="is-style-label-caps has-text-color" style="color:#42493e">Welcome to Sennen</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"var(--wp--preset--font-size--display-xl)","lineHeight":"1.1"}}} -->
      <h1 class="wp-block-heading" style="font-size:var(--wp--preset--font-size--display-xl);line-height:1.1">Rooted in Grace</h1>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-lg)"},"color":{"text":"#42493e"}},"placeholder":"Add a subtitle..."} -->
      <p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg)">A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.</p>
      <!-- /wp:paragraph -->

      <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|gutter"}}}} -->
      <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--gutter)">
        <!-- wp:button {"className":"is-style-sennen-primary"} -->
        <div class="wp-block-button is-style-sennen-primary"><a class="wp-block-button__link wp-element-button" href="/booking">Begin Your Journey</a></div>
        <!-- /wp:button -->

        <!-- wp:button {"className":"is-style-sennen-secondary"} -->
        <div class="wp-block-button is-style-sennen-secondary"><a class="wp-block-button__link wp-element-button" href="/about">Learn More</a></div>
        <!-- /wp:button -->
      </div>
      <!-- /wp:buttons -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
      <!-- wp:image {"sizeSlug":"large","className":"is-style-organic-shape-1"} -->
      <figure class="wp-block-image size-large is-style-organic-shape-1"><img src="" alt="Serene natural landscape with soft light"/></figure>
      <!-- /wp:image -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
</div>
<!-- /wp:group -->
