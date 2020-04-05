<?php
extract(shortcode_atts(array(
	'title'               => '',
	'link'                => '',
	'target'			  => '',
	'color'               => '',
	'button_custom_color' => '',
	'size'                => '',
	'position'			  => '',
	'margin_top'          => '',
	'margin_bottom'       => '',
	'margin_left'         => '',
	'margin_right'        => '',
	'el_class'            => '',
), $atts));


$href = $button_target = null;
// Button Link
if ( $link !== '' ) { $href = vc_build_link($link); }

if ( $target ) $button_target = 'target="_blank"';

// Button Class
$button_class = array();

	if ( $size ) {
		$button_class[] = ' btn-'.$size;
	}

	if ( $color == 'light' ) {
		$button_class[] = ' btn-light';
	}

	if ( $color == 'ghost' ) {
		$button_class[] = ' btn-ghost';
	}

	if ( $color == 'dark' ) {
		$button_class[] = ' btn-dark';
	}

	if ( $color == 'primary' ) {
		$button_class[] = ' btn-primary';
	}

	if ( $color == 'secondary' ) {
		$button_class[] = ' btn-secondary';
	}

	if ( $color == 'custom' ) {
		$button_class[] = ' btn-custom';
	}

	if ( $el_class) {
		$button_class[] = ' '.esc_attr($el_class);
	}

$button_class = implode('', $button_class);

// Button Style
$button_styles = array();

	if ( $margin_top || $margin_top == '0' ) {
		$button_styles[] = 'margin-top: ' . intval($margin_top) . 'px;';
	}
	
	if ( $margin_bottom || $margin_bottom == '0' ) {
		$button_styles[] = 'margin-bottom: ' . intval($margin_bottom) . 'px;';
	}

	if ( $margin_left || $margin_left == '0') {
		$button_styles[] = 'margin-left: ' . intval($margin_left) . 'px;';
	}

	if ( $margin_right || $margin_right == '0' ) {
		$button_styles[] = 'margin-right: ' . intval($margin_right) . 'px;';
	}
	
	if ( $color == 'custom' && $button_custom_color ) {
		$button_styles[] = 'background-color: ' . $button_custom_color . ';';
	}

$button_styles = implode('', $button_styles);

if ( $button_styles ) {
	//$button_styles = wp_kses( $button_styles, array() );
	$button_styles = ' style="' . esc_attr($button_styles) . '"';
}

if ( $position == 'center') echo '
<div class="text-center">';

if ( $position == 'right') echo '
<div class="text-right">';

echo '<a '. $button_target .' href="'. esc_url($href['url']) .'" class="btn '. $button_class .'"'. $button_styles .'>'. esc_attr($title) .'</a>';

if ( $position == 'center') echo '
</div>';

if ( $position == 'right') echo '
</div>';


