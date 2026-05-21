<?php
/**
 * Server-side render for sennen/contact-form block.
 *
 * @package SennenCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button_text      = $attributes['buttonText'] ?? __( 'Send Message', 'sennen-core' );
$success_message  = $attributes['successMessage'] ?? __( 'Your message has been sent. I look forward to connecting with you soon.', 'sennen-core' );
$block_inquiry    = $attributes['inquiryTypes'] ?? array();

// Get inquiry types: block override or global defaults.
if ( ! empty( $block_inquiry ) ) {
	$inquiry_types = $block_inquiry;
} else {
	$inquiry_types = apply_filters(
		'sennen_contact_inquiry_types',
		array(
			__( 'General Question', 'sennen-core' ),
			__( 'Bespoke Retreat', 'sennen-core' ),
			__( 'Virtual Session', 'sennen-core' ),
			__( 'In-Person Session', 'sennen-core' ),
		)
	);
}

$nonce = wp_create_nonce( 'sennen_contact_nonce' );
$rest_url = rest_url( 'sennen/v1/contact' );

ob_start();
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<form
		class="sennen-contact-form"
		data-rest-url="<?php echo esc_url( $rest_url ); ?>"
		data-nonce="<?php echo esc_attr( $nonce ); ?>"
		data-success-message="<?php echo esc_attr( $success_message ); ?>"
		method="post"
		novalidate
		style="max-width:640px"
	>
		<!-- Honeypot -->
		<div style="position:absolute;left:-9999px" aria-hidden="true">
			<label for="sennen-contact-website"><?php esc_html_e( 'Leave this empty', 'sennen-core' ); ?></label>
			<input type="text" id="sennen-contact-website" name="website" tabindex="-1" autocomplete="off">
		</div>

		<!-- Name -->
		<div style="margin-bottom:var(--wp--preset--spacing--gutter)">
			<label for="sennen-contact-name" style="display:block;font-weight:600;margin-bottom:0.5rem">
				<?php esc_html_e( 'Name', 'sennen-core' ); ?>
			</label>
			<input
				type="text"
				id="sennen-contact-name"
				name="name"
				required
				minlength="2"
				maxlength="120"
				style="width:100%;padding:0.75rem;border:1px solid var(--wp--preset--color--outline-variant);border-radius:4px;font-family:var(--wp--preset--font-family--sans);font-size:var(--wp--preset--font-size--body-md)"
				placeholder="<?php esc_attr_e( 'Your full name', 'sennen-core' ); ?>"
			>
		</div>

		<!-- Email -->
		<div style="margin-bottom:var(--wp--preset--spacing--gutter)">
			<label for="sennen-contact-email" style="display:block;font-weight:600;margin-bottom:0.5rem">
				<?php esc_html_e( 'Email', 'sennen-core' ); ?>
			</label>
			<input
				type="email"
				id="sennen-contact-email"
				name="email"
				required
				style="width:100%;padding:0.75rem;border:1px solid var(--wp--preset--color--outline-variant);border-radius:4px;font-family:var(--wp--preset--font-family--sans);font-size:var(--wp--preset--font-size--body-md)"
				placeholder="<?php esc_attr_e( 'you@example.com', 'sennen-core' ); ?>"
			>
		</div>

		<!-- Inquiry Type -->
		<?php if ( ! empty( $inquiry_types ) ) : ?>
			<div style="margin-bottom:var(--wp--preset--spacing--gutter)">
				<label for="sennen-contact-type" style="display:block;font-weight:600;margin-bottom:0.5rem">
					<?php esc_html_e( "I'm interested in", 'sennen-core' ); ?>
				</label>
				<select
					id="sennen-contact-type"
					name="inquiryType"
					required
					style="width:100%;padding:0.75rem;border:1px solid var(--wp--preset--color--outline-variant);border-radius:4px;font-family:var(--wp--preset--font-family--sans);font-size:var(--wp--preset--font-size--body-md);background:#ffffff"
				>
					<option value=""><?php esc_html_e( 'Select an option...', 'sennen-core' ); ?></option>
					<?php foreach ( $inquiry_types as $type ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<!-- Message -->
		<div style="margin-bottom:var(--wp--preset--spacing--gutter)">
			<label for="sennen-contact-message" style="display:block;font-weight:600;margin-bottom:0.5rem">
				<?php esc_html_e( 'Message', 'sennen-core' ); ?>
			</label>
			<textarea
				id="sennen-contact-message"
				name="message"
				required
				minlength="10"
				maxlength="5000"
				rows="5"
				style="width:100%;padding:0.75rem;border:1px solid var(--wp--preset--color--outline-variant);border-radius:4px;font-family:var(--wp--preset--font-family--sans);font-size:var(--wp--preset--font-size--body-md);resize:vertical"
				placeholder="<?php esc_attr_e( 'Tell me what you\'re looking for...', 'sennen-core' ); ?>"
			></textarea>
		</div>

		<!-- Submit -->
		<div class="wp-block-button is-style-sennen-primary">
			<button type="submit" class="wp-block-button__link wp-element-button">
				<?php echo esc_html( $button_text ); ?>
			</button>
		</div>

		<!-- Feedback message -->
		<div class="sennen-contact-feedback" style="margin-top:var(--wp--preset--spacing--base);display:none" role="alert" aria-live="polite"></div>
	</form>
</div>

<script>
(function() {
	var forms = document.querySelectorAll('.sennen-contact-form');
	if (!forms.length) return;

	forms.forEach(function(form) {
		form.addEventListener('submit', function(e) {
			e.preventDefault();

			var feedback = form.querySelector('.sennen-contact-feedback');
			var button = form.querySelector('button[type="submit"]');
			var restUrl = form.getAttribute('data-rest-url');
			var nonce = form.getAttribute('data-nonce');
			var successMsg = form.getAttribute('data-success-message');

			// Show loading state.
			if (button) {
				button.disabled = true;
				button.textContent = 'Sending...';
			}

			// Gather form data.
			var data = {
				name: (form.querySelector('[name="name"]') || {}).value || '',
				email: (form.querySelector('[name="email"]') || {}).value || '',
				inquiryType: (form.querySelector('[name="inquiryType"]') || {}).value || '',
				message: (form.querySelector('[name="message"]') || {}).value || '',
				website: (form.querySelector('[name="website"]') || {}).value || '',
				nonce: nonce
			};

			fetch(restUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(data)
			})
			.then(function(response) { return response.json(); })
			.then(function(result) {
				if (result.success) {
					feedback.style.display = 'block';
					feedback.style.color = '#2d5a27';
					feedback.style.background = '#bcf0ae';
					feedback.style.padding = '1rem';
					feedback.style.borderRadius = '4px';
					feedback.textContent = successMsg;
					form.reset();
				} else {
					feedback.style.display = 'block';
					feedback.style.color = '#ba1a1a';
					feedback.style.background = '#ffdad6';
					feedback.style.padding = '1rem';
					feedback.style.borderRadius = '4px';
					feedback.textContent = result.error || 'Something went wrong. Please try again.';
				}
			})
			.catch(function() {
				feedback.style.display = 'block';
				feedback.style.color = '#ba1a1a';
				feedback.style.background = '#ffdad6';
				feedback.style.padding = '1rem';
				feedback.style.borderRadius = '4px';
				feedback.textContent = 'Network error. Please check your connection and try again.';
			})
			.finally(function() {
				if (button) {
					button.disabled = false;
					button.textContent = '<?php echo esc_js( $button_text ); ?>';
				}
			});
		});
	});
})();
</script>
<?php
echo ob_get_clean();
