<?php
/**
 * Renders the post grid block on server.
 */
function egb_blocks_render_block_core_latest_posts( $attributes ) {
	$categories = isset( $attributes['categories'] ) ? $attributes['categories'] : '';

	$recent_posts = wp_get_recent_posts(
		[
			'numberposts' => $attributes['postsToShow'],
			'post_status' => 'publish',
			'order'       => $attributes['order'],
			'orderby'     => $attributes['orderBy'],
			'category'    => $categories,
		],
		'OBJECT'
	);

	$list_items_markup = '';

	if ( $recent_posts ) {
		foreach ( $recent_posts as $post ) {
			// Get the post ID
			$post_id = $post->ID;

			// Get the post thumbnail
			$post_thumb_id = get_post_thumbnail_id( $post_id );

			if ( $post_thumb_id && isset( $attributes['displayPostImage'] ) && $attributes['displayPostImage'] ) {
				$post_thumb_class = 'has-thumb';
			} else {
				$post_thumb_class = 'no-thumb';
			}

			// Start the markup for the post
			$list_items_markup .= sprintf(
				'<article class="%1$s">',
				esc_attr( $post_thumb_class )
			);

			// Get the featured image
			if ( isset( $attributes['displayPostImage'] ) && $attributes['displayPostImage'] && $post_thumb_id ) {
				if ( $attributes['imageCrop'] === 'landscape' ) {
					$post_thumb_size = 'egb-block-post-grid-landscape';
				} else {
					$post_thumb_size = 'egb-block-post-grid-square';
				}

				$list_items_markup .= sprintf(
					'<div class="egb-block-post-grid-image"><a href="%1$s" rel="bookmark">%2$s</a></div>',
					esc_url( get_permalink( $post_id ) ),
					wp_get_attachment_image( $post_thumb_id, $post_thumb_size )
				);
			}

			// Wrap the text content
			$list_items_markup .= sprintf(
				'<div class="egb-block-post-grid-text">'
			);

				// Get the post title
				$title = get_the_title( $post_id );

			if ( ! $title ) {
				$title = __( 'Untitled', 'egb' );
			}

			if ( isset( $attributes['displayPostTitle'] ) && $attributes['displayPostTitle'] ) {
				$list_items_markup .= sprintf(
					'<h2 class="egb-block-post-grid-title"><a href="%1$s" rel="bookmark">%2$s</a></h2>',
					esc_url( get_permalink( $post_id ) ),
					esc_html( $title )
				);
			}

				// Wrap the byline content
				$list_items_markup .= sprintf(
					'<div class="egb-block-post-grid-byline">'
				);

				// Get the post author
			if ( isset( $attributes['displayPostAuthor'] ) && $attributes['displayPostAuthor'] ) {
				$list_items_markup .= sprintf(
					'<div class="egb-block-post-grid-author"><a class="egb-text-link" href="%2$s">%1$s</a></div>',
					esc_html( get_the_author_meta( 'display_name', $post->post_author ) ),
					esc_html( get_author_posts_url( $post->post_author ) )
				);
			}

				// Get the post date
			if ( isset( $attributes['displayPostDate'] ) && $attributes['displayPostDate'] ) {
				$list_items_markup .= sprintf(
					'<time datetime="%1$s" class="egb-block-post-grid-date">%2$s</time>',
					esc_attr( get_the_date( 'c', $post_id ) ),
					esc_html( get_the_date( '', $post_id ) )
				);
			}

				// Close the byline content
				$list_items_markup .= sprintf(
					'</div>'
				);

				// Wrap the excerpt content
				$list_items_markup .= sprintf(
					'<div class="egb-block-post-grid-excerpt">'
				);

					// Get the excerpt
					$excerpt = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $post_id, 'display' ) );

			if ( empty( $excerpt ) ) {
				$excerpt = apply_filters( 'the_excerpt', wp_trim_words( $post->post_content, 25 ) );
			}

			if ( ! $excerpt ) {
				$excerpt = null;
			}

			if ( isset( $attributes['displayPostExcerpt'] ) && $attributes['displayPostExcerpt'] ) {
				$list_items_markup .= wp_kses_post( $excerpt );
			}

			if ( isset( $attributes['displayPostLink'] ) && $attributes['displayPostLink'] ) {
				$list_items_markup .= sprintf(
					'<p><a class="egb-block-post-grid-link egb-text-link" href="%1$s" rel="bookmark">%2$s</a></p>',
					esc_url( get_permalink( $post_id ) ),
					esc_html( $attributes['readMoreText'] )
				);
			}

				// Close the excerpt content
				$list_items_markup .= sprintf(
					'</div>'
				);

			// Wrap the text content
			$list_items_markup .= sprintf(
				'</div>'
			);

			// Close the markup for the post
			$list_items_markup .= "</article>\n";
		}
	}

	// Build the classes
	$class = "egb-block-post-grid align{$attributes['align']}";

	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}

	$grid_class = 'egb-post-grid-items';

	if ( isset( $attributes['postLayout'] ) && 'list' === $attributes['postLayout'] ) {
		$grid_class .= ' is-list';
	} else {
		$grid_class .= ' is-grid';
	}

	if ( isset( $attributes['columns'] ) && 'grid' === $attributes['postLayout'] ) {
		$grid_class .= ' columns-' . $attributes['columns'];
	}

	// Output the post markup
	$block_content = sprintf(
		'<div class="%1$s"><div class="%2$s">%3$s</div></div>',
		esc_attr( $class ),
		esc_attr( $grid_class ),
		$list_items_markup
	);

	return $block_content;
}

