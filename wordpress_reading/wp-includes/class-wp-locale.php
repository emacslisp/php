<?php

class WP_Locale {
	
	public $text_direction = 'ltr';
	
	public function is_rtl() {
		return 'rtl' == $this->text_direction;
	}
}

?>