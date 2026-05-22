<?php
/**
 * Render exact booking/contact grid matching app/booking/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$schedule = $attributes['scheduleHeading'] ?? 'Schedule Your Session';
$contact  = $attributes['contactHeading'] ?? 'Or send a gentle note';
$faq_head = $attributes['faqHeading'] ?? 'Good to know';
$faqs     = $attributes['faqs'] ?? array();

if ( empty( $faqs ) ) {
	$faqs = array(
		array( 'question' => 'Where do sessions take place?', 'answer' => 'In-person sessions are held at my private shala in Ubud. Virtual sessions take place via Zoom.' ),
		array( 'question' => 'What is your cancellation policy?', 'answer' => 'I ask for 48 hours notice for a full refund, honoring both your time and mine.' ),
	);
}

$booking_url = sennen_get_booking_url();
$nonce = wp_create_nonce( 'sennen_contact_form' );

$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array_map(
		static function ( array $faq ): array {
			return array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $faq['question'] ?? '' ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $faq['answer'] ?? '' ),
				),
			);
		},
		$faqs
	),
);

// Booking embed.
$booking_html = '';
if ( $booking_url ) {
	$booking_html = '<div class="sennen-booking-calendly" data-url="' . esc_attr( $booking_url ) . '" style="min-width:320px;height:700px;"></div>';
} else {
	$booking_html = '<div class="sennen-booking-placeholder"><div class="sennen-booking-placeholder-icon"><svg class="w-8 h-8" style="color: var(--color-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><p class="text-body-md font-light" style="color: var(--color-on-surface-variant);">Calendly booking widget will appear here once configured.</p><p class="text-label-caps tracking-widest uppercase text-sm" style="color: var(--color-on-surface-variant);">Set up your Calendly URL in site settings</p></div>';
}
?>

<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<section class="max-w-7xl mx-auto w-full relative z-20 -mt-20" style="padding-top: 4rem; padding-bottom: 4rem; padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding);">
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
		<!-- Booking Embed Column -->
		<div class="p-8 md:p-12 rounded-3xl shadow-lg border" style="background-color: var(--color-surface-container-lowest); border-color: rgba(194,201,187,0.3);">
			<h2 class="text-headline-md mb-8 pb-4" style="color: var(--color-primary); border-bottom: 1px solid rgba(194,201,187,0.3);"><?php echo esc_html( $schedule ); ?></h2>
			<?php echo $booking_html; ?>
		</div>

		<!-- Contact Form Column -->
		<div class="space-y-12">
			<div>
				<h2 class="text-headline-md mb-6" style="color: var(--color-primary);"><?php echo esc_html( $contact ); ?></h2>
				<form class="sennen-contact-form space-y-6" method="post">
					<div class="sennen-form-status"></div>
					<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
					<div class="space-y-2">
						<label for="sennen-name" class="text-label-caps uppercase tracking-widest" style="color: var(--color-on-surface-variant);">Name</label>
						<input type="text" id="sennen-name" name="name" placeholder="Your full name" />
						<p class="sennen-error-name text-label-caps mt-1" style="color: var(--color-error);"></p>
					</div>
					<div class="space-y-2">
						<label for="sennen-email" class="text-label-caps uppercase tracking-widest" style="color: var(--color-on-surface-variant);">Email</label>
						<input type="email" id="sennen-email" name="email" placeholder="hello@example.com" />
						<p class="sennen-error-email text-label-caps mt-1" style="color: var(--color-error);"></p>
					</div>
					<div class="space-y-2">
						<label for="sennen-inquiry" class="text-label-caps uppercase tracking-widest" style="color: var(--color-on-surface-variant);">Inquiry Type</label>
						<select id="sennen-inquiry" name="inquiryType">
							<option value="General Question">General Question</option>
							<option value="Bespoke Retreat">Bespoke Retreat</option>
							<option value="Virtual Session">Virtual Session</option>
							<option value="In-Person Session">In-Person Session</option>
						</select>
					</div>
					<div class="space-y-2">
						<label for="sennen-message" class="text-label-caps uppercase tracking-widest" style="color: var(--color-on-surface-variant);">Message</label>
						<textarea id="sennen-message" name="message" rows="4" placeholder="Share a bit about what brings you here..."></textarea>
						<p class="sennen-error-message text-label-caps mt-1" style="color: var(--color-error);"></p>
					</div>
					<button type="submit" class="group flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 rounded-full text-label-caps uppercase tracking-widest transition-all duration-300 disabled:opacity-50" style="background-color: var(--color-surface-container-highest); color: var(--color-primary);" onmouseover="this.style.backgroundColor='var(--color-secondary-container)';this.style.color='var(--color-on-secondary)'" onmouseout="this.style.backgroundColor='var(--color-surface-container-highest)';this.style.color='var(--color-primary)'">
						<span>Send Message</span>
						<svg class="w-4 h-4 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
					</button>
				</form>
			</div>

			<!-- FAQ -->
			<div class="space-y-6 pt-8" style="border-top: 1px solid rgba(194,201,187,0.3);">
				<h3 class="text-headline-md" style="color: var(--color-primary);"><?php echo esc_html( $faq_head ); ?></h3>
				<div class="space-y-4">
					<?php foreach ( $faqs as $faq ) : ?>
						<div>
							<h4 class="text-body-md font-bold mb-1" style="color: var(--color-on-surface);"><?php echo esc_html( $faq['question'] ?? '' ); ?></h4>
							<p class="text-body-md font-light" style="color: var(--color-on-surface-variant);"><?php echo esc_html( $faq['answer'] ?? '' ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
