<?php

require( dirname( __FILE__ ) . '/class-wp-hook.php' );

global $wp_filter, $wp_actions, $wp_current_filter;

if ($wp_filter) {
	$wp_filter = WP_Hook::build_preinitialized_hooks ( $wp_filter );
} else {
	$wp_filter = array ();
}

if (! isset ( $wp_actions ))
	$wp_actions = array ();

if (! isset ( $wp_current_filter ))
	$wp_current_filter = array ();
function has_action($tag, $function_to_check = false) {
	return has_filter ( $tag, $function_to_check );
}
function has_filter($tag, $function_to_check = false) {
	global $wp_filter;
	
	if (! isset ( $wp_filter [$tag] )) {
		return false;
	}
	
	return $wp_filter [$tag]->has_filter ( $tag, $function_to_check );
}
function _wp_call_all_hook($args) {
	global $wp_filter;
	
	$wp_filter ['all']->do_all_hook ( $args );
}


/**
 * Execute functions hooked on a specific filter hook, specifying arguments in an array.
 *
 * @since 3.0.0
 *
 * @see apply_filters() This function is identical, but the arguments passed to the
 * functions hooked to `$tag` are supplied using an array.
 *
 * @global array $wp_filter         Stores all of the filters
 * @global array $wp_current_filter Stores the list of current filters with the current one last
 *
 * @param string $tag  The name of the filter hook.
 * @param array  $args The arguments supplied to the functions hooked to $tag.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters_ref_array($tag, $args) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
global $wp_filter, $wp_current_filter;

// Do 'all' actions first
if ( isset($wp_filter['all']) ) {
	$wp_current_filter[] = $tag;
	$all_args = func_get_args();
	_wp_call_all_hook($all_args);
}

if ( !isset($wp_filter[$tag]) ) {
	if ( isset($wp_filter['all']) )
		array_pop($wp_current_filter);
		return $args[0];
}

if ( !isset($wp_filter['all']) )
	$wp_current_filter[] = $tag;
	
	$filtered = $wp_filter[ $tag ]->apply_filters( $args[0], $args );
	
	array_pop( $wp_current_filter );
	
	return $filtered;
}



/**
 * Execute functions hooked on a specific action hook, specifying arguments in an array.
 *
 * @since 2.1.0
 *
 * @see do_action() This function is identical, but the arguments passed to the
 *                  functions hooked to $tag< are supplied using an array.
 * @global array $wp_filter         Stores all of the filters
 * @global array $wp_actions        Increments the amount of times action was triggered.
 * @global array $wp_current_filter Stores the list of current filters with the current one last
 *
 * @param string $tag  The name of the action to be executed.
 * @param array  $args The arguments supplied to the functions hooked to `$tag`.
 */
