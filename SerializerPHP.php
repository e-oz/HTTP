<?php
namespace Jamm\HTTP;

class SerializerPHP implements ISerializer
{
	public function serialize($data)
	{
		return serialize($data);
	}

	public function unserialize($data)
	{
		return unserialize($data);
	}

	/**
	 * @return string
	 */
	public function getMethodName()
	{
		return 'PHP';
	}
}
