<?php
namespace Jamm\HTTP;

interface ISerializer
{
	public function serialize($data);
	
	public function unserialize($data);

	/**
	 * @return string
	 */
	public function getMethodName();
}
