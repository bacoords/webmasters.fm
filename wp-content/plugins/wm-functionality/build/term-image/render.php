<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Get the postID from the context.
$this_post_id = $block->context['postId'] ?? 0;
$display      = isset( $attributes['display'] ) && 'text' === $attributes['display'] ? 'text' : 'image';
$bc_term_id   = 14;

if ( is_tax( 'speaker' ) ) {
	$speakers   = array();
	$speakers[] = get_queried_object();
} else {

	// Get the speakers terms for the post.
	$speakers = get_the_terms( $this_post_id, 'speaker' );

	// If there are no speakers, return early.
	if ( ! $speakers || is_wp_error( $speakers ) ) {
		return;
	}

	usort(
		$speakers,
		function ( $a, $b ) use ( $bc_term_id ) {
			return $a->term_id === $bc_term_id ? -1 : ( $b->term_id === $bc_term_id ? 1 : 0 );
		}
	);
}

if ( 'text' === $display ) {
	$speakers = array_filter(
		$speakers,
		static function ( $speaker ) use ( $bc_term_id ) {
			return $speaker->term_id !== $bc_term_id;
		}
	);
	if ( ! $speakers ) {
		return;
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'is-display-' . $display ) ); ?>>
	<?php
	$first_speaker = true;
	foreach ( $speakers as $speaker ) :
		?>
		<?php
		if ( 'text' === $display ) {
			if ( ! $first_speaker ) {
				echo ', ';
			}
			$first_speaker = false;
			$link = get_term_link( $speaker );
			if ( ! is_wp_error( $link ) ) {
				printf(
					'<span><a href="%1$s">%2$s</a></span>',
					esc_url( $link ),
					esc_html( $speaker->name )
				);
			} else {
				printf( '<span>%s</span>', esc_html( $speaker->name ) );
			}
			continue;
		}

		// Get the speaker image.
		$image = get_term_meta( $speaker->term_id, 'image', true );
		if ( ! $image ) {
			continue;
		}
		if ( ! is_tax( 'speaker' ) ) {
			// translators: %s is the speaker name.
			$aria_label = wp_sprintf( __( 'All episodes with: %s', 'wm-functionality' ), $speaker->name );
			?>
			<a href="<?php echo esc_url( get_term_link( $speaker ) ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
				<?php echo wp_get_attachment_image( $image, 'thumbnail' ); ?>
			</a>
			<?php
		} else {
			echo wp_get_attachment_image( $image, 'thumbnail' );
		}
endforeach;
	?>
</div>
