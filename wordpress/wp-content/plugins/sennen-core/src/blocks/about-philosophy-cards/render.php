<?php
/**
 * Render the exact About philosophy cards matching app/about/page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cards = $attributes['cards'] ?? array();

if ( empty( $cards ) ) {
	$cards = array(
		array( 'title' => 'Radical Presence', 'body' => 'Healing cannot happen in the past or the future. We cultivate practices that anchor you firmly in the tactile, breathing reality of the now.', 'iconName' => 'Flower' ),
		array( 'title' => 'Fluid Resilience', 'body' => 'Like water carving through stone, true strength is found in adaptability. We learn to flow around obstacles rather than breaking against them.', 'iconName' => 'Droplet' ),
		array( 'title' => 'Earth Connection', 'body' => 'We are not separate from nature; we are expressions of it. Reconnecting with the earth\'s rhythms naturally realigns our own internal pacing.', 'iconName' => 'Leaf' ),
	);
}

$icon_svgs = array(
	'Flower' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2a4 4 0 0 1 0 8"/><path d="M12 22a4 4 0 0 1 0-8"/><path d="M2 12a4 4 0 0 1 8 0"/><path d="M22 12a4 4 0 0 1-8 0"/></svg>',
	'Droplet' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/><path d="M12.56 14.1c1.44 0 2.6-1.19 2.6-2.64 0-.76-.37-1.47-1.11-2.08S12.73 7.89 12.56 7c-.19.94-.74 1.84-1.49 2.44s-1.11 1.29-1.11 2.02c0 1.45 1.17 2.64 2.6 2.64z"/></svg>',
	'Leaf' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.9C15.5 4.9 17 3.5 19 2c1 2 2 4.5 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
	'Sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
	'Heart' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
);
?>

<section class="relative" style="background-color: var(--color-surface-container-low); padding-top: var(--spacing-section-gap); padding-bottom: var(--spacing-section-gap); padding-left: var(--spacing-container-padding); padding-right: var(--spacing-container-padding); margin-bottom: var(--spacing-section-gap);">
	<div class="max-w-7xl mx-auto w-full">
		<div class="text-center mb-16 space-y-4">
			<h2 class="text-headline-lg" style="color: var(--color-primary);">My Philosophy</h2>
			<p class="text-body-lg font-light" style="color: var(--color-on-surface-variant);">Guiding principles for a soulful existence.</p>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
			<?php foreach ( $cards as $i => $card ) :
				$icon_svg = $icon_svgs[ $card['iconName'] ?? 'Flower' ] ?? $icon_svgs['Flower'];
				$offset   = 1 === $i ? 'md:-translate-y-8' : '';
			?>
				<div class="p-8 rounded-2xl shadow-sm border overflow-hidden group relative transition-shadow duration-300 hover:shadow-md <?php echo esc_attr( $offset ); ?>" style="background-color: var(--color-surface-container-lowest); border-color: rgba(194,201,187,0.3);">
					<div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity" style="color: var(--color-primary);">
						<?php echo $icon_svg; ?>
					</div>
					<h3 class="text-headline-md mb-4" style="color: var(--color-secondary);"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
					<p class="text-body-md font-light leading-relaxed relative z-10" style="color: var(--color-on-surface);">
						<?php echo esc_html( $card['body'] ?? '' ); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
