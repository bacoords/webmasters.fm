<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Get the postID from the context.
$this_post_id = $block->context['postId'];

if ( is_tax( 'speaker' ) ) {
	$speakers   = array();
	$speakers[] = get_queried_object();
} else {

	// Get the speakers terms for the post.
	$speakers = get_the_terms( $this_post_id, 'speaker' );

	$bc_term_id = 14;

	// If there are no speakers, return early.
	if ( ! $speakers ) {
		return;
	}

	usort(
		$speakers,
		function ( $a, $b ) use ( $bc_term_id ) {
			return $a->term_id === $bc_term_id ? -1 : ( $b->term_id === $bc_term_id ? 1 : 0 );
		}
	);
}
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php foreach ( $speakers as $speaker ) : ?>
		<?php

		// Get the speaker image.
		$image = get_term_meta( $speaker->term_id, 'image', true );
		if ( ! $image ) {
			continue;
		}
		?>
		<a href="<?php echo esc_url( get_term_link( $speaker ) ); ?>">
			<?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?>
		</a>
	<?php endforeach; ?>
</div>