/**
 * Registers the `core/latest-posts` block on server.
 */
function egb_blocks_register_block_core_latest_posts() {

	// Check if the register function exists
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type(
		'egb/egb-post-grid',
		[
			'attributes'      => [
				'categories' => [
					'type' => 'string',
				],
				'className' => [
					'type' => 'string',
				],
				'postsToShow' => [
					'type'    => 'number',
					'default' => 6,
				],
				'displayPostDate' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayPostExcerpt' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayPostAuthor' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayPostImage' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayPostLink' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'displayPostTitle' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'postLayout' => [
					'type'    => 'string',
					'default' => 'grid',
				],
				'columns' => [
					'type'    => 'number',
					'default' => 2,
				],
				'align' => [
					'type'    => 'string',
					'default' => 'center',
				],
				'width' => [
					'type'    => 'string',
					'default' => 'wide',
				],
				'order' => [
					'type'    => 'string',
					'default' => 'desc',
				],
				'orderBy' => [
					'type'    => 'string',
					'default' => 'date',
				],
				'imageCrop' => [
					'type'    => 'string',
					'default' => 'landscape',
				],
				'readMoreText' => [
					'type'    => 'string',
					'default' => 'Continue Reading',
				],
			],
			'render_callback' => 'egb_blocks_render_block_core_latest_posts',
		]
	);
}

add_action( 'init', 'egb_blocks_register_block_core_latest_posts' );


/**
 * Create API fields for additional info
 */
function egb_blocks_register_rest_fields() {
	// Add landscape featured image source
	register_rest_field(
		'post',
		'featured_image_src',
		[
			'get_callback'    => 'egb_blocks_get_image_src_landscape',
			'update_callback' => null,
			'schema'          => null,
		]
	);

	// Add square featured image source
	register_rest_field(
		'post',
		'featured_image_src_square',
		[
			'get_callback'    => 'egb_blocks_get_image_src_square',
			'update_callback' => null,
			'schema'          => null,
		]
	);

	// Add author info
	register_rest_field(
		'post',
		'author_info',
		[
			'get_callback'    => 'egb_blocks_get_author_info',
			'update_callback' => null,
			'schema'          => null,
		]
	);
}
add_action( 'rest_api_init', 'egb_blocks_register_rest_fields' );


/**
 * Get landscape featured image source for the rest field
 */
function egb_blocks_get_image_src_landscape( $object, $field_name, $request ) {
	$feat_img_array = wp_get_attachment_image_src(
		$object['featured_media'],
		'egb-block-post-grid-landscape',
		false
	);
	return $feat_img_array[0];
}

/**
 * Get square featured image source for the rest field
 */
function egb_blocks_get_image_src_square( $object, $field_name, $request ) {
	$feat_img_array = wp_get_attachment_image_src(
		$object['featured_media'],
		'egb-block-post-grid-square',
		false
	);
	return $feat_img_array[0];
}

/**
 * Get author info for the rest field
 */
function egb_blocks_get_author_info( $object, $field_name, $request ) {
	// Get the author name
	$author_data['display_name'] = get_the_author_meta( 'display_name', $object['author'] );

	// Get the author link
	$author_data['author_link'] = get_author_posts_url( $object['author'] );

	// Return the author data
	return $author_data;
}

function egb_post_grid_excerpt_more( $more ) {
	return '';
}
add_filter( 'excerpt_more', 'egb_post_grid_excerpt_more' );