<?php
function wp_unslash($value) {
	return stripslashes_deep ( $value );
}

function stripslashes_deep( $value ) {
	return map_deep( $value, 'stripslashes_from_strings_only' );
}

function stripslashes_from_strings_only( $value ) {
	return is_string( $value ) ? stripslashes( $value ) : $value;
}

function map_deep( $value, $callback ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $index => $item ) {
			$value[ $index ] = map_deep( $item, $callback );
		}
	} elseif ( is_object( $value ) ) {
		$object_vars = get_object_vars( $value );
		foreach ( $object_vars as $property_name => $property_value ) {
			$value->$property_name = map_deep( $property_value, $callback );
		}
	} else {
		$value = call_user_func( $callback, $value );
	}
	
	return $value;
}

function wp_parse_str($string, &$array) {
	parse_str ( $string, $array );
	if (get_magic_quotes_gpc ())
		$array = stripslashes_deep ( $array );
	/**
	 * Filters the array of variables derived from a parsed string.
	 *
	 * @since 2.3.0
	 *       
	 * @param array $array
	 *        	The array populated with variables.
	 */
	$array = apply_filters ( 'wp_parse_str', $array );
}
function trailingslashit($string) {
	return untrailingslashit ( $string ) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 2.2.0
 *       
 * @param string $string
 *        	What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function untrailingslashit($string) {
	return rtrim ( $string, '/\\' );
}
function _deep_replace($search, $subject) {
	$subject = ( string ) $subject;
	
	$count = 1;
	while ( $count ) {
		$subject = str_replace ( $search, '', $subject, $count );
	}
	
	return $subject;
}
function wp_check_invalid_utf8($string, $strip = false) {
	$string = ( string ) $string;
	
	if (0 === strlen ( $string )) {
		return '';
	}
	
	// Store the site charset as a static to avoid multiple calls to get_option()
	static $is_utf8 = null;
	if (! isset ( $is_utf8 )) {
		$is_utf8 = in_array ( get_option ( 'blog_charset' ), array (
				'utf8',
				'utf-8',
				'UTF8',
				'UTF-8' 
		) );
	}
	if (! $is_utf8) {
		return $string;
	}
	
	// Check for support for utf8 in the installed PCRE library once and store the result in a static
	static $utf8_pcre = null;
	if (! isset ( $utf8_pcre )) {
		$utf8_pcre = @preg_match ( '/^./u', 'a' );
	}
	// We can't demand utf8 in the PCRE installation, so just return the string in those cases
	if (! $utf8_pcre) {
		return $string;
	}
	
	// preg_match fails when it encounters invalid UTF8 in $string
	if (1 === @preg_match ( '/^./us', $string )) {
		return $string;
	}
	
	// Attempt to strip the bad chars if requested (not recommended)
	if ($strip && function_exists ( 'iconv' )) {
		file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
		return iconv ( 'utf-8', 'utf-8', $string );
	}
	
	return '';
}
function _wp_specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false) {
	$string = ( string ) $string;
	
	if (0 === strlen ( $string ))
		return '';
	
	// Don't bother if there are no specialchars - saves some processing
	if (! preg_match ( '/[&<>"\']/', $string ))
		return $string;
	
	// Account for the previous behaviour of the function when the $quote_style is not an accepted value
	if (empty ( $quote_style ))
		$quote_style = ENT_NOQUOTES;
	elseif (! in_array ( $quote_style, array (
			0,
			2,
			3,
			'single',
			'double' 
	), true ))
		$quote_style = ENT_QUOTES;
	
	// Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
	if (! $charset) {
		static $_charset = null;
		if (! isset ( $_charset )) {
			$alloptions = wp_load_alloptions ();
			$_charset = isset ( $alloptions ['blog_charset'] ) ? $alloptions ['blog_charset'] : '';
		}
		$charset = $_charset;
	}
	
	if (in_array ( $charset, array (
			'utf8',
			'utf-8',
			'UTF8' 
	) ))
		$charset = 'UTF-8';
	
	$_quote_style = $quote_style;
	
	if ($quote_style === 'double') {
		$quote_style = ENT_COMPAT;
		$_quote_style = ENT_COMPAT;
	} elseif ($quote_style === 'single') {
		$quote_style = ENT_NOQUOTES;
	}
	
	if (! $double_encode) {
		// Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
		// This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
		$string = wp_kses_normalize_entities ( $string );
	}
	
	$string = @htmlspecialchars ( $string, $quote_style, $charset, $double_encode );
	
	// Back-compat.
	if ('single' === $_quote_style)
		$string = str_replace ( "'", '&#039;', $string );
	
	return $string;
}
function wp_strip_all_tags($string, $remove_breaks = false) {
	$string = preg_replace ( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags ( $string );
	
	if ($remove_breaks)
		$string = preg_replace ( '/[\r\n\t ]+/', ' ', $string );
	
	return trim ( $string );
}
function seems_utf8($str) {
	mbstring_binary_safe_encoding ();
	$length = strlen ( $str );
	reset_mbstring_encoding ();
	for($i = 0; $i < $length; $i ++) {
		$c = ord ( $str [$i] );
		if ($c < 0x80)
			$n = 0; // 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0)
			$n = 1; // 110bbbbb
		elseif (($c & 0xF0) == 0xE0)
			$n = 2; // 1110bbbb
		elseif (($c & 0xF8) == 0xF0)
			$n = 3; // 11110bbb
		elseif (($c & 0xFC) == 0xF8)
			$n = 4; // 111110bb
		elseif (($c & 0xFE) == 0xFC)
			$n = 5; // 1111110b
		else
			return false; // Does not match any model
		for($j = 0; $j < $n; $j ++) { // n bytes matching 10bbbbbb follow ?
			if ((++ $i == $length) || ((ord ( $str [$i] ) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}
function remove_accents($string) {
	if (! preg_match ( '/[\x80-\xff]/', $string ))
		return $string;
	
	if (seems_utf8 ( $string )) {
		$chars = array (
				// Decompositions for Latin-1 Supplement
				'ª' => 'a',
				'º' => 'o',
				'À' => 'A',
				'Á' => 'A',
				'Â' => 'A',
				'Ã' => 'A',
				'Ä' => 'A',
				'Å' => 'A',
				'Æ' => 'AE',
				'Ç' => 'C',
				'È' => 'E',
				'É' => 'E',
				'Ê' => 'E',
				'Ë' => 'E',
				'Ì' => 'I',
				'Í' => 'I',
				'Î' => 'I',
				'Ï' => 'I',
				'Ð' => 'D',
				'Ñ' => 'N',
				'Ò' => 'O',
				'Ó' => 'O',
				'Ô' => 'O',
				'Õ' => 'O',
				'Ö' => 'O',
				'Ù' => 'U',
				'Ú' => 'U',
				'Û' => 'U',
				'Ü' => 'U',
				'Ý' => 'Y',
				'Þ' => 'TH',
				'ß' => 's',
				'à' => 'a',
				'á' => 'a',
				'â' => 'a',
				'ã' => 'a',
				'ä' => 'a',
				'å' => 'a',
				'æ' => 'ae',
				'ç' => 'c',
				'è' => 'e',
				'é' => 'e',
				'ê' => 'e',
				'ë' => 'e',
				'ì' => 'i',
				'í' => 'i',
				'î' => 'i',
				'ï' => 'i',
				'ð' => 'd',
				'ñ' => 'n',
				'ò' => 'o',
				'ó' => 'o',
				'ô' => 'o',
				'õ' => 'o',
				'ö' => 'o',
				'ø' => 'o',
				'ù' => 'u',
				'ú' => 'u',
				'û' => 'u',
				'ü' => 'u',
				'ý' => 'y',
				'þ' => 'th',
				'ÿ' => 'y',
				'Ø' => 'O',
				// Decompositions for Latin Extended-A
				'Ā' => 'A',
				'ā' => 'a',
				'Ă' => 'A',
				'ă' => 'a',
				'Ą' => 'A',
				'ą' => 'a',
				'Ć' => 'C',
				'ć' => 'c',
				'Ĉ' => 'C',
				'ĉ' => 'c',
				'Ċ' => 'C',
				'ċ' => 'c',
				'Č' => 'C',
				'č' => 'c',
				'Ď' => 'D',
				'ď' => 'd',
				'Đ' => 'D',
				'đ' => 'd',
				'Ē' => 'E',
				'ē' => 'e',
				'Ĕ' => 'E',
				'ĕ' => 'e',
				'Ė' => 'E',
				'ė' => 'e',
				'Ę' => 'E',
				'ę' => 'e',
				'Ě' => 'E',
				'ě' => 'e',
				'Ĝ' => 'G',
				'ĝ' => 'g',
				'Ğ' => 'G',
				'ğ' => 'g',
				'Ġ' => 'G',
				'ġ' => 'g',
				'Ģ' => 'G',
				'ģ' => 'g',
				'Ĥ' => 'H',
				'ĥ' => 'h',
				'Ħ' => 'H',
				'ħ' => 'h',
				'Ĩ' => 'I',
				'ĩ' => 'i',
				'Ī' => 'I',
				'ī' => 'i',
				'Ĭ' => 'I',
				'ĭ' => 'i',
				'Į' => 'I',
				'į' => 'i',
				'İ' => 'I',
				'ı' => 'i',
				'Ĳ' => 'IJ',
				'ĳ' => 'ij',
				'Ĵ' => 'J',
				'ĵ' => 'j',
				'Ķ' => 'K',
				'ķ' => 'k',
				'ĸ' => 'k',
				'Ĺ' => 'L',
				'ĺ' => 'l',
				'Ļ' => 'L',
				'ļ' => 'l',
				'Ľ' => 'L',
				'ľ' => 'l',
				'Ŀ' => 'L',
				'ŀ' => 'l',
				'Ł' => 'L',
				'ł' => 'l',
				'Ń' => 'N',
				'ń' => 'n',
				'Ņ' => 'N',
				'ņ' => 'n',
				'Ň' => 'N',
				'ň' => 'n',
				'ŉ' => 'n',
				'Ŋ' => 'N',
				'ŋ' => 'n',
				'Ō' => 'O',
				'ō' => 'o',
				'Ŏ' => 'O',
				'ŏ' => 'o',
				'Ő' => 'O',
				'ő' => 'o',
				'Œ' => 'OE',
				'œ' => 'oe',
				'Ŕ' => 'R',
				'ŕ' => 'r',
				'Ŗ' => 'R',
				'ŗ' => 'r',
				'Ř' => 'R',
				'ř' => 'r',
				'Ś' => 'S',
				'ś' => 's',
				'Ŝ' => 'S',
				'ŝ' => 's',
				'Ş' => 'S',
				'ş' => 's',
				'Š' => 'S',
				'š' => 's',
				'Ţ' => 'T',
				'ţ' => 't',
				'Ť' => 'T',
				'ť' => 't',
				'Ŧ' => 'T',
				'ŧ' => 't',
				'Ũ' => 'U',
				'ũ' => 'u',
				'Ū' => 'U',
				'ū' => 'u',
				'Ŭ' => 'U',
				'ŭ' => 'u',
				'Ů' => 'U',
				'ů' => 'u',
				'Ű' => 'U',
				'ű' => 'u',
				'Ų' => 'U',
				'ų' => 'u',
				'Ŵ' => 'W',
				'ŵ' => 'w',
				'Ŷ' => 'Y',
				'ŷ' => 'y',
				'Ÿ' => 'Y',
				'Ź' => 'Z',
				'ź' => 'z',
				'Ż' => 'Z',
				'ż' => 'z',
				'Ž' => 'Z',
				'ž' => 'z',
				'ſ' => 's',
				// Decompositions for Latin Extended-B
				'Ș' => 'S',
				'ș' => 's',
				'Ț' => 'T',
				'ț' => 't',
				// Euro Sign
				'€' => 'E',
				// GBP (Pound) Sign
				'£' => '',
				// Vowels with diacritic (Vietnamese)
				// unmarked
				'Ơ' => 'O',
				'ơ' => 'o',
				'Ư' => 'U',
				'ư' => 'u',
				// grave accent
				'Ầ' => 'A',
				'ầ' => 'a',
				'Ằ' => 'A',
				'ằ' => 'a',
				'Ề' => 'E',
				'ề' => 'e',
				'Ồ' => 'O',
				'ồ' => 'o',
				'Ờ' => 'O',
				'ờ' => 'o',
				'Ừ' => 'U',
				'ừ' => 'u',
				'Ỳ' => 'Y',
				'ỳ' => 'y',
				// hook
				'Ả' => 'A',
				'ả' => 'a',
				'Ẩ' => 'A',
				'ẩ' => 'a',
				'Ẳ' => 'A',
				'ẳ' => 'a',
				'Ẻ' => 'E',
				'ẻ' => 'e',
				'Ể' => 'E',
				'ể' => 'e',
				'Ỉ' => 'I',
				'ỉ' => 'i',
				'Ỏ' => 'O',
				'ỏ' => 'o',
				'Ổ' => 'O',
				'ổ' => 'o',
				'Ở' => 'O',
				'ở' => 'o',
				'Ủ' => 'U',
				'ủ' => 'u',
				'Ử' => 'U',
				'ử' => 'u',
				'Ỷ' => 'Y',
				'ỷ' => 'y',
				// tilde
				'Ẫ' => 'A',
				'ẫ' => 'a',
				'Ẵ' => 'A',
				'ẵ' => 'a',
				'Ẽ' => 'E',
				'ẽ' => 'e',
				'Ễ' => 'E',
				'ễ' => 'e',
				'Ỗ' => 'O',
				'ỗ' => 'o',
				'Ỡ' => 'O',
				'ỡ' => 'o',
				'Ữ' => 'U',
				'ữ' => 'u',
				'Ỹ' => 'Y',
				'ỹ' => 'y',
				// acute accent
				'Ấ' => 'A',
				'ấ' => 'a',
				'Ắ' => 'A',
				'ắ' => 'a',
				'Ế' => 'E',
				'ế' => 'e',
				'Ố' => 'O',
				'ố' => 'o',
				'Ớ' => 'O',
				'ớ' => 'o',
				'Ứ' => 'U',
				'ứ' => 'u',
				// dot below
				'Ạ' => 'A',
				'ạ' => 'a',
				'Ậ' => 'A',
				'ậ' => 'a',
				'Ặ' => 'A',
				'ặ' => 'a',
				'Ẹ' => 'E',
				'ẹ' => 'e',
				'Ệ' => 'E',
				'ệ' => 'e',
				'Ị' => 'I',
				'ị' => 'i',
				'Ọ' => 'O',
				'ọ' => 'o',
				'Ộ' => 'O',
				'ộ' => 'o',
				'Ợ' => 'O',
				'ợ' => 'o',
				'Ụ' => 'U',
				'ụ' => 'u',
				'Ự' => 'U',
				'ự' => 'u',
				'Ỵ' => 'Y',
				'ỵ' => 'y',
				// Vowels with diacritic (Chinese, Hanyu Pinyin)
				'ɑ' => 'a',
				// macron
				'Ǖ' => 'U',
				'ǖ' => 'u',
				// acute accent
				'Ǘ' => 'U',
				'ǘ' => 'u',
				// caron
				'Ǎ' => 'A',
				'ǎ' => 'a',
				'Ǐ' => 'I',
				'ǐ' => 'i',
				'Ǒ' => 'O',
				'ǒ' => 'o',
				'Ǔ' => 'U',
				'ǔ' => 'u',
				'Ǚ' => 'U',
				'ǚ' => 'u',
				// grave accent
				'Ǜ' => 'U',
				'ǜ' => 'u' 
		);
		
		// Used for locale-specific rules
		$locale = get_locale ();
		
		if ('de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale) {
			$chars ['Ä'] = 'Ae';
			$chars ['ä'] = 'ae';
			$chars ['Ö'] = 'Oe';
			$chars ['ö'] = 'oe';
			$chars ['Ü'] = 'Ue';
			$chars ['ü'] = 'ue';
			$chars ['ß'] = 'ss';
		} elseif ('da_DK' === $locale) {
			$chars ['Æ'] = 'Ae';
			$chars ['æ'] = 'ae';
			$chars ['Ø'] = 'Oe';
			$chars ['ø'] = 'oe';
			$chars ['Å'] = 'Aa';
			$chars ['å'] = 'aa';
		} elseif ('ca' === $locale) {
			$chars ['l·l'] = 'll';
		} elseif ('sr_RS' === $locale) {
			$chars ['Đ'] = 'DJ';
			$chars ['đ'] = 'dj';
		}
		
		$string = strtr ( $string, $chars );
	} else {
		$chars = array ();
		// Assume ISO-8859-1 if not UTF-8
		$chars ['in'] = "\x80\x83\x8a\x8e\x9a\x9e" . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2" . "\xc3\xc4\xc5\xc7\xc8\xc9\xca" . "\xcb\xcc\xcd\xce\xcf\xd1\xd2" . "\xd3\xd4\xd5\xd6\xd8\xd9\xda" . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3" . "\xe4\xe5\xe7\xe8\xe9\xea\xeb" . "\xec\xed\xee\xef\xf1\xf2\xf3" . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb" . "\xfc\xfd\xff";
		
		$chars ['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
		
		$string = strtr ( $string, $chars ['in'], $chars ['out'] );
		$double_chars = array ();
		$double_chars ['in'] = array (
				"\x8c",
				"\x9c",
				"\xc6",
				"\xd0",
				"\xde",
				"\xdf",
				"\xe6",
				"\xf0",
				"\xfe" 
		);
		$double_chars ['out'] = array (
				'OE',
				'oe',
				'AE',
				'DH',
				'TH',
				'ss',
				'ae',
				'dh',
				'th' 
		);
		$string = str_replace ( $double_chars ['in'], $double_chars ['out'], $string );
	}
	
	return $string;
}
function sanitize_user($username, $strict = false) {
	$raw_username = $username;
	$username = wp_strip_all_tags ( $username );
	$username = remove_accents ( $username );
	// Kill octets
	$username = preg_replace ( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
	$username = preg_replace ( '/&.+?;/', '', $username ); // Kill entities
	                                                      
	// If strict, reduce to ASCII for max portability.
	if ($strict)
		$username = preg_replace ( '|[^a-z0-9 _.\-@]|i', '', $username );
	
	$username = trim ( $username );
	// Consolidate contiguous whitespace
	$username = preg_replace ( '|\s+|', ' ', $username );
	
	/**
	 * Filters a sanitized username string.
	 *
	 * @since 2.0.1
	 *       
	 * @param string $username
	 *        	Sanitized username.
	 * @param string $raw_username
	 *        	The username prior to sanitization.
	 * @param bool $strict
	 *        	Whether to limit the sanitization to specific characters. Default false.
	 */
	//return $username;
	return apply_filters ( 'sanitize_user', $username, $raw_username, $strict );
}
function esc_attr($text) {
	$safe_text = wp_check_invalid_utf8 ( $text );
	$safe_text = _wp_specialchars ( $safe_text, ENT_QUOTES );
	/**
	 * Filters a string cleaned and escaped for output in an HTML attribute.
	 *
	 * Text passed to esc_attr() is stripped of invalid or special characters
	 * before output.
	 *
	 * @since 2.0.6
	 *       
	 * @param string $safe_text
	 *        	The text after it has been escaped.
	 * @param string $text
	 *        	The text prior to being escaped.
	 */
	return apply_filters ( 'attribute_escape', $safe_text, $text );
}
function esc_url($url, $protocols = null, $_context = 'display') {
	$original_url = $url;
	
	if ('' == $url)
		return $url;
	
	$url = str_replace ( ' ', '%20', $url );
	$url = preg_replace ( '|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url );
	
	if ('' === $url) {
		return $url;
	}
	
	if (0 !== stripos ( $url, 'mailto:' )) {
		$strip = array (
				'%0d',
				'%0a',
				'%0D',
				'%0A' 
		);
		$url = _deep_replace ( $strip, $url );
	}
	
	$url = str_replace ( ';//', '://', $url );
	/*
	 * If the URL doesn't appear to contain a scheme, we
	 * presume it needs http:// prepended (unless a relative
	 * link starting with /, # or ? or a php file).
	 */
	if (strpos ( $url, ':' ) === false && ! in_array ( $url [0], array (
			'/',
			'#',
			'?' 
	) ) && ! preg_match ( '/^[a-z0-9-]+?\.php/i', $url ))
		$url = 'http://' . $url;
	
	// Replace ampersands and single quotes only when displaying.
	if ('display' == $_context) {
		$url = wp_kses_normalize_entities ( $url );
		$url = str_replace ( '&amp;', '&#038;', $url );
		$url = str_replace ( "'", '&#039;', $url );
	}
	
	if ((false !== strpos ( $url, '[' )) || (false !== strpos ( $url, ']' ))) {
		
		$parsed = wp_parse_url ( $url );
		$front = '';
		
		if (isset ( $parsed ['scheme'] )) {
			$front .= $parsed ['scheme'] . '://';
		} elseif ('/' === $url [0]) {
			$front .= '//';
		}
		
		if (isset ( $parsed ['user'] )) {
			$front .= $parsed ['user'];
		}
		
		if (isset ( $parsed ['pass'] )) {
			$front .= ':' . $parsed ['pass'];
		}
		
		if (isset ( $parsed ['user'] ) || isset ( $parsed ['pass'] )) {
			$front .= '@';
		}
		
		if (isset ( $parsed ['host'] )) {
			$front .= $parsed ['host'];
		}
		
		if (isset ( $parsed ['port'] )) {
			$front .= ':' . $parsed ['port'];
		}
		
		$end_dirty = str_replace ( $front, '', $url );
		$end_clean = str_replace ( array (
				'[',
				']' 
		), array (
				'%5B',
				'%5D' 
		), $end_dirty );
		$url = str_replace ( $end_dirty, $end_clean, $url );
	}
	
	if ('/' === $url [0]) {
		$good_protocol_url = $url;
	} else {
		if (! is_array ( $protocols ))
			$protocols = wp_allowed_protocols ();
		$good_protocol_url = wp_kses_bad_protocol ( $url, $protocols );
		if (strtolower ( $good_protocol_url ) != strtolower ( $url ))
			return '';
	}
	
	/**
	 * Filters a string cleaned and escaped for output as a URL.
	 *
	 * @since 2.3.0
	 *       
	 * @param string $good_protocol_url
	 *        	The cleaned URL to be returned.
	 * @param string $original_url
	 *        	The URL prior to cleaning.
	 * @param string $_context
	 *        	If 'display', replace ampersands and single quotes only.
	 */
	return apply_filters ( 'clean_url', $good_protocol_url, $original_url, $_context );
}


/**
 * Escaping for HTML blocks.
 *
 * @since 2.8.0
 *
 * @param string $text
 * @return string
 */
function esc_html( $text ) {
$safe_text = wp_check_invalid_utf8( $text );
$safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
/**
 * Filters a string cleaned and escaped for output in HTML.
 *
 * Text passed to esc_html() is stripped of invalid or special characters
 * before output.
 *
 * @since 2.8.0
 *
 * @param string $safe_text The text after it has been escaped.
 * @param string $text      The text prior to being escaped.
 */
return apply_filters( 'esc_html', $safe_text, $text );
}

/**
 * Strips out all characters that are not allowable in an email.
 *
 * @since 1.5.0
 *
 * @param string $email Email address to filter.
 * @return string Filtered email address.
 */
function sanitize_email( $email ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
// Test for the minimum length the email can be
if ( strlen( $email ) < 3 ) {
	/**
	 * Filters a sanitized email address.
	 *
	 * This filter is evaluated under several contexts, including 'email_too_short',
	 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
	 * 'domain_no_periods', 'domain_no_valid_subs', or no context.
	 *
	 * @since 2.8.0
	 *
	 * @param string $email   The sanitized email address.
	 * @param string $email   The email address, as provided to sanitize_email().
	 * @param string $message A message to pass to the user.
	 */
	return apply_filters( 'sanitize_email', '', $email, 'email_too_short' );
}

// Test for an @ character after the first position
if ( strpos( $email, '@', 1 ) === false ) {
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', '', $email, 'email_no_at' );
}

// Split out the local and domain parts
list( $local, $domain ) = explode( '@', $email, 2 );

// LOCAL PART
// Test for invalid characters
$local = preg_replace( '/[^a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]/', '', $local );
if ( '' === $local ) {
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', '', $email, 'local_invalid_chars' );
}

// DOMAIN PART
// Test for sequences of periods
$domain = preg_replace( '/\.{2,}/', '', $domain );
if ( '' === $domain ) {
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', '', $email, 'domain_period_sequence' );
}

// Test for leading and trailing periods and whitespace
$domain = trim( $domain, " \t\n\r\0\x0B." );
if ( '' === $domain ) {
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', '', $email, 'domain_period_limits' );
}

// Split the domain into subs
$subs = explode( '.', $domain );

// Assume the domain will have at least two subs
if ( 2 > count( $subs ) ) {
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', '', $email, 'domain_no_periods' );
}

// Create an array that will contain valid subs
$new_subs = array();

// Loop through each sub
foreach ( $subs as $sub ) {
	// Test for leading and trailing hyphens
	$sub = trim( $sub, " \t\n\r\0\x0B-" );
	
	// Test for invalid characters
	$sub = preg_replace( '/[^a-z0-9-]+/i', '', $sub );
	
	// If there's anything left, add it to the valid subs
	if ( '' !== $sub ) {
		$new_subs[] = $sub;
	}
}

// If there aren't 2 or more valid subs
if ( 2 > count( $new_subs ) ) {
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'sanitize_email', '', $email, 'domain_no_valid_subs' );
}

// Join valid subs into the new domain
$domain = join( '.', $new_subs );

// Put the email back together
$email = $local . '@' . $domain;

// Congratulations your email made it!
/** This filter is documented in wp-includes/formatting.php */
return apply_filters( 'sanitize_email', $email, $email, null );
}


function esc_url_raw( $url, $protocols = null ) {
	return esc_url( $url, $protocols, 'db' );
}

function sanitize_option($option, $value) {
	global $wpdb;
	
	$original_value = $value;
	$error = '';
	
	switch ($option) {
		case 'admin_email' :
		case 'new_admin_email' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				$value = sanitize_email ( $value );
				if (! is_email ( $value )) {
					$error = __ ( 'The email address entered did not appear to be a valid email address. Please enter a valid email address.' );
				}
			}
			break;
		
		case 'thumbnail_size_w' :
		case 'thumbnail_size_h' :
		case 'medium_size_w' :
		case 'medium_size_h' :
		case 'medium_large_size_w' :
		case 'medium_large_size_h' :
		case 'large_size_w' :
		case 'large_size_h' :
		case 'mailserver_port' :
		case 'comment_max_links' :
		case 'page_on_front' :
		case 'page_for_posts' :
		case 'rss_excerpt_length' :
		case 'default_category' :
		case 'default_email_category' :
		case 'default_link_category' :
		case 'close_comments_days_old' :
		case 'comments_per_page' :
		case 'thread_comments_depth' :
		case 'users_can_register' :
		case 'start_of_week' :
		case 'site_icon' :
			$value = absint ( $value );
			break;
		
		case 'posts_per_page' :
		case 'posts_per_rss' :
			$value = ( int ) $value;
			if (empty ( $value ))
				$value = 1;
			if ($value < - 1)
				$value = abs ( $value );
			break;
		
		case 'default_ping_status' :
		case 'default_comment_status' :
			// Options that if not there have 0 value but need to be something like "closed"
			if ($value == '0' || $value == '')
				$value = 'closed';
			break;
		
		case 'blogdescription' :
		case 'blogname' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if ($value !== $original_value) {
				$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', wp_encode_emoji ( $original_value ) );
			}
			
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				$value = esc_html ( $value );
			}
			break;
		
		case 'blog_charset' :
			$value = preg_replace ( '/[^a-zA-Z0-9_-]/', '', $value ); // strips slashes
			break;
		
		case 'blog_public' :
			// This is the value if the settings checkbox is not checked on POST. Don't rely on this.
			if (null === $value)
				$value = 1;
			else
				$value = intval ( $value );
			break;
		
		case 'date_format' :
		case 'time_format' :
		case 'mailserver_url' :
		case 'mailserver_login' :
		case 'mailserver_pass' :
		case 'upload_path' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				$value = strip_tags ( $value );
				$value = wp_kses_data ( $value );
			}
			break;
		
		case 'ping_sites' :
			$value = explode ( "\n", $value );
			$value = array_filter ( array_map ( 'trim', $value ) );
			$value = array_filter ( array_map ( 'esc_url_raw', $value ) );
			$value = implode ( "\n", $value );
			break;
		
		case 'gmt_offset' :
			$value = preg_replace ( '/[^0-9:.-]/', '', $value ); // strips slashes
			break;
		
		case 'siteurl' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				if (preg_match ( '#http(s?)://(.+)#i', $value )) {
					$value = esc_url_raw ( $value );
				} else {
					$error = __ ( 'The WordPress address you entered did not appear to be a valid URL. Please enter a valid URL.' );
				}
			}
			break;
		
		case 'home' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				if (preg_match ( '#http(s?)://(.+)#i', $value )) {
					$value = esc_url_raw ( $value );
				} else {
					$error = __ ( 'The Site address you entered did not appear to be a valid URL. Please enter a valid URL.' );
				}
			}
			break;
		
		case 'WPLANG' :
			$allowed = get_available_languages ();
			if (! is_multisite () && defined ( 'WPLANG' ) && '' !== WPLANG && 'en_US' !== WPLANG) {
				$allowed [] = WPLANG;
			}
			if (! in_array ( $value, $allowed ) && ! empty ( $value )) {
				$value = get_option ( $option );
			}
			break;
		
		case 'illegal_names' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				if (! is_array ( $value ))
					$value = explode ( ' ', $value );
				
				$value = array_values ( array_filter ( array_map ( 'trim', $value ) ) );
				
				if (! $value)
					$value = '';
			}
			break;
		
		case 'limited_email_domains' :
		case 'banned_email_domains' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				if (! is_array ( $value ))
					$value = explode ( "\n", $value );
				
				$domains = array_values ( array_filter ( array_map ( 'trim', $value ) ) );
				$value = array ();
				
				foreach ( $domains as $domain ) {
					if (! preg_match ( '/(--|\.\.)/', $domain ) && preg_match ( '|^([a-zA-Z0-9-\.])+$|', $domain )) {
						$value [] = $domain;
					}
				}
				if (! $value)
					$value = '';
			}
			break;
		
		case 'timezone_string' :
			$allowed_zones = timezone_identifiers_list ();
			if (! in_array ( $value, $allowed_zones ) && ! empty ( $value )) {
				$error = __ ( 'The timezone you have entered is not valid. Please select a valid timezone.' );
			}
			break;
		
		case 'permalink_structure' :
		case 'category_base' :
		case 'tag_base' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				$value = esc_url_raw ( $value );
				$value = str_replace ( 'http://', '', $value );
			}
			
			if ('permalink_structure' === $option && '' !== $value && ! preg_match ( '/%[^\/%]+%/', $value )) {
				$error = sprintf(
					/* translators: %s: Codex URL */
					__ ( 'A structure tag is required when using custom permalinks. <a href="%s">Learn more</a>' ), __ ( 'https://codex.wordpress.org/Using_Permalinks#Choosing_your_permalink_structure' ) );
			}
			break;
		
		case 'default_role' :
			if (! get_role ( $value ) && get_role ( 'subscriber' ))
				$value = 'subscriber';
			break;
		
		case 'moderation_keys' :
		case 'blacklist_keys' :
			$value = $wpdb->strip_invalid_text_for_column ( $wpdb->options, 'option_value', $value );
			if (is_wp_error ( $value )) {
				$error = $value->get_error_message ();
			} else {
				$value = explode ( "\n", $value );
				$value = array_filter ( array_map ( 'trim', $value ) );
				$value = array_unique ( $value );
				$value = implode ( "\n", $value );
			}
			break;
	}
	
	if (! empty ( $error )) {
		$value = get_option ( $option );
		if (function_exists ( 'add_settings_error' )) {
			file_put_contents ( '/Users/ewu/output.log', print_r ( (new Exception ())->getTraceAsString (), true ) . PHP_EOL . PHP_EOL, FILE_APPEND );
			add_settings_error ( $option, "invalid_{$option}", $error );
		}
	}
	
	/**
	 * Filters an option value following sanitization.
	 *
	 * @since 2.3.0
	 * @since 4.3.0 Added the `$original_value` parameter.
	 *       
	 * @param string $value
	 *        	The sanitized option value.
	 * @param string $option
	 *        	The option name.
	 * @param string $original_value
	 *        	The original value passed to the function.
	 */
	return apply_filters ( "sanitize_option_{$option}", $value, $option, $original_value );
}

