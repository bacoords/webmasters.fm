<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Get the postID from the context.
$this_post_id = $block->context['postId'];

// Get the speakers terms for the post.
$speakers = get_the_terms( $this_post_id, 'speaker' );

// If there are no speakers, return early.
if ( ! $speakers ) {
	return;
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
		<?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?>
	<?php endforeach; ?>
</div>