function do_action_ref_array($tag, $args) {
global $wp_filter, $wp_actions, $wp_current_filter;

if ( ! isset($wp_actions[$tag]) )
	$wp_actions[$tag] = 1;
	else
		++$wp_actions[$tag];
		
		// Do 'all' actions first
		if ( isset($wp_filter['all']) ) {
			$wp_current_filter[] = $tag;
			$all_args = func_get_args();
			_wp_call_all_hook($all_args);
		}
		
		if ( !isset($wp_filter[$tag]) ) {
			if ( isset($wp_filter['all']) )
				array_pop($wp_current_filter);
				return;
		}
		
		if ( !isset($wp_filter['all']) )
			$wp_current_filter[] = $tag;
			
			$wp_filter[ $tag ]->do_action( $args );
			
			array_pop($wp_current_filter);
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
 * // Our filter callback function
 * function example_callback( $string, $arg1, $arg2 ) {
 * // (maybe) modify $string
 * return $string;
 * }
 * add_filter( 'example_filter', 'example_callback', 10, 3 );
 *
 * /*
 * * Apply the filters by calling the 'example_callback' function we
 * * "hooked" to 'example_filter' using the add_filter() function above.
 * * - 'example_filter' is the filter hook $tag
 * * - 'filter me' is the value being filtered
 * * - $arg1 and $arg2 are the additional arguments passed to the callback.
 * $value = apply_filters( 'example_filter', 'filter me', $arg1, $arg2 );
 *
 * @since 0.71
 *       
 * @global array $wp_filter Stores all of the filters.
 * @global array $wp_current_filter Stores the list of current filters with the current one last.
 *        
 * @param string $tag
 *        	The name of the filter hook.
 * @param mixed $value
 *        	The value on which the filters hooked to `$tag` are applied on.
 * @param mixed $var,...
 *        	Additional variables passed to the functions hooked to `$tag`.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters($tag, $value) {
	global $wp_filter, $wp_current_filter;
	
	$args = array ();
	
	// Do 'all' actions first.
	if (isset ( $wp_filter ['all'] )) {
		$wp_current_filter [] = $tag;
		$args = func_get_args ();
		_wp_call_all_hook ( $args );
	}
	
	if (! isset ( $wp_filter [$tag] )) {
		if (isset ( $wp_filter ['all'] ))
			array_pop ( $wp_current_filter );
		return $value;
	}
	
	if (! isset ( $wp_filter ['all'] ))
		$wp_current_filter [] = $tag;
	
	if (empty ( $args ))
		$args = func_get_args ();
	
	// don't pass the tag name to WP_Hook
	array_shift ( $args );
	
	$filtered = $wp_filter [$tag]->apply_filters ( $value, $args );
	
	array_pop ( $wp_current_filter );
	
	return $filtered;
}

function did_action($tag) {
	global $wp_actions;
	
	if ( ! isset( $wp_actions[ $tag ] ) )
		return 0;
	
	return $wp_actions[$tag];
}

function do_action($tag, $arg = '') {
	global $wp_filter, $wp_actions, $wp_current_filter;
	
	if (! isset ( $wp_actions [$tag] ))
		$wp_actions [$tag] = 1;
	else
		++ $wp_actions [$tag];
	
	// Do 'all' actions first
	if (isset ( $wp_filter ['all'] )) {
		$wp_current_filter [] = $tag;
		$all_args = func_get_args ();
		_wp_call_all_hook ( $all_args );
	}
	
	if (! isset ( $wp_filter [$tag] )) {
		if (isset ( $wp_filter ['all'] ))
			array_pop ( $wp_current_filter );
		return;
	}
	
	if (! isset ( $wp_filter ['all'] ))
		$wp_current_filter [] = $tag;
	
	$args = array ();
	if (is_array ( $arg ) && 1 == count ( $arg ) && isset ( $arg [0] ) && is_object ( $arg [0] )) // array(&$this)
		$args [] = & $arg [0];
	else
		$args [] = $arg;
	for($a = 2, $num = func_num_args (); $a < $num; $a ++)
		$args [] = func_get_arg ( $a );
	
	$wp_filter [$tag]->do_action ( $args );
	
	array_pop ( $wp_current_filter );
}


function current_action() {
	return current_filter();
}

function current_filter() {
global $wp_current_filter;
return end( $wp_current_filter );
}


function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	return add_filter ( $tag, $function_to_add, $priority, $accepted_args );
}
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter;
	if (! isset ( $wp_filter [$tag] )) {
		$wp_filter [$tag] = new WP_Hook ();
	}
	$wp_filter [$tag]->add_filter ( $tag, $function_to_add, $priority, $accepted_args );
	return true;
}



/**
 * Removes a function from a specified filter hook.
 *
 * This function removes a function attached to a specified filter hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * To remove a hook, the $function_to_remove and $priority arguments must match
 * when the hook was added. This goes for both filters and actions. No warning
 * will be given on removal failure.
 *
 * @since 1.2.0
 *
 * @global array $wp_filter         Stores all of the filters
 *
 * @param string   $tag                The filter hook to which the function to be removed is hooked.
 * @param callable $function_to_remove The name of the function which should be removed.
 * @param int      $priority           Optional. The priority of the function. Default 10.
 * @return bool    Whether the function existed before it was removed.
 */
function remove_filter( $tag, $function_to_remove, $priority = 10 ) {
global $wp_filter;

$r = false;
if ( isset( $wp_filter[ $tag ] ) ) {
	$r = $wp_filter[ $tag ]->remove_filter( $tag, $function_to_remove, $priority );
	if ( ! $wp_filter[ $tag ]->callbacks ) {
		unset( $wp_filter[ $tag ] );
	}
}

return $r;
}

/**
 * Remove all of the hooks from a filter.
 *
 * @since 2.7.0
 *
 * @global array $wp_filter  Stores all of the filters
 *
 * @param string   $tag      The filter to remove hooks from.
 * @param int|bool $priority Optional. The priority number to remove. Default false.
 * @return true True when finished.
 */
function remove_all_filters( $tag, $priority = false ) {
global $wp_filter;

if ( isset( $wp_filter[ $tag ]) ) {
	$wp_filter[ $tag ]->remove_all_filters( $priority );
	if ( ! $wp_filter[ $tag ]->has_filters() ) {
		unset( $wp_filter[ $tag ] );
	}
}

return true;
}

function _wp_filter_build_unique_id($tag, $function, $priority) {
global $wp_filter;
static $filter_id_count = 0;

if ( is_string($function) )
	return $function;
	
	if ( is_object($function) ) {
	// Closures are currently implemented as objects
	$function = array( $function, '' );
	} else {
		$function = (array) $function;
	}
	
	if (is_object($function[0]) ) {
	// Object Class Calling
	if ( function_exists('spl_object_hash') ) {
	return spl_object_hash($function[0]) . $function[1];
	} else {
		$obj_idx = get_class($function[0]).$function[1];
		if ( !isset($function[0]->wp_filter_id) ) {
		if ( false === $priority )
			return false;
			$obj_idx .= isset($wp_filter[$tag][$priority]) ? count((array)$wp_filter[$tag][$priority]) : $filter_id_count;
			$function[0]->wp_filter_id = $filter_id_count;
			++$filter_id_count;
		} else {
			$obj_idx .= $function[0]->wp_filter_id;
		}
		
		return $obj_idx;
	}
	} elseif ( is_string( $function[0] ) ) {
		
	}
	// Static Calling
	return $function[0] . '::' . $function[1];
	}

?>