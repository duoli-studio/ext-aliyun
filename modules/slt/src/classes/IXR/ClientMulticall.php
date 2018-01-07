<?php namespace Slt\Classes\IXR;
/**
 * ClientMulticall
 *
 * @package IXR
 * @since 1.5.0
 */
class ClientMulticall extends Client
{
    var $calls = array();

	/**
	 * PHP5 constructor.
	 */
    function __construct( $server, $path = false, $port = 80 )
    {
        parent::__construct($server, $path, $port);
        $this->user_agent = 'The Incutio XML-RPC PHP Library (multicall client)';
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
