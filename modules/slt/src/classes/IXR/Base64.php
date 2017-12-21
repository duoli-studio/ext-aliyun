<?php namespace Slt\Classes\IXR;

/**
 * Base64
 *
 * @package IXR
 * @since   1.5.0
 */
class Base64
{
	var $data;

	/**
	 * PHP5 constructor.
	 */
	function __construct($data)
	{
		$this->data = $data;
	}


	function getXml()
	{
		return '<base64>' . base64_encode($this->data) . '</base64>';
	}
}
