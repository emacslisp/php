<?php


function wp_unslash( $value ) {
	return stripslashes_deep( $value );
}

?>