<?php

function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
echo get_submit_button( $text, $type, $name, $wrap, $other_attributes );
}

/**
 * Returns a submit button, with provided text and appropriate class
 *
 * @since 3.1.0
 *
 * @param string       $text             Optional. The text of the button. Default 'Save Changes'.
 * @param string       $type             Optional. The type of button. Accepts 'primary', 'secondary',
 *                                       or 'delete'. Default 'primary large'.
 * @param string       $name             Optional. The HTML name of the submit button. Defaults to "submit".
 *                                       If no id attribute is given in $other_attributes below, `$name` will
 *                                       be used as the button's id. Default 'submit'.
 * @param bool         $wrap             Optional. True if the output button should be wrapped in a paragraph
 *                                       tag, false otherwise. Default true.
 * @param array|string $other_attributes Optional. Other attributes that should be output with the button,
 *                                       mapping attributes to their values, such as `array( 'tabindex' => '1' )`.
 *                                       These attributes will be output as `attribute="value"`, such as
 *                                       `tabindex="1"`. Other attributes can also be provided as a string such
 *                                       as `tabindex="1"`, though the array format is typically cleaner.
 *                                       Default empty.
 * @return string Submit button HTML.
 */
function get_submit_button( $text = '', $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = '' ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
if ( ! is_array( $type ) )
	$type = explode( ' ', $type );
	
	$button_shorthand = array( 'primary', 'small', 'large' );
	$classes = array( 'button' );
	foreach ( $type as $t ) {
		if ( 'secondary' === $t || 'button-secondary' === $t )
			continue;
			$classes[] = in_array( $t, $button_shorthand ) ? 'button-' . $t : $t;
	}
	// Remove empty items, remove duplicate items, and finally build a string.
	$class = implode( ' ', array_unique( array_filter( $classes ) ) );
	
	$text = $text ? $text : __( 'Save Changes' );
	
	// Default the id attribute to $name unless an id was specifically provided in $other_attributes
	$id = $name;
	if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
		$id = $other_attributes['id'];
		unset( $other_attributes['id'] );
	}
	
	$attributes = '';
	if ( is_array( $other_attributes ) ) {
		foreach ( $other_attributes as $attribute => $value ) {
			$attributes .= $attribute . '="' . esc_attr( $value ) . '" '; // Trailing space is important
		}
	} elseif ( ! empty( $other_attributes ) ) { // Attributes provided as a string
		$attributes = $other_attributes;
	}
	
	// Don't output empty name and id attributes.
	$name_attr = $name ? ' name="' . esc_attr( $name ) . '"' : '';
	$id_attr = $id ? ' id="' . esc_attr( $id ) . '"' : '';
	
	$button = '<input type="submit"' . $name_attr . $id_attr . ' class="' . esc_attr( $class );
	$button	.= '" value="' . esc_attr( $text ) . '" ' . $attributes . ' />';
	
	if ( $wrap ) {
		$button = '<p class="submit">' . $button . '</p>';
	}
	
	return $button;
}

?>