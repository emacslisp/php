<?php

function wp_admin_css( $file = 'wp-admin', $force_echo = false ) {
	// For backward compatibility
	/*$handle = 0 === strpos( $file, 'css/' ) ? substr( $file, 4 ) : $file;
	
	if ( wp_styles()->query( $handle ) ) {
		if ( $force_echo || did_action( 'wp_print_styles' ) ) // we already printed the style queue. Print this one immediately
			wp_print_styles( $handle );
			else // Add to style queue
				wp_enqueue_style( $handle );
				return;
	}*/
	
	/**
	 * Filters the stylesheet link to the specified CSS file.
	 *
	 * If the site is set to display right-to-left, the RTL stylesheet link
	 * will be used instead.
	 *
	 * @since 2.3.0
	 *
	 * @param string $file Style handle name or filename (without ".css" extension)
	 *                     relative to wp-admin/. Defaults to 'wp-admin'.
	 */
	echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . esc_url( wp_admin_css_uri( $file ) ) . "' type='text/css' />\n", $file );
	
	/*
	 if ( function_exists( 'is_rtl' ) && is_rtl() ) {
		
		echo apply_filters( 'wp_admin_css', "<link rel='stylesheet' href='" . esc_url( wp_admin_css_uri( "$file-rtl" ) ) . "' type='text/css' />\n", "$file-rtl" );
	}
	*/

}

?>