<?php
function get_network($network = null) {
	global $current_site;
	if (empty ( $network ) && isset ( $current_site )) {
		$network = $current_site;
	}
	
	if ($network instanceof WP_Network) {
		$_network = $network;
	} elseif (is_object ( $network )) {
		$_network = new WP_Network ( $network );
	} else {
		$_network = WP_Network::get_instance ( $network );
	}
	
	if (! $_network) {
		return null;
	}
	
	/**
	 * Fires after a network is retrieved.
	 *
	 * @since 4.6.0
	 *       
	 * @param WP_Network $_network
	 *        	Network data.
	 */
	$_network = apply_filters ( 'get_network', $_network );
	
	return $_network;
}
?>