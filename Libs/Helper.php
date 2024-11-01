<?php
namespace JLTMB\Libs;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Helper Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */

if ( ! class_exists( 'Helper' ) ) {
	/**
	 * Helper class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Helper {

		public static function jltmb_valid(){
			return '<span class="jltmb-valid"><i class="dashicons-before dashicons-yes"></i></span>';
		}

		public static function jltmb_invalid(){
			return '<span class="jltmb-valid"><i class="dashicons-before dashicons-yes"></i></span>';
		}


		/**
		 * Remove spaces from Plugin Slug
		 */
		public static function jltmb_slug_cleanup() {
			return str_replace( '-', '_', strtolower( JLTMB_SLUG ) );
		}

		/**
		 * Function current_datetime() compability for wp version < 5.3
		 *
		 * @return DateTimeImmutable
		 */
		public static function jltmb_current_datetime() {
			if ( function_exists( 'current_datetime' ) ) {
				return current_datetime();
			}

			return new \DateTimeImmutable( 'now', self::jltmb_wp_timezone() );
		}

		/**
		 * Function jltmb_wp_timezone() compability for wp version < 5.3
		 *
		 * @return DateTimeZone
		 */
		public static function jltmb_wp_timezone() {
			if ( function_exists( 'wp_timezone' ) ) {
				return wp_timezone();
			}

			return new \DateTimeZone( self::jltmb_wp_timezone_string() );
		}

		/**
		 * API Endpoint
		 *
		 * @return string
		 */
		public static function api_endpoint() {
			$api_endpoint_url = 'https://bo.jeweltheme.com';
			$api_endpoint     = apply_filters( 'jltmb_endpoint', $api_endpoint_url );

			return trailingslashit( $api_endpoint );
		}

		/**
		 * CRM Endpoint
		 *
		 * @return string
		 */
		public static function crm_endpoint() {
			$crm_endpoint_url = 'https://bo.jeweltheme.com/wp-json/jlt-api/v1/subscribe'; // Endpoint .
			$crm_endpoint     = apply_filters( 'jltmb_crm_crm_endpoint', $crm_endpoint_url );

			return trailingslashit( $crm_endpoint );
		}

		/**
		 * CRM Endpoint
		 *
		 * @return string
		 */
		public static function crm_survey_endpoint() {
			$crm_feedback_endpoint_url = 'https://bo.jeweltheme.com/wp-json/jlt-api/v1/survey'; // Endpoint .
			$crm_feedback_endpoint     = apply_filters( 'jltmb_crm_crm_endpoint', $crm_feedback_endpoint_url );

			return trailingslashit( $crm_feedback_endpoint );
		}

		/**
		 * Function jltmb_wp_timezone_string() compability for wp version < 5.3
		 *
		 * @return string
		 */
		public static function jltmb_wp_timezone_string() {
			$timezone_string = get_option( 'timezone_string' );

			if ( $timezone_string ) {
				return $timezone_string;
			}

			$offset  = (float) get_option( 'gmt_offset' );
			$hours   = (int) $offset;
			$minutes = ( $offset - $hours );

			$sign      = ( $offset < 0 ) ? '-' : '+';
			$abs_hour  = abs( $hours );
			$abs_mins  = abs( $minutes * 60 );
			$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

			return $tz_offset;
		}

		/**
		 * Get Merged Data
		 *
		 * @param [type] $data .
		 * @param string $start_date .
		 * @param string $end_data .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function get_merged_data( $data, $start_date = '', $end_data = '' ) {
			$_data = shortcode_atts(
				array(
					'image_url'        => JLTMB_IMAGES . '/promo-image.png',
					'start_date'       => $start_date,
					'end_date'         => $end_data,
					'counter_time'     => '',
					'is_campaign'      => 'false',
					'button_text'      => 'Get Premium',
					'button_url'       => 'https://jeweltheme.com',
					'btn_color'        => '#CC22FF',
					'notice'           => '',
					'notice_timestamp' => '',
				),
				$data
			);

			if ( empty( $_data['image_url'] ) ) {
				$_data['image_url'] = JLTMB_IMAGES . '/promo-image.png';
			}

			return $_data;
		}


		/**
		 * Build Color
		 */
		public static function build_color($id, $value, $label)
		{
			return [
				'id' => $id,
				'value' => $value,
				'label' => $label
			];
		}

		/**
		 * Build Typography Data
		 */
		public static function build_typography_data($value = [])
		{
			return array_replace_recursive([
				'textFont' =>  [
					'font' => '',
					'weight' => '',
				],
				'textSize' =>  [
					'desktop' => '',
					'tablet' => '',
					'mobile' => '',
				],
				'textTransform' => '',
				'textStyle' => '',
				'textDecoration' => '',
				'textLineHeight' => [
					'desktop' => '',
					'tablet' => '',
					'mobile' => '',
				],
				'textLetterSpacing' => [
					'desktop' => '',
					'tablet' => '',
					'mobile' => '',
				],
				'textWordSpacing' => [
					'desktop' => '',
					'tablet' => '',
					'mobile' => '',
				]
			], $value);
		}


		/**
		 * Build Typography Data
		 */
		public static function build_typography($id, $value, $label, $tagName)
		{
			return [
				'id' => $id,
				'value' => self::build_typography_data($value),
				'label' => $label,
				'tagName' => $tagName
			];
		}


		/**
		 * wp_kses attributes map
		 *
		 * @param array $attrs .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function wp_kses_atts_map( array $attrs ) {
			return array_fill_keys( array_values( $attrs ), true );
		}

		/**
		 * Custom method
		 *
		 * @param [type] $content .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function wp_kses_custom( $content ) {
			$allowed_tags = wp_kses_allowed_html( 'post' );

			$custom_tags = array(
				'select'         => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'width', 'height', 'title', 'data', 'name', 'autofocus', 'disabled', 'multiple', 'required', 'size' ) ),
				'input'          => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'width', 'height', 'title', 'data', 'name', 'autofocus', 'disabled', 'required', 'size', 'type', 'checked', 'readonly', 'placeholder', 'value', 'maxlength', 'min', 'max', 'multiple', 'pattern', 'step', 'autocomplete' ) ),
				'textarea'       => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'width', 'height', 'title', 'data', 'name', 'autofocus', 'disabled', 'required', 'rows', 'cols', 'wrap', 'maxlength' ) ),
				'option'         => self::wp_kses_atts_map( array( 'class', 'id', 'label', 'disabled', 'label', 'selected', 'value' ) ),
				'optgroup'       => self::wp_kses_atts_map( array( 'disabled', 'label', 'class', 'id' ) ),
				'form'           => self::wp_kses_atts_map( array( 'class', 'id', 'data', 'style', 'width', 'height', 'accept-charset', 'action', 'autocomplete', 'enctype', 'method', 'name', 'novalidate', 'rel', 'target' ) ),
				'svg'            => self::wp_kses_atts_map( array( 'class', 'xmlns', 'viewbox', 'width', 'height', 'fill', 'aria-hidden', 'aria-labelledby', 'role' ) ),
				'rect'           => self::wp_kses_atts_map( array( 'rx', 'width', 'height', 'fill' ) ),
				'path'           => self::wp_kses_atts_map( array( 'd', 'fill' ) ),
				'g'              => self::wp_kses_atts_map( array( 'fill' ) ),
				'defs'           => self::wp_kses_atts_map( array( 'fill' ) ),
				'linearGradient' => self::wp_kses_atts_map( array( 'id', 'x1', 'x2', 'y1', 'y2', 'gradientUnits' ) ),
				'stop'           => self::wp_kses_atts_map( array( 'stop-color', 'offset', 'stop-opacity' ) ),
				'style'          => self::wp_kses_atts_map( array( 'type' ) ),
				'div'            => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'ul'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'li'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'label'          => self::wp_kses_atts_map( array( 'class', 'for' ) ),
				'span'           => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h1'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h2'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h3'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h4'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h5'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'h6'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'a'              => self::wp_kses_atts_map( array( 'class', 'href', 'target', 'rel' ) ),
				'p'              => self::wp_kses_atts_map( array( 'class', 'id', 'style', 'data' ) ),
				'table'          => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'thead'          => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'tbody'          => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'tr'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'th'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'td'             => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'i'              => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'button'         => self::wp_kses_atts_map( array( 'class', 'id' ) ),
				'nav'            => self::wp_kses_atts_map( array( 'class', 'id', 'style' ) ),
				'time'           => self::wp_kses_atts_map( array( 'datetime' ) ),
				'br'             => array(),
				'strong'         => array(),
				'style'          => array(),
				'img'            => self::wp_kses_atts_map( array( 'class', 'src', 'alt', 'height', 'width', 'srcset', 'id', 'loading' ) ),
			);

			$allowed_tags = array_merge_recursive( $allowed_tags, $custom_tags );

			return wp_kses( stripslashes_deep( $content ), $allowed_tags );
		}

		/**
		 * Tooltip Icon & Info
		 *
		 * @param [type] $info_name
		 * @param [type] $info_url
		 * @param [type] $info_icon
		 *
		 * @return void
		 */
		public static function jltmb_admin_tooltip_info( $info_name, $info_url, $info_icon ) {
			if ( ! empty( $info_url ) ) { ?>
				<div class="jltmb-tooltip-item tooltip-top">
					<i class="<?php echo esc_attr( $info_icon ); ?>"></i>
					<div class="jltmb-tooltip-text">
						<a href="<?php echo esc_url( $info_url ); ?>" class="jltmb-tooltip-content" target="_blank">
							<?php echo sprintf( esc_html__( '%s', 'ultimate-blocks-for-gutenberg' ), $info_name ); ?>
						</a>
					</div>
				</div>
				<?php
			}
		}


		public static function is_plugin_installed( $plugin_slug, $plugin_file ) {
			$installed_plugins = get_plugins();
			return isset( $installed_plugins[ $plugin_file ] );
		}

	}
}