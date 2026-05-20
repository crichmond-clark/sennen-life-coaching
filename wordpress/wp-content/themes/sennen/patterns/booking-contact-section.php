<?php
/**
 * Pattern: Booking / Contact Section
 * Slug: sennen/booking-contact-section
 * Categories: sennen-sections
 * Description: A combined booking intro and contact form section for the booking page.
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|section-gap","bottom":"var:preset|spacing|section-gap","left":"var:preset|spacing|container-padding","right":"var:preset|spacing|container-padding"}},"color":{"background":"#fcf9f2"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-background" style="background-color:#fcf9f2;padding-top:var(--wp--preset--spacing--section-gap);padding-right:var(--wp--preset--spacing--container-padding);padding-bottom:var(--wp--preset--spacing--section-gap);padding-left:var(--wp--preset--spacing--container-padding)">
  <!-- wp:paragraph {"className":"is-style-label-caps","style":{"color":{"text":"#42493e"}}} -->
  <p class="is-style-label-caps has-text-color" style="color:#42493e">Connect</p>
  <!-- /wp:paragraph -->

  <!-- wp:heading {"level":1} -->
  <h1 class="wp-block-heading">Begin Your Journey</h1>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"style":{"typography":{"fontSize":"var(--wp--preset--font-size--body-lg)"},"color":{"text":"#42493e"}}} -->
  <p class="has-text-color" style="color:#42493e;font-size:var(--wp--preset--font-size--body-lg)">Take a deep breath. Inquire about a session below, or simply send a note to connect. I look forward to holding space for you.</p>
  <!-- /wp:paragraph -->

  <!-- wp:columns {"style":{"spacing":{"margin":{"top":"var:preset|spacing|content-gap"},"blockGap":"var:preset|spacing|gutter"}}} -->
  <div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--content-gap)">
    <!-- wp:column {"width":"60%"} -->
    <div class="wp-block-column" style="flex-basis:60%">
      <!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"var(--wp--preset--font-size--headline-md)"}}} -->
      <h2 class="wp-block-heading" style="font-size:var(--wp--preset--font-size--headline-md)">Schedule Your Session</h2>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"placeholder":"Add your Calendly embed or scheduling instructions here."} -->
      <p>Use the booking widget below to find a time that works for you. All sessions can be held in-person at my Ubud shala or virtually via Zoom.</p>
      <!-- /wp:paragraph -->

      <!-- wp:paragraph {"className":"is-style-label-caps","style":{"spacing":{"margin":{"top":"var:preset|spacing|gutter"}}}} -->
      <p class="is-style-label-caps" style="margin-top:var(--wp--preset--spacing--gutter)">Or send a gentle note</p>
      <!-- /wp:paragraph -->

      <!-- wp:paragraph -->
      <p>If you'd prefer to reach out directly, email <a href="mailto:hello@sennenlifecoaching.com">hello@sennenlifecoaching.com</a> or use the contact form.</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"40%"} -->
    <div class="wp-block-column" style="flex-basis:40%">
      <!-- wp:group {"className":"is-style-ambient-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|gutter","bottom":"var:preset|spacing|gutter","left":"var:preset|spacing|gutter","right":"var:preset|spacing|gutter"}},"color":{"background":"#ffffff"},"border":{"radius":"4px"}},"layout":{"type":"constrained"}} -->
      <div class="wp-block-group is-style-ambient-card has-background" style="border-radius:4px;background-color:#ffffff;padding-top:var(--wp--preset--spacing--gutter);padding-right:var(--wp--preset--spacing--gutter);padding-bottom:var(--wp--preset--spacing--gutter);padding-left:var(--wp--preset--spacing--gutter)">
        <!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"},"spacing":{"margin":{"bottom":"0.5rem"}}}} -->
        <h3 class="wp-block-heading" style="font-size:1.25rem;margin-bottom:0.5rem">Session Details</h3>
        <!-- /wp:heading -->

        <!-- wp:list {"style":{"typography":{"lineHeight":"2"}}} -->
        <ul style="line-height:2">
          <li>In-person in Ubud, Bali</li>
          <li>Virtual via Zoom worldwide</li>
          <li>48-hour cancellation policy</li>
          <li>Sliding scale available</li>
        </ul>
        <!-- /wp:list -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
</div>
<!-- /wp:group -->
