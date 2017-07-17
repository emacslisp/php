<?php

/**
 * IXR_Base64
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_Base64
{
    var $data;

	/**
	 * PHP5 constructor.
	 */
    function __construct( $data )
    {
        $this->data = $data;
    }

	/**
	 * PHP4 constructor.
	 */
	public function IXR_Base64( $data ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		self::__construct( $data );
	}

    function getXml()
    {
        return '<base64>'.base64_encode($this->data).'</base64>';
    }
}
