<?php
/**
 * Render the exact botanical divider matching Next.js page.tsx.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = $attributes['variant'] ?? 'up';
$opacity = $attributes['opacity'] ?? 30;

$svg = sennen_botanical_svg( $variant, $opacity );
?>

<div class="w-full h-24 flex justify-center items-center my-10 relative" style="opacity: <?php echo esc_attr( $opacity / 100 ); ?>;">
	<?php echo $svg; ?>
</div>
