<?php
class StringHelper {
	public function __construct() {
		
	}
	
	function stringReverse($str){
		$r = '';
		for ($i = mb_strlen($str); $i>=0; $i--) {
			$r .= mb_substr($str, $i, 1);
		}
		return $r;
	}
}
?>