<?php
/**
 * IXR_ClientMulticall
 *
 * @package IXR
 * @since 1.5.0
 */
class IXR_ClientMulticall extends IXR_Client
{
    var $calls = array();

	/**
	 * PHP5 constructor.
	 */
    function __construct( $server, $path = false, $port = 80 )
    {
        parent::IXR_Client($server, $path, $port);
        $this->useragent = 'The Incutio XML-RPC PHP Library (multicall client)';
    }

	/**
	 * PHP4 constructor.
	 */
	public function IXR_ClientMulticall( $server, $path = false, $port = 80 ) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		self::__construct( $server, $path, $port );
	}

    function addCall()
    {
        $args = func_get_args();
        $methodName = array_shift($args);
        $struct = array(
            'methodName' => $methodName,
            'params' => $args
        );
        $this->calls[] = $struct;
    }

    function query()
    {
        // Prepare multicall, then call the parent::query() method
        return parent::query('system.multicall', $this->calls);
    }
}