function is_email( $email, $deprecated = false ) {
if ( ! empty( $deprecated ) )
	_deprecated_argument( __FUNCTION__, '3.0.0' );
	
	// Test for the minimum length the email can be
	if ( strlen( $email ) < 3 ) {
		/**
		 * Filters whether an email address is valid.
		 *
		 * This filter is evaluated under several different contexts, such as 'email_too_short',
		 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
		 * 'domain_no_periods', 'sub_hyphen_limits', 'sub_invalid_chars', or no specific context.
		 *
		 * @since 2.8.0
		 *
		 * @param bool   $is_email Whether the email address has passed the is_email() checks. Default false.
		 * @param string $email    The email address being checked.
		 * @param string $context  Context under which the email was tested.
		 */
		return apply_filters( 'is_email', false, $email, 'email_too_short' );
	}
	
	// Test for an @ character after the first position
	if ( strpos( $email, '@', 1 ) === false ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'email_no_at' );
	}
	
	// Split out the local and domain parts
	list( $local, $domain ) = explode( '@', $email, 2 );
	
	// LOCAL PART
	// Test for invalid characters
	if ( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'local_invalid_chars' );
	}
	
	// DOMAIN PART
	// Test for sequences of periods
	if ( preg_match( '/\.{2,}/', $domain ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'domain_period_sequence' );
	}
	
	// Test for leading and trailing periods and whitespace
	if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'domain_period_limits' );
	}
	
	// Split the domain into subs
	$subs = explode( '.', $domain );
	
	// Assume the domain will have at least two subs
	if ( 2 > count( $subs ) ) {
		/** This filter is documented in wp-includes/formatting.php */
		return apply_filters( 'is_email', false, $email, 'domain_no_periods' );
	}
	
	// Loop through each sub
	foreach ( $subs as $sub ) {
		// Test for leading and trailing hyphens and whitespace
		if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) {
			/** This filter is documented in wp-includes/formatting.php */
			return apply_filters( 'is_email', false, $email, 'sub_hyphen_limits' );
		}
		
		// Test for invalid characters
		if ( !preg_match('/^[a-z0-9-]+$/i', $sub ) ) {
			/** This filter is documented in wp-includes/formatting.php */
			return apply_filters( 'is_email', false, $email, 'sub_invalid_chars' );
		}
	}
	
	// Congratulations your email made it!
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters( 'is_email', $email, $email, null );
}

function wp_slash( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $k => $v ) {
			if ( is_array( $v ) ) {
				$value[$k] = wp_slash( $v );
			} else {
				$value[$k] = addslashes( $v );
			}
		}
	} else {
		$value = addslashes( $value );
	}
	
	return $value;
}

?>