<?php

/**
 * Server Side Rendering for Social Share
 * Register the block on the server
 */
function egb_register_sharing() {
	// Check if the register function exists
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Register the sharing block
	register_block_type(
		'master-blocks/social-share',
		[
			'style'           => 'jltmb-style-css',
			'attributes'      => [
				'facebook' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'twitter' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'google' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'linkedin' => [
					'type'    => 'boolean',
					'default' => false,
				],
				'pinterest' => [
					'type'    => 'boolean',
					'default' => false,
				],
				'email' => [
					'type'    => 'boolean',
					'default' => false,
				],
				'reddit' => [
					'type'    => 'boolean',
					'default' => false,
				],
				'shareAlignment' => [
					'type' => 'string',
				],
				'shareButtonStyle' => [
					'type'    => 'string',
					'default' => 'jltmb-share-icon-text',
				],
				'shareButtonShape' => [
					'type'    => 'string',
					'default' => 'jltmb-share-shape-circular',
				],
				'shareButtonEffect' => [
					'type'    => 'string',
					'default' => 'jltmb-social-rounded',
				],
				'shareButtonSize' => [
					'type'    => 'string',
					'default' => 'jltmb-share-size-medium',
				],
				'shareButtonColor' => [
					'type'    => 'string',
					'default' => 'jltmb-share-color-standard',
				],
			],
			'render_callback' => 'egb_render_sharing',
		]
	);
}
add_action( 'init', 'egb_register_sharing' );


/**
 * Add the pop-up share window to the footer
 */
function egb_social_icon_footer_script() {  ?>
	<script type="text/javascript">
		function egbBlockShare(url, title, w, h) {
			var left = (window.innerWidth / 2) - (w / 2);
			var top = (window.innerHeight / 2) - (h / 2);
			return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=600, height=600, top=' + top + ', left=' + left);
		}
	</script>
	<?php
}
add_action( 'wp_footer', 'egb_social_icon_footer_script' );

/**
 * Render the sharing links
 */
function egb_render_sharing( $attributes ) {
	global $post;

	// Setup the featured image
	if ( has_post_thumbnail() ) {
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		$thumbnail    = $thumbnail_id ? current( wp_get_attachment_image_src( $thumbnail_id, 'large', true ) ) : '';
	} else {
		$thumbnail = null;
	}

	// Twitter share URL
	$twitter_url = 'http://twitter.com/share?text=' . get_the_title() . '&url=' . get_the_permalink() . '';

	// Facebook share URL
	$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink() . '&title=' . get_the_title() . '';

	// Google share URL
	$google_url = 'https://plus.google.com/share?url=' . get_the_permalink() . '';

	// LinkedIn share URL
	$linkedin_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . get_the_permalink() . '&title=' . get_the_title() . '';

	// Pinterest share URL
	$pinterest_url = 'https://pinterest.com/pin/create/button/?&url=' . get_the_permalink() . '&description=' . get_the_title() . '&media=' . esc_url( $thumbnail ) . '';

	// Email URL
	$email_url = 'mailto:?subject=' . get_the_title() . '&body=' . get_the_title() . '&mdash;' . get_the_permalink() . '';

	// Reddit URL
	$reddit_url = 'https://www.reddit.com/submit?url=' . get_the_permalink() . '';

	// Build the share URLs
	$share_url = '';

	if ( isset( $attributes['twitter'] ) && $attributes['twitter'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="javascript:void(0)"
					onClick="javascript:egbBlockShare(\'%1$s\', \'%2$s\', \'600\', \'600\')"
					class="jltmb-share-twitter"
					title="%2$s">
					<i class="fab fa-twitter"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $twitter_url ),
			esc_html__( 'Share on Twitter', 'atomic-blocks' )
		);
	}

	if ( isset( $attributes['facebook'] ) && $attributes['facebook'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="javascript:void(0)"
					onClick="javascript:egbBlockShare(\'%1$s\', \'%2$s\', \'600\', \'600\')"
					class="jltmb-share-facebook"
					title="%2$s">
					<i class="fab fa-facebook-f"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $facebook_url ),
			esc_html__( 'Share on Facebook', 'atomic-blocks' )
		);
	}

	if ( isset( $attributes['google'] ) && $attributes['google'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="javascript:void(0)"
					onClick="javascript:egbBlockShare(\'%1$s\', \'%2$s\', \'600\', \'600\')"
					class="jltmb-share-google"
					title="%2$s">
					<i class="fab fa-google"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $google_url ),
			esc_html__( 'Share on Google', 'atomic-blocks' )
		);
	}

	if ( isset( $attributes['pinterest'] ) && $attributes['pinterest'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="javascript:void(0)"
					onClick="javascript:egbBlockShare(\'%1$s\', \'%2$s\', \'600\', \'600\')"
					class="jltmb-share-pinterest"
					title="%2$s">
					<i class="fab fa-pinterest-p"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $pinterest_url ),
			esc_html__( 'Share on Pinterest', 'atomic-blocks' )
		);
	}

	if ( isset( $attributes['linkedin'] ) && $attributes['linkedin'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="javascript:void(0)"
					onClick="javascript:egbBlockShare(\'%1$s\', \'%2$s\', \'600\', \'600\')"
					class="jltmb-share-linkedin"
					title="%2$s">
					<i class="fab fa-linkedin-in"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $linkedin_url ),
			esc_html__( 'Share on LinkedIn', 'atomic-blocks' )
		);
	}

	if ( isset( $attributes['reddit'] ) && $attributes['reddit'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="javascript:void(0)"
					onClick="javascript:egbBlockShare(\'%1$s\', \'%2$s\', \'600\', \'600\')"
					class="jltmb-share-reddit"
					title="%2$s">
					<i class="fab fa-reddit-alien"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $reddit_url ),
			esc_html__( 'Share on Reddit', 'atomic-blocks' )
		);
	}

	if ( isset( $attributes['email'] ) && $attributes['email'] ) {
		$share_url .= sprintf(
			'<li>
				<a
					href="%1$s"
					class="jltmb-share-email"
					title="%2$s">
					<i class="fas fa-envelope"></i> <span class="jltmb-social-text">%2$s</span>
				</a>
			</li>',
			esc_url( $email_url ),
			esc_html__( 'Share via Email', 'atomic-blocks' )
		);
	}

	// Render the list of share links
	$block_content = sprintf(
		'<div class="wp-block-jltmb-jltmb-sharing jltmb-block-sharing %2$s %3$s %4$s %5$s %6$s">
			<ul class="jltmb-share-list">%1$s</ul>
		</div>',
		$share_url,
		isset( $attributes['shareButtonStyle'] ) ? $attributes['shareButtonStyle'] : null,
		isset( $attributes['shareButtonShape'] ) ? $attributes['shareButtonShape'] : null,
		isset( $attributes['shareButtonEffect'] ) ? $attributes['shareButtonEffect'] : null,
		isset( $attributes['shareButtonSize'] ) ? $attributes['shareButtonSize'] : null,
		isset( $attributes['shareButtonColor'] ) ? $attributes['shareButtonColor'] : null,
		isset( $attributes['shareAlignment'] ) ? $attributes['shareAlignment'] : null
	);

	return $block_content;
}