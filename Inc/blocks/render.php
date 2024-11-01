<?php
register_block_type(
	'master-blocks/instagram',
	[
		'render_callback' => 'easy_instagram_render_callback',
		'attributes'      => [
			'numCols' => [
				'type'    => 'number',
				'default' => '4', // nb: a default is needed!
			],
			'token' => [
				'type'    => 'string',
				'default' => '',
			],
			'useThumbnail' => [
				'type'    => 'boolean',
				'default' => false,
			],
			'numImages' => [
				'type'    => 'number',
				'default' => 4,
			],
			'gridGap' => [
				'type'    => 'number',
				'default' => 0,
			],
			'showProfile' => [
				'type'    => 'boolean',
				'default' => false,
			],
			'backgroundColor' => [
				'type'    => 'string',
				'default' => 'transparent',
			],
		],
	]
);

/**
 * Generic data fetching wrapper
 * Uses the WP-API for fetching
 */
function easy_fetchData( $url ) {
	$request = wp_remote_get( $url );

	if ( is_wp_error( $request ) ) {
		return false;
	}

	return wp_remote_retrieve_body( $request );
}

/**
 * Caching functions
 * The number of images is used as a suffix in the case that the user
 * adds/removes images and expects a refreshed feed.
 */
function easy_add_to_cache( $result, $suffix = '' ) {
	$expire = 6 * 60 * 60; // 6 hours in seconds
	set_transient( 'jltmb_api_' . $suffix, $result, '', $expire );
}

function easy_get_from_cache( $suffix = '' ) {
	return get_transient( 'jltmb_api_' . $suffix );
}

/**
 * Server side rendering functions
 */
function easy_instagram_render_callback( array $attributes ) {
	$token        = $attributes['token'];
	$useThumbnail = $attributes['useThumbnail'] ? 'use-thumbnail' : '';
	$numImages    = $attributes['numImages'];
	$numCols      = $attributes['numCols'];
	$gridGap      = $attributes['gridGap'];
	$showProfile  = $attributes['showProfile'];

	// get the user ID from the token
	$user = substr( $token, 0, stripos( $token, '.' ) );

	// create a unique id so there is no double ups
	$suffix = $user . '_' . $numImages;

	if ( ! easy_get_from_cache() ) {
		// no valid cache found
		// hit the network
		$result = json_decode( easy_fetchData( "https://api.instagram.com/v1/users/self/media/recent/?access_token={$token}&count={$numImages}" ) );
		if ( $showProfile ) {
			$result->profile = json_decode( easy_fetchData( "https://api.instagram.com/v1/users/self?access_token={$token}" ) );
		}
		easy_add_to_cache( $result, $suffix ); // add the result to the cache
	} else {
		$result = easy_get_from_cache( $suffix ); // hit the cache
	}

	$thumbs  = $result->data;
	$profile = ''; // our empty profile container

	if ( $showProfile ) {
		$profile = $result->profile->data;

		$profileContainer = '<a href="https://instagram.com/' . $profile->username . '" target="_blank" class="display-grid easy-instagram-container">
			<div class="easy-instagram-picture-container">
				<img
					class="instagram_profile_picture"
					src="' . esc_attr( $profile->profile_picture ) . '"
					alt="' . esc_attr( $profile->full_name ) . '"
				/>
			</div>
			<div class="easy-instagram-bio-container">
				<h3>' . $profile->username . '</h3>
				<p>' . $profile->bio . '</p>
			</div>
		</a>';
		return "{$profileContainer}";
	}

	$imageContainer = '<div class="wp-block-jltmb-instagram">
	<div class="display-grid easy-instagram-grid ' . $useThumbnail . '"
	style="grid-template-columns: repeat(' . esc_attr( $numCols ) . ', 1fr);
	margin-left: -' . esc_attr( $gridGap ) . 'px;
	margin-right: -' . esc_attr( $gridGap ) . 'px;
	grid-gap: ' . esc_attr( $gridGap ) . 'px";
	>';

	foreach ( $thumbs as $thumb ) {
		$image = esc_attr( $thumb->images->standard_resolution->url );

		$imageContainer .= '
		<a class="easy_instagram_wrapper" href="' . esc_attr( $thumb->link ) . '"
		target="_blank" rel="noopener noreferrer"
		style="background-color: ' . esc_attr( $attributes['backgroundColor'] ) . '">
			<img
			class="easy_image"
			key="' . esc_attr( $thumb->id ) . '"
			src="' . $image . '"
			alt="' . esc_attr( $thumb->caption->text ) . '"
			/>
			<div class="easy_image_overlay"></div>
		</a>';
	}

	return "{$imageContainer}</div></div>";
}