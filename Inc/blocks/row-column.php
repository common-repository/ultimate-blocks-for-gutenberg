<?php


class Easy_Blocks_Gutenberg_Row_Columns {


	private static $instance;

	/* Initialize */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new Easy_Blocks_Gutenberg_Row_Columns();
		}
	}


	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'init', [ $this, 'egb_init' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'egb_frontend_inline_css' ], 20 );
		}
	}

	/**
	 * Parse blocks
	 */
	public function jltmb_parse_blocks( $content ) {
		$parser_class = apply_filters( 'block_parser_class', 'WP_Block_Parser' );
		if ( class_exists( $parser_class ) ) {
			$parser = new $parser_class();
			return $parser->parse( $content );
		} elseif ( function_exists( 'gutenberg_parse_blocks' ) ) {
			return gutenberg_parse_blocks( $content );
		} else {
			return false;
		}
	}


	public function egb_frontend_inline_css() {

		global $post;
		if ( ! is_object( $post ) ) {
			return;
		}

		$blocks = $this->jltmb_parse_blocks( $post->post_content );
		// print_r( $blocks );
		if ( ! is_array( $blocks ) || empty( $blocks ) ) {
			return;
		}

		foreach ( $blocks as $indexkey => $block ) {
			if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {
				if ( 'master-blocks/row-column' === $block['blockName'] ) {
					if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
						$blockattr = $block['attrs'];
						$this->render_row_layout_css_head( $blockattr );
						// $this->render_row_layout_scripts( $blockattr );
					}
				}
				if ( 'master-blocks/column' === $block['blockName'] ) {
					if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
						$blockattr = $block['attrs'];
						$this->render_column_layout_css_head( $blockattr );
					}
				}
			}
		}
	}


	public function render_row_layout_css_head( $attributes ) {
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id  = 'egb-blocks ' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $this->egb_row_layout_css_render( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->egb_render_inline_css( $css, $style_id );
				}
			}
		}
	}




	// Frontend Render Init
	public function egb_init() {
		// If Gutenberg is available then load only
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'master-blocks/row-column',
			[
				'render_callback' => [ $this, 'egb_row_layout_css_render' ],
			]
		);
	}


	public function egb_row_layout_css_render( $attributes, $content ) {
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id  = 'egb-row-column-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $this->egb_row_layout_array_css( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->egb_render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}


	public function egb_render_inline_css( $css, $style_id, $in_content = false ) {
		if ( ! is_admin() ) {
			wp_register_style( $style_id, false );
			wp_enqueue_style( $style_id );
			wp_add_inline_style( $style_id, $css );
			if ( 1 === did_action( 'wp_head' ) && $in_content ) {
				wp_print_styles( $style_id );
			}
		}
	}

	public function egb_row_layout_array_css( $attr, $unique_id ) {
		$css = '';
		if ( isset( $attr['bgColor'] ) || isset( $attr['bgImg'] ) || isset( $attr['topMargin'] ) || isset( $attr['height'] ) || isset( $attr['bottomMargin'] ) ) {
			$css .= '#egb-layout-id' . $unique_id . ' {';
			if ( isset( $attr['topMargin'] ) ) {
				$css .= 'margin-top:' . $attr['topMargin'] . 'px;';
			}
			if ( isset( $attr['bottomMargin'] ) ) {
				$css .= 'margin-bottom:' . $attr['bottomMargin'] . 'px;';
			}
			if ( isset( $attr['bgColor'] ) ) {
				$css .= 'background-color:' . $attr['bgColor'] . ';';
			}

			if ( isset( $attr['height'] ) ) {
				$css .= 'height:' . $attr['height'] . 'px;';
			}

			if ( isset( $attr['bgImg'] ) ) {
				if ( isset( $attr['bgImgAttachment'] ) ) {
					if ( 'parallax' === $attr['bgImgAttachment'] ) {
						$bg_attach = 'fixed';
					} else {
						$bg_attach = $attr['bgImgAttachment'];
					}
				} else {
					$bg_attach = 'scroll';
				}
				$css .= 'background-image:url(' . $attr['bgImg'] . ');';
				$css .= 'background-size:' . ( isset( $attr['bgImgSize'] ) ? $attr['bgImgSize'] : 'cover' ) . ';';
				$css .= 'background-position:' . ( isset( $attr['bgImgPosition'] ) ? $attr['bgImgPosition'] : 'center center' ) . ';';
				$css .= 'background-attachment:' . $bg_attach . ';';
				$css .= 'background-repeat:' . ( isset( $attr['bgImgRepeat'] ) ? $attr['bgImgRepeat'] : 'no-repeat' ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['textColor'] ) ) {
			$css .= '.egb-layout-id' . $unique_id . ', .egb-layout-id' . $unique_id . ' h1, .egb-layout-id' . $unique_id . ' h2, .egb-layout-id' . $unique_id . ' h3, .egb-layout-id' . $unique_id . ' h4, .egb-layout-id' . $unique_id . ' h5, .egb-layout-id' . $unique_id . ' h6 {';
			$css .= 'color:' . $attr['textColor'] . ';';
			$css .= '}';
		}
		if ( isset( $attr['linkColor'] ) ) {
			$css .= '.egb-layout-id' . $unique_id . ' a {';
			$css .= 'color:' . $attr['linkColor'] . ';';
			$css .= '}';
		}
		if ( isset( $attr['linkHoverColor'] ) ) {
			$css .= '.egb-layout-id' . $unique_id . ' a:hover {';
			$css .= 'color:' . $attr['linkHoverColor'] . ';';
			$css .= '}';
		}
		if ( isset( $attr['bottomSep'] ) && 'none' != $attr['bottomSep'] ) {
			if ( isset( $attr['bottomSepHeight'] ) || isset( $attr['bottomSepWidth'] ) ) {
				if ( isset( $attr['bottomSepHeight'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-bottom-sep {';
					$css .= 'height:' . $attr['bottomSepHeight'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['bottomSepWidth'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-bottom-sep svg {';
					$css .= 'width:' . $attr['bottomSepWidth'] . '%;';
					$css .= '}';
				}
			}
			if ( isset( $attr['bottomSepHeightTablet'] ) || isset( $attr['bottomSepWidthTablet'] ) ) {
				$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
				if ( isset( $attr['bottomSepHeightTablet'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-bottom-sep {';
					$css .= 'height:' . $attr['bottomSepHeightTablet'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['bottomSepWidthTablet'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-bottom-sep svg {';
					$css .= 'width:' . $attr['bottomSepWidthTablet'] . '%;';
					$css .= '}';
				}
				$css .= '}';
			}
			if ( isset( $attr['bottomSepHeightMobile'] ) || isset( $attr['bottomSepWidthMobile'] ) ) {
				$css .= '@media (max-width: 767px) {';
				if ( isset( $attr['bottomSepHeightMobile'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-bottom-sep {';
					$css .= 'height:' . $attr['bottomSepHeightMobile'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['bottomSepWidthMobile'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-bottom-sep svg {';
					$css .= 'width:' . $attr['bottomSepWidthMobile'] . '%;';
					$css .= '}';
				}
				$css .= '}';
			}
		}
		if ( isset( $attr['topSep'] ) && 'none' != $attr['topSep'] ) {
			if ( isset( $attr['topSepHeight'] ) || isset( $attr['topSepWidth'] ) ) {
				if ( isset( $attr['topSepHeight'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-top-sep {';
					$css .= 'height:' . $attr['topSepHeight'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['topSepWidth'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-top-sep svg {';
					$css .= 'width:' . $attr['topSepWidth'] . '%;';
					$css .= '}';
				}
			}
			if ( isset( $attr['topSepHeightTablet'] ) || isset( $attr['topSepWidthTablet'] ) ) {
				$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
				if ( isset( $attr['topSepHeightTablet'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-top-sep {';
					$css .= 'height:' . $attr['topSepHeightTablet'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['topSepWidthTablet'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-top-sep svg {';
					$css .= 'width:' . $attr['topSepWidthTablet'] . '%;';
					$css .= '}';
				}
				$css .= '}';
			}
			if ( isset( $attr['topSepHeightMobile'] ) || isset( $attr['topSepWidthMobile'] ) ) {
				$css .= '@media (max-width: 767px) {';
				if ( isset( $attr['topSepHeightMobile'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-top-sep {';
					$css .= 'height:' . $attr['topSepHeightMobile'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['topSepWidthMobile'] ) ) {
					$css .= '#egb-layout-id' . $unique_id . ' .egb-row-layout-top-sep svg {';
					$css .= 'width:' . $attr['topSepWidthMobile'] . '%;';
					$css .= '}';
				}
				$css .= '}';
			}
		}
		if ( isset( $attr['topPadding'] ) || isset( $attr['bottomPadding'] ) || isset( $attr['leftPadding'] ) || isset( $attr['rightPadding'] ) || isset( $attr['minHeight'] ) || isset( $attr['maxWidth'] ) ) {
			$css .= '#egb-layout-id' . $unique_id . ' > .egb-row-column-wrap {';
			if ( isset( $attr['topPadding'] ) ) {
				$css .= 'padding-top:' . $attr['topPadding'] . 'px;';
			}
			if ( isset( $attr['bottomPadding'] ) ) {
				$css .= 'padding-bottom:' . $attr['bottomPadding'] . 'px;';
			}
			if ( isset( $attr['leftPadding'] ) ) {
				$css .= 'padding-left:' . $attr['leftPadding'] . 'px;';
			}
			if ( isset( $attr['rightPadding'] ) ) {
				$css .= 'padding-right:' . $attr['rightPadding'] . 'px;';
			}
			if ( isset( $attr['minHeight'] ) ) {
				$css .= 'min-height:' . $attr['minHeight'] . 'px;';
			}
			if ( isset( $attr['maxWidth'] ) ) {
				$css .= 'max-width:' . $attr['maxWidth'] . 'px;';
				$css .= 'margin-left:auto;';
				$css .= 'margin-right:auto;';
			}
			$css .= '}';
		}
		if ( isset( $attr['overlay'] ) || isset( $attr['overlayBgImg'] ) || isset( $attr['overlaySecond'] ) ) {
			$css .= '#egb-layout-id' . $unique_id . ' > .egb-row-layout-overlay {';
			if ( isset( $attr['overlayOpacity'] ) ) {
				if ( $attr['overlayOpacity'] < 10 ) {
					$css .= 'opacity:0.0' . $attr['overlayOpacity'] . ';';
				} elseif ( $attr['overlayOpacity'] >= 100 ) {
					$css .= 'opacity:1;';
				} else {
					$css .= 'opacity:0.' . $attr['overlayOpacity'] . ';';
				}
			}
			if ( isset( $attr['currentOverlayTab'] ) && 'grad' == $attr['currentOverlayTab'] ) {
				$type = ( isset( $attr['overlayGradType'] ) ? $attr['overlayGradType'] : 'linear' );
				if ( 'radial' === $type ) {
					$angle = ( isset( $attr['overlayBgImgPosition'] ) ? 'at ' . $attr['overlayBgImgPosition'] : 'at center center' );
				} else {
					$angle = ( isset( $attr['overlayGradAngle'] ) ? $attr['overlayGradAngle'] . 'deg' : '180deg' );
				}
				$loc         = ( isset( $attr['overlayGradLoc'] ) ? $attr['overlayGradLoc'] : '0' );
				$color       = ( isset( $attr['overlay'] ) ? $attr['overlay'] : 'transparent' );
				$locsecond   = ( isset( $attr['overlayGradLocSecond'] ) ? $attr['overlayGradLocSecond'] : '100' );
				$colorsecond = ( isset( $attr['overlaySecond'] ) ? $attr['overlaySecond'] : '#00B5E2' );
				$css        .= 'background-image: ' . $type . '-gradient(' . $angle . ', ' . $color . ' ' . $loc . '%, ' . $colorsecond . ' ' . $locsecond . '%);';
			} else {
				if ( isset( $attr['overlay'] ) ) {
					$css .= 'background-color:' . $attr['overlay'] . ';';
				}
				if ( isset( $attr['overlayBgImg'] ) ) {
					if ( isset( $attr['overlayBgImgAttachment'] ) ) {
						if ( 'parallax' === $attr['overlayBgImgAttachment'] ) {
							$overbg_attach = 'fixed';
						} else {
							$overbg_attach = $attr['overlayBgImgAttachment'];
						}
					} else {
						$overbg_attach = 'scroll';
					}
					$css .= 'background-image:url(' . $attr['overlayBgImg'] . ');';
					$css .= 'background-size:' . ( isset( $attr['overlayBgImgSize'] ) ? $attr['overlayBgImgSize'] : 'cover' ) . ';';
					$css .= 'background-position:' . ( isset( $attr['overlayBgImgPosition'] ) ? $attr['overlayBgImgPosition'] : 'center center' ) . ';';
					$css .= 'background-attachment:' . $overbg_attach . ';';
					$css .= 'background-repeat:' . ( isset( $attr['overlayBgImgRepeat'] ) ? $attr['overlayBgImgRepeat'] : 'no-repeat' ) . ';';
				}
			}
			if ( isset( $attr['overlayBlendMode'] ) ) {
				$css .= 'mix-blend-mode:' . $attr['overlayBlendMode'] . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['topPaddingM'] ) || isset( $attr['bottomPaddingM'] ) || isset( $attr['leftPaddingM'] ) || isset( $attr['rightPaddingM'] ) || isset( $attr['topMarginM'] ) || isset( $attr['bottomMarginM'] ) ) {
			$css .= '@media (max-width: 767px) {';
			if ( isset( $attr['topMarginM'] ) || isset( $attr['bottomMarginM'] ) ) {
				$css .= '#egb-layout-id' . $unique_id . ' {';
				if ( isset( $attr['topMarginM'] ) ) {
					$css .= 'margin-top:' . $attr['topMarginM'] . 'px;';
				}
				if ( isset( $attr['bottomMarginM'] ) ) {
					$css .= 'margin-bottom:' . $attr['bottomMarginM'] . 'px;';
				}
				$css .= '}';
			}
			if ( isset( $attr['topPaddingM'] ) || isset( $attr['bottomPaddingM'] ) || isset( $attr['leftPaddingM'] ) || isset( $attr['rightPaddingM'] ) ) {
				$css .= '#egb-layout-id' . $unique_id . ' > .egb-row-column-wrap {';
				if ( isset( $attr['topPaddingM'] ) ) {
					$css .= 'padding-top:' . $attr['topPaddingM'] . 'px;';
				}
				if ( isset( $attr['bottomPaddingM'] ) ) {
					$css .= 'padding-bottom:' . $attr['bottomPaddingM'] . 'px;';
				}
				if ( isset( $attr['leftPaddingM'] ) ) {
					$css .= 'padding-left:' . $attr['leftPaddingM'] . 'px;';
				}
				if ( isset( $attr['rightPaddingM'] ) ) {
					$css .= 'padding-right:' . $attr['rightPaddingM'] . 'px;';
				}
				$css .= '}';
			}
			$css .= '}';
		}
		return $css;
	}
}