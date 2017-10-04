<?php


function wp_unslash( $value ) {
	return stripslashes_deep( $value );
}

function wp_parse_str( $string, &$array ) {
parse_str( $string, $array );
if ( get_magic_quotes_gpc() )
	$array = stripslashes_deep( $array );
	/**
	 * Filters the array of variables derived from a parsed string.
	 *
	 * @since 2.3.0
	 *
	 * @param array $array The array populated with variables.
	 */
	$array = apply_filters( 'wp_parse_str', $array );
}


function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 2.2.0
 *
 * @param string $string What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}

function _deep_replace( $search, $subject ) {
	$subject = (string) $subject;
	
	$count = 1;
	while ( $count ) {
		$subject = str_replace( $search, '', $subject, $count );
	}
	
	return $subject;
}

function esc_url( $url, $protocols = null, $_context = 'display' ) {
$original_url = $url;

if ( '' == $url )
	return $url;
	
	$url = str_replace( ' ', '%20', $url );
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
	
	if ( '' === $url ) {
		return $url;
	}
	
	if ( 0 !== stripos( $url, 'mailto:' ) ) {
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = _deep_replace($strip, $url);
	}
	
	$url = str_replace(';//', '://', $url);
	/* If the URL doesn't appear to contain a scheme, we
	 * presume it needs http:// prepended (unless a relative
	 * link starting with /, # or ? or a php file).
	 */
	if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) &&
			! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
		$url = 'http://' . $url;
		
		// Replace ampersands and single quotes only when displaying.
		if ( 'display' == $_context ) {
			$url = wp_kses_normalize_entities( $url );
			$url = str_replace( '&amp;', '&#038;', $url );
			$url = str_replace( "'", '&#039;', $url );
		}
		
		if ( ( false !== strpos( $url, '[' ) ) || ( false !== strpos( $url, ']' ) ) ) {
			
			$parsed = wp_parse_url( $url );
			$front  = '';
			
			if ( isset( $parsed['scheme'] ) ) {
				$front .= $parsed['scheme'] . '://';
			} elseif ( '/' === $url[0] ) {
				$front .= '//';
			}
			
			if ( isset( $parsed['user'] ) ) {
				$front .= $parsed['user'];
			}
			
			if ( isset( $parsed['pass'] ) ) {
				$front .= ':' . $parsed['pass'];
			}
			
			if ( isset( $parsed['user'] ) || isset( $parsed['pass'] ) ) {
				$front .= '@';
			}
			
			if ( isset( $parsed['host'] ) ) {
				$front .= $parsed['host'];
			}
			
			if ( isset( $parsed['port'] ) ) {
				$front .= ':' . $parsed['port'];
			}
			
			$end_dirty = str_replace( $front, '', $url );
			$end_clean = str_replace( array( '[', ']' ), array( '%5B', '%5D' ), $end_dirty );
			$url       = str_replace( $end_dirty, $end_clean, $url );
			
		}
		
		if ( '/' === $url[0] ) {
			$good_protocol_url = $url;
		} else {
			if ( ! is_array( $protocols ) )
				$protocols = wp_allowed_protocols();
				$good_protocol_url = wp_kses_bad_protocol( $url, $protocols );
				if ( strtolower( $good_protocol_url ) != strtolower( $url ) )
					return '';
		}
		
		/**
		 * Filters a string cleaned and escaped for output as a URL.
		 *
		 * @since 2.3.0
		 *
		 * @param string $good_protocol_url The cleaned URL to be returned.
		 * @param string $original_url      The URL prior to cleaning.
		 * @param string $_context          If 'display', replace ampersands and single quotes only.
		 */
		return apply_filters( 'clean_url', $good_protocol_url, $original_url, $_context );
}

?>