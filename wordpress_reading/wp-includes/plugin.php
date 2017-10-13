<?php

function has_action($tag, $function_to_check = false) {
	return has_filter($tag, $function_to_check);
}


function has_filter($tag, $function_to_check = false) {
	global $wp_filter;
	
	if ( ! isset( $wp_filter[ $tag ] ) ) {
		return false;
	}
	
	return $wp_filter[ $tag ]->has_filter( $tag, $function_to_check );
}


function _wp_call_all_hook($args) {
	global $wp_filter;
	
	$wp_filter['all']->do_all_hook( $args );
}

/**
 * Call the functions added to a filter hook.
 *
 * The callback functions attached to filter hook $tag are invoked by calling
 * this function. This function can be used to create a new filter hook by
 * simply calling this function with the name of the new hook specified using
 * the $tag parameter.
 *
 * The function allows for additional arguments to be added and passed to hooks.
 *
 *     // Our filter callback function
 *     function example_callback( $string, $arg1, $arg2 ) {
 *         // (maybe) modify $string
 *         return $string;
 *     }
 *     add_filter( 'example_filter', 'example_callback', 10, 3 );
 *
 *     /*
 *      * Apply the filters by calling the 'example_callback' function we
 *      * "hooked" to 'example_filter' using the add_filter() function above.
 *      * - 'example_filter' is the filter hook $tag
 *      * - 'filter me' is the value being filtered
 *      * - $arg1 and $arg2 are the additional arguments passed to the callback.
 *     $value = apply_filters( 'example_filter', 'filter me', $arg1, $arg2 );
 *
 * @since 0.71
 *
 * @global array $wp_filter         Stores all of the filters.
 * @global array $wp_current_filter Stores the list of current filters with the current one last.
 *
 * @param string $tag     The name of the filter hook.
 * @param mixed  $value   The value on which the filters hooked to `$tag` are applied on.
 * @param mixed  $var,... Additional variables passed to the functions hooked to `$tag`.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters( $tag, $value ) {
	global $wp_filter, $wp_current_filter;
	
	$args = array();
	
	// Do 'all' actions first.
	if ( isset($wp_filter['all']) ) {
		$wp_current_filter[] = $tag;
		$args = func_get_args();
		_wp_call_all_hook($args);
	}
	
	if ( !isset($wp_filter[$tag]) ) {
		if ( isset($wp_filter['all']) )
			array_pop($wp_current_filter);
			return $value;
	}
	
	if ( !isset($wp_filter['all']) )
		$wp_current_filter[] = $tag;
		
		if ( empty($args) )
			$args = func_get_args();
			
			// don't pass the tag name to WP_Hook
			array_shift( $args );
			
			$filtered = $wp_filter[ $tag ]->apply_filters( $value, $args );
			
			array_pop( $wp_current_filter );
			
			return $filtered;
}

?>